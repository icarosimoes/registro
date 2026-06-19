from datetime import datetime
from typing import Any

from pydantic import BaseModel, Field, field_validator


class PlatformLoginRequest(BaseModel):
    email: str = Field(min_length=3, max_length=255)
    password: str = Field(min_length=1, max_length=72)

    @field_validator("email")
    @classmethod
    def normalize_email(cls, value: str) -> str:
        return value.strip().lower()


class PlatformTokenResponse(BaseModel):
    access_token: str
    token_type: str = "bearer"
    expires_in: int
    name: str
    role: str


class PlatformMetricsResponse(BaseModel):
    tenants_total: int
    tenants_active: int
    tenants_trial: int
    tenants_past_due: int
    mrr_cents: int


class TenantSummary(BaseModel):
    id: int
    name: str
    slug: str
    status: str
    users_count: int
    subscription_status: str | None
    plan_name: str | None
    trial_ends_at: datetime | None


class PlanResponse(BaseModel):
    id: int
    code: str
    name: str
    price_cents: int
    currency: str
    billing_period: str
    features: dict[str, Any]
    limits: dict[str, Any]
    active: bool
    public: bool
