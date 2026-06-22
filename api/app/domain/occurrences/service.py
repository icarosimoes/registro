from datetime import datetime
from typing import NamedTuple

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.core.cache import invalidate_dashboard
from app.core.pagination import CursorPage, paginate_by_cursor
from app.integrations.notifications import notify_record_event
from app.models import (
    Location,
    Occurrence,
    OccurrenceParticipant,
    Sector,
    User,
)

STATUS_LABELS = {1: "Em andamento", 2: "Concluído", 3: "Aguardando"}


class OccurrenceRow(NamedTuple):
    occurrence: Occurrence
    sector_name: str | None
    location_name: str | None
    owner_name: str | None


class OccurrenceNames(NamedTuple):
    sector_name: str | None
    location_name: str | None
    owner_name: str | None


class OccurrenceDetail(NamedTuple):
    record: Occurrence
    names: OccurrenceNames
    participants: list[tuple[int, str]]


def status_label(value: int) -> str:
    return STATUS_LABELS.get(value, f"Status {value}")


async def _resolve_names(session: AsyncSession, occurrence: Occurrence) -> OccurrenceNames:
    cid = occurrence.company_id
    sector_name = (
        await session.scalar(
            select(Sector.name).where(
                Sector.id == occurrence.sector_id,
                Sector.company_id == cid,
                Sector.deleted_at.is_(None),
            )
        )
        if occurrence.sector_id
        else None
    )
    location_name = (
        await session.scalar(
            select(Location.name).where(
                Location.id == occurrence.location_id,
                Location.company_id == cid,
                Location.deleted_at.is_(None),
            )
        )
        if occurrence.location_id
        else None
    )
    owner_name = (
        await session.scalar(
            select(User.name).where(
                User.id == occurrence.owner_user_id,
                User.company_id == cid,
                User.deleted_at.is_(None),
            )
        )
        if occurrence.owner_user_id
        else None
    )
    return OccurrenceNames(sector_name, location_name, owner_name)


async def list_occurrences(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list[OccurrenceRow], int]:
    filters = [Occurrence.company_id == company_id, Occurrence.deleted_at.is_(None)]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(or_(Occurrence.title.ilike(pattern), Occurrence.description.ilike(pattern)))
    total = await session.scalar(select(func.count(Occurrence.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(Occurrence, Sector.name, Location.name, User.name)
            .outerjoin(Sector, Sector.id == Occurrence.sector_id)
            .outerjoin(Location, Location.id == Occurrence.location_id)
            .outerjoin(User, User.id == Occurrence.owner_user_id)
            .where(*filters)
            .order_by(Occurrence.updated_at.desc(), Occurrence.id.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def list_occurrences_cursor(
    session: AsyncSession,
    company_id: int,
    limit: int = 20,
    cursor: str | None = None,
    search: str | None = None,
) -> CursorPage:
    filters = [Occurrence.company_id == company_id, Occurrence.deleted_at.is_(None)]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(or_(Occurrence.title.ilike(pattern), Occurrence.description.ilike(pattern)))

    stmt = (
        select(Occurrence, Sector.name, Location.name, User.name)
        .outerjoin(Sector, Sector.id == Occurrence.sector_id)
        .outerjoin(Location, Location.id == Occurrence.location_id)
        .outerjoin(User, User.id == Occurrence.owner_user_id)
        .where(*filters)
    )
    return await paginate_by_cursor(
        session, stmt, id_column=Occurrence.id, cursor=cursor, limit=limit
    )


async def _sync_participants(
    session: AsyncSession, occurrence_id: int, participant_ids: list[int]
) -> None:
    from sqlalchemy import delete as sa_delete

    await session.execute(
        sa_delete(OccurrenceParticipant).where(OccurrenceParticipant.occurrence_id == occurrence_id)
    )
    for uid in participant_ids:
        session.add(OccurrenceParticipant(occurrence_id=occurrence_id, user_id=uid))


async def _get_participants(session: AsyncSession, occurrence_id: int) -> list[tuple[int, str]]:
    rows = (
        await session.execute(
            select(OccurrenceParticipant.user_id, User.name)
            .join(User, User.id == OccurrenceParticipant.user_id)
            .where(OccurrenceParticipant.occurrence_id == occurrence_id)
            .order_by(User.name)
        )
    ).all()
    return [(uid, name) for uid, name in rows]


async def get_occurrence(
    session: AsyncSession, company_id: int, occurrence_id: int
) -> OccurrenceDetail | None:
    record = await session.scalar(
        select(Occurrence).where(
            Occurrence.id == occurrence_id,
            Occurrence.company_id == company_id,
            Occurrence.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    names = await _resolve_names(session, record)
    participants = await _get_participants(session, record.id)
    return OccurrenceDetail(record, names, participants)


async def create_occurrence(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    *,
    title: str,
    description: str | None,
    unit: str | None,
    deadline,
    status: int,
    sector_id: int | None,
    location_id: int | None,
    owner_user_id: int | None,
    notify_user_ids: list[int] | None,
    participant_ids: list[int] | None = None,
) -> tuple[Occurrence, OccurrenceNames]:
    record = Occurrence(
        company_id=company_id,
        title=title,
        description=description,
        unit=unit,
        deadline=deadline,
        status=status,
        sector_id=sector_id,
        location_id=location_id,
        owner_user_id=owner_user_id,
        created_by_user_id=user_id,
        updated_by_user_id=user_id,
        notify_user_ids=notify_user_ids,
    )
    session.add(record)
    await session.flush()
    if participant_ids:
        await _sync_participants(session, record.id, participant_ids)
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="occurrence",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await invalidate_dashboard(company_id)
    await session.refresh(record)
    await notify_record_event(
        session,
        company_id=company_id,
        actor_name=user_name,
        actor_email=user_email,
        event="create",
        title=record.title,
        module="Ocorrências",
        owner_user_id=owner_user_id,
        notify_user_ids=notify_user_ids,
    )
    names = await _resolve_names(session, record)
    return record, names


async def update_occurrence(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    occurrence_id: int,
    updates: dict,
) -> tuple[Occurrence, OccurrenceNames] | None:
    record = await session.scalar(
        select(Occurrence).where(
            Occurrence.id == occurrence_id,
            Occurrence.company_id == company_id,
            Occurrence.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    participant_ids = updates.pop("participant_ids", None)
    before = {k: str(getattr(record, k)) for k in updates if k != "notify_user_ids"}
    for field, value in updates.items():
        setattr(record, field, value)
    record.updated_by_user_id = user_id
    if participant_ids is not None:
        await _sync_participants(session, record.id, participant_ids)
    diff = compute_diff(
        before,
        {k: str(v) for k, v in updates.items() if k != "notify_user_ids"},
    )
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="occurrence",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    await invalidate_dashboard(company_id)
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
            module="Ocorrências",
            owner_user_id=record.owner_user_id,
            created_by_user_id=record.created_by_user_id,
            notify_user_ids=record.notify_user_ids,
            detail=detail,
        )
    names = await _resolve_names(session, record)
    return record, names


async def delete_occurrence(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    occurrence_id: int,
) -> bool:
    record = await session.scalar(
        select(Occurrence).where(
            Occurrence.id == occurrence_id,
            Occurrence.company_id == company_id,
            Occurrence.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    record.updated_by_user_id = user_id
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="occurrence",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    await invalidate_dashboard(company_id)
    return True


async def export_occurrences(
    session: AsyncSession,
    company_id: int,
    search: str | None = None,
) -> list[OccurrenceRow]:
    from app.core.export import MAX_EXPORT_ROWS

    filters = [Occurrence.company_id == company_id, Occurrence.deleted_at.is_(None)]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(or_(Occurrence.title.ilike(pattern), Occurrence.description.ilike(pattern)))
    rows = (
        await session.execute(
            select(Occurrence, Sector.name, Location.name, User.name)
            .outerjoin(Sector, Sector.id == Occurrence.sector_id)
            .outerjoin(Location, Location.id == Occurrence.location_id)
            .outerjoin(User, User.id == Occurrence.owner_user_id)
            .where(*filters)
            .order_by(Occurrence.updated_at.desc(), Occurrence.id.desc())
            .limit(MAX_EXPORT_ROWS)
        )
    ).all()
    return rows


async def clone_occurrence(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    occurrence_id: int,
) -> tuple[Occurrence, OccurrenceNames] | None:
    original = await session.scalar(
        select(Occurrence).where(
            Occurrence.id == occurrence_id,
            Occurrence.company_id == company_id,
            Occurrence.deleted_at.is_(None),
        )
    )
    if original is None:
        return None

    participants = await _get_participants(session, original.id)
    p_ids = [uid for uid, _ in participants]

    return await create_occurrence(
        session,
        company_id,
        user_id,
        user_name,
        user_email,
        title=f"Cópia de {original.title}",
        description=original.description,
        unit=original.unit,
        deadline=original.deadline,
        status=1,
        sector_id=original.sector_id,
        location_id=original.location_id,
        owner_user_id=original.owner_user_id,
        notify_user_ids=original.notify_user_ids,
        participant_ids=p_ids,
    )
