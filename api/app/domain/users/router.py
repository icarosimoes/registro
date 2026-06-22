from datetime import datetime
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query, Request, UploadFile
from pydantic import BaseModel, field_validator
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.auth import current_user
from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.core.rate_limit import limiter
from app.domain.auth.repository import AuthenticatedUser
from app.domain.auth.schemas import validate_password_strength
from app.domain.users.service import (
    create_user,
    delete_user,
    get_role_name,
    get_sector_name,
    invite_user,
    list_users,
    search_users,
    update_profile,
    update_user,
    upload_avatar,
)

router = APIRouter(prefix="/users", tags=["users"])


class UserSummary(BaseModel):
    id: int
    name: str
    email: str
    phone: str | None = None
    role_id: int | None = None
    role_name: str | None
    job_title: str | None = None
    sector_name: str | None = None
    avatar_url: str | None = None
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
    phone: str | None = None
    password: str
    role_id: int | None = None
    job_title: str | None = None
    sector_id: int | None = None
    active: bool = True

    @field_validator("password")
    @classmethod
    def check_password(cls, v: str) -> str:
        return validate_password_strength(v)


class UserUpdate(BaseModel):
    name: str | None = None
    email: str | None = None
    phone: str | None = None
    password: str | None = None
    role_id: int | None = None
    job_title: str | None = None
    sector_id: int | None = None
    avatar_url: str | None = None
    active: bool | None = None

    @field_validator("password")
    @classmethod
    def check_password(cls, v: str | None) -> str | None:
        if v is not None:
            return validate_password_strength(v)
        return v


class ProfileUpdate(BaseModel):
    name: str | None = None
    phone: str | None = None
    password: str | None = None

    @field_validator("password")
    @classmethod
    def check_password(cls, v: str | None) -> str | None:
        if v is not None:
            return validate_password_strength(v)
        return v


class UserInvite(BaseModel):
    name: str
    email: str
    phone: str | None = None
    role_id: int | None = None
    job_title: str | None = None
    sector_id: int | None = None
    active: bool = True


class UserOption(BaseModel):
    id: int
    name: str
    email: str


async def _to_summary(session: AsyncSession, record) -> UserSummary:
    role_name = await get_role_name(session, record.role_id)
    sector_name = await get_sector_name(session, record.sector_id)
    return UserSummary(
        id=record.id,
        name=record.name,
        email=record.email,
        phone=record.phone,
        role_id=record.role_id,
        role_name=role_name,
        job_title=record.job_title,
        sector_name=sector_name,
        avatar_url=record.avatar_url,
        active=record.active,
        updated_at=record.updated_at,
    )


@router.patch("/me", response_model=UserSummary)
@limiter.limit("10/minute")
async def update_profile_endpoint(
    request: Request,
    body: ProfileUpdate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> UserSummary:
    updates = body.model_dump(exclude_none=True)
    if not updates:
        raise HTTPException(status_code=422, detail={"code": "no_fields"})
    record = await update_profile(session, user.id, user.company_id, updates)
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return await _to_summary(session, record)


@router.get("", response_model=UserListResponse)
async def list_users_endpoint(
    user: Annotated[AuthenticatedUser, require_permission("user.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> UserListResponse:
    rows, total = await list_users(session, user.company_id, page, page_size, search)
    return UserListResponse(
        items=[
            UserSummary(
                id=u.id,
                name=u.name,
                email=u.email,
                phone=u.phone,
                role_id=u.role_id,
                role_name=role_name,
                job_title=u.job_title,
                sector_name=sector_name,
                avatar_url=u.avatar_url,
                active=u.active,
                updated_at=u.updated_at,
            )
            for u, role_name, sector_name in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/search", response_model=list[UserOption])
async def search_users_endpoint(
    user: Annotated[AuthenticatedUser, require_permission("user.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    q: str = "",
) -> list[UserOption]:
    users = await search_users(session, user.company_id, q)
    return [UserOption(id=u.id, name=u.name, email=u.email) for u in users]


@router.post("", response_model=UserSummary, status_code=201)
async def create_user_endpoint(
    body: UserCreate,
    user: Annotated[AuthenticatedUser, require_permission("user.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> UserSummary:
    record = await create_user(
        session,
        user.company_id,
        user.id,
        name=body.name,
        email=body.email,
        phone=body.phone,
        password=body.password,
        role_id=body.role_id,
        active=body.active,
        job_title=body.job_title,
        sector_id=body.sector_id,
    )
    if record is None:
        raise HTTPException(
            status_code=409,
            detail={"code": "conflict", "message": "Não foi possível criar o usuário"},
        )
    return await _to_summary(session, record)


@router.patch("/{user_id}", response_model=UserSummary)
async def update_user_endpoint(
    user_id: int,
    body: UserUpdate,
    user: Annotated[AuthenticatedUser, require_permission("user.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> UserSummary:
    updates = body.model_dump(exclude_none=True)
    record = await update_user(session, user.company_id, user.id, user_id, updates)
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return await _to_summary(session, record)


@router.post("/invite", response_model=UserSummary, status_code=201)
@limiter.limit("10/minute")
async def invite_user_endpoint(
    request: Request,
    body: UserInvite,
    user: Annotated[AuthenticatedUser, require_permission("user.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> UserSummary:
    record = await invite_user(
        session,
        user.company_id,
        user.id,
        name=body.name,
        email=body.email,
        phone=body.phone,
        role_id=body.role_id,
        job_title=body.job_title,
        sector_id=body.sector_id,
        active=body.active,
    )
    if record is None:
        raise HTTPException(
            status_code=409,
            detail={"code": "conflict", "message": "E-mail já cadastrado"},
        )
    return await _to_summary(session, record)


@router.post("/{user_id}/avatar", response_model=UserSummary)
async def upload_avatar_endpoint(
    user_id: int,
    file: UploadFile,
    user: Annotated[AuthenticatedUser, require_permission("user.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> UserSummary:
    data = await file.read()
    max_size = 2 * 1024 * 1024
    if len(data) > max_size:
        raise HTTPException(status_code=400, detail={"code": "file_too_large"})
    try:
        record = await upload_avatar(
            session,
            user.company_id,
            user.id,
            user_id,
            data=data,
            filename=file.filename or "avatar.jpg",
            content_type=file.content_type or "image/jpeg",
        )
    except ValueError as e:
        raise HTTPException(
            status_code=400, detail={"code": "invalid_file", "message": str(e)}
        ) from e
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return await _to_summary(session, record)


@router.delete("/{user_id}", status_code=204)
async def delete_user_endpoint(
    user_id: int,
    user: Annotated[AuthenticatedUser, require_permission("user.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    if user_id == user.id:
        raise HTTPException(status_code=400, detail={"code": "cannot_delete_self"})
    deleted = await delete_user(session, user.company_id, user.id, user_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
