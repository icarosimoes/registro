from datetime import UTC, datetime
from typing import Annotated

import jwt
from fastapi import APIRouter, Depends, HTTPException
from fastapi.security import OAuth2PasswordBearer
from sqlalchemy import case, func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.security import create_platform_token, decode_platform_token, verify_laravel_password
from app.domain.platform.schemas import (
    PlanResponse,
    PlatformLoginRequest,
    PlatformMetricsResponse,
    PlatformTokenResponse,
    TenantSummary,
)
from app.models import Company, Plan, PlatformAuditLog, PlatformUser, Subscription, User

router = APIRouter(prefix="/platform", tags=["platform"])
platform_oauth = OAuth2PasswordBearer(tokenUrl="/api/v1/platform/auth/login")


async def current_platform_user(
    token: Annotated[str, Depends(platform_oauth)],
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> PlatformUser:
    try:
        claims = decode_platform_token(token, settings.jwt_secret)
        user_id = int(claims["sub"])
    except (jwt.InvalidTokenError, KeyError, TypeError, ValueError) as exc:
        raise HTTPException(status_code=401, detail={"code": "invalid_platform_token"}) from exc
    user = await session.scalar(
        select(PlatformUser).where(
            PlatformUser.id == user_id,
            PlatformUser.active.is_(True),
            PlatformUser.deleted_at.is_(None),
        )
    )
    if user is None:
        raise HTTPException(status_code=401, detail={"code": "inactive_platform_user"})
    return user


@router.post("/auth/login", response_model=PlatformTokenResponse)
async def platform_login(
    payload: PlatformLoginRequest,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> PlatformTokenResponse:
    user = await session.scalar(
        select(PlatformUser).where(
            PlatformUser.email == payload.email,
            PlatformUser.active.is_(True),
            PlatformUser.deleted_at.is_(None),
        )
    )
    if user is None or not verify_laravel_password(payload.password, user.password_hash):
        raise HTTPException(status_code=401, detail={"code": "invalid_credentials"})
    user.last_login_at = datetime.now(UTC).replace(tzinfo=None)
    session.add(
        PlatformAuditLog(
            platform_user_id=user.id,
            action="platform.login",
            target_type="platform_user",
            target_id=str(user.id),
            payload=None,
            created_at=datetime.now(UTC).replace(tzinfo=None),
        )
    )
    await session.commit()
    return PlatformTokenResponse(
        access_token=create_platform_token(
            subject=user.id,
            role=user.role,
            secret=settings.jwt_secret,
            minutes=settings.access_token_minutes,
        ),
        expires_in=settings.access_token_minutes * 60,
        name=user.name,
        role=user.role,
    )


@router.get("/metrics", response_model=PlatformMetricsResponse)
async def platform_metrics(
    _: Annotated[PlatformUser, Depends(current_platform_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> PlatformMetricsResponse:
    tenants_total, tenants_active = (
        await session.execute(
            select(
                func.count(Company.id), func.sum(case((Company.status == "active", 1), else_=0))
            ).where(Company.deleted_at.is_(None))
        )
    ).one()
    trial, past_due = (
        await session.execute(
            select(
                func.sum(case((Subscription.status == "trial", 1), else_=0)),
                func.sum(case((Subscription.status == "past_due", 1), else_=0)),
            )
        )
    ).one()
    mrr = await session.scalar(
        select(func.coalesce(func.sum(Plan.price_cents), 0))
        .join(Subscription, Subscription.plan_id == Plan.id)
        .where(Subscription.status == "active", Plan.billing_period == "monthly")
    )
    return PlatformMetricsResponse(
        tenants_total=tenants_total or 0,
        tenants_active=tenants_active or 0,
        tenants_trial=trial or 0,
        tenants_past_due=past_due or 0,
        mrr_cents=mrr or 0,
    )


@router.get("/tenants", response_model=list[TenantSummary])
async def list_tenants(
    _: Annotated[PlatformUser, Depends(current_platform_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> list[TenantSummary]:
    rows = (
        (
            await session.execute(
                select(
                    Company.id,
                    Company.name,
                    Company.slug,
                    Company.status,
                    func.count(User.id.distinct()).label("users_count"),
                    Subscription.status.label("subscription_status"),
                    Plan.name.label("plan_name"),
                    Subscription.trial_ends_at,
                )
                .outerjoin(User, User.company_id == Company.id)
                .outerjoin(Subscription, Subscription.company_id == Company.id)
                .outerjoin(Plan, Plan.id == Subscription.plan_id)
                .where(Company.deleted_at.is_(None))
                .group_by(Company.id, Subscription.id, Plan.id)
                .order_by(Company.name)
            )
        )
        .mappings()
        .all()
    )
    return [TenantSummary(**dict(row)) for row in rows]


@router.get("/plans", response_model=list[PlanResponse])
async def list_plans(
    _: Annotated[PlatformUser, Depends(current_platform_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> list[PlanResponse]:
    plans = (await session.execute(select(Plan).order_by(Plan.price_cents))).scalars().all()
    return [PlanResponse.model_validate(plan, from_attributes=True) for plan in plans]
