from datetime import date, datetime
from typing import Any

from pydantic import BaseModel

SHIFT_LABELS = {
    "morning": "Manhã",
    "afternoon": "Tarde",
    "night": "Noite",
    "diurno": "Diurno",
    "noturno": "Noturno",
}


class ShiftReportSummary(BaseModel):
    id: int
    title: str
    description: str | None = None
    shift_date: date | None = None
    shift_type: str | None = None
    shift_label: str | None = None
    status: str
    owner: str
    updated_at: datetime


class ShiftReportDetail(BaseModel):
    id: int
    title: str
    description: str | None = None
    shift_date: date | None = None
    shift_type: str | None = None
    shift_label: str | None = None
    status: str
    owner: str
    started_at: datetime | None = None
    ended_at: datetime | None = None
    supervisor: str | None = None
    occupation: str | None = None
    average_daily: str | None = None
    guests: int | None = None
    uhs: int | None = None
    maintenance_count: int | None = None
    cleaning: int | None = None
    walk_in: int | None = None
    input_quantity: int | None = None
    output_quantity: int | None = None
    return_of_customers: int | None = None
    observations: str | None = None
    notes_ab: str | None = None
    notes_reception: str | None = None
    notes_reservations: str | None = None
    notes_governance: str | None = None
    notes_maintenance: str | None = None
    notes_ti: str | None = None
    notes_security: str | None = None
    payload: dict[str, Any] | None = None
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
    shift_type: str | None = None
    started_at: datetime | None = None
    ended_at: datetime | None = None
    status: str = "Em andamento"
    supervisor: str | None = None
    occupation: str | None = None
    average_daily: str | None = None
    guests: int | None = None
    uhs: int | None = None
    maintenance_count: int | None = None
    cleaning: int | None = None
    walk_in: int | None = None
    input_quantity: int | None = None
    output_quantity: int | None = None
    return_of_customers: int | None = None
    observations: str | None = None
    notes_ab: str | None = None
    notes_reception: str | None = None
    notes_reservations: str | None = None
    notes_governance: str | None = None
    notes_maintenance: str | None = None
    notes_ti: str | None = None
    notes_security: str | None = None
    payload: dict[str, Any] | None = None
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
    supervisor: str | None = None
    occupation: str | None = None
    average_daily: str | None = None
    guests: int | None = None
    uhs: int | None = None
    maintenance_count: int | None = None
    cleaning: int | None = None
    walk_in: int | None = None
    input_quantity: int | None = None
    output_quantity: int | None = None
    return_of_customers: int | None = None
    observations: str | None = None
    notes_ab: str | None = None
    notes_reception: str | None = None
    notes_reservations: str | None = None
    notes_governance: str | None = None
    notes_maintenance: str | None = None
    notes_ti: str | None = None
    notes_security: str | None = None
    payload: dict[str, Any] | None = None
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None
