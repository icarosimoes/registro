from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.preventive_plans import schemas
from app.domain.preventive_plans.service import (
    create_plan,
    delete_plan,
    generate_due_orders,
    get_plan,
    list_plans,
    update_plan,
)

router = APIRouter(prefix="/preventive-plans", tags=["preventive-plans"])


def _row_to_out(row) -> schemas.PreventivePlanOut:
    plan = row[0]
    return schemas.PreventivePlanOut(
        id=plan.id,
        name=plan.name,
        description=plan.description,
        recurrence=plan.recurrence,
        category=plan.category,
        priority=plan.priority,
        sla_hours=plan.sla_hours,
        location_id=plan.location_id,
        location_name=row.location_name,
        assigned_user_id=plan.assigned_user_id,
        assigned_user_name=row.assigned_user_name,
        active=plan.active,
        next_due=plan.next_due,
        last_generated_at=plan.last_generated_at,
        created_at=plan.created_at,
        updated_at=plan.updated_at,
    )


@router.get("", response_model=schemas.PreventivePlanList)
async def list_preventive_plans(
    user: Annotated[AuthenticatedUser, require_permission("preventive_plan.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: int = 1,
    page_size: int = 25,
    search: str | None = None,
    active_only: bool = False,
):
    rows, total = await list_plans(
        session, user.company_id, page, page_size, search, active_only,
    )
    return schemas.PreventivePlanList(
        items=[_row_to_out(r) for r in rows],
        total=total, page=page, page_size=page_size,
    )


@router.get("/{plan_id}", response_model=schemas.PreventivePlanOut)
async def get_preventive_plan(
    plan_id: int,
    user: Annotated[AuthenticatedUser, require_permission("preventive_plan.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    row = await get_plan(session, user.company_id, plan_id)
    if row is None:
        raise HTTPException(404, detail="Plano não encontrado")
    return _row_to_out(row)


@router.post("", response_model=schemas.PreventivePlanOut, status_code=201)
async def create_preventive_plan(
    body: schemas.PreventivePlanCreate,
    user: Annotated[AuthenticatedUser, require_permission("preventive_plan.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    try:
        row = await create_plan(
            session, user.company_id, user.id, **body.model_dump(),
        )
    except ValueError as exc:
        raise HTTPException(422, detail=str(exc)) from None
    return _row_to_out(row)


@router.patch("/{plan_id}", response_model=schemas.PreventivePlanOut)
async def update_preventive_plan(
    plan_id: int,
    body: schemas.PreventivePlanUpdate,
    user: Annotated[AuthenticatedUser, require_permission("preventive_plan.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    updates = body.model_dump(exclude_unset=True)
    if not updates:
        raise HTTPException(422, detail="Nenhum campo alterado")
    try:
        row = await update_plan(
            session, user.company_id, user.id, plan_id, updates,
        )
    except ValueError as exc:
        raise HTTPException(422, detail=str(exc)) from None
    if row is None:
        raise HTTPException(404, detail="Plano não encontrado")
    return _row_to_out(row)


@router.delete("/{plan_id}", status_code=204)
async def delete_preventive_plan(
    plan_id: int,
    user: Annotated[AuthenticatedUser, require_permission("preventive_plan.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    if not await delete_plan(session, user.company_id, user.id, plan_id):
        raise HTTPException(404, detail="Plano não encontrado")


@router.post("/generate", response_model=schemas.GenerateResult)
async def generate_orders(
    user: Annotated[AuthenticatedUser, require_permission("preventive_plan.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    ids = await generate_due_orders(
        session, user.company_id, user.id, user.name, user.email,
    )
    return schemas.GenerateResult(generated=len(ids), work_order_ids=ids)
