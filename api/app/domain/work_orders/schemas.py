from pydantic import BaseModel


class WorkOrderOut(BaseModel):
    id: int
    title: str
    description: str | None = None
    status: str
    priority: str | None = None
    category: str | None = None
    location_id: int | None = None
    occurrence_id: int | None = None
    maintenance_id: int | None = None
    assigned_user_id: int | None = None
    assigned_user_name: str | None = None
    created_by_user_id: int | None = None
    created_by_user_name: str | None = None
    validated_by_user_id: int | None = None
    sla_hours: int | None = None
    sla_deadline: str | None = None
    started_at: str | None = None
    completed_at: str | None = None
    validated_at: str | None = None
    created_at: str
    updated_at: str


class WorkOrderListResponse(BaseModel):
    items: list[WorkOrderOut]
    total: int
    page: int
    page_size: int


class WorkOrderCreate(BaseModel):
    title: str
    description: str | None = None
    priority: str | None = None
    category: str | None = None
    location_id: int | None = None
    occurrence_id: int | None = None
    maintenance_id: int | None = None
    assigned_user_id: int | None = None
    notify_user_ids: list[int] | None = None
    sla_hours: int | None = None


class WorkOrderUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    priority: str | None = None
    category: str | None = None
    location_id: int | None = None
    assigned_user_id: int | None = None
    notify_user_ids: list[int] | None = None
    sla_hours: int | None = None


class WorkOrderTransition(BaseModel):
    notes: str | None = None
