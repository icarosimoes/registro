from datetime import date, datetime

from pydantic import BaseModel


class ParticipantSummary(BaseModel):
    id: int
    name: str


class OccurrenceSummary(BaseModel):
    id: int
    legacy_id: int | None
    title: str
    description: str | None
    category: str
    location: str | None
    owner: str
    status: str
    deadline: date | None
    updated_at: datetime


class OccurrenceDetail(OccurrenceSummary):
    unit: str | None
    participants: list[ParticipantSummary]
    notify_user_ids: list[int] | None


class OccurrenceListResponse(BaseModel):
    items: list[OccurrenceSummary]
    total: int
    page: int
    page_size: int


class OccurrenceCursorResponse(BaseModel):
    items: list[OccurrenceSummary]
    next_cursor: str | None
    has_more: bool


class OccurrenceCreate(BaseModel):
    title: str
    description: str | None = None
    unit: str | None = None
    deadline: date | None = None
    status: int = 1
    sector_id: int | None = None
    location_id: int | None = None
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None
    participant_ids: list[int] | None = None


class OccurrenceUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    unit: str | None = None
    deadline: date | None = None
    status: int | None = None
    sector_id: int | None = None
    location_id: int | None = None
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None
    participant_ids: list[int] | None = None
