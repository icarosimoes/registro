from datetime import datetime

from sqlalchemy import String, cast, func, literal_column, select, union_all
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import record_event
from app.models import Function, Location, Sector

MODELS = {"Setor": Sector, "Local": Location, "Função": Function}


async def list_registries(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list, int]:
    queries = []
    for label, model in MODELS.items():
        q = select(
            model.id,
            model.name,
            cast(literal_column(f"'{label}'"), String).label("category"),
            model.updated_at,
        ).where(model.company_id == company_id, model.deleted_at.is_(None))
        if search:
            q = q.where(model.name.ilike(f"%{search.strip()}%"))
        queries.append(q)

    combined = union_all(*queries).subquery()
    total = await session.scalar(select(func.count()).select_from(combined)) or 0
    rows = (
        await session.execute(
            select(combined)
            .order_by(combined.c.category, combined.c.name)
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def create_registry(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    name: str,
    category: str,
) -> tuple | None:
    model = MODELS.get(category)
    if model is None:
        return None
    record = model(company_id=company_id, name=name)
    session.add(record)
    await session.flush()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="registry",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(record)
    return record


async def update_registry(
    session: AsyncSession,
    company_id: int,
    registry_id: int,
    category: str,
    name: str | None,
):
    model = MODELS.get(category)
    if model is None:
        return "invalid_category"
    record = await session.scalar(
        select(model).where(
            model.id == registry_id,
            model.company_id == company_id,
            model.deleted_at.is_(None),
        )
    )
    if record is None:
        return None
    if name is not None:
        record.name = name
    await session.commit()
    await session.refresh(record)
    return record


async def delete_registry(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    registry_id: int,
    category: str,
) -> str | bool:
    model = MODELS.get(category)
    if model is None:
        return "invalid_category"
    record = await session.scalar(
        select(model).where(
            model.id == registry_id,
            model.company_id == company_id,
            model.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="registry",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True
