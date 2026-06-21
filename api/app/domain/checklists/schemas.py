from datetime import date, datetime

from pydantic import BaseModel


class TemplateItemIn(BaseModel):
    label: str
    sort_order: int = 0


class TemplateCreate(BaseModel):
    name: str
    description: str | None = None
    recurrence: str
    category: str | None = None
    assigned_user_id: int | None = None
    next_due: date | None = None
    items: list[TemplateItemIn] = []


class TemplateUpdate(BaseModel):
    name: str | None = None
    description: str | None = None
    recurrence: str | None = None
    category: str | None = None
    assigned_user_id: int | None = None
    active: bool | None = None
    next_due: date | None = None
    items: list[TemplateItemIn] | None = None


class TemplateItemOut(BaseModel):
    id: int
    label: str
    sort_order: int


class TemplateOut(BaseModel):
    id: int
    name: str
    description: str | None
    recurrence: str
    category: str | None
    assigned_user_id: int | None
    assigned_user_name: str | None = None
    active: bool
    next_due: date | None
    last_generated_at: datetime | None
    item_count: int = 0
    items: list[TemplateItemOut] | None = None
    created_at: datetime | None
    updated_at: datetime | None


class TemplateList(BaseModel):
    items: list[TemplateOut]
    total: int
    page: int
    page_size: int


class ExecutionItemOut(BaseModel):
    id: int
    label: str
    sort_order: int
    checked: bool
    checked_at: datetime | None


class ExecutionOut(BaseModel):
    id: int
    template_id: int
    template_name: str | None = None
    due_date: date
    status: str
    completed_at: datetime | None
    completed_by_user_id: int | None
    completed_by_name: str | None = None
    notes: str | None
    items: list[ExecutionItemOut] | None = None
    progress: int = 0
    created_at: datetime | None
    updated_at: datetime | None


class ExecutionList(BaseModel):
    items: list[ExecutionOut]
    total: int
    page: int
    page_size: int


class ExecutionToggle(BaseModel):
    item_id: int
    checked: bool


class ExecutionComplete(BaseModel):
    notes: str | None = None


class GenerateResult(BaseModel):
    generated: int
    execution_ids: list[int]
