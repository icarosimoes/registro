from datetime import date, datetime

from pydantic import BaseModel


class PreventivePlanCreate(BaseModel):
    name: str
    description: str | None = None
    recurrence: str
    category: str | None = None
    priority: str = "media"
    sla_hours: int | None = None
    location_id: int | None = None
    assigned_user_id: int | None = None
    next_due: date | None = None


class PreventivePlanUpdate(BaseModel):
    name: str | None = None
    description: str | None = None
    recurrence: str | None = None
    category: str | None = None
    priority: str | None = None
    sla_hours: int | None = None
    location_id: int | None = None
    assigned_user_id: int | None = None
    active: bool | None = None
    next_due: date | None = None


class PreventivePlanOut(BaseModel):
    id: int
    name: str
    description: str | None
    recurrence: str
    category: str | None
    priority: str
    sla_hours: int | None
    location_id: int | None
    location_name: str | None = None
    assigned_user_id: int | None
    assigned_user_name: str | None = None
    active: bool
    next_due: date | None
    last_generated_at: datetime | None
    created_at: datetime | None
    updated_at: datetime | None


class PreventivePlanList(BaseModel):
    items: list[PreventivePlanOut]
    total: int
    page: int
    page_size: int


class GenerateResult(BaseModel):
    generated: int
    work_order_ids: list[int]
