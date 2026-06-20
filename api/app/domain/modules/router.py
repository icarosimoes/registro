from typing import Annotated

import jwt
from fastapi import APIRouter, Depends, HTTPException, Query
from fastapi.security import OAuth2PasswordBearer
from pydantic import BaseModel
from datetime import datetime

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.security import decode_access_token
from app.domain.auth.repository import AuthenticatedUser, find_active_user_by_id
from app.models import ModuleRecord, User

router = APIRouter(prefix="/modules", tags=["modules"])
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/login")

VALID_MODULES = {"reunioes", "relatorios-turno", "inspecoes", "diarios-obra", "manutencao", "mural"}


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


class ModuleRecordSummary(BaseModel):
    id: int
    title: str
    description: str | None
    category: str | None
    owner: str
    status: str
    updated_at: datetime


class ModuleRecordListResponse(BaseModel):
    items: list[ModuleRecordSummary]
    total: int
    page: int
    page_size: int


class ModuleRecordCreate(BaseModel):
    title: str
    description: str | None = None
    category: str | None = None
    status: str = "Em andamento"
    owner_user_id: int | None = None


class ModuleRecordUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    category: str | None = None
    status: str | None = None
    owner_user_id: int | None = None


@router.get("/{module_slug}", response_model=ModuleRecordListResponse)
async def list_records(
    module_slug: str,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> ModuleRecordListResponse:
    if module_slug not in VALID_MODULES:
        raise HTTPException(status_code=404, detail={"code": "invalid_module"})
    filters = [
        ModuleRecord.company_id == user.company_id,
        ModuleRecord.module == module_slug,
        ModuleRecord.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(or_(ModuleRecord.title.ilike(pattern), ModuleRecord.description.ilike(pattern)))
    total = await session.scalar(select(func.count(ModuleRecord.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(ModuleRecord, User.name)
            .outerjoin(User, User.id == ModuleRecord.owner_user_id)
            .where(*filters)
            .order_by(ModuleRecord.updated_at.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return ModuleRecordListResponse(
        items=[
            ModuleRecordSummary(
                id=rec.id, title=rec.title, description=rec.description,
                category=rec.category, owner=owner_name or "Não atribuído",
                status=rec.status, updated_at=rec.updated_at,
            )
            for rec, owner_name in rows
        ],
        total=total, page=page, page_size=page_size,
    )


@router.post("/{module_slug}", response_model=ModuleRecordSummary, status_code=201)
async def create_record(
    module_slug: str,
    body: ModuleRecordCreate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ModuleRecordSummary:
    if module_slug not in VALID_MODULES:
        raise HTTPException(status_code=404, detail={"code": "invalid_module"})
    owner_id = body.owner_user_id or user.id
    record = ModuleRecord(
        company_id=user.company_id,
        module=module_slug,
        title=body.title,
        description=body.description,
        category=body.category,
        status=body.status,
        owner_user_id=owner_id,
    )
    session.add(record)
    await session.commit()
    await session.refresh(record)
    await record_event(session, company_id=user.company_id, user_id=user.id,
                       entity_type=module_slug, entity_id=record.id, event_type="create")
    await session.commit()
    owner_name = await session.scalar(select(User.name).where(User.id == owner_id))
    return ModuleRecordSummary(
        id=record.id, title=record.title, description=record.description,
        category=record.category, owner=owner_name or "Não atribuído",
        status=record.status, updated_at=record.updated_at,
    )


@router.patch("/{module_slug}/{record_id}", response_model=ModuleRecordSummary)
async def update_record(
    module_slug: str,
    record_id: int,
    body: ModuleRecordUpdate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ModuleRecordSummary:
    if module_slug not in VALID_MODULES:
        raise HTTPException(status_code=404, detail={"code": "invalid_module"})
    record = await session.scalar(
        select(ModuleRecord).where(
            ModuleRecord.id == record_id,
            ModuleRecord.company_id == user.company_id,
            ModuleRecord.module == module_slug,
            ModuleRecord.deleted_at.is_(None),
        )
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    updates = body.model_dump(exclude_none=True)
    before = {k: str(getattr(record, k)) for k in updates}
    for field, value in updates.items():
        setattr(record, field, value)
    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(session, company_id=user.company_id, user_id=user.id,
                           entity_type=module_slug, entity_id=record.id,
                           event_type="update", diff=diff)
    await session.commit()
    await session.refresh(record)
    owner_name = await session.scalar(select(User.name).where(User.id == record.owner_user_id)) if record.owner_user_id else None
    return ModuleRecordSummary(
        id=record.id, title=record.title, description=record.description,
        category=record.category, owner=owner_name or "Não atribuído",
        status=record.status, updated_at=record.updated_at,
    )


@router.delete("/{module_slug}/{record_id}", status_code=204)
async def delete_record(
    module_slug: str,
    record_id: int,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    if module_slug not in VALID_MODULES:
        raise HTTPException(status_code=404, detail={"code": "invalid_module"})
    record = await session.scalar(
        select(ModuleRecord).where(
            ModuleRecord.id == record_id,
            ModuleRecord.company_id == user.company_id,
            ModuleRecord.module == module_slug,
            ModuleRecord.deleted_at.is_(None),
        )
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    record.deleted_at = datetime.now()
    await record_event(session, company_id=user.company_id, user_id=user.id,
                       entity_type=module_slug, entity_id=record.id, event_type="delete")
    await session.commit()
