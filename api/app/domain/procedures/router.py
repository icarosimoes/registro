from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.procedures.schemas import (
    ProcedureCreate,
    ProcedureListResponse,
    ProcedureSummary,
    ProcedureUpdate,
)
from app.domain.procedures.service import (
    create_procedure,
    delete_procedure,
    list_procedures,
    update_procedure,
)

router = APIRouter(prefix="/procedures", tags=["procedures"])


@router.get("", response_model=ProcedureListResponse)
async def list_procedures_endpoint(
    user: Annotated[AuthenticatedUser, require_permission("procedure.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> ProcedureListResponse:
    rows, total = await list_procedures(session, user.company_id, page, page_size, search)
    return ProcedureListResponse(
        items=[
            ProcedureSummary(
                id=p.id,
                name=p.name,
                link=p.link,
                file=p.file,
                updated_at=p.updated_at,
            )
            for p in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.post("", response_model=ProcedureSummary, status_code=201)
async def create_procedure_endpoint(
    body: ProcedureCreate,
    user: Annotated[AuthenticatedUser, require_permission("procedure.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ProcedureSummary:
    record = await create_procedure(
        session,
        user.company_id,
        user.id,
        name=body.name,
        link=body.link,
        file=body.file,
    )
    return ProcedureSummary(
        id=record.id,
        name=record.name,
        link=record.link,
        file=record.file,
        updated_at=record.updated_at,
    )


@router.patch("/{procedure_id}", response_model=ProcedureSummary)
async def update_procedure_endpoint(
    procedure_id: int,
    body: ProcedureUpdate,
    user: Annotated[AuthenticatedUser, require_permission("procedure.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ProcedureSummary:
    updates = body.model_dump(exclude_none=True)
    record = await update_procedure(session, user.company_id, user.id, procedure_id, updates)
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return ProcedureSummary(
        id=record.id,
        name=record.name,
        link=record.link,
        file=record.file,
        updated_at=record.updated_at,
    )


@router.delete("/{procedure_id}", status_code=204)
async def delete_procedure_endpoint(
    procedure_id: int,
    user: Annotated[AuthenticatedUser, require_permission("procedure.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_procedure(session, user.company_id, user.id, procedure_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
