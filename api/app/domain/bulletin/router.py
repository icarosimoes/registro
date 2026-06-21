from datetime import datetime
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from pydantic import BaseModel
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.bulletin.service import create_post, delete_post, list_posts, update_post
from app.models import User

router = APIRouter(prefix="/bulletin", tags=["bulletin"])


class BulletinOut(BaseModel):
    id: int
    title: str
    body: str | None = None
    pinned: bool
    expires_at: str | None = None
    author_user_id: int | None = None
    author_name: str | None = None
    created_at: str
    updated_at: str


class BulletinListResponse(BaseModel):
    items: list[BulletinOut]
    total: int
    page: int
    page_size: int


class BulletinCreate(BaseModel):
    title: str
    body: str | None = None
    pinned: bool = False
    expires_at: datetime | None = None
    notify_user_ids: list[int] | None = None


class BulletinUpdate(BaseModel):
    title: str | None = None
    body: str | None = None
    pinned: bool | None = None
    expires_at: datetime | None = None


def _to_out(post, author_name: str | None) -> BulletinOut:
    return BulletinOut(
        id=post.id,
        title=post.title,
        body=post.body,
        pinned=post.pinned,
        expires_at=str(post.expires_at) if post.expires_at else None,
        author_user_id=post.author_user_id,
        author_name=author_name,
        created_at=str(post.created_at),
        updated_at=str(post.updated_at),
    )


@router.get("", response_model=BulletinListResponse)
async def list_bulletin(
    user: Annotated[AuthenticatedUser, require_permission("bulletin.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: int = Query(1, ge=1),
    page_size: int = Query(20, ge=1, le=100),
    search: str | None = None,
    pinned: bool = False,
) -> BulletinListResponse:
    rows, total = await list_posts(session, user.company_id, page, page_size, search, pinned)
    return BulletinListResponse(
        items=[_to_out(post, name) for post, name in rows],
        total=total, page=page, page_size=page_size,
    )


@router.post("", response_model=BulletinOut, status_code=201)
async def create_bulletin(
    body: BulletinCreate,
    user: Annotated[AuthenticatedUser, require_permission("bulletin.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> BulletinOut:
    post, author_name = await create_post(
        session, user.company_id, user.id, user.name, user.email,
        title=body.title, body=body.body, pinned=body.pinned,
        expires_at=body.expires_at, notify_user_ids=body.notify_user_ids,
    )
    return _to_out(post, author_name)


@router.patch("/{post_id}", response_model=BulletinOut)
async def update_bulletin(
    post_id: int,
    body: BulletinUpdate,
    user: Annotated[AuthenticatedUser, require_permission("bulletin.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> BulletinOut:
    updates = body.model_dump(exclude_unset=True)
    if not updates:
        raise HTTPException(status_code=422, detail={"code": "no_fields"})
    post = await update_post(session, user.company_id, user.id, post_id, updates)
    if post is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    author_name = await session.scalar(
        select(User.name).where(User.id == post.author_user_id)
    ) if post.author_user_id else None
    return _to_out(post, author_name)


@router.delete("/{post_id}", status_code=204)
async def delete_bulletin(
    post_id: int,
    user: Annotated[AuthenticatedUser, require_permission("bulletin.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_post(session, user.company_id, user.id, post_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
