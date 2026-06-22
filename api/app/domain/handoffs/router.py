from datetime import date
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.handoffs import schemas
from app.domain.handoffs.service import (
    create_handoff,
    delete_handoff,
    get_handoff,
    list_handoffs,
    mark_read,
    pending_for_shift,
    resolve_handoff,
    update_handoff,
)

router = APIRouter(prefix="/handoffs", tags=["handoffs"])


def _to_out(row) -> schemas.HandoffOut:
    h = row[0]
    return schemas.HandoffOut(
        id=h.id,
        title=h.title,
        description=h.description,
        priority=h.priority,
        category=h.category,
        target_shift=h.target_shift,
        target_date=h.target_date,
        status=h.status,
        shift_report_id=h.shift_report_id,
        read_at=h.read_at,
        read_by_user_id=h.read_by_user_id,
        read_by_name=getattr(row, "read_by_name", None),
        resolved_at=h.resolved_at,
        resolved_by_user_id=h.resolved_by_user_id,
        resolved_by_name=getattr(row, "resolved_by_name", None),
        resolution_notes=h.resolution_notes,
        created_by_user_id=h.created_by_user_id,
        created_by_name=getattr(row, "created_by_name", None),
        created_at=h.created_at,
        updated_at=h.updated_at,
    )


@router.get("", response_model=schemas.HandoffList)
async def list_shift_handoffs(
    user: Annotated[AuthenticatedUser, require_permission("handoff.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: int = 1,
    page_size: int = 25,
    target_date: date | None = None,
    target_shift: str | None = None,
    status: str | None = None,
    search: str | None = None,
):
    rows, total = await list_handoffs(
        session,
        user.company_id,
        page,
        page_size,
        target_date,
        target_shift,
        status,
        search,
    )
    return schemas.HandoffList(
        items=[_to_out(r) for r in rows],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/pending", response_model=list[schemas.HandoffOut])
async def get_pending_handoffs(
    user: Annotated[AuthenticatedUser, require_permission("handoff.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    target_date: date | None = None,
    target_shift: str | None = None,
):
    d = target_date or date.today()
    rows = await pending_for_shift(
        session,
        user.company_id,
        d,
        target_shift,
    )
    return [_to_out(r) for r in rows]


@router.get("/{handoff_id}", response_model=schemas.HandoffOut)
async def get_shift_handoff(
    handoff_id: int,
    user: Annotated[AuthenticatedUser, require_permission("handoff.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    row = await get_handoff(session, user.company_id, handoff_id)
    if row is None:
        raise HTTPException(404, detail="Pendência não encontrada")
    return _to_out(row)


@router.post("", response_model=schemas.HandoffOut, status_code=201)
async def create_shift_handoff(
    body: schemas.HandoffCreate,
    user: Annotated[AuthenticatedUser, require_permission("handoff.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    row = await create_handoff(
        session,
        user.company_id,
        user.id,
        **body.model_dump(),
    )
    return _to_out(row)


@router.patch("/{handoff_id}", response_model=schemas.HandoffOut)
async def update_shift_handoff(
    handoff_id: int,
    body: schemas.HandoffUpdate,
    user: Annotated[AuthenticatedUser, require_permission("handoff.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    updates = body.model_dump(exclude_unset=True)
    if not updates:
        raise HTTPException(422, detail="Nenhum campo alterado")
    row = await update_handoff(
        session,
        user.company_id,
        user.id,
        handoff_id,
        updates,
    )
    if row is None:
        raise HTTPException(404, detail="Pendência não encontrada")
    return _to_out(row)


@router.post("/{handoff_id}/read", response_model=schemas.HandoffOut)
async def mark_handoff_read(
    handoff_id: int,
    user: Annotated[AuthenticatedUser, require_permission("handoff.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    row = await mark_read(session, user.company_id, user.id, handoff_id)
    if row is None:
        raise HTTPException(404, detail="Pendência não encontrada")
    return _to_out(row)


@router.post("/{handoff_id}/resolve", response_model=schemas.HandoffOut)
async def resolve_shift_handoff(
    handoff_id: int,
    body: schemas.HandoffResolve,
    user: Annotated[AuthenticatedUser, require_permission("handoff.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    row = await resolve_handoff(
        session,
        user.company_id,
        user.id,
        handoff_id,
        body.resolution_notes,
    )
    if row is None:
        raise HTTPException(404, detail="Pendência não encontrada")
    return _to_out(row)


@router.delete("/{handoff_id}", status_code=204)
async def delete_shift_handoff(
    handoff_id: int,
    user: Annotated[AuthenticatedUser, require_permission("handoff.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    if not await delete_handoff(
        session,
        user.company_id,
        user.id,
        handoff_id,
    ):
        raise HTTPException(404, detail="Pendência não encontrada")
