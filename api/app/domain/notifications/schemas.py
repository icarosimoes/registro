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
    email_sent_at: datetime | None = None
    created_at: datetime


class NotificationListResponse(BaseModel):
    items: list[NotificationOut]
    total: int
    unread: int
    page: int
    page_size: int


class PreferenceOut(BaseModel):
    module: str
    in_app: bool
    email: bool


class PreferenceUpdate(BaseModel):
    in_app: bool = True
    email: bool = True


class ModuleRecipientsOut(BaseModel):
    module: str
    user_ids: list[int]


class ModuleRecipientsUpdate(BaseModel):
    user_ids: list[int]
