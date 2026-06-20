from datetime import datetime
from typing import Any

from pydantic import BaseModel, ConfigDict, Field


class FiscalRequestCreate(BaseModel):
    model_config = ConfigDict(extra="allow")

    module: str
    request_type: str = Field(alias="requestType")
    apartment: str | None = None
    requester: str = Field(alias="solicitante")
    origin: str = Field(default="chess-hotel", alias="origem")


class FiscalRequestCreated(BaseModel):
    protocol: str


class FiscalRequestUserCreate(BaseModel):
    request_type: str
    title: str
    apartment: str | None = None
    requester: str
    description: str | None = None
    status: str = "Em andamento"
    payload: dict[str, Any] = Field(default_factory=dict)


class FiscalRequestUpdate(BaseModel):
    request_type: str | None = None
    title: str | None = None
    apartment: str | None = None
    requester: str | None = None
    description: str | None = None
    status: str | None = None
    payload: dict[str, Any] | None = None


class FiscalRequestSummary(BaseModel):
    id: int
    protocol: str
    request_type: str
    apartment: str | None
    requester: str
    status: str
    payload: dict[str, Any]
    created_at: datetime
    updated_at: datetime


class FiscalRequestListResponse(BaseModel):
    items: list[FiscalRequestSummary]
