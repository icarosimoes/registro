from datetime import datetime

from pydantic import BaseModel


class NotificationOut(BaseModel):
    id: int
    title: str
    body: str | None
    category: str
    entity_type: str | None
    entity_id: int | None
    read_at: datetime | None
    created_at: datetime


class NotificationListResponse(BaseModel):
    items: list[NotificationOut]
    total: int
    unread: int
    page: int
    page_size: int
