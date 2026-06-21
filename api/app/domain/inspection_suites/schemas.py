from datetime import datetime

from pydantic import BaseModel, Field


class InspectionSuiteItemInput(BaseModel):
    area: str | None = None
    item_name: str = Field(max_length=255)
    expected_condition: str | None = None
    sort_order: int = 0


class InspectionSuiteItemResponse(BaseModel):
    id: int
    area: str | None
    item_name: str
    expected_condition: str | None
    sort_order: int


class InspectionSuiteSummary(BaseModel):
    id: int
    name: str
    description: str | None
    type: str | None
    status: str
    owner_user_id: int | None
    owner_name: str | None
    item_count: int
    updated_at: datetime


class InspectionSuiteDetail(InspectionSuiteSummary):
    items: list[InspectionSuiteItemResponse]
    created_at: datetime


class InspectionSuiteListResponse(BaseModel):
    items: list[InspectionSuiteSummary]
    total: int
    page: int
    page_size: int


class InspectionSuiteCreate(BaseModel):
    name: str = Field(min_length=1, max_length=255)
    description: str | None = None
    type: str | None = None
    status: str = "Ativo"
    owner_user_id: int | None = None
    items: list[InspectionSuiteItemInput] | None = None


class InspectionSuiteUpdate(BaseModel):
    name: str | None = None
    description: str | None = None
    type: str | None = None
    status: str | None = None
    owner_user_id: int | None = None
    items: list[InspectionSuiteItemInput] | None = None
