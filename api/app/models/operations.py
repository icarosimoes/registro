from datetime import date, datetime
from decimal import Decimal

import sqlalchemy as sa
from sqlalchemy import (
    JSON,
    Boolean,
    Date,
    DateTime,
    ForeignKey,
    Index,
    Integer,
    Numeric,
    String,
    Text,
    UniqueConstraint,
    func,
)
from sqlalchemy.orm import Mapped, mapped_column

from app.models.base import Base, TenantMixin, TimestampMixin


class LegacyEntityMixin:
    legacy_id: Mapped[int | None] = mapped_column(Integer, nullable=True)


class Sector(Base, TenantMixin, LegacyEntityMixin, TimestampMixin):
    __tablename__ = "sectors"
    __table_args__ = (UniqueConstraint("company_id", "legacy_id", name="uq_sectors_legacy"),)

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class Location(Base, TenantMixin, LegacyEntityMixin, TimestampMixin):
    __tablename__ = "locations"
    __table_args__ = (UniqueConstraint("company_id", "legacy_id", name="uq_locations_legacy"),)

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class Function(Base, TenantMixin, LegacyEntityMixin, TimestampMixin):
    __tablename__ = "functions"
    __table_args__ = (UniqueConstraint("company_id", "legacy_id", name="uq_functions_legacy"),)

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class Procedure(Base, TenantMixin, LegacyEntityMixin, TimestampMixin):
    __tablename__ = "procedures"
    __table_args__ = (UniqueConstraint("company_id", "legacy_id", name="uq_procedures_legacy"),)

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    link: Mapped[str | None] = mapped_column(String(255))
    file: Mapped[str | None] = mapped_column(String(255))
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class Occurrence(Base, TenantMixin, LegacyEntityMixin, TimestampMixin):
    __tablename__ = "occurrences"
    __table_args__ = (UniqueConstraint("company_id", "legacy_id", name="uq_occurrences_legacy"),)

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    title: Mapped[str] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(Text)
    comments: Mapped[str | None] = mapped_column(Text)
    unit: Mapped[str | None] = mapped_column(String(255))
    deadline: Mapped[date | None] = mapped_column(Date)
    status: Mapped[int] = mapped_column(Integer, default=1, index=True)
    legacy_type_id: Mapped[int | None] = mapped_column(Integer)
    legacy_receiver_user_id: Mapped[int | None] = mapped_column(Integer)
    location_id: Mapped[int | None] = mapped_column(
        ForeignKey("locations.id", ondelete="SET NULL"),
    )
    sector_id: Mapped[int | None] = mapped_column(
        ForeignKey("sectors.id", ondelete="SET NULL"),
    )
    owner_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    created_by_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    updated_by_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    notify_user_ids: Mapped[list | None] = mapped_column(JSON)
    file: Mapped[str | None] = mapped_column(Text)
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class FiscalRequest(Base, TenantMixin, TimestampMixin):
    __tablename__ = "fiscal_requests"
    __table_args__ = (
        Index("ix_fiscal_requests_company_status", "company_id", "status"),
    )

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    protocol: Mapped[str] = mapped_column(String(40), unique=True)
    request_type: Mapped[str] = mapped_column(String(120), index=True)
    title: Mapped[str | None] = mapped_column(String(255))
    apartment: Mapped[str | None] = mapped_column(String(40))
    requester: Mapped[str] = mapped_column(String(160))
    requester_email: Mapped[str | None] = mapped_column(String(255), index=True)
    requester_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"), index=True,
    )
    responsible_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"), index=True,
    )
    chess_user_id: Mapped[str | None] = mapped_column(String(80))
    reservation_number: Mapped[str | None] = mapped_column(String(80))
    sla_deadline: Mapped[datetime | None] = mapped_column(DateTime)
    sla_paused_at: Mapped[datetime | None] = mapped_column(DateTime)
    sla_paused_seconds: Mapped[int] = mapped_column(Integer, default=0)
    description: Mapped[str | None] = mapped_column(Text)
    origin: Mapped[str] = mapped_column(String(80), default="chess-hotel")
    status: Mapped[str] = mapped_column(String(40), default="Em andamento", index=True)
    payload: Mapped[dict] = mapped_column(JSON)


class AuditEvent(Base, TenantMixin):
    __tablename__ = "audit_events"
    __table_args__ = (
        Index("ix_audit_events_company_entity", "company_id", "entity_type", "entity_id"),
    )

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    user_id: Mapped[int] = mapped_column(ForeignKey("users.id", ondelete="CASCADE"))
    entity_type: Mapped[str] = mapped_column(String(80))
    entity_id: Mapped[int] = mapped_column(Integer)
    event_type: Mapped[str] = mapped_column(String(40))
    diff: Mapped[dict | None] = mapped_column(JSON)
    created_at: Mapped[datetime] = mapped_column(DateTime, server_default=func.now())


class ModuleRecord(Base, TenantMixin, TimestampMixin):
    __tablename__ = "module_records"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    legacy_id: Mapped[int | None] = mapped_column(Integer, nullable=True)
    module: Mapped[str] = mapped_column(String(80), index=True)
    title: Mapped[str] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(Text)
    category: Mapped[str | None] = mapped_column(String(120))
    status: Mapped[str] = mapped_column(String(60), default="Em andamento")
    owner_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    created_by_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    notify_user_ids: Mapped[list | None] = mapped_column(JSON)
    payload: Mapped[dict | None] = mapped_column(JSON)
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class Notification(Base, TenantMixin):
    __tablename__ = "notifications"
    __table_args__ = (
        Index("ix_notifications_user_unread", "user_id", "read_at"),
    )

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    user_id: Mapped[int] = mapped_column(ForeignKey("users.id", ondelete="CASCADE"))
    title: Mapped[str] = mapped_column(String(255))
    body: Mapped[str | None] = mapped_column(Text)
    category: Mapped[str] = mapped_column(String(60), default="info", index=True)
    entity_type: Mapped[str | None] = mapped_column(String(80))
    entity_id: Mapped[int | None] = mapped_column(Integer)
    read_at: Mapped[datetime | None] = mapped_column(DateTime)
    email_sent_at: Mapped[datetime | None] = mapped_column(DateTime)
    created_at: Mapped[datetime] = mapped_column(DateTime, server_default=func.now())


class NotificationPreference(Base, TenantMixin):
    __tablename__ = "notification_preferences"
    __table_args__ = (
        UniqueConstraint("user_id", "company_id", "module", name="uq_notif_pref_user_module"),
        Index("ix_notif_pref_company_user", "company_id", "user_id"),
    )

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    user_id: Mapped[int] = mapped_column(ForeignKey("users.id", ondelete="CASCADE"))
    module: Mapped[str] = mapped_column(String(80))
    in_app: Mapped[bool] = mapped_column(Boolean, server_default=sa.true())
    email: Mapped[bool] = mapped_column(Boolean, server_default=sa.true())


class Attachment(Base, TenantMixin):
    __tablename__ = "attachments"
    __table_args__ = (
        Index("ix_attachments_company_entity", "company_id", "entity_type", "entity_id"),
    )

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    entity_type: Mapped[str] = mapped_column(String(80))
    entity_id: Mapped[int] = mapped_column(Integer)
    filename: Mapped[str] = mapped_column(String(255))
    content_type: Mapped[str] = mapped_column(String(120))
    size_bytes: Mapped[int] = mapped_column(Integer)
    storage_key: Mapped[str] = mapped_column(String(500), unique=True)
    uploaded_by_user_id: Mapped[int] = mapped_column(
        ForeignKey("users.id"),
    )
    created_at: Mapped[datetime] = mapped_column(
        DateTime, server_default=func.now(),
    )


class OccurrenceParticipant(Base):
    __tablename__ = "occurrence_participants"

    occurrence_id: Mapped[int] = mapped_column(
        ForeignKey("occurrences.id", ondelete="CASCADE"), primary_key=True,
    )
    user_id: Mapped[int] = mapped_column(
        ForeignKey("users.id", ondelete="CASCADE"), primary_key=True,
    )
    created_at: Mapped[datetime] = mapped_column(DateTime, server_default=func.now())


class Meeting(Base, TenantMixin, TimestampMixin):
    __tablename__ = "meetings"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    title: Mapped[str] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(Text)
    scheduled_at: Mapped[datetime | None] = mapped_column(DateTime)
    location: Mapped[str | None] = mapped_column(String(255))
    status: Mapped[str] = mapped_column(String(60), default="Agendada")
    owner_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    created_by_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    notify_user_ids: Mapped[list | None] = mapped_column(JSON)
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class MeetingParticipant(Base):
    __tablename__ = "meeting_participants"
    __table_args__ = (
        UniqueConstraint("meeting_id", "user_id", name="uq_meeting_participant"),
    )

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    meeting_id: Mapped[int] = mapped_column(
        ForeignKey("meetings.id", ondelete="CASCADE"),
    )
    user_id: Mapped[int] = mapped_column(
        ForeignKey("users.id", ondelete="CASCADE"),
    )
    role: Mapped[str] = mapped_column(String(20), default="attendee")
    created_at: Mapped[datetime] = mapped_column(DateTime, server_default=func.now())


class MeetingSubject(Base):
    __tablename__ = "meeting_subjects"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    meeting_id: Mapped[int] = mapped_column(
        ForeignKey("meetings.id", ondelete="CASCADE"),
    )
    title: Mapped[str] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(Text)
    sort_order: Mapped[int] = mapped_column(Integer, default=0)
    resolved: Mapped[bool] = mapped_column(default=False)
    created_at: Mapped[datetime] = mapped_column(DateTime, server_default=func.now())


class ShiftReport(Base, TenantMixin, TimestampMixin):
    __tablename__ = "shift_reports"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    title: Mapped[str] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(Text)
    shift_date: Mapped[date | None] = mapped_column(Date)
    shift_type: Mapped[str | None] = mapped_column(String(20))
    status: Mapped[str] = mapped_column(String(60), default="Em andamento")
    started_at: Mapped[datetime | None] = mapped_column(DateTime)
    ended_at: Mapped[datetime | None] = mapped_column(DateTime)
    owner_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    created_by_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    notify_user_ids: Mapped[list | None] = mapped_column(JSON)
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


# ---------------------------------------------------------------------------
# Inspections & Construction (P4)
# ---------------------------------------------------------------------------


class CheckSuite(Base, TenantMixin, TimestampMixin):
    __tablename__ = "check_suites"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(Text)
    status: Mapped[str] = mapped_column(String(60), default="Ativo")
    owner_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class CheckSuiteItem(Base):
    __tablename__ = "check_suite_items"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    suite_id: Mapped[int] = mapped_column(
        ForeignKey("check_suites.id", ondelete="CASCADE"),
    )
    label: Mapped[str] = mapped_column(String(255))
    sort_order: Mapped[int] = mapped_column(Integer, default=0)
    checked: Mapped[bool] = mapped_column(Boolean, default=False)


class InspectionSuite(Base, TenantMixin, TimestampMixin):
    __tablename__ = "inspection_suites"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(Text)
    type: Mapped[str | None] = mapped_column(String(80))
    status: Mapped[str] = mapped_column(String(60), default="Ativo")
    owner_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class InspectionSuiteItem(Base):
    __tablename__ = "inspection_suite_items"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    suite_id: Mapped[int] = mapped_column(
        ForeignKey("inspection_suites.id", ondelete="CASCADE"),
    )
    area: Mapped[str | None] = mapped_column(String(255))
    item_name: Mapped[str] = mapped_column(String(255))
    expected_condition: Mapped[str | None] = mapped_column(String(255))
    sort_order: Mapped[int] = mapped_column(Integer, default=0)


class ApartmentInspection(Base, TenantMixin, TimestampMixin):
    __tablename__ = "apartment_inspections"
    __table_args__ = (
        Index("ix_apartment_inspections_type", "company_id", "inspection_type"),
    )

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    unit: Mapped[str | None] = mapped_column(String(80))
    apartment: Mapped[str | None] = mapped_column(String(80))
    inspection_type: Mapped[str] = mapped_column(String(40))
    inspection_suite_id: Mapped[int | None] = mapped_column(
        ForeignKey("inspection_suites.id", ondelete="SET NULL"),
    )
    inspector_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    scheduled_at: Mapped[datetime | None] = mapped_column(DateTime)
    completed_at: Mapped[datetime | None] = mapped_column(DateTime)
    status: Mapped[str] = mapped_column(String(60), default="Pendente")
    notes: Mapped[str | None] = mapped_column(Text)
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class ApartmentInspectionItem(Base):
    __tablename__ = "apartment_inspection_items"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    inspection_id: Mapped[int] = mapped_column(
        ForeignKey("apartment_inspections.id", ondelete="CASCADE"),
    )
    suite_item_id: Mapped[int | None] = mapped_column(
        ForeignKey("inspection_suite_items.id", ondelete="SET NULL"),
    )
    condition: Mapped[str] = mapped_column(String(40), default="ok")
    notes: Mapped[str | None] = mapped_column(Text)
    sort_order: Mapped[int] = mapped_column(Integer, default=0)


class AuditReport(Base, TenantMixin, TimestampMixin):
    __tablename__ = "audit_reports"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    report_date: Mapped[date] = mapped_column(Date)
    shift_type: Mapped[str | None] = mapped_column(String(20))
    auditor_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    status: Mapped[str] = mapped_column(String(60), default="Em andamento")
    notes: Mapped[str | None] = mapped_column(Text)
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class AuditReportItem(Base):
    __tablename__ = "audit_report_items"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    report_id: Mapped[int] = mapped_column(
        ForeignKey("audit_reports.id", ondelete="CASCADE"),
    )
    category: Mapped[str | None] = mapped_column(String(120))
    description: Mapped[str | None] = mapped_column(Text)
    status: Mapped[str] = mapped_column(String(40), default="ok")
    notes: Mapped[str | None] = mapped_column(Text)
    sort_order: Mapped[int] = mapped_column(Integer, default=0)


class WorkDiary(Base, TenantMixin, TimestampMixin):
    __tablename__ = "work_diaries"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    diary_date: Mapped[date] = mapped_column(Date)
    title: Mapped[str] = mapped_column(String(255))
    description: Mapped[str | None] = mapped_column(Text)
    weather: Mapped[str | None] = mapped_column(String(60))
    status: Mapped[str] = mapped_column(String(60), default="Em andamento")
    owner_user_id: Mapped[int | None] = mapped_column(
        ForeignKey("users.id", ondelete="SET NULL"),
    )
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class WorkDiaryActivity(Base):
    __tablename__ = "work_diary_activities"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    diary_id: Mapped[int] = mapped_column(
        ForeignKey("work_diaries.id", ondelete="CASCADE"),
    )
    description: Mapped[str] = mapped_column(Text)
    start_time: Mapped[datetime | None] = mapped_column(DateTime)
    end_time: Mapped[datetime | None] = mapped_column(DateTime)
    status: Mapped[str] = mapped_column(String(60), default="Planejada")
    sort_order: Mapped[int] = mapped_column(Integer, default=0)


class WorkDiaryTeam(Base):
    __tablename__ = "work_diary_teams"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    diary_id: Mapped[int] = mapped_column(
        ForeignKey("work_diaries.id", ondelete="CASCADE"),
    )
    worker_name: Mapped[str] = mapped_column(String(160))
    role: Mapped[str | None] = mapped_column(String(120))
    hours_worked: Mapped[Decimal | None] = mapped_column(Numeric(5, 2))
    sort_order: Mapped[int] = mapped_column(Integer, default=0)


class WorkDiaryEquipment(Base):
    __tablename__ = "work_diary_equipment"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    diary_id: Mapped[int] = mapped_column(
        ForeignKey("work_diaries.id", ondelete="CASCADE"),
    )
    equipment_name: Mapped[str] = mapped_column(String(160))
    quantity: Mapped[int] = mapped_column(Integer, default=1)
    hours_used: Mapped[Decimal | None] = mapped_column(Numeric(5, 2))
    sort_order: Mapped[int] = mapped_column(Integer, default=0)


class WorkDiaryObservation(Base):
    __tablename__ = "work_diary_observations"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    diary_id: Mapped[int] = mapped_column(
        ForeignKey("work_diaries.id", ondelete="CASCADE"),
    )
    content: Mapped[str] = mapped_column(Text)
    category: Mapped[str | None] = mapped_column(String(80))
    sort_order: Mapped[int] = mapped_column(Integer, default=0)


class LegacyImportRun(Base):
    __tablename__ = "legacy_import_runs"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    source: Mapped[str] = mapped_column(String(80))
    checksum_sha256: Mapped[str] = mapped_column(String(64), unique=True)
    status: Mapped[str] = mapped_column(String(20))
    report: Mapped[str | None] = mapped_column(Text)
    started_at: Mapped[datetime] = mapped_column(DateTime)
    finished_at: Mapped[datetime | None] = mapped_column(DateTime)
