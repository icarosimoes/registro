from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.work_diaries.schemas import (
    WorkDiaryActivitySummary,
    WorkDiaryCreate,
    WorkDiaryDetail,
    WorkDiaryEquipmentSummary,
    WorkDiaryListResponse,
    WorkDiaryObservationSummary,
    WorkDiarySummary,
    WorkDiaryTeamSummary,
    WorkDiaryUpdate,
)
from app.domain.work_diaries.service import (
    create_work_diary,
    delete_work_diary,
    get_work_diary,
    list_work_diaries,
    update_work_diary,
)

router = APIRouter(prefix="/work-diaries", tags=["work-diaries"])

ViewUser = Annotated[
    AuthenticatedUser,
    require_permission("work_diary.view"),
]
CreateUser = Annotated[
    AuthenticatedUser,
    require_permission("work_diary.create"),
]
EditUser = Annotated[
    AuthenticatedUser,
    require_permission("work_diary.edit"),
]
DeleteUser = Annotated[
    AuthenticatedUser,
    require_permission("work_diary.delete"),
]
Session = Annotated[AsyncSession, Depends(require_session)]


def _build_detail(data: dict) -> WorkDiaryDetail:
    d = data["diary"]
    return WorkDiaryDetail(
        id=d.id,
        diary_date=d.diary_date,
        title=d.title,
        description=d.description,
        weather=d.weather,
        status=d.status,
        owner=data["owner_name"],
        owner_user_id=d.owner_user_id,
        updated_at=d.updated_at,
        activities=[WorkDiaryActivitySummary(**a) for a in data["activities"]],
        teams=[WorkDiaryTeamSummary(**t) for t in data["teams"]],
        equipment=[WorkDiaryEquipmentSummary(**e) for e in data["equipment"]],
        observations=[WorkDiaryObservationSummary(**o) for o in data["observations"]],
    )


@router.get("", response_model=WorkDiaryListResponse)
async def list_work_diaries_endpoint(
    user: ViewUser,
    session: Session,
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> WorkDiaryListResponse:
    rows, total = await list_work_diaries(session, user.company_id, page, page_size, search)
    return WorkDiaryListResponse(
        items=[
            WorkDiarySummary(
                id=diary.id,
                diary_date=diary.diary_date,
                title=diary.title,
                description=diary.description,
                weather=diary.weather,
                status=diary.status,
                owner=owner_name or "Não atribuído",
                updated_at=diary.updated_at,
            )
            for diary, owner_name in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/{diary_id}", response_model=WorkDiaryDetail)
async def get_work_diary_endpoint(
    diary_id: int,
    user: ViewUser,
    session: Session,
) -> WorkDiaryDetail:
    data = await get_work_diary(session, user.company_id, diary_id)
    if data is None:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
    return _build_detail(data)


@router.post("", response_model=WorkDiaryDetail, status_code=201)
async def create_work_diary_endpoint(
    body: WorkDiaryCreate,
    user: CreateUser,
    session: Session,
) -> WorkDiaryDetail:
    activities = [a.model_dump() for a in body.activities] if body.activities else None
    teams = [t.model_dump() for t in body.teams] if body.teams else None
    equipment = [e.model_dump() for e in body.equipment] if body.equipment else None
    observations = [o.model_dump() for o in body.observations] if body.observations else None
    data = await create_work_diary(
        session,
        user.company_id,
        user.id,
        diary_date=body.diary_date,
        title=body.title,
        description=body.description,
        weather=body.weather,
        status=body.status,
        owner_user_id=body.owner_user_id,
        activities=activities,
        teams=teams,
        equipment=equipment,
        observations=observations,
    )
    return _build_detail(data)


@router.patch("/{diary_id}", response_model=WorkDiaryDetail)
async def update_work_diary_endpoint(
    diary_id: int,
    body: WorkDiaryUpdate,
    user: EditUser,
    session: Session,
) -> WorkDiaryDetail:
    updates = body.model_dump(exclude_none=True)
    for key in ("activities", "teams", "equipment", "observations"):
        raw = getattr(body, key)
        if raw is not None:
            updates[key] = [item.model_dump() for item in raw]
    data = await update_work_diary(
        session,
        user.company_id,
        user.id,
        diary_id,
        updates,
    )
    if data is None:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
    return _build_detail(data)


@router.delete("/{diary_id}", status_code=204)
async def delete_work_diary_endpoint(
    diary_id: int,
    user: DeleteUser,
    session: Session,
) -> None:
    deleted = await delete_work_diary(session, user.company_id, user.id, diary_id)
    if not deleted:
        raise HTTPException(
            status_code=404,
            detail={"code": "not_found"},
        )
