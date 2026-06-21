from datetime import datetime

from pydantic import BaseModel


class ParticipantInput(BaseModel):
    user_id: int
    role: str = "attendee"  # organizer, attendee, optional


class ParticipantSummary(BaseModel):
    user_id: int
    name: str
    role: str


class SubjectSummary(BaseModel):
    id: int
    title: str
    description: str | None
    sort_order: int
    resolved: bool


class SubjectCreate(BaseModel):
    title: str
    description: str | None = None
    sort_order: int = 0


class SubjectUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    sort_order: int | None = None
    resolved: bool | None = None


class MeetingSummary(BaseModel):
    id: int
    title: str
    description: str | None
    scheduled_at: datetime | None
    location: str | None
    status: str
    owner: str
    participant_count: int
    subject_count: int
    updated_at: datetime


class MeetingDetail(MeetingSummary):
    participants: list[ParticipantSummary]
    subjects: list[SubjectSummary]
    notify_user_ids: list[int] | None


class MeetingListResponse(BaseModel):
    items: list[MeetingSummary]
    total: int
    page: int
    page_size: int


class MeetingCreate(BaseModel):
    title: str
    description: str | None = None
    scheduled_at: datetime | None = None
    location: str | None = None
    status: str = "Agendada"
    owner_user_id: int | None = None
    participants: list[ParticipantInput] | None = None
    subjects: list[SubjectCreate] | None = None
    notify_user_ids: list[int] | None = None


class MeetingUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    scheduled_at: datetime | None = None
    location: str | None = None
    status: str | None = None
    owner_user_id: int | None = None
    participants: list[ParticipantInput] | None = None
    notify_user_ids: list[int] | None = None
