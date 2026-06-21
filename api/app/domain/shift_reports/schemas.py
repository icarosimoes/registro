from datetime import date, datetime

from pydantic import BaseModel

SHIFT_LABELS = {
    "morning": "Manhã",
    "afternoon": "Tarde",
    "night": "Noite",
}


class ShiftReportSummary(BaseModel):
    id: int
    title: str
    description: str | None
    shift_date: date | None
    shift_type: str | None
    shift_label: str | None  # human-readable
    status: str
    owner: str
    updated_at: datetime


class ShiftReportListResponse(BaseModel):
    items: list[ShiftReportSummary]
    total: int
    page: int
    page_size: int


class ShiftReportCreate(BaseModel):
    title: str
    description: str | None = None
    shift_date: date | None = None
    shift_type: str | None = None  # morning, afternoon, night
    started_at: datetime | None = None
    ended_at: datetime | None = None
    status: str = "Em andamento"
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None


class ShiftReportUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    shift_date: date | None = None
    shift_type: str | None = None
    started_at: datetime | None = None
    ended_at: datetime | None = None
    status: str | None = None
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None
