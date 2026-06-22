from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException
from fastapi.responses import StreamingResponse
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.export import generate_xlsx
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.checklists import schemas
from app.domain.checklists.service import (
    complete_execution,
    create_template,
    delete_template,
    export_executions,
    generate_due_executions,
    get_execution,
    get_template,
    list_executions,
    list_templates,
    toggle_item,
    update_template,
)

router = APIRouter(prefix="/checklists", tags=["checklists"])


def _tmpl_row_to_out(row, items=None) -> schemas.TemplateOut:
    tmpl = row[0] if not isinstance(row, dict) else row["template"]
    if isinstance(row, dict):
        return schemas.TemplateOut(
            id=tmpl.id,
            name=tmpl.name,
            description=tmpl.description,
            recurrence=tmpl.recurrence,
            category=tmpl.category,
            assigned_user_id=tmpl.assigned_user_id,
            assigned_user_name=row.get("assigned_user_name"),
            active=tmpl.active,
            next_due=tmpl.next_due,
            last_generated_at=tmpl.last_generated_at,
            item_count=len(row.get("items", [])),
            items=[
                schemas.TemplateItemOut(
                    id=i.id,
                    label=i.label,
                    sort_order=i.sort_order,
                )
                for i in row.get("items", [])
            ],
            created_at=tmpl.created_at,
            updated_at=tmpl.updated_at,
        )
    return schemas.TemplateOut(
        id=tmpl.id,
        name=tmpl.name,
        description=tmpl.description,
        recurrence=tmpl.recurrence,
        category=tmpl.category,
        assigned_user_id=tmpl.assigned_user_id,
        assigned_user_name=row.assigned_user_name,
        active=tmpl.active,
        next_due=tmpl.next_due,
        last_generated_at=tmpl.last_generated_at,
        item_count=row.item_count,
        created_at=tmpl.created_at,
        updated_at=tmpl.updated_at,
    )


def _exec_to_out(data, with_items=False) -> schemas.ExecutionOut:
    if isinstance(data, dict):
        row = data["row"]
        items = data.get("items", [])
        exc = row[0]
        total = len(items)
        checked = sum(1 for i in items if i.checked)
        progress = round(checked / total * 100) if total > 0 else 0
        return schemas.ExecutionOut(
            id=exc.id,
            template_id=exc.template_id,
            template_name=row.template_name,
            due_date=exc.due_date,
            status=exc.status,
            completed_at=exc.completed_at,
            completed_by_user_id=exc.completed_by_user_id,
            completed_by_name=row.completed_by_name,
            notes=exc.notes,
            progress=progress,
            items=[
                schemas.ExecutionItemOut(
                    id=i.id,
                    label=i.label,
                    sort_order=i.sort_order,
                    checked=i.checked,
                    checked_at=i.checked_at,
                )
                for i in items
            ]
            if with_items
            else None,
            created_at=exc.created_at,
            updated_at=exc.updated_at,
        )
    exc = data[0]
    return schemas.ExecutionOut(
        id=exc.id,
        template_id=exc.template_id,
        template_name=data.template_name,
        due_date=exc.due_date,
        status=exc.status,
        completed_at=exc.completed_at,
        completed_by_user_id=exc.completed_by_user_id,
        completed_by_name=data.completed_by_name,
        notes=exc.notes,
        created_at=exc.created_at,
        updated_at=exc.updated_at,
    )


# ─── Templates ────────────────────────────────────────────────────────────────


@router.get("/templates", response_model=schemas.TemplateList)
async def list_checklist_templates(
    user: Annotated[AuthenticatedUser, require_permission("checklist.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: int = 1,
    page_size: int = 25,
    search: str | None = None,
    active_only: bool = False,
):
    rows, total = await list_templates(
        session,
        user.company_id,
        page,
        page_size,
        search,
        active_only,
    )
    return schemas.TemplateList(
        items=[_tmpl_row_to_out(r) for r in rows],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/templates/{template_id}", response_model=schemas.TemplateOut)
async def get_checklist_template(
    template_id: int,
    user: Annotated[AuthenticatedUser, require_permission("checklist.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    data = await get_template(session, user.company_id, template_id)
    if data is None:
        raise HTTPException(404, detail="Template não encontrado")
    return _tmpl_row_to_out(data)


@router.post("/templates", response_model=schemas.TemplateOut, status_code=201)
async def create_checklist_template(
    body: schemas.TemplateCreate,
    user: Annotated[AuthenticatedUser, require_permission("checklist.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    try:
        data = await create_template(
            session,
            user.company_id,
            user.id,
            name=body.name,
            description=body.description,
            recurrence=body.recurrence,
            category=body.category,
            assigned_user_id=body.assigned_user_id,
            next_due=body.next_due,
            items=[i.model_dump() for i in body.items],
        )
    except ValueError as exc:
        raise HTTPException(422, detail=str(exc)) from None
    return _tmpl_row_to_out(data)


@router.patch("/templates/{template_id}", response_model=schemas.TemplateOut)
async def update_checklist_template(
    template_id: int,
    body: schemas.TemplateUpdate,
    user: Annotated[AuthenticatedUser, require_permission("checklist.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    updates = body.model_dump(exclude_unset=True)
    if "items" in updates and updates["items"] is not None:
        updates["items"] = [
            i.model_dump() if hasattr(i, "model_dump") else i for i in updates["items"]
        ]
    if not updates:
        raise HTTPException(422, detail="Nenhum campo alterado")
    try:
        data = await update_template(
            session,
            user.company_id,
            user.id,
            template_id,
            updates,
        )
    except ValueError as exc:
        raise HTTPException(422, detail=str(exc)) from None
    if data is None:
        raise HTTPException(404, detail="Template não encontrado")
    return _tmpl_row_to_out(data)


@router.delete("/templates/{template_id}", status_code=204)
async def delete_checklist_template(
    template_id: int,
    user: Annotated[AuthenticatedUser, require_permission("checklist.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    if not await delete_template(
        session,
        user.company_id,
        user.id,
        template_id,
    ):
        raise HTTPException(404, detail="Template não encontrado")


# ─── Executions ───────────────────────────────────────────────────────────────


@router.get("/executions", response_model=schemas.ExecutionList)
async def list_checklist_executions(
    user: Annotated[AuthenticatedUser, require_permission("checklist.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: int = 1,
    page_size: int = 25,
    template_id: int | None = None,
    status: str | None = None,
):
    rows, total = await list_executions(
        session,
        user.company_id,
        page,
        page_size,
        template_id,
        status,
    )
    return schemas.ExecutionList(
        items=[_exec_to_out(r) for r in rows],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/executions/export")
async def export_checklist_executions(
    user: Annotated[AuthenticatedUser, require_permission("checklist.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    template_id: int | None = None,
    status: str | None = None,
) -> StreamingResponse:
    rows = await export_executions(
        session,
        user.company_id,
        template_id,
        status,
    )
    headers = [
        "ID",
        "Template",
        "Data Prevista",
        "Status",
        "Concluído em",
        "Concluído por",
        "Notas",
    ]
    data = []
    for row in rows:
        exc = row[0]
        data.append(
            [
                exc.id,
                row.template_name,
                exc.due_date,
                exc.status,
                exc.completed_at,
                row.completed_by_name or "",
                exc.notes or "",
            ]
        )
    buf = generate_xlsx(title="Checklists", headers=headers, rows=data)
    return StreamingResponse(
        buf,
        media_type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        headers={"Content-Disposition": 'attachment; filename="checklists.xlsx"'},
    )


@router.get("/executions/{execution_id}", response_model=schemas.ExecutionOut)
async def get_checklist_execution(
    execution_id: int,
    user: Annotated[AuthenticatedUser, require_permission("checklist.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    data = await get_execution(session, user.company_id, execution_id)
    if data is None:
        raise HTTPException(404, detail="Execução não encontrada")
    return _exec_to_out(data, with_items=True)


@router.post(
    "/executions/{execution_id}/toggle",
    response_model=schemas.ExecutionOut,
)
async def toggle_checklist_item(
    execution_id: int,
    body: schemas.ExecutionToggle,
    user: Annotated[AuthenticatedUser, require_permission("checklist.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    data = await toggle_item(
        session,
        user.company_id,
        user.id,
        execution_id,
        body.item_id,
        body.checked,
    )
    if data is None:
        raise HTTPException(404, detail="Item não encontrado")
    return _exec_to_out(data, with_items=True)


@router.post(
    "/executions/{execution_id}/complete",
    response_model=schemas.ExecutionOut,
)
async def complete_checklist_execution(
    execution_id: int,
    body: schemas.ExecutionComplete,
    user: Annotated[AuthenticatedUser, require_permission("checklist.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    data = await complete_execution(
        session,
        user.company_id,
        user.id,
        execution_id,
        body.notes,
    )
    if data is None:
        raise HTTPException(404, detail="Execução não encontrada")
    return _exec_to_out(data, with_items=True)


@router.post("/generate", response_model=schemas.GenerateResult)
async def generate_executions(
    user: Annotated[AuthenticatedUser, require_permission("checklist.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
):
    ids = await generate_due_executions(session, user.company_id)
    return schemas.GenerateResult(generated=len(ids), execution_ids=ids)
