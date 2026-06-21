from datetime import datetime

from pydantic import BaseModel, Field


class CheckSuiteItemInput(BaseModel):
    label: str = Field(max_length=255)
    sort_order: int = 0
    checked: bool = False


class CheckSuiteItemResponse(BaseModel):
    id: int
    label: str
    sort_order: int
    checked: bool


class CheckSuiteSummary(BaseModel):
    id: int
    name: str
    description: str | None
    status: str
    owner_user_id: int | None
    owner_name: str | None
    item_count: int
    updated_at: datetime


class CheckSuiteDetail(CheckSuiteSummary):
    items: list[CheckSuiteItemResponse]
    created_at: datetime


class CheckSuiteListResponse(BaseModel):
    items: list[CheckSuiteSummary]
    total: int
    page: int
    page_size: int


class CheckSuiteCreate(BaseModel):
    name: str = Field(min_length=1, max_length=255)
    description: str | None = None
    status: str = "Ativo"
    owner_user_id: int | None = None
    items: list[CheckSuiteItemInput] | None = None


class CheckSuiteUpdate(BaseModel):
    name: str | None = None
    description: str | None = None
    status: str | None = None
    owner_user_id: int | None = None
    items: list[CheckSuiteItemInput] | None = None
