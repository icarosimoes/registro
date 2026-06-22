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
    Meeting,
    MeetingParticipant,
    MeetingSubject,
    ModuleRecord,
    Notification,
    Occurrence,
    Permission,
    Plan,
    Procedure,
    Role,
    Sector,
    ShiftReport,
    Subscription,
    User,
)

SOURCE = "aero-hotel"
TENANT_SLUG = "aero-hotel"


def permission_code(controller: str, action: str) -> str:
    normalized = re.sub(r"[^a-z0-9]+", "-", controller.lower()).strip("-")
    return f"legacy.{normalized}.{action.lower()}"[:120]


async def source_rows(session: AsyncSession, table: str, columns: str = "*") -> list[dict]:
    dialect = session.bind.dialect.name if session.bind else "postgresql"
    q = "`" if dialect == "mysql" else '"'
    result = await session.execute(text(f"SELECT {columns} FROM {q}{table}{q}"))  # noqa: S608
    return [dict(row) for row in result.mappings().all()]


async def get_or_create_tenant(session: AsyncSession) -> Company:
    company = await session.scalar(select(Company).where(Company.slug == TENANT_SLUG))
    if company:
        return company
    company = Company(
        name=os.getenv("LEGACY_TENANT_NAME", "Aero Hotel"),
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
) -> tuple[int, dict[int, int]]:
    mapping: dict[int, int] = {}
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
        await target.flush()
        mapping[row["id"]] = item.id
        count += 1
    return count, mapping


def _status_label(v1_status: int) -> str:
    return {1: "Em andamento", 2: "Concluído", 3: "Concluído"}.get(v1_status, "Em andamento")


def _str(val: Any) -> str | None:
    if val is None:
        return None
    s = str(val).strip()
    return s if s else None


def _dt(val: Any) -> Any:
    if val is None:
        return None
    if isinstance(val, str):
        return datetime.fromisoformat(val)
    return val


async def import_meetings(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
    users: dict[int, int],
) -> int:
    meetings = await source_rows(legacy, "meetings")
    subjects = await source_rows(legacy, "meeting_subjects")
    new_subjects = await source_rows(legacy, "meeting_new_subjects")
    invited = await source_rows(legacy, "meeting_invited_participants")
    registered = await source_rows(legacy, "meeting_registered_participants")

    registered_by_meeting: dict[int, list[int]] = {}
    for row in registered:
        uid = users.get(row["users_id"])
        if uid:
            registered_by_meeting.setdefault(row["meetings_id"], []).append(uid)

    invited_by_meeting: dict[int, list[int]] = {}
    for row in invited:
        uid = users.get(row["participants_id"]) if row.get("participants_id") else None
        if uid:
            invited_by_meeting.setdefault(row["meetings_id"], []).append(uid)

    subjects_by_meeting: dict[int, list[dict]] = {}
    for s in subjects:
        subjects_by_meeting.setdefault(s["meetings_id"], []).append(
            {
                "subject": s["subject"],
                "obs": _str(s.get("obs_subject")),
            }
        )
    for s in new_subjects:
        subjects_by_meeting.setdefault(s["meetings_id"], []).append(
            {
                "subject": s["subject"],
                "obs": _str(s.get("obs_subject")),
            }
        )

    count = 0
    for m in meetings:
        item = await target.scalar(
            select(Meeting).where(
                Meeting.company_id == company.id,
                Meeting.legacy_id == m["id"],
            )
        )
        if item is None:
            item = Meeting(company_id=company.id, legacy_id=m["id"])
            target.add(item)

        local_str = _str(m.get("local")) or ""
        dt_str = str(m.get("datetime") or "")[:16]
        scheduled = _dt(m.get("datetime"))

        item.title = f"Reunião {dt_str} — {local_str}".strip(" —")
        item.description = local_str
        item.location = local_str
        item.scheduled_at = scheduled
        item.status = _status_label(m.get("status", 1))
        item.owner_user_id = users.get(m["users_id"])
        item.created_by_user_id = users.get(m["users_id"])
        item.notify_user_ids = registered_by_meeting.get(m["id"])
        item.deleted_at = _dt(m.get("deleted_at"))
        item.created_at = _dt(m.get("created_at")) or datetime.now(UTC).replace(tzinfo=None)
        item.updated_at = _dt(m.get("updated_at")) or item.created_at
        await target.flush()

        participant_ids = set(registered_by_meeting.get(m["id"], []))
        participant_ids.update(invited_by_meeting.get(m["id"], []))
        for uid in participant_ids:
            existing = await target.scalar(
                select(MeetingParticipant).where(
                    MeetingParticipant.meeting_id == item.id,
                    MeetingParticipant.user_id == uid,
                )
            )
            if not existing:
                target.add(
                    MeetingParticipant(
                        meeting_id=item.id,
                        user_id=uid,
                        role="attendee",
                    )
                )

        meeting_subjects = subjects_by_meeting.get(m["id"], [])
        existing_subjects = (
            await target.scalars(select(MeetingSubject).where(MeetingSubject.meeting_id == item.id))
        ).all()
        if not existing_subjects:
            for idx, s in enumerate(meeting_subjects):
                target.add(
                    MeetingSubject(
                        meeting_id=item.id,
                        title=s["subject"][:255],
                        description=s.get("obs"),
                        sort_order=idx,
                    )
                )

        count += 1
    return count


async def import_shift_reports(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
    users: dict[int, int],
    locations: dict[int, int],
    functions: dict[int, int],
    occurrences_map: dict[int, int],
) -> int:
    reports = await source_rows(legacy, "shift_reports")
    frequencies = await source_rows(legacy, "shift_report_frequencies")
    maintenances = await source_rows(legacy, "shift_report_maintenences")
    complaints = await source_rows(legacy, "shift_report_customer_complaints")
    extras = await source_rows(legacy, "shift_report_extras")
    comments = await source_rows(legacy, "shift_report_comments")

    freq_by_report: dict[int, list[dict]] = {}
    for f in frequencies:
        freq_by_report.setdefault(f["shift_reports_id"], []).append(
            {
                "employee": f.get("employee"),
                "occupation": f.get("occupation"),
                "func_id": functions.get(f["func_id"]) if f.get("func_id") else None,
            }
        )

    maint_by_report: dict[int, list[dict]] = {}
    for m in maintenances:
        maint_by_report.setdefault(m["shift_reports_id"], []).append(
            {
                "uh": m.get("uh"),
                "status": m.get("status"),
                "reason": _str(m.get("reason")),
                "providence": _str(m.get("providence")),
                "location_id": locations.get(m["local_id"]) if m.get("local_id") else None,
                "occurrence_id": (
                    occurrences_map.get(m["occurrences_id"]) if m.get("occurrences_id") else None
                ),
            }
        )

    compl_by_report: dict[int, list[dict]] = {}
    for c in complaints:
        compl_by_report.setdefault(c["shift_reports_id"], []).append(
            {
                "problem": _str(c.get("problem")),
                "providence": _str(c.get("providence")),
                "occurrence_id": (
                    occurrences_map.get(c["occurrences_id"]) if c.get("occurrences_id") else None
                ),
            }
        )

    extra_by_report: dict[int, list[dict]] = {}
    for e in extras:
        extra_by_report.setdefault(e["shift_reports_id"], []).append(
            {
                "extrawork": _str(e.get("extrawork")),
                "reasons": _str(e.get("reasons")),
            }
        )

    comment_by_report: dict[int, list[dict]] = {}
    for c in comments:
        comment_by_report.setdefault(c["shift_reports_id"], []).append(
            {
                "comments": _str(c.get("comments")),
                "occurrence_id": (
                    occurrences_map.get(c["occurrences_id"]) if c.get("occurrences_id") else None
                ),
            }
        )

    count = 0
    for r in reports:
        item = await target.scalar(
            select(ShiftReport).where(
                ShiftReport.company_id == company.id,
                ShiftReport.legacy_id == r["id"],
            )
        )
        if item is None:
            item = ShiftReport(company_id=company.id, legacy_id=r["id"])
            target.add(item)

        beginning = str(r.get("beginning") or "")[:16]
        supervisor = _str(r.get("supervisor")) or ""
        item.title = f"Turno {beginning} — {supervisor}".strip(" —")
        item.shift_date = _dt(r.get("beginning")).date() if r.get("beginning") else None
        item.shift_type = "diurno" if r.get("beginning") and "06:" in beginning else "noturno"
        item.started_at = _dt(r.get("beginning"))
        item.ended_at = _dt(r.get("end"))
        item.supervisor = supervisor
        item.return_of_customers = r.get("return_of_customers")
        item.input_quantity = r.get("inputQuantity")
        item.output_quantity = r.get("outputQuantity")
        item.status = _status_label(r.get("status", 1))
        item.owner_user_id = users.get(r["users_id"])
        item.created_by_user_id = users.get(r["users_id"])
        item.payload = {
            "frequencies": freq_by_report.get(r["id"], []),
            "maintenances": maint_by_report.get(r["id"], []),
            "complaints": compl_by_report.get(r["id"], []),
            "extras": extra_by_report.get(r["id"], []),
            "comments": comment_by_report.get(r["id"], []),
        }
        item.created_at = _dt(r.get("created_at")) or datetime.now(UTC).replace(tzinfo=None)
        item.updated_at = _dt(r.get("updated_at")) or item.created_at
        count += 1
    return count


async def import_occurrence_children(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
    users: dict[int, int],
    occurrences_map: dict[int, int],
) -> dict[str, int]:
    occ_comments = await source_rows(legacy, "occurrence_comments")
    occ_participants = await source_rows(legacy, "occurrence_participants")

    comments_by_occ: dict[int, list[dict]] = {}
    for c in occ_comments:
        new_occ_id = occurrences_map.get(c["occurrences_id"])
        if new_occ_id:
            comments_by_occ.setdefault(new_occ_id, []).append(
                {
                    "legacy_id": c["id"],
                    "comments": _str(c.get("comments")),
                    "user_id": users.get(c["users_id"]) if c.get("users_id") else None,
                    "created_at": str(c.get("created_at") or ""),
                }
            )

    participants_by_occ: dict[int, list[int]] = {}
    for p in occ_participants:
        new_occ_id = occurrences_map.get(p["occurrences_id"])
        uid = users.get(p["users_id"]) if p.get("users_id") else None
        if new_occ_id and uid:
            participants_by_occ.setdefault(new_occ_id, []).append(uid)

    updated = 0
    all_occ_ids = set(comments_by_occ.keys()) | set(participants_by_occ.keys())
    for occ_id in all_occ_ids:
        occ = await target.scalar(select(Occurrence).where(Occurrence.id == occ_id))
        if occ is None:
            continue
        occ.comments = json.dumps(comments_by_occ.get(occ_id, []), ensure_ascii=False, default=str)
        occ.notify_user_ids = participants_by_occ.get(occ_id)
        updated += 1

    return {
        "comments": len(occ_comments),
        "participants": len(occ_participants),
        "occurrences_updated": updated,
    }


async def import_check_suites(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
    users: dict[int, int],
    locations: dict[int, int],
    occurrences_map: dict[int, int],
) -> int:
    suites = await source_rows(legacy, "check_suites")
    items = await source_rows(legacy, "check_suite_items")

    items_by_suite: dict[int, list[dict]] = {}
    for i in items:
        items_by_suite.setdefault(i["check_suite_id"], []).append(
            {
                "item": _str(i.get("item")),
                "valuation": i.get("valuation"),
                "register": _str(i.get("register")),
                "occurrence_id": (
                    occurrences_map.get(i["occurrences_id"]) if i.get("occurrences_id") else None
                ),
            }
        )

    count = 0
    for s in suites:
        item = await target.scalar(
            select(ModuleRecord).where(
                ModuleRecord.company_id == company.id,
                ModuleRecord.module == "inspecoes",
                ModuleRecord.legacy_id == s["id"],
            )
        )
        if item is None:
            item = ModuleRecord(company_id=company.id, module="inspecoes", legacy_id=s["id"])
            target.add(item)

        date_str = str(s.get("date") or "")[:10]
        maid = _str(s.get("maid")) or ""
        item.title = f"Conferência {date_str} — {maid}".strip(" —")
        item.description = _str(s.get("obs"))
        item.category = "Conferência de Suíte"
        item.status = _status_label(s.get("status", 1))
        item.owner_user_id = users.get(s["user_id"]) if s.get("user_id") else None
        item.deleted_at = _dt(s.get("deleted_at"))
        item.created_at = _dt(s.get("created_at")) or datetime.now(UTC).replace(tzinfo=None)
        item.updated_at = _dt(s.get("updated_at")) or item.created_at
        item.payload = {
            "date": date_str,
            "maid": maid,
            "obs": _str(s.get("obs")),
            "location_id": locations.get(s["local_id"]) if s.get("local_id") else None,
            "items": items_by_suite.get(s["id"], []),
        }
        count += 1
    return count


async def import_audit_reports(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
    users: dict[int, int],
) -> int:
    reports = await source_rows(legacy, "audit_reports")
    items1 = await source_rows(legacy, "audit_report_item1s")
    items2 = await source_rows(legacy, "audit_report_item2s")
    items3 = await source_rows(legacy, "audit_report_item3s")

    def group_items(rows: list[dict]) -> dict[int, list[dict]]:
        by_report: dict[int, list[dict]] = {}
        for r in rows:
            by_report.setdefault(r["audit_report_id"], []).append(
                {
                    "reserve": _str(r.get("reserve")),
                    "name": _str(r.get("name")),
                    "pax": r.get("pax"),
                    "deleted_at": str(r.get("deleted_at") or ""),
                }
            )
        return by_report

    items1_by = group_items(items1)
    items2_by = group_items(items2)
    items3_by = group_items(items3)

    count = 0
    for r in reports:
        item = await target.scalar(
            select(ModuleRecord).where(
                ModuleRecord.company_id == company.id,
                ModuleRecord.module == "manutencao",
                ModuleRecord.legacy_id == r["id"],
                ModuleRecord.category == "Auditoria Noturna",
            )
        )
        if item is None:
            item = ModuleRecord(
                company_id=company.id,
                module="manutencao",
                legacy_id=r["id"],
                category="Auditoria Noturna",
            )
            target.add(item)

        date_str = str(r.get("date") or "")[:10]
        item.title = f"Auditoria Noturna {date_str}"
        item.status = "Concluído"
        item.owner_user_id = users.get(r["user_id"]) if r.get("user_id") else None
        item.created_at = _dt(r.get("created_at")) or datetime.now(UTC).replace(tzinfo=None)
        item.updated_at = _dt(r.get("updated_at")) or item.created_at
        item.payload = {
            "date": date_str,
            "occupation": _str(r.get("occupation")),
            "average_daily": _str(r.get("average_daily")),
            "guests": r.get("guests"),
            "uh": r.get("uh"),
            "maintenance_apartment": r.get("maintenance_apartment"),
            "cleaning": r.get("cleaning"),
            "walk_in": r.get("walk_in"),
            "obs": _str(r.get("obs")),
            "AB": _str(r.get("AB")),
            "reception": _str(r.get("reception")),
            "reservations": _str(r.get("reservations")),
            "governance": _str(r.get("governance")),
            "housekeeping": _str(r.get("housekeeping")),
            "maintenance": _str(r.get("maintenance")),
            "ti": _str(r.get("ti")),
            "security": _str(r.get("security")),
            "items1": items1_by.get(r["id"], []),
            "items2": items2_by.get(r["id"], []),
            "items3": items3_by.get(r["id"], []),
        }
        count += 1
    return count


async def import_procedure_files(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
) -> int:
    files = await source_rows(legacy, "procedure_files")
    count = 0
    for f in files:
        proc = await target.scalar(
            select(Procedure).where(
                Procedure.company_id == company.id,
                Procedure.legacy_id == f["procedure_id"],
            )
        )
        if proc is None:
            continue
        existing_file = proc.file or ""
        file_entry = _str(f.get("file")) or ""
        name_entry = _str(f.get("name")) or ""
        entry = f"{name_entry}:{file_entry}" if name_entry else file_entry
        if entry and entry not in existing_file:
            proc.file = f"{existing_file}|{entry}".strip("|") if existing_file else entry
        count += 1
    return count


async def import_notifications_v1(
    target: AsyncSession,
    legacy: AsyncSession,
    company: Company,
    users: dict[int, int],
    occurrences_map: dict[int, int],
) -> int:
    rows = await source_rows(legacy, "notifications")
    count = 0
    for n in rows:
        new_user_id = users.get(n["user_id"]) if n.get("user_id") else None
        if not new_user_id:
            continue

        entity_type = None
        entity_id = None
        if n.get("occurrence_id"):
            entity_type = "occurrence"
            entity_id = occurrences_map.get(n["occurrence_id"])
        elif n.get("meeting_id"):
            entity_type = "meeting"
            entity_id = n["meeting_id"]

        notif = Notification(
            company_id=company.id,
            user_id=new_user_id,
            title=_str(n.get("msg")) or "Notificação V1",
            category="legacy",
            entity_type=entity_type,
            entity_id=entity_id,
            read_at=_dt(n.get("created_at")) if n.get("checked") else None,
            created_at=_dt(n.get("created_at")) or datetime.now(UTC).replace(tzinfo=None),
        )
        target.add(notif)
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
            procedure_files = await import_procedure_files(target, legacy, company)
            occurrences, occurrences_map = await import_occurrences(
                target, legacy, company, users, sectors, locations
            )
            occ_children = await import_occurrence_children(
                target, legacy, company, users, occurrences_map
            )
            meetings = await import_meetings(target, legacy, company, users)
            shift_reports = await import_shift_reports(
                target, legacy, company, users, locations, functions, occurrences_map
            )
            check_suites = await import_check_suites(
                target, legacy, company, users, locations, occurrences_map
            )
            audit_reports = await import_audit_reports(target, legacy, company, users)
            notifications_v1 = await import_notifications_v1(
                target, legacy, company, users, occurrences_map
            )
            report = {
                "tenant": company.slug,
                "users": len(users),
                "sectors": len(sectors),
                "locations": len(locations),
                "functions": len(functions),
                "procedures": procedures,
                "procedure_files": procedure_files,
                "occurrences": occurrences,
                "occurrence_comments": occ_children["comments"],
                "occurrence_participants": occ_children["participants"],
                "meetings": meetings,
                "shift_reports": shift_reports,
                "check_suites": check_suites,
                "audit_reports": audit_reports,
                "notifications_v1": notifications_v1,
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
