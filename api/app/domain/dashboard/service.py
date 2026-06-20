from datetime import datetime

from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.models import FiscalRequest, Occurrence, Sector, User

STATUS_LABELS = {1: "Em andamento", 2: "Concluído", 3: "Aguardando"}


async def get_metrics(
    session: AsyncSession,
    company_id: int,
    user_id: int,
) -> dict:
    base = [Occurrence.company_id == company_id, Occurrence.deleted_at.is_(None)]

    open_occurrences = (
        await session.scalar(
            select(func.count(Occurrence.id)).where(*base, Occurrence.status.in_([1, 3]))
        )
        or 0
    )

    my_occurrences = (
        await session.scalar(
            select(func.count(Occurrence.id)).where(
                *base, Occurrence.status.in_([1, 3]), Occurrence.owner_user_id == user_id
            )
        )
        or 0
    )

    open_fiscal = (
        await session.scalar(
            select(func.count(FiscalRequest.id)).where(
                FiscalRequest.company_id == company_id,
                FiscalRequest.status != "Concluído",
            )
        )
        or 0
    )

    now = datetime.now()
    month_start = now.replace(day=1, hour=0, minute=0, second=0, microsecond=0)
    completed_month = (
        await session.scalar(
            select(func.count(Occurrence.id)).where(
                *base, Occurrence.status == 2, Occurrence.updated_at >= month_start
            )
        )
        or 0
    )

    active_users = (
        await session.scalar(
            select(func.count(User.id)).where(
                User.company_id == company_id,
                User.active.is_(True),
                User.deleted_at.is_(None),
            )
        )
        or 0
    )

    active_sectors = (
        await session.scalar(
            select(func.count(Sector.id)).where(
                Sector.company_id == company_id, Sector.deleted_at.is_(None)
            )
        )
        or 0
    )

    rows = (
        await session.execute(
            select(Occurrence, Sector.name, User.name)
            .outerjoin(Sector, Sector.id == Occurrence.sector_id)
            .outerjoin(User, User.id == Occurrence.owner_user_id)
            .where(*base)
            .order_by(Occurrence.updated_at.desc())
            .limit(10)
        )
    ).all()

    recent = [
        {
            "id": occ.id,
            "title": occ.title,
            "area": sector_name or "Sem setor",
            "owner": owner_name or "Não atribuído",
            "status": STATUS_LABELS.get(occ.status, f"Status {occ.status}"),
            "updated_at": occ.updated_at,
        }
        for occ, sector_name, owner_name in rows
    ]

    return {
        "open_occurrences": open_occurrences,
        "my_occurrences": my_occurrences,
        "open_fiscal": open_fiscal,
        "completed_month": completed_month,
        "active_users": active_users,
        "active_sectors": active_sectors,
        "recent": recent,
    }
