from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings
from app.core.security import create_access_token, verify_laravel_password
from app.domain.auth.repository import LegacyUser, find_active_user_by_email
from app.domain.auth.schemas import TokenResponse, UserResponse


def to_response(user: LegacyUser) -> UserResponse:
    return UserResponse(
        id=user.id, name=user.name, email=user.email, company_id=user.company_id,
        role_id=user.role_id, role_name=user.role_name, permissions=user.permissions,
    )


async def authenticate(
    session: AsyncSession, email: str, password: str, settings: Settings,
) -> TokenResponse | None:
    user = await find_active_user_by_email(session, email)
    if user is None or not verify_laravel_password(password, user.password_hash):
        return None
    token = create_access_token(
        subject=user.id, company_id=user.company_id, role_id=user.role_id,
        permissions=user.permissions, secret=settings.jwt_secret,
        minutes=settings.access_token_minutes,
    )
    return TokenResponse(
        access_token=token, expires_in=settings.access_token_minutes * 60,
        user=to_response(user),
    )
