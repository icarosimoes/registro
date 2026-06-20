from datetime import date, datetime

from sqlalchemy import (
    JSON,
    Date,
    DateTime,
    ForeignKey,
    Index,
    Integer,
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
    created_at: Mapped[datetime] = mapped_column(DateTime, server_default=func.now())


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


class LegacyImportRun(Base):
    __tablename__ = "legacy_import_runs"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    source: Mapped[str] = mapped_column(String(80))
    checksum_sha256: Mapped[str] = mapped_column(String(64), unique=True)
    status: Mapped[str] = mapped_column(String(20))
    report: Mapped[str | None] = mapped_column(Text)
    started_at: Mapped[datetime] = mapped_column(DateTime)
    finished_at: Mapped[datetime | None] = mapped_column(DateTime)
