from datetime import date, datetime

from pydantic import BaseModel


class HandoffCreate(BaseModel):
    title: str
    description: str | None = None
    priority: str = "normal"
    category: str | None = None
    target_shift: str | None = None
    target_date: date | None = None
    shift_report_id: int | None = None


class HandoffUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    priority: str | None = None
    category: str | None = None
    target_shift: str | None = None
    target_date: date | None = None


class HandoffOut(BaseModel):
    id: int
    title: str
    description: str | None
    priority: str
    category: str | None
    target_shift: str | None
    target_date: date
    status: str
    shift_report_id: int | None
    read_at: datetime | None
    read_by_user_id: int | None
    read_by_name: str | None = None
    resolved_at: datetime | None
    resolved_by_user_id: int | None
    resolved_by_name: str | None = None
    resolution_notes: str | None
    created_by_user_id: int | None
    created_by_name: str | None = None
    created_at: datetime | None
    updated_at: datetime | None


class HandoffList(BaseModel):
    items: list[HandoffOut]
    total: int
    page: int
    page_size: int


class HandoffResolve(BaseModel):
    resolution_notes: str | None = None
