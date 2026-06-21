from datetime import datetime

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.integrations.notifications import notify_record_event
from app.models import MaintenanceRecord, User


async def list_records(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
    status: str | None = None,
) -> tuple[list[tuple], int]:
    filters = [
        MaintenanceRecord.company_id == company_id,
        MaintenanceRecord.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(
                MaintenanceRecord.title.ilike(pattern),
                MaintenanceRecord.description.ilike(pattern),
            )
        )
    if status:
        filters.append(MaintenanceRecord.status == status)
    total = await session.scalar(select(func.count(MaintenanceRecord.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(MaintenanceRecord, User.name)
            .outerjoin(User, User.id == MaintenanceRecord.owner_user_id)
            .where(*filters)
            .order_by(MaintenanceRecord.updated_at.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_record(
    session: AsyncSession, company_id: int, record_id: int,
) -> tuple[MaintenanceRecord, str | None] | None:
    row = (
        await session.execute(
            select(MaintenanceRecord, User.name)
            .outerjoin(User, User.id == MaintenanceRecord.owner_user_id)
            .where(
                MaintenanceRecord.id == record_id,
                MaintenanceRecord.company_id == company_id,
                MaintenanceRecord.deleted_at.is_(None),
            )
        )
    ).first()
    if row is None:
        return None
    return row[0], row[1]


async def create_record(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    *,
    title: str,
    description: str | None,
    category: str | None,
    status: str,
    priority: str | None,
    location_id: int | None,
    owner_user_id: int | None,
    notify_user_ids: list[int] | None,
) -> tuple[MaintenanceRecord, str | None]:
    owner_id = owner_user_id or user_id
    rec = MaintenanceRecord(
        company_id=company_id,
        title=title,
        description=description,
        category=category,
        status=status,
        priority=priority,
        location_id=location_id,
        owner_user_id=owner_id,
        created_by_user_id=user_id,
        notify_user_ids=notify_user_ids,
    )
    session.add(rec)
    await session.commit()
    await session.refresh(rec)
    await record_event(
        session, company_id=company_id, user_id=user_id,
        entity_type="maintenance", entity_id=rec.id, event_type="create",
    )
    await session.commit()
    await notify_record_event(
        session, company_id=company_id, actor_name=user_name, actor_email=user_email,
        event="create", title=rec.title, module="Manutenção",
        owner_user_id=owner_id, notify_user_ids=notify_user_ids,
    )
    owner_name = await session.scalar(select(User.name).where(User.id == owner_id))
    return rec, owner_name


async def update_record(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    record_id: int,
    updates: dict,
) -> tuple[MaintenanceRecord, str | None] | None:
    rec = await session.scalar(
        select(MaintenanceRecord).where(
            MaintenanceRecord.id == record_id,
            MaintenanceRecord.company_id == company_id,
            MaintenanceRecord.deleted_at.is_(None),
        )
    )
    if rec is None:
        return None
    before = {k: str(getattr(rec, k)) for k in updates if k != "notify_user_ids"}
    for field, value in updates.items():
        setattr(rec, field, value)
    diff = compute_diff(before, {k: str(v) for k, v in updates.items() if k != "notify_user_ids"})
    if diff:
        await record_event(
            session, company_id=company_id, user_id=user_id,
            entity_type="maintenance", entity_id=rec.id,
            event_type="update", diff=diff,
        )
    await session.commit()
    await session.refresh(rec)
    if diff:
        detail = "; ".join(f"{k}: {v}" for k, v in diff.items())
        await notify_record_event(
            session, company_id=company_id, actor_name=user_name, actor_email=user_email,
            event="update", title=rec.title, module="Manutenção",
            owner_user_id=rec.owner_user_id, created_by_user_id=rec.created_by_user_id,
            notify_user_ids=rec.notify_user_ids, detail=detail,
        )
    owner_name = (
        await session.scalar(select(User.name).where(User.id == rec.owner_user_id))
        if rec.owner_user_id else None
    )
    return rec, owner_name


async def delete_record(
    session: AsyncSession, company_id: int, user_id: int, record_id: int,
) -> bool:
    rec = await session.scalar(
        select(MaintenanceRecord).where(
            MaintenanceRecord.id == record_id,
            MaintenanceRecord.company_id == company_id,
            MaintenanceRecord.deleted_at.is_(None),
        )
    )
    if rec is None:
        return False
    rec.deleted_at = datetime.now()
    await record_event(
        session, company_id=company_id, user_id=user_id,
        entity_type="maintenance", entity_id=rec.id, event_type="delete",
    )
    await session.commit()
    return True
