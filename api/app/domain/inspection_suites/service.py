from datetime import datetime

from sqlalchemy import delete, func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.models import InspectionSuite, InspectionSuiteItem, User


async def list_inspection_suites(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list[tuple], int]:
    filters = [
        InspectionSuite.company_id == company_id,
        InspectionSuite.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(
                InspectionSuite.name.ilike(pattern),
                InspectionSuite.description.ilike(pattern),
            )
        )
    total = await session.scalar(select(func.count(InspectionSuite.id)).where(*filters)) or 0
    item_count = (
        select(func.count(InspectionSuiteItem.id))
        .where(InspectionSuiteItem.suite_id == InspectionSuite.id)
        .correlate(InspectionSuite)
        .scalar_subquery()
    )
    rows = (
        await session.execute(
            select(
                InspectionSuite,
                User.name.label("owner_name"),
                item_count.label("item_count"),
            )
            .outerjoin(User, User.id == InspectionSuite.owner_user_id)
            .where(*filters)
            .order_by(InspectionSuite.updated_at.desc(), InspectionSuite.id.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_inspection_suite(
    session: AsyncSession,
    company_id: int,
    suite_id: int,
) -> dict | None:
    record = await session.scalar(
        select(InspectionSuite).where(
            InspectionSuite.id == suite_id,
            InspectionSuite.company_id == company_id,
            InspectionSuite.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    owner_name = (
        await session.scalar(select(User.name).where(User.id == record.owner_user_id))
        if record.owner_user_id
        else None
    )
    items = (
        (
            await session.execute(
                select(InspectionSuiteItem)
                .where(InspectionSuiteItem.suite_id == suite_id)
                .order_by(InspectionSuiteItem.sort_order)
            )
        )
        .scalars()
        .all()
    )
    return {
        "suite": record,
        "owner_name": owner_name,
        "items": items,
        "item_count": len(items),
    }


async def create_inspection_suite(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    *,
    name: str,
    description: str | None,
    type: str | None,
    status: str,
    owner_user_id: int | None,
    items: list[dict] | None,
) -> dict:
    record = InspectionSuite(
        company_id=company_id,
        name=name,
        description=description,
        type=type,
        status=status,
        owner_user_id=owner_user_id,
    )
    session.add(record)
    await session.flush()
    if items:
        for item in items:
            session.add(InspectionSuiteItem(suite_id=record.id, **item))
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="inspection_suite",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(record)
    return await get_inspection_suite(session, company_id, record.id)  # type: ignore


async def update_inspection_suite(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    suite_id: int,
    updates: dict,
) -> dict | None:
    record = await session.scalar(
        select(InspectionSuite).where(
            InspectionSuite.id == suite_id,
            InspectionSuite.company_id == company_id,
            InspectionSuite.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    items = updates.pop("items", None)
    audit_fields = {k: v for k, v in updates.items()}
    before = {k: str(getattr(record, k)) for k in audit_fields}
    for field, value in updates.items():
        setattr(record, field, value)
    if items is not None:
        await session.execute(
            delete(InspectionSuiteItem).where(InspectionSuiteItem.suite_id == suite_id)
        )
        for item in items:
            session.add(InspectionSuiteItem(suite_id=suite_id, **item))
    diff = compute_diff(before, {k: str(v) for k, v in audit_fields.items()})
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="inspection_suite",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    await session.refresh(record)
    return await get_inspection_suite(session, company_id, record.id)


async def delete_inspection_suite(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    suite_id: int,
) -> bool:
    record = await session.scalar(
        select(InspectionSuite).where(
            InspectionSuite.id == suite_id,
            InspectionSuite.company_id == company_id,
            InspectionSuite.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="inspection_suite",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True
