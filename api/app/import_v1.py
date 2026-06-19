import asyncio
import json
import os
import re
from datetime import UTC, datetime, timedelta
from typing import Any

import bcrypt
from sqlalchemy import select, text
from sqlalchemy.ext.asyncio import AsyncSession, create_async_engine
from sqlalchemy.orm import selectinload

from app.core.database import SessionLocal
from app.models import (
    Company,
    Function,
    LegacyImportRun,
    Location,
    Occurrence,
    Permission,
    Plan,
    Procedure,
    Role,
    Sector,
    Subscription,
    User,
)

SOURCE = "aero-v1"
TENANT_SLUG = "aero-v1"


def permission_code(controller: str, action: str) -> str:
    normalized = re.sub(r"[^a-z0-9]+", "-", controller.lower()).strip("-")
    return f"legacy.{normalized}.{action.lower()}"[:120]


async def source_rows(session: AsyncSession, table: str, columns: str = "*") -> list[dict]:
    result = await session.execute(text(f"SELECT {columns} FROM `{table}`"))  # noqa: S608
    return [dict(row) for row in result.mappings().all()]


async def get_or_create_tenant(session: AsyncSession) -> Company:
    company = await session.scalar(select(Company).where(Company.slug == TENANT_SLUG))
    if company:
        return company
    company = Company(
        name=os.getenv("LEGACY_TENANT_NAME", "Aero Hotel — V1"),
        slug=TENANT_SLUG,
        email=os.getenv("LEGACY_TENANT_EMAIL", "legado@registro.local"),
        status="active",
    )
    session.add(company)
    await session.flush()
    plan = await session.scalar(select(Plan).where(Plan.code == "professional"))
    if plan:
        session.add(
            Subscription(
                company_id=company.id,
                plan_id=plan.id,
                status="trial",
                trial_ends_at=(datetime.now(UTC) + timedelta(days=30)).replace(tzinfo=None),
            )
        )
    return company


async def import_permissions(target: AsyncSession, legacy: AsyncSession, company: Company) -> Role:
    role = await target.scalar(
        select(Role)
        .options(selectinload(Role.permissions))
        .where(Role.company_id == company.id, Role.code == "legacy-admin")
    )
    if role is None:
        role = Role(
            company_id=company.id,
            code="legacy-admin",
            name="Administrador V1",
            permissions=[],
        )
        target.add(role)
        await target.flush()

    permissions: list[Permission] = []
    for row in await source_rows(legacy, "acls"):
        code = permission_code(row["controller"], row["action"])
        permission = await target.scalar(select(Permission).where(Permission.code == code))
        if permission is None:
            permission = Permission(
                code=code,
                name=row.get("name") or f"{row['controller']} {row['action']}",
                module="legacy",
            )
            target.add(permission)
            await target.flush()
        permissions.append(permission)
    role.permissions = permissions
    return role


async def import_users(
    target: AsyncSession, legacy: AsyncSession, company: Company, role: Role
) -> dict[int, int]:
    mapping: dict[int, int] = {}
    for row in await source_rows(legacy, "users"):
        user = await target.scalar(
            select(User).where(User.company_id == company.id, User.legacy_id == row["id"])
        )
        if user is None:
            user = User(company_id=company.id, legacy_id=row["id"])
            target.add(user)
        user.role_id = role.id
        user.name = row["name"]
        user.email = row["email"].strip().lower()
        user.password = row["password"]
        user.active = bool(row.get("status")) and row.get("deleted_at") is None
        user.email_verified_at = row.get("email_verified_at")
        user.deleted_at = row.get("deleted_at")
        user.created_at = row.get("created_at") or datetime.now(UTC).replace(tzinfo=None)
        user.updated_at = row.get("updated_at") or user.created_at
        await target.flush()
        mapping[row["id"]] = user.id
    return mapping


async def create_demo_access(target: AsyncSession, company: Company, role: Role) -> None:
    password = os.getenv("LEGACY_DEMO_PASSWORD")
    if not password:
        return
    email = "v1-demo@registro.local"
    user = await target.scalar(
        select(User).where(User.company_id == company.id, User.email == email)
    )
    if user is None:
        user = User(company_id=company.id, name="Acesso local V1", email=email)
        target.add(user)
    user.role_id = role.id
    user.password = bcrypt.hashpw(password.encode(), bcrypt.gensalt()).decode()
    user.active = True
    user.deleted_at = None


async def import_catalog(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
    source_table: str,
    model: Any,
) -> dict[int, int]:
    mapping: dict[int, int] = {}
    for row in await source_rows(legacy, source_table):
        item = await target.scalar(
            select(model).where(model.company_id == company.id, model.legacy_id == row["id"])
        )
        if item is None:
            item = model(company_id=company.id, legacy_id=row["id"])
            target.add(item)
        item.name = row["name"]
        item.deleted_at = row.get("deleted_at")
        item.created_at = row.get("created_at") or datetime.now(UTC).replace(tzinfo=None)
        item.updated_at = row.get("updated_at") or item.created_at
        await target.flush()
        mapping[row["id"]] = item.id
    return mapping


async def import_procedures(target: AsyncSession, legacy: AsyncSession, company: Company) -> int:
    count = 0
    for row in await source_rows(legacy, "procedures"):
        item = await target.scalar(
            select(Procedure).where(
                Procedure.company_id == company.id, Procedure.legacy_id == row["id"]
            )
        )
        if item is None:
            item = Procedure(company_id=company.id, legacy_id=row["id"])
            target.add(item)
        for field in ("name", "link", "file", "deleted_at"):
            setattr(item, field, row.get(field))
        item.created_at = row.get("created_at") or datetime.now(UTC).replace(tzinfo=None)
        item.updated_at = row.get("updated_at") or item.created_at
        count += 1
    return count


async def import_occurrences(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
    users: dict[int, int],
    sectors: dict[int, int],
    locations: dict[int, int],
) -> int:
    count = 0
    for row in await source_rows(legacy, "occurrences"):
        item = await target.scalar(
            select(Occurrence).where(
                Occurrence.company_id == company.id, Occurrence.legacy_id == row["id"]
            )
        )
        if item is None:
            item = Occurrence(company_id=company.id, legacy_id=row["id"])
            target.add(item)
        item.title = row["title"]
        item.description = row.get("description")
        item.comments = row.get("comments")
        item.unit = row.get("unit")
        item.deadline = row.get("deadline")
        item.status = row.get("status") or 1
        item.legacy_type_id = row.get("type_occurrences_id")
        item.legacy_receiver_user_id = row.get("receiver_user")
        item.location_id = locations.get(row["local_id"]) if row.get("local_id") else None
        item.sector_id = sectors.get(row["sector_id"]) if row.get("sector_id") else None
        item.owner_user_id = users.get(row["users_id"]) if row.get("users_id") else None
        item.created_by_user_id = users.get(row["created_by"]) if row.get("created_by") else None
        item.updated_by_user_id = users.get(row["updated_by"]) if row.get("updated_by") else None
        item.file = row.get("file")
        item.deleted_at = row.get("deleted_at")
        item.created_at = row.get("created_at") or datetime.now(UTC).replace(tzinfo=None)
        item.updated_at = row.get("updated_at") or item.created_at
        count += 1
    return count


async def run() -> None:
    legacy_url = os.environ["LEGACY_DATABASE_URL"]
    checksum = os.getenv("LEGACY_DUMP_SHA256", "unknown-" + datetime.now(UTC).isoformat())
    if SessionLocal is None:
        raise RuntimeError("DATABASE_URL não configurada")
    legacy_engine = create_async_engine(legacy_url, pool_pre_ping=True)
    report: dict[str, int | str] = {}
    try:
        async with SessionLocal() as target, AsyncSession(legacy_engine) as legacy:
            previous = await target.scalar(
                select(LegacyImportRun).where(
                    LegacyImportRun.checksum_sha256 == checksum,
                    LegacyImportRun.status == "completed",
                )
            )
            if previous:
                company = await target.scalar(select(Company).where(Company.slug == TENANT_SLUG))
                role = (
                    await target.scalar(
                        select(Role).where(
                            Role.company_id == company.id,
                            Role.code == "legacy-admin",
                        )
                    )
                    if company
                    else None
                )
                if company and role:
                    await create_demo_access(target, company, role)
                    await target.commit()
                print(previous.report)
                return
            started = datetime.now(UTC).replace(tzinfo=None)
            company = await get_or_create_tenant(target)
            role = await import_permissions(target, legacy, company)
            users = await import_users(target, legacy, company, role)
            await create_demo_access(target, company, role)
            sectors = await import_catalog(target, legacy, company, "sectors", Sector)
            locations = await import_catalog(target, legacy, company, "locals", Location)
            functions = await import_catalog(target, legacy, company, "funcs", Function)
            procedures = await import_procedures(target, legacy, company)
            occurrences = await import_occurrences(
                target, legacy, company, users, sectors, locations
            )
            report = {
                "tenant": company.slug,
                "users": len(users),
                "sectors": len(sectors),
                "locations": len(locations),
                "functions": len(functions),
                "procedures": procedures,
                "occurrences": occurrences,
            }
            target.add(
                LegacyImportRun(
                    source=SOURCE,
                    checksum_sha256=checksum,
                    status="completed",
                    report=json.dumps(report, ensure_ascii=False),
                    started_at=started,
                    finished_at=datetime.now(UTC).replace(tzinfo=None),
                )
            )
            await target.commit()
            print(json.dumps(report, ensure_ascii=False))
    finally:
        await legacy_engine.dispose()


if __name__ == "__main__":
    asyncio.run(run())
