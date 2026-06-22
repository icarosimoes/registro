from datetime import datetime

from pydantic import BaseModel


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
