from datetime import date, datetime
from decimal import Decimal

from pydantic import BaseModel


class WorkDiaryActivityInput(BaseModel):
    description: str
    start_time: datetime | None = None
    end_time: datetime | None = None
    status: str = "Planejada"
    sort_order: int = 0


class WorkDiaryTeamInput(BaseModel):
    worker_name: str
    role: str | None = None
    hours_worked: Decimal | None = None
    sort_order: int = 0


class WorkDiaryEquipmentInput(BaseModel):
    equipment_name: str
    quantity: int = 1
    hours_used: Decimal | None = None
    sort_order: int = 0


class WorkDiaryObservationInput(BaseModel):
    content: str
    category: str | None = None
    sort_order: int = 0


class WorkDiaryActivitySummary(BaseModel):
    id: int
    description: str
    start_time: datetime | None
    end_time: datetime | None
    status: str
    sort_order: int


class WorkDiaryTeamSummary(BaseModel):
    id: int
    worker_name: str
    role: str | None
    hours_worked: Decimal | None
    sort_order: int


class WorkDiaryEquipmentSummary(BaseModel):
    id: int
    equipment_name: str
    quantity: int
    hours_used: Decimal | None
    sort_order: int


class WorkDiaryObservationSummary(BaseModel):
    id: int
    content: str
    category: str | None
    sort_order: int


class WorkDiarySummary(BaseModel):
    id: int
    diary_date: date
    title: str
    description: str | None
    weather: str | None
    status: str
    owner: str
    updated_at: datetime


class WorkDiaryDetail(WorkDiarySummary):
    owner_user_id: int | None
    activities: list[WorkDiaryActivitySummary]
    teams: list[WorkDiaryTeamSummary]
    equipment: list[WorkDiaryEquipmentSummary]
    observations: list[WorkDiaryObservationSummary]


class WorkDiaryListResponse(BaseModel):
    items: list[WorkDiarySummary]
    total: int
    page: int
    page_size: int


class WorkDiaryCreate(BaseModel):
    diary_date: date
    title: str
    description: str | None = None
    weather: str | None = None
    status: str = "Em andamento"
    owner_user_id: int | None = None
    activities: list[WorkDiaryActivityInput] | None = None
    teams: list[WorkDiaryTeamInput] | None = None
    equipment: list[WorkDiaryEquipmentInput] | None = None
    observations: list[WorkDiaryObservationInput] | None = None


class WorkDiaryUpdate(BaseModel):
    diary_date: date | None = None
    title: str | None = None
    description: str | None = None
    weather: str | None = None
    status: str | None = None
    owner_user_id: int | None = None
    activities: list[WorkDiaryActivityInput] | None = None
    teams: list[WorkDiaryTeamInput] | None = None
    equipment: list[WorkDiaryEquipmentInput] | None = None
    observations: list[WorkDiaryObservationInput] | None = None
