from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.work_orders.schemas import (
    WorkOrderCreate,
    WorkOrderListResponse,
    WorkOrderOut,
    WorkOrderTransition,
    WorkOrderUpdate,
)
from app.domain.work_orders.service import (
    STATUS_LABELS,
    TRANSITIONS,
    count_by_status,
    create_order,
    delete_order,
    get_order,
    list_orders,
    transition_order,
    update_order,
)

router = APIRouter(prefix="/work-orders", tags=["work-orders"])


def _to_out(row) -> WorkOrderOut:
    rec, assigned_name, created_by_name = row
    return WorkOrderOut(
        id=rec.id,
        title=rec.title,
        description=rec.description,
        status=rec.status,
        priority=rec.priority,
        category=rec.category,
        location_id=rec.location_id,
        occurrence_id=rec.occurrence_id,
        maintenance_id=rec.maintenance_id,
        assigned_user_id=rec.assigned_user_id,
        assigned_user_name=assigned_name or "Não atribuído",
        created_by_user_id=rec.created_by_user_id,
        created_by_user_name=created_by_name,
        validated_by_user_id=rec.validated_by_user_id,
        sla_hours=rec.sla_hours,
        sla_deadline=str(rec.sla_deadline) if rec.sla_deadline else None,
        started_at=str(rec.started_at) if rec.started_at else None,
        completed_at=str(rec.completed_at) if rec.completed_at else None,
        validated_at=str(rec.validated_at) if rec.validated_at else None,
        created_at=str(rec.created_at),
        updated_at=str(rec.updated_at),
    )


@router.get("", response_model=WorkOrderListResponse)
async def list_work_orders(
    user: Annotated[AuthenticatedUser, require_permission("work_order.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: int = Query(1, ge=1),
    page_size: int = Query(20, ge=1, le=100),
    search: str | None = None,
    status: str | None = None,
    priority: str | None = None,
) -> WorkOrderListResponse:
    rows, total = await list_orders(
        session, user.company_id, page, page_size, search, status, priority,
    )
    return WorkOrderListResponse(
        items=[_to_out(row) for row in rows],
        total=total, page=page, page_size=page_size,
    )


@router.get("/summary")
async def work_order_summary(
    user: Annotated[AuthenticatedUser, require_permission("work_order.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> dict:
    counts = await count_by_status(session, user.company_id)
    return {
        "by_status": counts,
        "total": sum(counts.values()),
        "status_labels": STATUS_LABELS,
        "transitions": TRANSITIONS,
    }


@router.get("/{order_id}", response_model=WorkOrderOut)
async def get_work_order(
    order_id: int,
    user: Annotated[AuthenticatedUser, require_permission("work_order.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> WorkOrderOut:
    row = await get_order(session, user.company_id, order_id)
    if row is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return _to_out(row)


@router.post("", response_model=WorkOrderOut, status_code=201)
async def create_work_order(
    body: WorkOrderCreate,
    user: Annotated[AuthenticatedUser, require_permission("work_order.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> WorkOrderOut:
    row = await create_order(
        session, user.company_id, user.id, user.name, user.email,
        title=body.title, description=body.description, priority=body.priority,
        category=body.category, location_id=body.location_id,
        occurrence_id=body.occurrence_id, maintenance_id=body.maintenance_id,
        assigned_user_id=body.assigned_user_id,
        notify_user_ids=body.notify_user_ids, sla_hours=body.sla_hours,
    )
    return _to_out(row)


@router.patch("/{order_id}", response_model=WorkOrderOut)
async def update_work_order(
    order_id: int,
    body: WorkOrderUpdate,
    user: Annotated[AuthenticatedUser, require_permission("work_order.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> WorkOrderOut:
    updates = body.model_dump(exclude_unset=True)
    if not updates:
        raise HTTPException(status_code=422, detail={"code": "no_fields"})
    row = await update_order(
        session, user.company_id, user.id, user.name, user.email, order_id, updates,
    )
    if row is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return _to_out(row)


@router.post("/{order_id}/transition/{target_status}", response_model=WorkOrderOut)
async def transition_work_order(
    order_id: int,
    target_status: str,
    body: WorkOrderTransition | None = None,
    user: Annotated[AuthenticatedUser, require_permission("work_order.edit")] = None,
    session: Annotated[AsyncSession, Depends(require_session)] = None,
) -> WorkOrderOut:
    try:
        row = await transition_order(
            session, user.company_id, user.id, user.name, user.email,
            order_id, target_status, notes=body.notes if body else None,
        )
    except ValueError as exc:
        raise HTTPException(
            status_code=422,
            detail={"code": "invalid_transition", "message": str(exc)},
        ) from None
    if row is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return _to_out(row)


@router.delete("/{order_id}", status_code=204)
async def delete_work_order(
    order_id: int,
    user: Annotated[AuthenticatedUser, require_permission("work_order.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_order(session, user.company_id, user.id, order_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
