from typing import Annotated

import jwt
from fastapi import APIRouter, Depends, HTTPException, Query
from fastapi.security import OAuth2PasswordBearer
from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.security import decode_access_token
from app.domain.auth.repository import AuthenticatedUser, find_active_user_by_id
from app.domain.occurrences.schemas import OccurrenceListResponse, OccurrenceSummary
from app.models import Location, Occurrence, Sector, User

router = APIRouter(prefix="/occurrences", tags=["occurrences"])
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


def status_label(value: int) -> str:
    return {1: "Em andamento", 2: "Concluído", 3: "Aguardando"}.get(value, f"Status {value}")


@router.get("", response_model=OccurrenceListResponse)
async def list_occurrences(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> OccurrenceListResponse:
    filters = [Occurrence.company_id == user.company_id, Occurrence.deleted_at.is_(None)]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(or_(Occurrence.title.ilike(pattern), Occurrence.description.ilike(pattern)))
    total = await session.scalar(select(func.count(Occurrence.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(Occurrence, Sector.name, Location.name, User.name)
            .outerjoin(Sector, Sector.id == Occurrence.sector_id)
            .outerjoin(Location, Location.id == Occurrence.location_id)
            .outerjoin(User, User.id == Occurrence.owner_user_id)
            .where(*filters)
            .order_by(Occurrence.updated_at.desc(), Occurrence.id.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return OccurrenceListResponse(
        items=[
            OccurrenceSummary(
                id=occurrence.id,
                legacy_id=occurrence.legacy_id,
                title=occurrence.title,
                description=occurrence.description,
                category=sector_name or "Sem setor",
                location=location_name,
                owner=owner_name or "Não atribuído",
                status=status_label(occurrence.status),
                deadline=occurrence.deadline,
                updated_at=occurrence.updated_at,
            )
            for occurrence, sector_name, location_name, owner_name in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )
