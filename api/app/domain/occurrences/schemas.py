from datetime import date, datetime

from pydantic import BaseModel


class OccurrenceSummary(BaseModel):
    id: int
    legacy_id: int
    title: str
    description: str | None
    category: str
    location: str | None
    owner: str
    status: str
    deadline: date | None
    updated_at: datetime


class OccurrenceListResponse(BaseModel):
    items: list[OccurrenceSummary]
    total: int
    page: int
    page_size: int
