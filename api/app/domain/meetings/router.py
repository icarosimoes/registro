from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from fastapi.responses import StreamingResponse
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.meetings.schemas import (
    MeetingCreate,
    MeetingDetail,
    MeetingListResponse,
    MeetingSummary,
    MeetingUpdate,
    ParticipantSummary,
    SubjectCreate,
    SubjectSummary,
    SubjectUpdate,
)
from app.domain.meetings.service import (
    add_subject,
    clone_meeting,
    create_meeting,
    delete_meeting,
    delete_subject,
    get_meeting,
    list_meetings,
    update_meeting,
    update_subject,
)

router = APIRouter(prefix="/meetings", tags=["meetings"])

ViewUser = Annotated[
    AuthenticatedUser,
    require_permission("meeting.view"),
]
CreateUser = Annotated[
    AuthenticatedUser,
    require_permission("meeting.create"),
]
EditUser = Annotated[
    AuthenticatedUser,
    require_permission("meeting.edit"),
]
DeleteUser = Annotated[
    AuthenticatedUser,
    require_permission("meeting.delete"),
]
Session = Annotated[AsyncSession, Depends(require_session)]


@router.get("", response_model=MeetingListResponse)
async def list_meetings_endpoint(
    user: ViewUser,
    session: Session,
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> MeetingListResponse:
    rows, total = await list_meetings(session, user.company_id, page, page_size, search)
    return MeetingListResponse(
        items=[
            MeetingSummary(
                id=meeting.id,
                title=meeting.title,
                description=meeting.description,
                scheduled_at=meeting.scheduled_at,
                location=meeting.location,
                status=meeting.status,
                owner=owner_name or "Não atribuído",
                participant_count=p_count,
                subject_count=s_count,
                updated_at=meeting.updated_at,
            )
            for meeting, owner_name, p_count, s_count in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )


def _build_detail(data: dict) -> MeetingDetail:
    m = data["meeting"]
    return MeetingDetail(
        id=m.id,
        title=m.title,
        description=m.description,
        scheduled_at=m.scheduled_at,
        location=m.location,
        status=m.status,
        owner=data["owner_name"],
        participant_count=data["participant_count"],
        subject_count=data["subject_count"],
        updated_at=m.updated_at,
        participants=[ParticipantSummary(**p) for p in data["participants"]],
        subjects=[SubjectSummary(**s) for s in data["subjects"]],
        notify_user_ids=m.notify_user_ids,
    )


@router.get("/{meeting_id}", response_model=MeetingDetail)
async def get_meeting_endpoint(
    meeting_id: int,
    user: ViewUser,
    session: Session,
) -> MeetingDetail:
    data = await get_meeting(session, user.company_id, meeting_id)
    if data is None:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
    return _build_detail(data)


@router.get("/{meeting_id}/pdf")
async def meeting_pdf_endpoint(
    meeting_id: int,
    user: ViewUser,
    session: Session,
) -> StreamingResponse:
    data = await get_meeting(session, user.company_id, meeting_id)
    if data is None:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )

    from app.domain.meetings.pdf import generate_meeting_pdf
    from app.domain.timeline.service import get_timeline

    timeline = await get_timeline(session, user.company_id, "meeting", meeting_id)
    buf = generate_meeting_pdf(
        company_name=user.company_name,
        meeting=data["meeting"],
        owner_name=data["owner_name"],
        participants=data["participants"],
        subjects=data["subjects"],
        timeline=timeline,
    )
    filename = f"reuniao_{meeting_id}.pdf"
    return StreamingResponse(
        buf,
        media_type="application/pdf",
        headers={"Content-Disposition": (f'attachment; filename="{filename}"')},
    )


@router.post("", response_model=MeetingDetail, status_code=201)
async def create_meeting_endpoint(
    body: MeetingCreate,
    user: CreateUser,
    session: Session,
) -> MeetingDetail:
    participants = [p.model_dump() for p in body.participants] if body.participants else None
    subjects = [s.model_dump() for s in body.subjects] if body.subjects else None
    data = await create_meeting(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        title=body.title,
        description=body.description,
        scheduled_at=body.scheduled_at,
        location=body.location,
        status=body.status,
        owner_user_id=body.owner_user_id,
        participants=participants,
        subjects=subjects,
        notify_user_ids=body.notify_user_ids,
    )
    return _build_detail(data)


@router.patch("/{meeting_id}", response_model=MeetingDetail)
async def update_meeting_endpoint(
    meeting_id: int,
    body: MeetingUpdate,
    user: EditUser,
    session: Session,
) -> MeetingDetail:
    updates = body.model_dump(exclude_none=True)
    if "participants" in updates:
        updates["participants"] = [
            p.model_dump()
            for p in body.participants  # type: ignore
        ]
    data = await update_meeting(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        meeting_id,
        updates,
    )
    if data is None:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
    return _build_detail(data)


@router.delete("/{meeting_id}", status_code=204)
async def delete_meeting_endpoint(
    meeting_id: int,
    user: DeleteUser,
    session: Session,
) -> None:
    deleted = await delete_meeting(session, user.company_id, user.id, meeting_id)
    if not deleted:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )


@router.post(
    "/{meeting_id}/clone",
    response_model=MeetingDetail,
    status_code=201,
)
async def clone_meeting_endpoint(
    meeting_id: int,
    user: CreateUser,
    session: Session,
) -> MeetingDetail:
    data = await clone_meeting(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        meeting_id,
    )
    if data is None:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
    return _build_detail(data)


@router.post(
    "/{meeting_id}/subjects",
    response_model=SubjectSummary,
    status_code=201,
)
async def add_subject_endpoint(
    meeting_id: int,
    body: SubjectCreate,
    user: EditUser,
    session: Session,
) -> SubjectSummary:
    subject = await add_subject(
        session,
        user.company_id,
        user.id,
        meeting_id,
        title=body.title,
        description=body.description,
        sort_order=body.sort_order,
    )
    if subject is None:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
    return SubjectSummary(
        id=subject.id,
        title=subject.title,
        description=subject.description,
        sort_order=subject.sort_order,
        resolved=subject.resolved,
    )


@router.patch(
    "/{meeting_id}/subjects/{subject_id}",
    response_model=SubjectSummary,
)
async def update_subject_endpoint(
    meeting_id: int,
    subject_id: int,
    body: SubjectUpdate,
    user: EditUser,
    session: Session,
) -> SubjectSummary:
    updates = body.model_dump(exclude_none=True)
    subject = await update_subject(
        session,
        user.company_id,
        user.id,
        meeting_id,
        subject_id,
        updates,
    )
    if subject is None:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
    return SubjectSummary(
        id=subject.id,
        title=subject.title,
        description=subject.description,
        sort_order=subject.sort_order,
        resolved=subject.resolved,
    )


@router.delete(
    "/{meeting_id}/subjects/{subject_id}",
    status_code=204,
)
async def delete_subject_endpoint(
    meeting_id: int,
    subject_id: int,
    user: EditUser,
    session: Session,
) -> None:
    deleted = await delete_subject(
        session,
        user.company_id,
        user.id,
        meeting_id,
        subject_id,
    )
    if not deleted:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
