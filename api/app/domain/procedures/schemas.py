from datetime import datetime

from pydantic import BaseModel


class ProcedureSummary(BaseModel):
    id: int
    name: str
    link: str | None
    file: str | None
    updated_at: datetime


class ProcedureListResponse(BaseModel):
    items: list[ProcedureSummary]
    total: int
    page: int
    page_size: int


class ProcedureCreate(BaseModel):
    name: str
    link: str | None = None
    file: str | None = None


class ProcedureUpdate(BaseModel):
    name: str | None = None
    link: str | None = None
    file: str | None = None
