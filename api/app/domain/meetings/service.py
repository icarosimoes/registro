from datetime import datetime

from sqlalchemy import delete, func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.integrations.notifications import notify_record_event
from app.models import Meeting, MeetingParticipant, MeetingSubject, User


async def list_meetings(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list[tuple], int]:
    filters = [
        Meeting.company_id == company_id,
        Meeting.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(
                Meeting.title.ilike(pattern),
                Meeting.description.ilike(pattern),
            )
        )

    total = (
        await session.scalar(
            select(func.count(Meeting.id)).where(*filters)
        )
    ) or 0

    participant_count = (
        select(func.count(MeetingParticipant.id))
        .where(MeetingParticipant.meeting_id == Meeting.id)
        .correlate(Meeting)
        .scalar_subquery()
    )
    subject_count = (
        select(func.count(MeetingSubject.id))
        .where(MeetingSubject.meeting_id == Meeting.id)
        .correlate(Meeting)
        .scalar_subquery()
    )

    rows = (
        await session.execute(
            select(
                Meeting,
                User.name.label("owner_name"),
                participant_count.label("participant_count"),
                subject_count.label("subject_count"),
            )
            .outerjoin(User, User.id == Meeting.owner_user_id)
            .where(*filters)
            .order_by(
                Meeting.updated_at.desc(),
                Meeting.id.desc(),
            )
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_meeting(
    session: AsyncSession,
    company_id: int,
    meeting_id: int,
) -> dict | None:
    record = await session.scalar(
        select(Meeting).where(
            Meeting.id == meeting_id,
            Meeting.company_id == company_id,
            Meeting.deleted_at.is_(None),
        )
    )
    if record is None:
        return None

    owner_name = (
        await session.scalar(
            select(User.name).where(
                User.id == record.owner_user_id
            )
        )
        if record.owner_user_id
        else None
    )

    participant_rows = (
        await session.execute(
            select(
                MeetingParticipant.user_id,
                MeetingParticipant.role,
                User.name,
            )
            .join(User, User.id == MeetingParticipant.user_id)
            .where(
                MeetingParticipant.meeting_id == meeting_id
            )
            .order_by(MeetingParticipant.id)
        )
    ).all()

    subject_rows = (
        await session.execute(
            select(MeetingSubject).where(
                MeetingSubject.meeting_id == meeting_id
            )
            .order_by(MeetingSubject.sort_order)
        )
    ).scalars().all()

    participant_count = len(participant_rows)
    subject_count = len(subject_rows)

    return {
        "meeting": record,
        "owner_name": owner_name or "Não atribuído",
        "participants": [
            {
                "user_id": uid,
                "name": name,
                "role": role,
            }
            for uid, role, name in participant_rows
        ],
        "subjects": [
            {
                "id": s.id,
                "title": s.title,
                "description": s.description,
                "sort_order": s.sort_order,
                "resolved": s.resolved,
            }
            for s in subject_rows
        ],
        "participant_count": participant_count,
        "subject_count": subject_count,
    }


async def create_meeting(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    *,
    title: str,
    description: str | None,
    scheduled_at: datetime | None,
    location: str | None,
    status: str,
    owner_user_id: int | None,
    participants: list[dict] | None,
    subjects: list[dict] | None,
    notify_user_ids: list[int] | None,
) -> dict:
    record = Meeting(
        company_id=company_id,
        title=title,
        description=description,
        scheduled_at=scheduled_at,
        location=location,
        status=status,
        owner_user_id=owner_user_id,
        created_by_user_id=user_id,
        notify_user_ids=notify_user_ids,
    )
    session.add(record)
    await session.flush()

    if participants:
        for p in participants:
            session.add(
                MeetingParticipant(
                    meeting_id=record.id,
                    user_id=p["user_id"],
                    role=p.get("role", "attendee"),
                )
            )

    if subjects:
        for s in subjects:
            session.add(
                MeetingSubject(
                    meeting_id=record.id,
                    title=s["title"],
                    description=s.get("description"),
                    sort_order=s.get("sort_order", 0),
                )
            )

    await session.commit()
    await session.refresh(record)

    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="meeting",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()

    await notify_record_event(
        session,
        company_id=company_id,
        actor_name=user_name,
        actor_email=user_email,
        event="create",
        title=record.title,
        module="Reuniões",
        owner_user_id=owner_user_id,
        notify_user_ids=notify_user_ids,
    )

    return await get_meeting(session, company_id, record.id)  # type: ignore


async def update_meeting(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    meeting_id: int,
    updates: dict,
) -> dict | None:
    record = await session.scalar(
        select(Meeting).where(
            Meeting.id == meeting_id,
            Meeting.company_id == company_id,
            Meeting.deleted_at.is_(None),
        )
    )
    if record is None:
        return None

    participants = updates.pop("participants", None)

    audit_fields = {
        k: v for k, v in updates.items()
        if k != "notify_user_ids"
    }
    before = {
        k: str(getattr(record, k)) for k in audit_fields
    }

    for field, value in updates.items():
        setattr(record, field, value)

    if participants is not None:
        await session.execute(
            delete(MeetingParticipant).where(
                MeetingParticipant.meeting_id == meeting_id
            )
        )
        for p in participants:
            session.add(
                MeetingParticipant(
                    meeting_id=meeting_id,
                    user_id=p["user_id"],
                    role=p.get("role", "attendee"),
                )
            )

    diff = compute_diff(
        before,
        {k: str(v) for k, v in audit_fields.items()},
    )
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="meeting",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )

    await session.commit()
    await session.refresh(record)

    if diff:
        detail = "; ".join(
            f"{k}: {v}" for k, v in diff.items()
        )
        await notify_record_event(
            session,
            company_id=company_id,
            actor_name=user_name,
            actor_email=user_email,
            event="update",
            title=record.title,
            module="Reuniões",
            owner_user_id=record.owner_user_id,
            created_by_user_id=record.created_by_user_id,
            notify_user_ids=record.notify_user_ids,
            detail=detail,
        )

    return await get_meeting(
        session, company_id, record.id
    )


async def delete_meeting(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    meeting_id: int,
) -> bool:
    record = await session.scalar(
        select(Meeting).where(
            Meeting.id == meeting_id,
            Meeting.company_id == company_id,
            Meeting.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="meeting",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True


async def clone_meeting(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    meeting_id: int,
) -> dict | None:
    original = await session.scalar(
        select(Meeting).where(
            Meeting.id == meeting_id,
            Meeting.company_id == company_id,
            Meeting.deleted_at.is_(None),
        )
    )
    if original is None:
        return None

    participant_rows = (
        await session.execute(
            select(MeetingParticipant).where(
                MeetingParticipant.meeting_id == meeting_id
            )
        )
    ).scalars().all()

    subject_rows = (
        await session.execute(
            select(MeetingSubject).where(
                MeetingSubject.meeting_id == meeting_id
            )
        )
    ).scalars().all()

    return await create_meeting(
        session,
        company_id,
        user_id,
        user_name,
        user_email,
        title=f"Cópia de {original.title}",
        description=original.description,
        scheduled_at=original.scheduled_at,
        location=original.location,
        status="Agendada",
        owner_user_id=original.owner_user_id,
        participants=[
            {"user_id": p.user_id, "role": p.role}
            for p in participant_rows
        ],
        subjects=[
            {
                "title": s.title,
                "description": s.description,
                "sort_order": s.sort_order,
            }
            for s in subject_rows
        ],
        notify_user_ids=original.notify_user_ids,
    )


async def add_subject(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    meeting_id: int,
    *,
    title: str,
    description: str | None = None,
    sort_order: int = 0,
) -> MeetingSubject | None:
    meeting = await session.scalar(
        select(Meeting).where(
            Meeting.id == meeting_id,
            Meeting.company_id == company_id,
            Meeting.deleted_at.is_(None),
        )
    )
    if meeting is None:
        return None

    subject = MeetingSubject(
        meeting_id=meeting_id,
        title=title,
        description=description,
        sort_order=sort_order,
    )
    session.add(subject)
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="meeting",
        entity_id=meeting_id,
        event_type="update",
        diff={"pauta_adicionada": title},
    )
    await session.commit()
    await session.refresh(subject)
    return subject


async def update_subject(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    meeting_id: int,
    subject_id: int,
    updates: dict,
) -> MeetingSubject | None:
    meeting = await session.scalar(
        select(Meeting).where(
            Meeting.id == meeting_id,
            Meeting.company_id == company_id,
            Meeting.deleted_at.is_(None),
        )
    )
    if meeting is None:
        return None

    subject = await session.scalar(
        select(MeetingSubject).where(
            MeetingSubject.id == subject_id,
            MeetingSubject.meeting_id == meeting_id,
        )
    )
    if subject is None:
        return None

    before = {
        k: str(getattr(subject, k)) for k in updates
    }
    for field, value in updates.items():
        setattr(subject, field, value)

    diff = compute_diff(
        before, {k: str(v) for k, v in updates.items()}
    )
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="meeting",
            entity_id=meeting_id,
            event_type="update",
            diff=diff,
        )

    await session.commit()
    await session.refresh(subject)
    return subject


async def delete_subject(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    meeting_id: int,
    subject_id: int,
) -> bool:
    meeting = await session.scalar(
        select(Meeting).where(
            Meeting.id == meeting_id,
            Meeting.company_id == company_id,
            Meeting.deleted_at.is_(None),
        )
    )
    if meeting is None:
        return False

    subject = await session.scalar(
        select(MeetingSubject).where(
            MeetingSubject.id == subject_id,
            MeetingSubject.meeting_id == meeting_id,
        )
    )
    if subject is None:
        return False

    await session.delete(subject)
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="meeting",
        entity_id=meeting_id,
        event_type="update",
        diff={"pauta_removida": subject.title},
    )
    await session.commit()
    return True
