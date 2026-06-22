from datetime import date, datetime, timedelta

from sqlalchemy import delete, func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.models import (
    ChecklistExecution,
    ChecklistExecutionItem,
    ChecklistTemplate,
    ChecklistTemplateItem,
    User,
)
from app.models.operations import CHECKLIST_RECURRENCE_TYPES

RECURRENCE_DAYS: dict[str, int] = {
    "daily": 1,
    "weekly": 7,
    "biweekly": 14,
    "monthly": 30,
}


def _next_due_from(current: date, recurrence: str) -> date:
    return current + timedelta(days=RECURRENCE_DAYS.get(recurrence, 7))


# ─── Templates ────────────────────────────────────────────────────────────────

async def list_templates(
    session: AsyncSession, company_id: int,
    page: int, page_size: int,
    search: str | None = None, active_only: bool = False,
) -> tuple[list, int]:
    filters = [
        ChecklistTemplate.company_id == company_id,
        ChecklistTemplate.deleted_at.is_(None),
    ]
    if active_only:
        filters.append(ChecklistTemplate.active.is_(True))
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(ChecklistTemplate.name.ilike(pattern), ChecklistTemplate.description.ilike(pattern))
        )
    total = await session.scalar(select(func.count(ChecklistTemplate.id)).where(*filters)) or 0

    item_count = (
        select(func.count(ChecklistTemplateItem.id))
        .where(ChecklistTemplateItem.template_id == ChecklistTemplate.id)
        .correlate(ChecklistTemplate)
        .scalar_subquery()
    )
    assigned = User.__table__.alias("assigned")

    rows = (
        await session.execute(
            select(
                ChecklistTemplate,
                assigned.c.name.label("assigned_user_name"),
                item_count.label("item_count"),
            )
            .outerjoin(assigned, assigned.c.id == ChecklistTemplate.assigned_user_id)
            .where(*filters)
            .order_by(ChecklistTemplate.updated_at.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_template(
    session: AsyncSession, company_id: int, template_id: int,
) -> dict | None:
    rec = await session.scalar(
        select(ChecklistTemplate).where(
            ChecklistTemplate.id == template_id,
            ChecklistTemplate.company_id == company_id,
            ChecklistTemplate.deleted_at.is_(None),
        )
    )
    if rec is None:
        return None
    assigned_name = None
    if rec.assigned_user_id:
        assigned_name = await session.scalar(
            select(User.name).where(User.id == rec.assigned_user_id)
        )
    items = (
        await session.execute(
            select(ChecklistTemplateItem)
            .where(ChecklistTemplateItem.template_id == template_id)
            .order_by(ChecklistTemplateItem.sort_order)
        )
    ).scalars().all()
    return {
        "template": rec,
        "assigned_user_name": assigned_name,
        "items": items,
    }


async def create_template(
    session: AsyncSession, company_id: int, user_id: int,
    *, name: str, description: str | None, recurrence: str,
    category: str | None, assigned_user_id: int | None,
    next_due: date | None, items: list[dict],
) -> dict:
    if recurrence not in CHECKLIST_RECURRENCE_TYPES:
        raise ValueError(f"Recorrência inválida: {recurrence}")
    if not next_due:
        next_due = date.today() + timedelta(days=RECURRENCE_DAYS.get(recurrence, 7))

    rec = ChecklistTemplate(
        company_id=company_id, name=name, description=description,
        recurrence=recurrence, category=category,
        assigned_user_id=assigned_user_id, next_due=next_due,
    )
    session.add(rec)
    await session.flush()
    for item in items:
        session.add(ChecklistTemplateItem(template_id=rec.id, **item))
    await record_event(
        session, company_id=company_id, user_id=user_id,
        entity_type="checklist_template", entity_id=rec.id, event_type="create",
    )
    await session.commit()
    await session.refresh(rec)
    return await get_template(session, company_id, rec.id)


async def update_template(
    session: AsyncSession, company_id: int, user_id: int,
    template_id: int, updates: dict,
) -> dict | None:
    rec = await session.scalar(
        select(ChecklistTemplate).where(
            ChecklistTemplate.id == template_id,
            ChecklistTemplate.company_id == company_id,
            ChecklistTemplate.deleted_at.is_(None),
        )
    )
    if rec is None:
        return None

    items = updates.pop("items", None)
    if "recurrence" in updates and updates["recurrence"] not in CHECKLIST_RECURRENCE_TYPES:
        raise ValueError(f"Recorrência inválida: {updates['recurrence']}")

    before = {k: str(getattr(rec, k)) for k in updates}
    for field, value in updates.items():
        setattr(rec, field, value)

    if items is not None:
        await session.execute(
            delete(ChecklistTemplateItem).where(ChecklistTemplateItem.template_id == template_id)
        )
        for item in items:
            session.add(ChecklistTemplateItem(template_id=template_id, **item))

    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(
            session, company_id=company_id, user_id=user_id,
            entity_type="checklist_template", entity_id=rec.id,
            event_type="update", diff=diff,
        )
    await session.commit()
    return await get_template(session, company_id, template_id)


async def delete_template(
    session: AsyncSession, company_id: int, user_id: int, template_id: int,
) -> bool:
    rec = await session.scalar(
        select(ChecklistTemplate).where(
            ChecklistTemplate.id == template_id,
            ChecklistTemplate.company_id == company_id,
            ChecklistTemplate.deleted_at.is_(None),
        )
    )
    if rec is None:
        return False
    rec.deleted_at = datetime.now()
    await record_event(
        session, company_id=company_id, user_id=user_id,
        entity_type="checklist_template", entity_id=rec.id, event_type="delete",
    )
    await session.commit()
    return True


# ─── Executions ───────────────────────────────────────────────────────────────

async def list_executions(
    session: AsyncSession, company_id: int,
    page: int, page_size: int,
    template_id: int | None = None,
    status: str | None = None,
) -> tuple[list, int]:
    filters = [
        ChecklistExecution.company_id == company_id,
        ChecklistExecution.deleted_at.is_(None),
    ]
    if template_id:
        filters.append(ChecklistExecution.template_id == template_id)
    if status:
        filters.append(ChecklistExecution.status == status)

    total = await session.scalar(
        select(func.count(ChecklistExecution.id)).where(*filters)
    ) or 0

    completed_by = User.__table__.alias("completed_by")

    rows = (
        await session.execute(
            select(
                ChecklistExecution,
                ChecklistTemplate.name.label("template_name"),
                completed_by.c.name.label("completed_by_name"),
            )
            .join(ChecklistTemplate, ChecklistTemplate.id == ChecklistExecution.template_id)
            .outerjoin(completed_by, completed_by.c.id == ChecklistExecution.completed_by_user_id)
            .where(*filters)
            .order_by(ChecklistExecution.due_date.desc(), ChecklistExecution.id.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_execution(
    session: AsyncSession, company_id: int, execution_id: int,
) -> dict | None:
    completed_by = User.__table__.alias("completed_by")
    row = (
        await session.execute(
            select(
                ChecklistExecution,
                ChecklistTemplate.name.label("template_name"),
                completed_by.c.name.label("completed_by_name"),
            )
            .join(ChecklistTemplate, ChecklistTemplate.id == ChecklistExecution.template_id)
            .outerjoin(completed_by, completed_by.c.id == ChecklistExecution.completed_by_user_id)
            .where(
                ChecklistExecution.id == execution_id,
                ChecklistExecution.company_id == company_id,
                ChecklistExecution.deleted_at.is_(None),
            )
        )
    ).first()
    if row is None:
        return None

    items = (
        await session.execute(
            select(ChecklistExecutionItem)
            .where(ChecklistExecutionItem.execution_id == execution_id)
            .order_by(ChecklistExecutionItem.sort_order)
        )
    ).scalars().all()

    return {"row": row, "items": items}


async def toggle_item(
    session: AsyncSession, company_id: int, user_id: int,
    execution_id: int, item_id: int, checked: bool,
) -> dict | None:
    exec_rec = await session.scalar(
        select(ChecklistExecution).where(
            ChecklistExecution.id == execution_id,
            ChecklistExecution.company_id == company_id,
            ChecklistExecution.deleted_at.is_(None),
        )
    )
    if exec_rec is None:
        return None

    item = await session.scalar(
        select(ChecklistExecutionItem).where(
            ChecklistExecutionItem.id == item_id,
            ChecklistExecutionItem.execution_id == execution_id,
        )
    )
    if item is None:
        return None

    item.checked = checked
    item.checked_at = datetime.now() if checked else None
    await session.commit()
    return await get_execution(session, company_id, execution_id)


async def complete_execution(
    session: AsyncSession, company_id: int, user_id: int,
    execution_id: int, notes: str | None = None,
) -> dict | None:
    rec = await session.scalar(
        select(ChecklistExecution).where(
            ChecklistExecution.id == execution_id,
            ChecklistExecution.company_id == company_id,
            ChecklistExecution.deleted_at.is_(None),
        )
    )
    if rec is None:
        return None
    rec.status = "concluido"
    rec.completed_at = datetime.now()
    rec.completed_by_user_id = user_id
    if notes:
        rec.notes = notes

    await record_event(
        session, company_id=company_id, user_id=user_id,
        entity_type="checklist_execution", entity_id=rec.id, event_type="update",
        diff={"status": {"from": "pendente", "to": "concluido"}},
    )
    await session.commit()
    return await get_execution(session, company_id, execution_id)


# ─── Generation ───────────────────────────────────────────────────────────────

async def generate_due_executions(
    session: AsyncSession, company_id: int,
) -> list[int]:
    today = date.today()
    templates = (
        await session.execute(
            select(ChecklistTemplate).where(
                ChecklistTemplate.company_id == company_id,
                ChecklistTemplate.deleted_at.is_(None),
                ChecklistTemplate.active.is_(True),
                ChecklistTemplate.next_due <= today,
            )
        )
    ).scalars().all()

    created_ids: list[int] = []
    now = datetime.now()

    for tmpl in templates:
        items = (
            await session.execute(
                select(ChecklistTemplateItem)
                .where(ChecklistTemplateItem.template_id == tmpl.id)
                .order_by(ChecklistTemplateItem.sort_order)
            )
        ).scalars().all()

        execution = ChecklistExecution(
            company_id=company_id,
            template_id=tmpl.id,
            due_date=tmpl.next_due,
            status="pendente",
        )
        session.add(execution)
        await session.flush()

        for item in items:
            session.add(ChecklistExecutionItem(
                execution_id=execution.id,
                label=item.label,
                sort_order=item.sort_order,
            ))

        tmpl.last_generated_at = now
        tmpl.next_due = _next_due_from(tmpl.next_due, tmpl.recurrence)
        created_ids.append(execution.id)

    if created_ids:
        await session.commit()

    return created_ids
