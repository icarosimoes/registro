from pydantic import BaseModel


class MaintenanceOut(BaseModel):
    id: int
    title: str
    description: str | None = None
    category: str | None = None
    status: str
    priority: str | None = None
    location_id: int | None = None
    owner_user_id: int | None = None
    owner_name: str | None = None
    created_at: str
    updated_at: str


class MaintenanceListResponse(BaseModel):
    items: list[MaintenanceOut]
    total: int
    page: int
    page_size: int


class MaintenanceCreate(BaseModel):
    title: str
    description: str | None = None
    category: str | None = None
    status: str = "Em andamento"
    priority: str | None = None
    location_id: int | None = None
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None


class MaintenanceUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    category: str | None = None
    status: str | None = None
    priority: str | None = None
    location_id: int | None = None
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None
