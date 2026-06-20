from datetime import datetime

from pydantic import BaseModel


class AttachmentOut(BaseModel):
    id: int
    entity_type: str
    entity_id: int
    filename: str
    content_type: str
    size_bytes: int
    uploaded_by_user_id: int
    created_at: datetime


class AttachmentListResponse(BaseModel):
    items: list[AttachmentOut]
    total: int
