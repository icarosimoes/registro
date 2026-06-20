from typing import Annotated

import jwt
from fastapi import APIRouter, Depends, HTTPException, Query
from fastapi.security import OAuth2PasswordBearer
from pydantic import BaseModel
from datetime import datetime

from sqlalchemy import func, or_, select, union_all, literal_column, String, cast
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import record_event
from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.security import decode_access_token
from app.domain.auth.repository import AuthenticatedUser, find_active_user_by_id
from app.models import Function, Location, Sector

router = APIRouter(prefix="/registries", tags=["registries"])
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/login")

MODELS = {"Setor": Sector, "Local": Location, "Função": Function}


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


class RegistrySummary(BaseModel):
    id: int
    name: str
    category: str
    updated_at: datetime


class RegistryListResponse(BaseModel):
    items: list[RegistrySummary]
    total: int
    page: int
    page_size: int


class RegistryCreate(BaseModel):
    name: str
    category: str


class RegistryUpdate(BaseModel):
    name: str | None = None


@router.get("", response_model=RegistryListResponse)
async def list_registries(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> RegistryListResponse:
    queries = []
    for label, model in MODELS.items():
        q = select(
            model.id,
            model.name,
            cast(literal_column(f"'{label}'"), String).label("category"),
            model.updated_at,
        ).where(model.company_id == user.company_id, model.deleted_at.is_(None))
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
    return RegistryListResponse(
        items=[
            RegistrySummary(id=row.id, name=row.name, category=row.category, updated_at=row.updated_at)
            for row in rows
        ],
        total=total, page=page, page_size=page_size,
    )


@router.post("", response_model=RegistrySummary, status_code=201)
async def create_registry(
    body: RegistryCreate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> RegistrySummary:
    model = MODELS.get(body.category)
    if model is None:
        raise HTTPException(status_code=400, detail={"code": "invalid_category", "valid": list(MODELS.keys())})
    record = model(company_id=user.company_id, name=body.name)
    session.add(record)
    await session.commit()
    await session.refresh(record)
    await record_event(session, company_id=user.company_id, user_id=user.id,
                       entity_type="registry", entity_id=record.id, event_type="create")
    await session.commit()
    return RegistrySummary(id=record.id, name=record.name, category=body.category, updated_at=record.updated_at)


@router.patch("/{registry_id}", response_model=RegistrySummary)
async def update_registry(
    registry_id: int,
    body: RegistryUpdate,
    category: str = Query(..., description="Setor, Local ou Função"),
    user: Annotated[AuthenticatedUser, Depends(current_user)] = None,
    session: Annotated[AsyncSession, Depends(require_session)] = None,
) -> RegistrySummary:
    model = MODELS.get(category)
    if model is None:
        raise HTTPException(status_code=400, detail={"code": "invalid_category"})
    record = await session.scalar(
        select(model).where(model.id == registry_id, model.company_id == user.company_id, model.deleted_at.is_(None))
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    if body.name is not None:
        record.name = body.name
    await session.commit()
    await session.refresh(record)
    return RegistrySummary(id=record.id, name=record.name, category=category, updated_at=record.updated_at)


@router.delete("/{registry_id}", status_code=204)
async def delete_registry(
    registry_id: int,
    category: str = Query(..., description="Setor, Local ou Função"),
    user: Annotated[AuthenticatedUser, Depends(current_user)] = None,
    session: Annotated[AsyncSession, Depends(require_session)] = None,
) -> None:
    model = MODELS.get(category)
    if model is None:
        raise HTTPException(status_code=400, detail={"code": "invalid_category"})
    record = await session.scalar(
        select(model).where(model.id == registry_id, model.company_id == user.company_id, model.deleted_at.is_(None))
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    record.deleted_at = datetime.now()
    await record_event(session, company_id=user.company_id, user_id=user.id,
                       entity_type="registry", entity_id=record.id, event_type="delete")
    await session.commit()
