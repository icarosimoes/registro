from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from fastapi.responses import StreamingResponse
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.export import generate_xlsx
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.maintenance.schemas import (
    MaintenanceCreate,
    MaintenanceListResponse,
    MaintenanceOut,
    MaintenanceUpdate,
)
from app.domain.maintenance.service import (
    create_record,
    delete_record,
    export_records,
    get_record,
    list_records,
    update_record,
)

router = APIRouter(prefix="/maintenance", tags=["maintenance"])


def _to_out(rec, owner_name: str | None) -> MaintenanceOut:
    return MaintenanceOut(
        id=rec.id,
        title=rec.title,
        description=rec.description,
        category=rec.category,
        status=rec.status,
        priority=rec.priority,
        location_id=rec.location_id,
        owner_user_id=rec.owner_user_id,
        owner_name=owner_name or "Não atribuído",
        created_at=str(rec.created_at),
        updated_at=str(rec.updated_at),
    )


@router.get("", response_model=MaintenanceListResponse)
async def list_maintenance(
    user: Annotated[AuthenticatedUser, require_permission("maintenance.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: int = Query(1, ge=1),
    page_size: int = Query(20, ge=1, le=100),
    search: str | None = None,
    status: str | None = None,
) -> MaintenanceListResponse:
    rows, total = await list_records(session, user.company_id, page, page_size, search, status)
    return MaintenanceListResponse(
        items=[_to_out(rec, name) for rec, name in rows],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/export")
async def export_maintenance(
    user: Annotated[AuthenticatedUser, require_permission("maintenance.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    search: str | None = None,
    status: str | None = None,
) -> StreamingResponse:
    rows = await export_records(session, user.company_id, search, status)
    headers = [
        "ID", "Título", "Descrição", "Categoria", "Status",
        "Prioridade", "Responsável", "Criado em", "Atualizado em",
    ]
    data = [
        [
            rec.id, rec.title, rec.description or "",
            rec.category or "", rec.status or "",
            rec.priority or "", owner_name or "",
            rec.created_at, rec.updated_at,
        ]
        for rec, owner_name in rows
    ]
    buf = generate_xlsx(title="Manutenção", headers=headers, rows=data)
    return StreamingResponse(
        buf,
        media_type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        headers={"Content-Disposition": 'attachment; filename="manutencao.xlsx"'},
    )


@router.get("/{record_id}", response_model=MaintenanceOut)
async def get_maintenance(
    record_id: int,
    user: Annotated[AuthenticatedUser, require_permission("maintenance.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> MaintenanceOut:
    result = await get_record(session, user.company_id, record_id)
    if result is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return _to_out(result[0], result[1])


@router.post("", response_model=MaintenanceOut, status_code=201)
async def create_maintenance(
    body: MaintenanceCreate,
    user: Annotated[AuthenticatedUser, require_permission("maintenance.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> MaintenanceOut:
    rec, owner_name = await create_record(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        title=body.title,
        description=body.description,
        category=body.category,
        status=body.status,
        priority=body.priority,
        location_id=body.location_id,
        owner_user_id=body.owner_user_id,
        notify_user_ids=body.notify_user_ids,
    )
    return _to_out(rec, owner_name)


@router.patch("/{record_id}", response_model=MaintenanceOut)
async def update_maintenance(
    record_id: int,
    body: MaintenanceUpdate,
    user: Annotated[AuthenticatedUser, require_permission("maintenance.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> MaintenanceOut:
    updates = body.model_dump(exclude_unset=True)
    if not updates:
        raise HTTPException(status_code=422, detail={"code": "no_fields"})
    result = await update_record(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        record_id,
        updates,
    )
    if result is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return _to_out(result[0], result[1])


@router.delete("/{record_id}", status_code=204)
async def delete_maintenance(
    record_id: int,
    user: Annotated[AuthenticatedUser, require_permission("maintenance.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_record(session, user.company_id, user.id, record_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
