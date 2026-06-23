from datetime import datetime
from typing import NamedTuple

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.integrations.notifications import notify_record_event
from app.models import ModuleRecord, User


class ModuleRecordRow(NamedTuple):
    record: ModuleRecord
    owner_name: str | None


VALID_MODULES = {"inspecoes", "diarios-obra", "manutencao"}


async def list_records(
    session: AsyncSession,
    company_id: int,
    module_slug: str,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list[ModuleRecordRow], int]:
    filters = [
        ModuleRecord.company_id == company_id,
        ModuleRecord.module == module_slug,
        ModuleRecord.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(ModuleRecord.title.ilike(pattern), ModuleRecord.description.ilike(pattern))
        )
    total = await session.scalar(select(func.count(ModuleRecord.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(ModuleRecord, User.name)
            .outerjoin(User, User.id == ModuleRecord.owner_user_id)
            .where(*filters)
            .order_by(ModuleRecord.updated_at.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def create_record(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    module_slug: str,
    *,
    title: str,
    description: str | None,
    category: str | None,
    status: str,
    owner_user_id: int | None,
    notify_user_ids: list[int] | None,
    payload: dict | None = None,
) -> ModuleRecordRow:
    owner_id = owner_user_id or user_id
    record = ModuleRecord(
        company_id=company_id,
        module=module_slug,
        title=title,
        description=description,
        category=category,
        status=status,
        owner_user_id=owner_id,
        created_by_user_id=user_id,
        notify_user_ids=notify_user_ids,
        payload=payload,
    )
    session.add(record)
    await session.flush()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type=module_slug,
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
        module=module_slug,
        owner_user_id=owner_id,
        notify_user_ids=notify_user_ids,
    )
    owner_name = await session.scalar(select(User.name).where(User.id == owner_id))
    return ModuleRecordRow(record, owner_name)


async def update_record(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    module_slug: str,
    record_id: int,
    updates: dict,
) -> ModuleRecordRow | None:
    record = await session.scalar(
        select(ModuleRecord).where(
            ModuleRecord.id == record_id,
            ModuleRecord.company_id == company_id,
            ModuleRecord.module == module_slug,
            ModuleRecord.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    skip_diff = {"notify_user_ids", "payload"}
    before = {k: str(getattr(record, k)) for k in updates if k not in skip_diff}
    for field, value in updates.items():
        setattr(record, field, value)
    if "payload" in updates:
        from sqlalchemy.orm.attributes import flag_modified
        flag_modified(record, "payload")
    diff = compute_diff(before, {k: str(v) for k, v in updates.items() if k not in skip_diff})
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type=module_slug,
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
            module=module_slug,
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
    return ModuleRecordRow(record, owner_name)


async def delete_record(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    module_slug: str,
    record_id: int,
) -> bool:
    record = await session.scalar(
        select(ModuleRecord).where(
            ModuleRecord.id == record_id,
            ModuleRecord.company_id == company_id,
            ModuleRecord.module == module_slug,
            ModuleRecord.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type=module_slug,
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True
