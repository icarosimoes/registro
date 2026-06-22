from typing import NamedTuple

from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.orm import selectinload

from app.core.audit import record_event
from app.core.cache import cache_get, cache_set
from app.models import Permission, Role, User


class RoleRow(NamedTuple):
    role: Role
    user_count: int


TTL_PERMISSIONS = 3600


async def list_permissions(session: AsyncSession) -> list:
    from types import SimpleNamespace

    cache_key = "registro:global:permissions"
    hit = await cache_get(cache_key)
    if hit is not None:
        return [SimpleNamespace(**p) for p in hit]

    rows = (
        (await session.execute(select(Permission).order_by(Permission.module, Permission.code)))
        .scalars()
        .all()
    )
    serialized = [{"id": p.id, "code": p.code, "name": p.name, "module": p.module} for p in rows]
    await cache_set(cache_key, serialized, TTL_PERMISSIONS)
    return list(rows)


async def list_roles(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
) -> tuple[list[RoleRow], int]:
    base = [Role.company_id == company_id]
    total = await session.scalar(select(func.count(Role.id)).where(*base)) or 0

    user_count_sub = (
        select(func.count(User.id))
        .where(User.role_id == Role.id, User.deleted_at.is_(None))
        .correlate(Role)
        .scalar_subquery()
    )
    rows = (
        await session.execute(
            select(Role, user_count_sub.label("user_count"))
            .options(selectinload(Role.permissions))
            .where(*base)
            .order_by(Role.name)
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_role(session: AsyncSession, company_id: int, role_id: int) -> Role | None:
    return (
        await session.execute(
            select(Role)
            .options(selectinload(Role.permissions))
            .where(Role.id == role_id, Role.company_id == company_id)
        )
    ).scalar_one_or_none()


async def create_role(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    *,
    code: str,
    name: str,
    permission_codes: list[str],
) -> Role:
    role = Role(company_id=company_id, code=code, name=name)
    session.add(role)
    await session.flush()

    if permission_codes:
        perms = (
            (await session.execute(select(Permission).where(Permission.code.in_(permission_codes))))
            .scalars()
            .all()
        )
        role.permissions = list(perms)

    await session.flush()

    role_id = role.id
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="role",
        entity_id=role_id,
        event_type="create",
    )
    await session.commit()
    role = (
        await session.execute(
            select(Role).options(selectinload(Role.permissions)).where(Role.id == role_id)
        )
    ).scalar_one()
    return role


async def update_role(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    role_id: int,
    *,
    name: str | None = None,
    permission_codes: list[str] | None = None,
) -> Role | None:
    role = await get_role(session, company_id, role_id)
    if role is None:
        return None

    diff = {}
    if name is not None and name != role.name:
        diff["name"] = {"from": role.name, "to": name}
        role.name = name

    if permission_codes is not None:
        old_codes = sorted(p.code for p in role.permissions)
        if sorted(permission_codes) != old_codes:
            diff["permissions"] = {"from": old_codes, "to": sorted(permission_codes)}
            perms = (
                (
                    await session.execute(
                        select(Permission).where(Permission.code.in_(permission_codes))
                    )
                )
                .scalars()
                .all()
            )
            role.permissions = list(perms)

    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="role",
            entity_id=role.id,
            event_type="update",
            diff=diff,
        )

    await session.commit()
    role = (
        await session.execute(
            select(Role).options(selectinload(Role.permissions)).where(Role.id == role_id)
        )
    ).scalar_one()
    return role


async def delete_role(
    session: AsyncSession, company_id: int, user_id: int, role_id: int
) -> bool | str:
    role = await get_role(session, company_id, role_id)
    if role is None:
        return False

    user_count = await session.scalar(
        select(func.count(User.id)).where(User.role_id == role_id, User.deleted_at.is_(None))
    )
    if user_count and user_count > 0:
        return "has_users"

    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="role",
        entity_id=role.id,
        event_type="delete",
    )
    await session.delete(role)
    await session.commit()
    return True
