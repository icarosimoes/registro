from datetime import date, datetime, timedelta

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.integrations.notifications import notify_record_event
from app.models import Location, PreventivePlan, User, WorkOrder
from app.models.operations import RECURRENCE_TYPES

RECURRENCE_DAYS: dict[str, int] = {
    "daily": 1,
    "weekly": 7,
    "biweekly": 14,
    "monthly": 30,
    "quarterly": 90,
    "semiannual": 180,
    "annual": 365,
}


def _next_due_from(current: date, recurrence: str) -> date:
    return current + timedelta(days=RECURRENCE_DAYS.get(recurrence, 30))


async def list_plans(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
    active_only: bool = False,
) -> tuple[list[tuple], int]:
    filters = [
        PreventivePlan.company_id == company_id,
        PreventivePlan.deleted_at.is_(None),
    ]
    if active_only:
        filters.append(PreventivePlan.active.is_(True))
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(PreventivePlan.name.ilike(pattern), PreventivePlan.description.ilike(pattern))
        )
    total = await session.scalar(select(func.count(PreventivePlan.id)).where(*filters)) or 0

    assigned = User.__table__.alias("assigned")
    loc = Location.__table__.alias("loc")

    rows = (
        await session.execute(
            select(
                PreventivePlan,
                assigned.c.name.label("assigned_user_name"),
                loc.c.name.label("location_name"),
            )
            .outerjoin(assigned, assigned.c.id == PreventivePlan.assigned_user_id)
            .outerjoin(loc, loc.c.id == PreventivePlan.location_id)
            .where(*filters)
            .order_by(PreventivePlan.next_due.asc().nullslast(), PreventivePlan.id.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_plan(
    session: AsyncSession,
    company_id: int,
    plan_id: int,
) -> tuple | None:
    assigned = User.__table__.alias("assigned")
    loc = Location.__table__.alias("loc")
    row = (
        await session.execute(
            select(
                PreventivePlan,
                assigned.c.name.label("assigned_user_name"),
                loc.c.name.label("location_name"),
            )
            .outerjoin(assigned, assigned.c.id == PreventivePlan.assigned_user_id)
            .outerjoin(loc, loc.c.id == PreventivePlan.location_id)
            .where(
                PreventivePlan.id == plan_id,
                PreventivePlan.company_id == company_id,
                PreventivePlan.deleted_at.is_(None),
            )
        )
    ).first()
    return row


async def create_plan(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    **fields,
) -> tuple:
    if fields.get("recurrence") and fields["recurrence"] not in RECURRENCE_TYPES:
        raise ValueError(f"Recorrência inválida: {fields['recurrence']}")

    if not fields.get("next_due"):
        recurrence = fields.get("recurrence", "monthly")
        fields["next_due"] = date.today() + timedelta(
            days=RECURRENCE_DAYS.get(recurrence, 30),
        )

    rec = PreventivePlan(company_id=company_id, **fields)
    session.add(rec)
    await session.flush()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="preventive_plan",
        entity_id=rec.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(rec)
    return await get_plan(session, company_id, rec.id)


async def update_plan(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    plan_id: int,
    updates: dict,
) -> tuple | None:
    rec = await session.scalar(
        select(PreventivePlan).where(
            PreventivePlan.id == plan_id,
            PreventivePlan.company_id == company_id,
            PreventivePlan.deleted_at.is_(None),
        )
    )
    if rec is None:
        return None

    if "recurrence" in updates and updates["recurrence"] not in RECURRENCE_TYPES:
        raise ValueError(f"Recorrência inválida: {updates['recurrence']}")

    before = {k: str(getattr(rec, k)) for k in updates}
    for field, value in updates.items():
        setattr(rec, field, value)

    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="preventive_plan",
            entity_id=rec.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    return await get_plan(session, company_id, plan_id)


async def delete_plan(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    plan_id: int,
) -> bool:
    rec = await session.scalar(
        select(PreventivePlan).where(
            PreventivePlan.id == plan_id,
            PreventivePlan.company_id == company_id,
            PreventivePlan.deleted_at.is_(None),
        )
    )
    if rec is None:
        return False
    rec.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="preventive_plan",
        entity_id=rec.id,
        event_type="delete",
    )
    await session.commit()
    return True


async def generate_due_orders(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
) -> list[int]:
    today = date.today()
    plans = (
        (
            await session.execute(
                select(PreventivePlan).where(
                    PreventivePlan.company_id == company_id,
                    PreventivePlan.deleted_at.is_(None),
                    PreventivePlan.active.is_(True),
                    PreventivePlan.next_due <= today,
                )
            )
        )
        .scalars()
        .all()
    )

    created_ids: list[int] = []
    now = datetime.now()

    for plan in plans:
        sla_deadline = None
        if plan.sla_hours:
            sla_deadline = now + timedelta(hours=plan.sla_hours)

        wo = WorkOrder(
            company_id=company_id,
            title=f"[Preventiva] {plan.name}",
            description=plan.description,
            status="aberta",
            priority=plan.priority,
            category=plan.category,
            location_id=plan.location_id,
            maintenance_id=None,
            assigned_user_id=plan.assigned_user_id,
            created_by_user_id=user_id,
            sla_hours=plan.sla_hours,
            sla_deadline=sla_deadline,
        )
        session.add(wo)
        await session.flush()

        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="work_order",
            entity_id=wo.id,
            event_type="create",
        )

        plan.last_generated_at = now
        plan.next_due = _next_due_from(plan.next_due, plan.recurrence)
        created_ids.append(wo.id)

    if created_ids:
        await session.commit()

        await notify_record_event(
            session,
            company_id=company_id,
            actor_name=user_name,
            actor_email=user_email,
            event="create",
            title=f"{len(created_ids)} OS preventivas geradas",
            module="Manutenção Preventiva",
        )

    return created_ids
