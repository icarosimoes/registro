from datetime import date, datetime

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.models import ShiftHandoff, User


async def list_handoffs(
    session: AsyncSession, company_id: int,
    page: int, page_size: int,
    target_date: date | None = None,
    target_shift: str | None = None,
    status: str | None = None,
    search: str | None = None,
) -> tuple[list, int]:
    filters = [
        ShiftHandoff.company_id == company_id,
        ShiftHandoff.deleted_at.is_(None),
    ]
    if target_date:
        filters.append(ShiftHandoff.target_date == target_date)
    if target_shift:
        filters.append(ShiftHandoff.target_shift == target_shift)
    if status:
        filters.append(ShiftHandoff.status == status)
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(
                ShiftHandoff.title.ilike(pattern),
                ShiftHandoff.description.ilike(pattern),
            )
        )

    total = await session.scalar(
        select(func.count(ShiftHandoff.id)).where(*filters),
    ) or 0

    created_by = User.__table__.alias("created_by")
    read_by = User.__table__.alias("read_by")
    resolved_by = User.__table__.alias("resolved_by")

    rows = (
        await session.execute(
            select(
                ShiftHandoff,
                created_by.c.name.label("created_by_name"),
                read_by.c.name.label("read_by_name"),
                resolved_by.c.name.label("resolved_by_name"),
            )
            .outerjoin(created_by, created_by.c.id == ShiftHandoff.created_by_user_id)
            .outerjoin(read_by, read_by.c.id == ShiftHandoff.read_by_user_id)
            .outerjoin(resolved_by, resolved_by.c.id == ShiftHandoff.resolved_by_user_id)
            .where(*filters)
            .order_by(ShiftHandoff.target_date.desc(), ShiftHandoff.id.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_handoff(
    session: AsyncSession, company_id: int, handoff_id: int,
) -> tuple | None:
    created_by = User.__table__.alias("created_by")
    read_by = User.__table__.alias("read_by")
    resolved_by = User.__table__.alias("resolved_by")

    return (
        await session.execute(
            select(
                ShiftHandoff,
                created_by.c.name.label("created_by_name"),
                read_by.c.name.label("read_by_name"),
                resolved_by.c.name.label("resolved_by_name"),
            )
            .outerjoin(created_by, created_by.c.id == ShiftHandoff.created_by_user_id)
            .outerjoin(read_by, read_by.c.id == ShiftHandoff.read_by_user_id)
            .outerjoin(resolved_by, resolved_by.c.id == ShiftHandoff.resolved_by_user_id)
            .where(
                ShiftHandoff.id == handoff_id,
                ShiftHandoff.company_id == company_id,
                ShiftHandoff.deleted_at.is_(None),
            )
        )
    ).first()


async def create_handoff(
    session: AsyncSession, company_id: int, user_id: int, **fields,
) -> tuple:
    if not fields.get("target_date"):
        fields["target_date"] = date.today()
    rec = ShiftHandoff(
        company_id=company_id, created_by_user_id=user_id, **fields,
    )
    session.add(rec)
    await session.commit()
    await session.refresh(rec)

    await record_event(
        session, company_id=company_id, user_id=user_id,
        entity_type="shift_handoff", entity_id=rec.id, event_type="create",
    )
    await session.commit()
    return await get_handoff(session, company_id, rec.id)


async def update_handoff(
    session: AsyncSession, company_id: int, user_id: int,
    handoff_id: int, updates: dict,
) -> tuple | None:
    rec = await session.scalar(
        select(ShiftHandoff).where(
            ShiftHandoff.id == handoff_id,
            ShiftHandoff.company_id == company_id,
            ShiftHandoff.deleted_at.is_(None),
        )
    )
    if rec is None:
        return None
    before = {k: str(getattr(rec, k)) for k in updates}
    for field, value in updates.items():
        setattr(rec, field, value)
    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(
            session, company_id=company_id, user_id=user_id,
            entity_type="shift_handoff", entity_id=rec.id,
            event_type="update", diff=diff,
        )
    await session.commit()
    return await get_handoff(session, company_id, handoff_id)


async def mark_read(
    session: AsyncSession, company_id: int, user_id: int,
    handoff_id: int,
) -> tuple | None:
    rec = await session.scalar(
        select(ShiftHandoff).where(
            ShiftHandoff.id == handoff_id,
            ShiftHandoff.company_id == company_id,
            ShiftHandoff.deleted_at.is_(None),
        )
    )
    if rec is None:
        return None
    if rec.read_at is None:
        rec.read_at = datetime.now()
        rec.read_by_user_id = user_id
        rec.status = "lido"
        await record_event(
            session, company_id=company_id, user_id=user_id,
            entity_type="shift_handoff", entity_id=rec.id,
            event_type="update",
            diff={"status": {"from": "pendente", "to": "lido"}},
        )
        await session.commit()
    return await get_handoff(session, company_id, handoff_id)


async def resolve_handoff(
    session: AsyncSession, company_id: int, user_id: int,
    handoff_id: int, resolution_notes: str | None = None,
) -> tuple | None:
    rec = await session.scalar(
        select(ShiftHandoff).where(
            ShiftHandoff.id == handoff_id,
            ShiftHandoff.company_id == company_id,
            ShiftHandoff.deleted_at.is_(None),
        )
    )
    if rec is None:
        return None
    old_status = rec.status
    rec.status = "resolvido"
    rec.resolved_at = datetime.now()
    rec.resolved_by_user_id = user_id
    if resolution_notes:
        rec.resolution_notes = resolution_notes
    if rec.read_at is None:
        rec.read_at = rec.resolved_at
        rec.read_by_user_id = user_id

    await record_event(
        session, company_id=company_id, user_id=user_id,
        entity_type="shift_handoff", entity_id=rec.id,
        event_type="update",
        diff={"status": {"from": old_status, "to": "resolvido"}},
    )
    await session.commit()
    return await get_handoff(session, company_id, handoff_id)


async def delete_handoff(
    session: AsyncSession, company_id: int, user_id: int,
    handoff_id: int,
) -> bool:
    rec = await session.scalar(
        select(ShiftHandoff).where(
            ShiftHandoff.id == handoff_id,
            ShiftHandoff.company_id == company_id,
            ShiftHandoff.deleted_at.is_(None),
        )
    )
    if rec is None:
        return False
    rec.deleted_at = datetime.now()
    await record_event(
        session, company_id=company_id, user_id=user_id,
        entity_type="shift_handoff", entity_id=rec.id,
        event_type="delete",
    )
    await session.commit()
    return True


async def pending_for_shift(
    session: AsyncSession, company_id: int,
    target_date: date, target_shift: str | None = None,
) -> list:
    filters = [
        ShiftHandoff.company_id == company_id,
        ShiftHandoff.deleted_at.is_(None),
        ShiftHandoff.target_date <= target_date,
        ShiftHandoff.status != "resolvido",
    ]
    if target_shift:
        filters.append(
            or_(
                ShiftHandoff.target_shift == target_shift,
                ShiftHandoff.target_shift.is_(None),
            )
        )

    created_by = User.__table__.alias("created_by")
    rows = (
        await session.execute(
            select(
                ShiftHandoff,
                created_by.c.name.label("created_by_name"),
            )
            .outerjoin(
                created_by,
                created_by.c.id == ShiftHandoff.created_by_user_id,
            )
            .where(*filters)
            .order_by(
                ShiftHandoff.target_date.asc(),
                ShiftHandoff.id.asc(),
            )
            .limit(50)
        )
    ).all()
    return rows
