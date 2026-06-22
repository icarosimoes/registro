from datetime import datetime

from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.models import Procedure


async def list_procedures(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list, int]:
    filters = [Procedure.company_id == company_id, Procedure.deleted_at.is_(None)]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(Procedure.name.ilike(pattern))
    total = await session.scalar(select(func.count(Procedure.id)).where(*filters)) or 0
    rows = (
        await session.scalars(
            select(Procedure)
            .where(*filters)
            .order_by(Procedure.name)
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return list(rows), total


async def create_procedure(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    *,
    name: str,
    link: str | None,
    file: str | None,
) -> Procedure:
    record = Procedure(
        company_id=company_id,
        name=name,
        link=link,
        file=file,
    )
    session.add(record)
    await session.flush()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="procedure",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(record)
    return record


async def update_procedure(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    procedure_id: int,
    updates: dict,
) -> Procedure | None:
    record = await session.scalar(
        select(Procedure).where(
            Procedure.id == procedure_id,
            Procedure.company_id == company_id,
            Procedure.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    before = {k: str(getattr(record, k)) for k in updates}
    for field, value in updates.items():
        setattr(record, field, value)
    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="procedure",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    await session.refresh(record)
    return record


async def delete_procedure(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    procedure_id: int,
) -> bool:
    record = await session.scalar(
        select(Procedure).where(
            Procedure.id == procedure_id,
            Procedure.company_id == company_id,
            Procedure.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="procedure",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True
