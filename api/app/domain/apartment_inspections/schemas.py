from datetime import datetime

from pydantic import BaseModel


class ApartmentInspectionItemInput(BaseModel):
    suite_item_id: int | None = None
    condition: str = "ok"
    notes: str | None = None
    sort_order: int = 0


class ApartmentInspectionItemSummary(BaseModel):
    id: int
    suite_item_id: int | None
    condition: str
    notes: str | None
    sort_order: int


class ApartmentInspectionCreate(BaseModel):
    unit: str | None = None
    apartment: str | None = None
    inspection_type: str
    inspection_suite_id: int | None = None
    inspector_user_id: int | None = None
    scheduled_at: datetime | None = None
    completed_at: datetime | None = None
    status: str = "Pendente"
    notes: str | None = None
    items: list[ApartmentInspectionItemInput] | None = None


class ApartmentInspectionUpdate(BaseModel):
    unit: str | None = None
    apartment: str | None = None
    inspection_type: str | None = None
    inspection_suite_id: int | None = None
    inspector_user_id: int | None = None
    scheduled_at: datetime | None = None
    completed_at: datetime | None = None
    status: str | None = None
    notes: str | None = None
    items: list[ApartmentInspectionItemInput] | None = None


class ApartmentInspectionSummary(BaseModel):
    id: int
    unit: str | None
    apartment: str | None
    inspection_type: str
    inspector_name: str | None
    scheduled_at: datetime | None
    completed_at: datetime | None
    status: str
    updated_at: datetime


class ApartmentInspectionDetail(ApartmentInspectionSummary):
    inspection_suite_id: int | None
    inspector_user_id: int | None
    notes: str | None
    items: list[ApartmentInspectionItemSummary]


class ApartmentInspectionListResponse(BaseModel):
    items: list[ApartmentInspectionSummary]
    total: int
    page: int
    page_size: int
