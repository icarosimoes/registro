from datetime import date, datetime
from typing import Any

from sqlalchemy import JSON, Boolean, Date, DateTime, ForeignKey, Integer, String
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.models.base import Base, TenantMixin, TimestampMixin
from app.models.identity import Company


class PlatformUser(Base, TimestampMixin):
    __tablename__ = "platform_users"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    email: Mapped[str] = mapped_column(String(255), unique=True)
    name: Mapped[str] = mapped_column(String(160))
    password_hash: Mapped[str] = mapped_column(String(255))
    role: Mapped[str] = mapped_column(String(30), default="read_only")
    active: Mapped[bool] = mapped_column(Boolean, default=True)
    last_login_at: Mapped[datetime | None] = mapped_column(DateTime)
    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)


class Plan(Base, TimestampMixin):
    __tablename__ = "plans"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    code: Mapped[str] = mapped_column(String(60), unique=True)
    name: Mapped[str] = mapped_column(String(120))
    price_cents: Mapped[int] = mapped_column(Integer, default=0)
    currency: Mapped[str] = mapped_column(String(3), default="BRL")
    billing_period: Mapped[str] = mapped_column(String(20), default="monthly")
    features: Mapped[dict[str, Any]] = mapped_column(JSON)
    limits: Mapped[dict[str, Any]] = mapped_column(JSON)
    active: Mapped[bool] = mapped_column(Boolean, default=True)
    public: Mapped[bool] = mapped_column(Boolean, default=True)


class Subscription(Base, TenantMixin, TimestampMixin):
    __tablename__ = "subscriptions"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    plan_id: Mapped[int] = mapped_column(ForeignKey("plans.id"))
    status: Mapped[str] = mapped_column(String(20), default="trial", index=True)
    trial_ends_at: Mapped[datetime | None] = mapped_column(DateTime)
    current_period_start: Mapped[datetime | None] = mapped_column(DateTime)
    current_period_end: Mapped[datetime | None] = mapped_column(DateTime)
    billing_provider_subscription_id: Mapped[str | None] = mapped_column(String(120))
    past_due_since: Mapped[datetime | None] = mapped_column(DateTime)
    overdue_warned_at: Mapped[datetime | None] = mapped_column(DateTime)
    suspended_at: Mapped[datetime | None] = mapped_column(DateTime)
    company: Mapped[Company] = relationship(lazy="selectin")
    plan: Mapped[Plan] = relationship(lazy="selectin")


class Invoice(Base, TenantMixin, TimestampMixin):
    __tablename__ = "invoices"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    subscription_id: Mapped[int] = mapped_column(ForeignKey("subscriptions.id"))
    external_payment_id: Mapped[str | None] = mapped_column(String(120), unique=True)
    value_cents: Mapped[int] = mapped_column(Integer)
    status: Mapped[str] = mapped_column(String(20), default="pending", index=True)
    due_date: Mapped[date] = mapped_column(Date)
    payment_date: Mapped[date | None] = mapped_column(Date)


class PlatformAuditLog(Base):
    __tablename__ = "platform_audit_logs"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    platform_user_id: Mapped[int | None] = mapped_column(ForeignKey("platform_users.id"))
    action: Mapped[str] = mapped_column(String(120), index=True)
    target_type: Mapped[str] = mapped_column(String(80))
    target_id: Mapped[str | None] = mapped_column(String(80))
    payload: Mapped[dict[str, Any] | None] = mapped_column(JSON)
    ip_address: Mapped[str | None] = mapped_column(String(45))
    created_at: Mapped[datetime] = mapped_column(DateTime)
