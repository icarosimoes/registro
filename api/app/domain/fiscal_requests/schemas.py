from datetime import datetime
from typing import Any, Self

from pydantic import BaseModel, ConfigDict, Field, field_validator, model_validator

from app.core.validators import validate_cpf_cnpj, validate_email_basic


def _validate_payload(payload: dict[str, Any]) -> dict[str, Any]:
    if (doc := payload.get("taxpayerDoc")) and isinstance(doc, str) and doc.strip():
        payload["taxpayerDoc"] = validate_cpf_cnpj(doc)
    if (email := payload.get("taxpayerEmail")) and isinstance(email, str) and email.strip():
        payload["taxpayerEmail"] = validate_email_basic(email)
    return payload


class FiscalRequestCreate(BaseModel):
    model_config = ConfigDict(extra="allow")

    module: str
    request_type: str = Field(alias="requestType")
    apartment: str | None = None
    requester: str = Field(alias="solicitante")
    requester_email: str = Field(alias="solicitanteEmail")
    chess_user_id: str = Field(alias="chessUserId")
    hotel: str
    reservation_number: str | None = Field(default=None, alias="reservationNumber")
    origin: str = Field(default="chess-hotel", alias="origem")

    @field_validator("requester_email")
    @classmethod
    def normalize_requester_email(cls, value: str) -> str:
        return validate_email_basic(value)


class FiscalRequestCreated(BaseModel):
    protocol: str
    status: str
    responsible: str | None
    sla_deadline: datetime
    url: str


class ChessUserResolve(BaseModel):
    email: str

    @field_validator("email")
    @classmethod
    def normalize_email(cls, value: str) -> str:
        return validate_email_basic(value)


class ChessUserResolved(BaseModel):
    exists: bool
    id: int | None = None
    name: str | None = None
    email: str


class FiscalHistoryEntry(BaseModel):
    event: str
    user: str
    at: datetime
    changes: dict[str, Any] | None = None


class FiscalRequestTracking(BaseModel):
    protocol: str
    request_type: str
    status: str
    responsible: str | None
    sla_deadline: datetime | None
    completed: bool
    updated_at: datetime
    url: str
    history: list[FiscalHistoryEntry] = Field(default_factory=list)


class FiscalRequestTrackingList(BaseModel):
    user: ChessUserResolved
    items: list[FiscalRequestTracking]


class FiscalRequestUserCreate(BaseModel):
    request_type: str
    title: str
    apartment: str | None = None
    requester: str
    description: str | None = None
    status: str = "Em andamento"
    payload: dict[str, Any] = Field(default_factory=dict)

    @model_validator(mode="after")
    def normalize_payload(self) -> Self:
        if self.payload:
            self.payload = _validate_payload(self.payload)
        return self


class FiscalRequestUpdate(BaseModel):
    request_type: str | None = None
    title: str | None = None
    apartment: str | None = None
    requester: str | None = None
    description: str | None = None
    status: str | None = None
    payload: dict[str, Any] | None = None

    @model_validator(mode="after")
    def normalize_payload(self) -> Self:
        if self.payload:
            self.payload = _validate_payload(self.payload)
        return self


class FiscalRequestSummary(BaseModel):
    id: int
    protocol: str
    request_type: str
    title: str | None = None
    apartment: str | None
    requester: str
    description: str | None = None
    reservation_number: str | None = None
    sla_deadline: datetime | None = None
    sla_status: str | None = None
    status: str
    payload: dict[str, Any]
    created_at: datetime
    updated_at: datetime


class FiscalRequestListResponse(BaseModel):
    items: list[FiscalRequestSummary]
    total: int
    page: int
    page_size: int
