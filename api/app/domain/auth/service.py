from dataclasses import dataclass

from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings
from app.core.security import create_access_token, verify_laravel_password
from app.domain.auth.repository import (
    AuthenticatedUser,
    find_active_users_by_email,
    find_tenant_names_by_email,
)
from app.domain.auth.schemas import TenantOption, TokenResponse, UserResponse


def to_response(user: AuthenticatedUser) -> UserResponse:
    return UserResponse(
        id=user.id,
        name=user.name,
        email=user.email,
        company_id=user.company_id,
        role_id=user.role_id,
        role_name=user.role_name,
        permissions=user.permissions,
    )


@dataclass
class MultiTenantResult:
    tenants: list[TenantOption]


async def authenticate(
    session: AsyncSession,
    email: str,
    password: str,
    settings: Settings,
    company_id: int | None = None,
) -> TokenResponse | MultiTenantResult | None:
    users = await find_active_users_by_email(session, email, company_id)

    if len(users) > 1:
        tenants = await find_tenant_names_by_email(session, email)
        return MultiTenantResult(
            tenants=[TenantOption(id=tid, name=tname) for tid, tname in tenants],
        )

    if not users:
        return None

    user = users[0]
    if not verify_laravel_password(password, user.password_hash):
        return None

    token = create_access_token(
        subject=user.id,
        company_id=user.company_id,
        role_id=user.role_id,
        permissions=user.permissions,
        secret=settings.jwt_secret,
        minutes=settings.access_token_minutes,
    )
    return TokenResponse(
        access_token=token,
        expires_in=settings.access_token_minutes * 60,
        user=to_response(user),
    )
