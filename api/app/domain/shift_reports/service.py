from datetime import date, datetime
from typing import NamedTuple

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.integrations.notifications import notify_record_event
from app.models import ShiftReport, User


class ShiftReportRow(NamedTuple):
    report: ShiftReport
    owner_name: str | None


async def list_shift_reports(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
    date_from: date | None = None,
    date_to: date | None = None,
) -> tuple[list[ShiftReportRow], int]:
    filters = [
        ShiftReport.company_id == company_id,
        ShiftReport.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(
                ShiftReport.title.ilike(pattern),
                ShiftReport.description.ilike(pattern),
            )
        )
    if date_from:
        filters.append(ShiftReport.shift_date >= date_from)
    if date_to:
        filters.append(ShiftReport.shift_date <= date_to)
    total = await session.scalar(select(func.count(ShiftReport.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(ShiftReport, User.name)
            .outerjoin(User, User.id == ShiftReport.owner_user_id)
            .where(*filters)
            .order_by(
                ShiftReport.updated_at.desc(),
                ShiftReport.id.desc(),
            )
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_shift_report(
    session: AsyncSession,
    company_id: int,
    report_id: int,
) -> ShiftReportRow | None:
    row = (
        await session.execute(
            select(ShiftReport, User.name)
            .outerjoin(User, User.id == ShiftReport.owner_user_id)
            .where(
                ShiftReport.id == report_id,
                ShiftReport.company_id == company_id,
                ShiftReport.deleted_at.is_(None),
            )
        )
    ).first()
    if row is None:
        return None
    return ShiftReportRow(row[0], row[1])


async def create_shift_report(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    *,
    title: str,
    description: str | None,
    shift_date: date | None,
    shift_type: str | None,
    started_at: datetime | None,
    ended_at: datetime | None,
    status: str,
    owner_user_id: int | None,
    notify_user_ids: list[int] | None,
) -> ShiftReportRow:
    record = ShiftReport(
        company_id=company_id,
        title=title,
        description=description,
        shift_date=shift_date,
        shift_type=shift_type,
        started_at=started_at,
        ended_at=ended_at,
        status=status,
        owner_user_id=owner_user_id,
        created_by_user_id=user_id,
        notify_user_ids=notify_user_ids,
    )
    session.add(record)
    await session.flush()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="shift_report",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(record)
    await notify_record_event(
        session,
        company_id=company_id,
        actor_name=user_name,
        actor_email=user_email,
        event="create",
        title=record.title,
        module="Passagem de Turno",
        owner_user_id=owner_user_id,
        notify_user_ids=notify_user_ids,
    )
    owner_name = (
        await session.scalar(select(User.name).where(User.id == record.owner_user_id))
        if record.owner_user_id
        else None
    )
    return ShiftReportRow(record, owner_name)


async def update_shift_report(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    report_id: int,
    updates: dict,
) -> ShiftReportRow | None:
    record = await session.scalar(
        select(ShiftReport).where(
            ShiftReport.id == report_id,
            ShiftReport.company_id == company_id,
            ShiftReport.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    before = {k: str(getattr(record, k)) for k in updates if k != "notify_user_ids"}
    for field, value in updates.items():
        setattr(record, field, value)
    diff = compute_diff(
        before,
        {k: str(v) for k, v in updates.items() if k != "notify_user_ids"},
    )
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="shift_report",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    await session.refresh(record)
    if diff:
        detail = "; ".join(f"{k}: {v}" for k, v in diff.items())
        await notify_record_event(
            session,
            company_id=company_id,
            actor_name=user_name,
            actor_email=user_email,
            event="update",
            title=record.title,
            module="Passagem de Turno",
            owner_user_id=record.owner_user_id,
            created_by_user_id=record.created_by_user_id,
            notify_user_ids=record.notify_user_ids,
            detail=detail,
        )
    owner_name = (
        await session.scalar(select(User.name).where(User.id == record.owner_user_id))
        if record.owner_user_id
        else None
    )
    return ShiftReportRow(record, owner_name)


async def delete_shift_report(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    report_id: int,
) -> bool:
    record = await session.scalar(
        select(ShiftReport).where(
            ShiftReport.id == report_id,
            ShiftReport.company_id == company_id,
            ShiftReport.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="shift_report",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True
