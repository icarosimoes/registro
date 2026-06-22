from datetime import datetime

from sqlalchemy import delete, func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.models import (
    User,
    WorkDiary,
    WorkDiaryActivity,
    WorkDiaryEquipment,
    WorkDiaryObservation,
    WorkDiaryTeam,
)


async def list_work_diaries(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list[tuple], int]:
    filters = [
        WorkDiary.company_id == company_id,
        WorkDiary.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(
                WorkDiary.title.ilike(pattern),
                WorkDiary.description.ilike(pattern),
            )
        )

    total = (await session.scalar(select(func.count(WorkDiary.id)).where(*filters))) or 0

    rows = (
        await session.execute(
            select(
                WorkDiary,
                User.name.label("owner_name"),
            )
            .outerjoin(User, User.id == WorkDiary.owner_user_id)
            .where(*filters)
            .order_by(
                WorkDiary.updated_at.desc(),
                WorkDiary.id.desc(),
            )
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def _load_children(session: AsyncSession, diary_id: int) -> dict:
    activities = (
        (
            await session.execute(
                select(WorkDiaryActivity)
                .where(WorkDiaryActivity.diary_id == diary_id)
                .order_by(WorkDiaryActivity.sort_order)
            )
        )
        .scalars()
        .all()
    )

    teams = (
        (
            await session.execute(
                select(WorkDiaryTeam)
                .where(WorkDiaryTeam.diary_id == diary_id)
                .order_by(WorkDiaryTeam.sort_order)
            )
        )
        .scalars()
        .all()
    )

    equipment = (
        (
            await session.execute(
                select(WorkDiaryEquipment)
                .where(WorkDiaryEquipment.diary_id == diary_id)
                .order_by(WorkDiaryEquipment.sort_order)
            )
        )
        .scalars()
        .all()
    )

    observations = (
        (
            await session.execute(
                select(WorkDiaryObservation)
                .where(WorkDiaryObservation.diary_id == diary_id)
                .order_by(WorkDiaryObservation.sort_order)
            )
        )
        .scalars()
        .all()
    )

    return {
        "activities": [
            {
                "id": a.id,
                "description": a.description,
                "start_time": a.start_time,
                "end_time": a.end_time,
                "status": a.status,
                "sort_order": a.sort_order,
            }
            for a in activities
        ],
        "teams": [
            {
                "id": t.id,
                "worker_name": t.worker_name,
                "role": t.role,
                "hours_worked": t.hours_worked,
                "sort_order": t.sort_order,
            }
            for t in teams
        ],
        "equipment": [
            {
                "id": e.id,
                "equipment_name": e.equipment_name,
                "quantity": e.quantity,
                "hours_used": e.hours_used,
                "sort_order": e.sort_order,
            }
            for e in equipment
        ],
        "observations": [
            {
                "id": o.id,
                "content": o.content,
                "category": o.category,
                "sort_order": o.sort_order,
            }
            for o in observations
        ],
    }


async def get_work_diary(
    session: AsyncSession,
    company_id: int,
    diary_id: int,
) -> dict | None:
    record = await session.scalar(
        select(WorkDiary).where(
            WorkDiary.id == diary_id,
            WorkDiary.company_id == company_id,
            WorkDiary.deleted_at.is_(None),
        )
    )
    if record is None:
        return None

    owner_name = (
        await session.scalar(select(User.name).where(User.id == record.owner_user_id))
        if record.owner_user_id
        else None
    )

    children = await _load_children(session, record.id)

    return {
        "diary": record,
        "owner_name": owner_name or "Não atribuído",
        **children,
    }


async def _sync_children(
    session: AsyncSession,
    diary_id: int,
    *,
    activities: list[dict] | None = None,
    teams: list[dict] | None = None,
    equipment: list[dict] | None = None,
    observations: list[dict] | None = None,
) -> None:
    if activities is not None:
        await session.execute(
            delete(WorkDiaryActivity).where(WorkDiaryActivity.diary_id == diary_id)
        )
        for a in activities:
            session.add(WorkDiaryActivity(diary_id=diary_id, **a))

    if teams is not None:
        await session.execute(delete(WorkDiaryTeam).where(WorkDiaryTeam.diary_id == diary_id))
        for t in teams:
            session.add(WorkDiaryTeam(diary_id=diary_id, **t))

    if equipment is not None:
        await session.execute(
            delete(WorkDiaryEquipment).where(WorkDiaryEquipment.diary_id == diary_id)
        )
        for e in equipment:
            session.add(WorkDiaryEquipment(diary_id=diary_id, **e))

    if observations is not None:
        await session.execute(
            delete(WorkDiaryObservation).where(WorkDiaryObservation.diary_id == diary_id)
        )
        for o in observations:
            session.add(WorkDiaryObservation(diary_id=diary_id, **o))


async def create_work_diary(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    *,
    diary_date,
    title: str,
    description: str | None,
    weather: str | None,
    status: str,
    owner_user_id: int | None,
    activities: list[dict] | None,
    teams: list[dict] | None,
    equipment: list[dict] | None,
    observations: list[dict] | None,
) -> dict:
    record = WorkDiary(
        company_id=company_id,
        diary_date=diary_date,
        title=title,
        description=description,
        weather=weather,
        status=status,
        owner_user_id=owner_user_id,
    )
    session.add(record)
    await session.flush()

    await _sync_children(
        session,
        record.id,
        activities=activities,
        teams=teams,
        equipment=equipment,
        observations=observations,
    )

    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="work_diary",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(record)

    return await get_work_diary(session, company_id, record.id)  # type: ignore


async def update_work_diary(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    diary_id: int,
    updates: dict,
) -> dict | None:
    record = await session.scalar(
        select(WorkDiary).where(
            WorkDiary.id == diary_id,
            WorkDiary.company_id == company_id,
            WorkDiary.deleted_at.is_(None),
        )
    )
    if record is None:
        return None

    child_keys = ("activities", "teams", "equipment", "observations")
    children = {k: updates.pop(k) for k in child_keys if k in updates}

    before = {k: str(getattr(record, k)) for k in updates}

    for field, value in updates.items():
        setattr(record, field, value)

    if children:
        await _sync_children(session, record.id, **children)

    diff = compute_diff(
        before,
        {k: str(v) for k, v in updates.items()},
    )
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="work_diary",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )

    await session.commit()
    await session.refresh(record)

    return await get_work_diary(session, company_id, record.id)


async def delete_work_diary(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    diary_id: int,
) -> bool:
    record = await session.scalar(
        select(WorkDiary).where(
            WorkDiary.id == diary_id,
            WorkDiary.company_id == company_id,
            WorkDiary.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="work_diary",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True
