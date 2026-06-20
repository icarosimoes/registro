from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.auth import current_user
from app.core.dependencies import require_session
from app.domain.auth.repository import AuthenticatedUser
from app.domain.occurrences.schemas import (
    OccurrenceCreate,
    OccurrenceListResponse,
    OccurrenceSummary,
    OccurrenceUpdate,
)
from app.domain.occurrences.service import (
    create_occurrence,
    delete_occurrence,
    list_occurrences,
    status_label,
    update_occurrence,
)

router = APIRouter(prefix="/occurrences", tags=["occurrences"])


@router.get("", response_model=OccurrenceListResponse)
async def list_occurrences_endpoint(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> OccurrenceListResponse:
    rows, total = await list_occurrences(session, user.company_id, page, page_size, search)
    return OccurrenceListResponse(
        items=[
            OccurrenceSummary(
                id=occurrence.id,
                legacy_id=occurrence.legacy_id,
                title=occurrence.title,
                description=occurrence.description,
                category=sector_name or "Sem setor",
                location=location_name,
                owner=owner_name or "Não atribuído",
                status=status_label(occurrence.status),
                deadline=occurrence.deadline,
                updated_at=occurrence.updated_at,
            )
            for occurrence, sector_name, location_name, owner_name in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.post("", response_model=OccurrenceSummary, status_code=201)
async def create_occurrence_endpoint(
    body: OccurrenceCreate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> OccurrenceSummary:
    record, (sector_name, location_name, owner_name) = await create_occurrence(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        title=body.title,
        description=body.description,
        unit=body.unit,
        deadline=body.deadline,
        status=body.status,
        sector_id=body.sector_id,
        location_id=body.location_id,
        owner_user_id=body.owner_user_id,
        notify_user_ids=body.notify_user_ids,
    )
    return OccurrenceSummary(
        id=record.id,
        legacy_id=record.legacy_id,
        title=record.title,
        description=record.description,
        category=sector_name or "Sem setor",
        location=location_name,
        owner=owner_name or "Não atribuído",
        status=status_label(record.status),
        deadline=record.deadline,
        updated_at=record.updated_at,
    )


@router.patch("/{occurrence_id}", response_model=OccurrenceSummary)
async def update_occurrence_endpoint(
    occurrence_id: int,
    body: OccurrenceUpdate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> OccurrenceSummary:
    updates = body.model_dump(exclude_none=True)
    result = await update_occurrence(
        session, user.company_id, user.id, user.name, user.email, occurrence_id, updates
    )
    if result is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    record, (sector_name, location_name, owner_name) = result
    return OccurrenceSummary(
        id=record.id,
        legacy_id=record.legacy_id,
        title=record.title,
        description=record.description,
        category=sector_name or "Sem setor",
        location=location_name,
        owner=owner_name or "Não atribuído",
        status=status_label(record.status),
        deadline=record.deadline,
        updated_at=record.updated_at,
    )


@router.delete("/{occurrence_id}", status_code=204)
async def delete_occurrence_endpoint(
    occurrence_id: int,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_occurrence(session, user.company_id, user.id, occurrence_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
