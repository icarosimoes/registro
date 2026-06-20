from typing import Annotated

import bcrypt
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
from app.models import Role, User

router = APIRouter(prefix="/users", tags=["users"])
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


class UserSummary(BaseModel):
    id: int
    name: str
    email: str
    role_name: str | None
    active: bool
    updated_at: datetime


class UserListResponse(BaseModel):
    items: list[UserSummary]
    total: int
    page: int
    page_size: int


class UserCreate(BaseModel):
    name: str
    email: str
    password: str
    role_id: int | None = None
    active: bool = True


class UserUpdate(BaseModel):
    name: str | None = None
    email: str | None = None
    password: str | None = None
    role_id: int | None = None
    active: bool | None = None


@router.get("", response_model=UserListResponse)
async def list_users(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> UserListResponse:
    filters = [User.company_id == user.company_id, User.deleted_at.is_(None)]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(or_(User.name.ilike(pattern), User.email.ilike(pattern)))
    total = await session.scalar(select(func.count(User.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(User, Role.name)
            .outerjoin(Role, Role.id == User.role_id)
            .where(*filters)
            .order_by(User.name)
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return UserListResponse(
        items=[
            UserSummary(
                id=u.id, name=u.name, email=u.email,
                role_name=role_name, active=u.active,
                updated_at=u.updated_at,
            )
            for u, role_name in rows
        ],
        total=total, page=page, page_size=page_size,
    )


@router.post("", response_model=UserSummary, status_code=201)
async def create_user(
    body: UserCreate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> UserSummary:
    existing = await session.scalar(
        select(User.id).where(
            User.company_id == user.company_id,
            User.email == body.email.lower(),
            User.deleted_at.is_(None),
        )
    )
    if existing:
        raise HTTPException(status_code=409, detail={"code": "email_exists"})

    password_hash = bcrypt.hashpw(body.password.encode(), bcrypt.gensalt()).decode()
    record = User(
        company_id=user.company_id,
        name=body.name,
        email=body.email.lower(),
        password=password_hash,
        role_id=body.role_id,
        active=body.active,
    )
    session.add(record)
    await session.commit()
    await session.refresh(record)
    await record_event(session, company_id=user.company_id, user_id=user.id,
                       entity_type="user", entity_id=record.id, event_type="create")
    await session.commit()
    role_name = None
    if record.role_id:
        role_name = await session.scalar(select(Role.name).where(Role.id == record.role_id))
    return UserSummary(
        id=record.id, name=record.name, email=record.email,
        role_name=role_name, active=record.active, updated_at=record.updated_at,
    )


@router.patch("/{user_id}", response_model=UserSummary)
async def update_user(
    user_id: int,
    body: UserUpdate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> UserSummary:
    record = await session.scalar(
        select(User).where(
            User.id == user_id,
            User.company_id == user.company_id,
            User.deleted_at.is_(None),
        )
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})

    updates = body.model_dump(exclude_none=True)
    if "password" in updates:
        updates["password"] = bcrypt.hashpw(updates["password"].encode(), bcrypt.gensalt()).decode()

    before = {}
    for k in updates:
        if k == "password":
            before[k] = "***"
        else:
            before[k] = str(getattr(record, k))

    for field, value in updates.items():
        setattr(record, field, value)

    after = {k: "***" if k == "password" else str(v) for k, v in updates.items()}
    diff = compute_diff(before, after)
    if diff:
        await record_event(session, company_id=user.company_id, user_id=user.id,
                           entity_type="user", entity_id=record.id,
                           event_type="update", diff=diff)
    await session.commit()
    await session.refresh(record)
    role_name = None
    if record.role_id:
        role_name = await session.scalar(select(Role.name).where(Role.id == record.role_id))
    return UserSummary(
        id=record.id, name=record.name, email=record.email,
        role_name=role_name, active=record.active, updated_at=record.updated_at,
    )


@router.delete("/{user_id}", status_code=204)
async def delete_user(
    user_id: int,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    if user_id == user.id:
        raise HTTPException(status_code=400, detail={"code": "cannot_delete_self"})
    record = await session.scalar(
        select(User).where(
            User.id == user_id,
            User.company_id == user.company_id,
            User.deleted_at.is_(None),
        )
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    record.deleted_at = datetime.now()
    record.active = False
    await record_event(session, company_id=user.company_id, user_id=user.id,
                       entity_type="user", entity_id=record.id, event_type="delete")
    await session.commit()
