from typing import Annotated

import jwt
from fastapi import APIRouter, Depends, HTTPException
from fastapi.security import OAuth2PasswordBearer
from pydantic import BaseModel
from datetime import datetime

from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.security import decode_access_token
from app.domain.auth.repository import AuthenticatedUser, find_active_user_by_id
from app.models import Occurrence, FiscalRequest, User, Sector

router = APIRouter(prefix="/dashboard", tags=["dashboard"])
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/login")


async def current_user(
    token: Annotated[str, Depends(oauth2_scheme)],
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> AuthenticatedUser:
    try:
        claims = decode_access_token(token, settings.jwt_secret)
        user = await find_active_user_by_id(session, int(claims["sub"]), int(claims["company_id"]))
    except (jwt.InvalidTokenError, KeyError, TypeError, ValueError) as exc:
        raise HTTPException(status_code=401, detail={"code": "invalid_token"}) from exc
    if user is None:
        raise HTTPException(status_code=401, detail={"code": "inactive_user"})
    return user


STATUS_LABELS = {1: "Em andamento", 2: "Concluído", 3: "Aguardando"}


class RecentActivity(BaseModel):
    id: int
    title: str
    area: str
    owner: str
    status: str
    updated_at: datetime


class DashboardMetrics(BaseModel):
    open_occurrences: int
    my_occurrences: int
    open_fiscal: int
    completed_month: int
    active_users: int
    active_sectors: int
    recent: list[RecentActivity]


@router.get("/metrics", response_model=DashboardMetrics)
async def get_metrics(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> DashboardMetrics:
    cid = user.company_id
    base = [Occurrence.company_id == cid, Occurrence.deleted_at.is_(None)]

    open_occurrences = await session.scalar(
        select(func.count(Occurrence.id)).where(*base, Occurrence.status.in_([1, 3]))
    ) or 0

    my_occurrences = await session.scalar(
        select(func.count(Occurrence.id)).where(*base, Occurrence.status.in_([1, 3]), Occurrence.owner_user_id == user.id)
    ) or 0

    open_fiscal = await session.scalar(
        select(func.count(FiscalRequest.id)).where(
            FiscalRequest.company_id == cid,
            FiscalRequest.status != "Concluído",
        )
    ) or 0

    now = datetime.now()
    month_start = now.replace(day=1, hour=0, minute=0, second=0, microsecond=0)
    completed_month = await session.scalar(
        select(func.count(Occurrence.id)).where(
            *base, Occurrence.status == 2, Occurrence.updated_at >= month_start
        )
    ) or 0

    active_users = await session.scalar(
        select(func.count(User.id)).where(
            User.company_id == cid, User.active.is_(True), User.deleted_at.is_(None)
        )
    ) or 0

    active_sectors = await session.scalar(
        select(func.count(Sector.id)).where(
            Sector.company_id == cid, Sector.deleted_at.is_(None)
        )
    ) or 0

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
        RecentActivity(
            id=occ.id,
            title=occ.title,
            area=sector_name or "Sem setor",
            owner=owner_name or "Não atribuído",
            status=STATUS_LABELS.get(occ.status, f"Status {occ.status}"),
            updated_at=occ.updated_at,
        )
        for occ, sector_name, owner_name in rows
    ]

    return DashboardMetrics(
        open_occurrences=open_occurrences,
        my_occurrences=my_occurrences,
        open_fiscal=open_fiscal,
        completed_month=completed_month,
        active_users=active_users,
        active_sectors=active_sectors,
        recent=recent,
    )
