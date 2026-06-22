from datetime import datetime
from typing import NamedTuple

from sqlalchemy import delete as sa_delete
from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.models import ApartmentInspection, ApartmentInspectionItem, User


class InspectionListRow(NamedTuple):
    inspection: ApartmentInspection
    inspector_name: str | None


class InspectionDetail(NamedTuple):
    inspection: ApartmentInspection
    inspector_name: str | None
    items: list[ApartmentInspectionItem]


async def list_apartment_inspections(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list[InspectionListRow], int]:
    filters = [
        ApartmentInspection.company_id == company_id,
        ApartmentInspection.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(
                ApartmentInspection.unit.ilike(pattern),
                ApartmentInspection.apartment.ilike(pattern),
                ApartmentInspection.notes.ilike(pattern),
            )
        )
    total = await session.scalar(select(func.count(ApartmentInspection.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(ApartmentInspection, User.name)
            .outerjoin(User, User.id == ApartmentInspection.inspector_user_id)
            .where(*filters)
            .order_by(ApartmentInspection.updated_at.desc(), ApartmentInspection.id.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def _get_items(session: AsyncSession, inspection_id: int) -> list[ApartmentInspectionItem]:
    result = await session.execute(
        select(ApartmentInspectionItem)
        .where(ApartmentInspectionItem.inspection_id == inspection_id)
        .order_by(ApartmentInspectionItem.sort_order, ApartmentInspectionItem.id)
    )
    return list(result.scalars().all())


async def _sync_items(
    session: AsyncSession,
    inspection_id: int,
    items: list[dict],
) -> None:
    await session.execute(
        sa_delete(ApartmentInspectionItem).where(
            ApartmentInspectionItem.inspection_id == inspection_id
        )
    )
    for item_data in items:
        session.add(
            ApartmentInspectionItem(
                inspection_id=inspection_id,
                suite_item_id=item_data.get("suite_item_id"),
                condition=item_data.get("condition", "ok"),
                notes=item_data.get("notes"),
                sort_order=item_data.get("sort_order", 0),
            )
        )


async def get_apartment_inspection(
    session: AsyncSession, company_id: int, inspection_id: int
) -> InspectionDetail | None:
    record = await session.scalar(
        select(ApartmentInspection).where(
            ApartmentInspection.id == inspection_id,
            ApartmentInspection.company_id == company_id,
            ApartmentInspection.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    inspector_name = (
        await session.scalar(select(User.name).where(User.id == record.inspector_user_id))
        if record.inspector_user_id
        else None
    )
    items = await _get_items(session, record.id)
    return InspectionDetail(record, inspector_name, items)


async def create_apartment_inspection(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    *,
    unit: str | None,
    apartment: str | None,
    inspection_type: str,
    inspection_suite_id: int | None,
    inspector_user_id: int | None,
    scheduled_at: datetime | None,
    completed_at: datetime | None,
    status: str,
    notes: str | None,
    items: list[dict] | None = None,
) -> InspectionDetail:
    record = ApartmentInspection(
        company_id=company_id,
        unit=unit,
        apartment=apartment,
        inspection_type=inspection_type,
        inspection_suite_id=inspection_suite_id,
        inspector_user_id=inspector_user_id,
        scheduled_at=scheduled_at,
        completed_at=completed_at,
        status=status,
        notes=notes,
    )
    session.add(record)
    await session.flush()
    if items:
        await _sync_items(session, record.id, items)
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="apartment_inspection",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(record)
    inspector_name = (
        await session.scalar(select(User.name).where(User.id == record.inspector_user_id))
        if record.inspector_user_id
        else None
    )
    db_items = await _get_items(session, record.id)
    return InspectionDetail(record, inspector_name, db_items)


async def update_apartment_inspection(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    inspection_id: int,
    updates: dict,
) -> InspectionDetail | None:
    record = await session.scalar(
        select(ApartmentInspection).where(
            ApartmentInspection.id == inspection_id,
            ApartmentInspection.company_id == company_id,
            ApartmentInspection.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    items_data = updates.pop("items", None)
    before = {k: str(getattr(record, k)) for k in updates}
    for field, value in updates.items():
        setattr(record, field, value)
    if items_data is not None:
        await _sync_items(session, record.id, items_data)
    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="apartment_inspection",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    await session.refresh(record)
    inspector_name = (
        await session.scalar(select(User.name).where(User.id == record.inspector_user_id))
        if record.inspector_user_id
        else None
    )
    db_items = await _get_items(session, record.id)
    return InspectionDetail(record, inspector_name, db_items)


async def delete_apartment_inspection(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    inspection_id: int,
) -> bool:
    record = await session.scalar(
        select(ApartmentInspection).where(
            ApartmentInspection.id == inspection_id,
            ApartmentInspection.company_id == company_id,
            ApartmentInspection.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="apartment_inspection",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True
