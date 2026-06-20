from dataclasses import dataclass

from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.orm import selectinload

from app.models import Company, Role, User


@dataclass(frozen=True)
class AuthenticatedUser:
    id: int
    name: str
    email: str
    phone: str | None
    password_hash: str
    company_id: int
    company_name: str
    role_id: int | None
    role_name: str | None
    permissions: list[str]


def map_user(user: User) -> AuthenticatedUser:
    return AuthenticatedUser(
        id=user.id,
        name=user.name,
        email=user.email,
        phone=user.phone,
        password_hash=user.password,
        company_id=user.company_id,
        company_name=user.company.name,
        role_id=user.role_id,
        role_name=user.role.name if user.role else None,
        permissions=sorted(permission.code for permission in user.role.permissions)
        if user.role
        else [],
    )


async def find_active_users_by_email(
    session: AsyncSession,
    email: str,
    company_id: int | None = None,
) -> list[AuthenticatedUser]:
    query = (
        select(User)
        .join(Company)
        .options(
            selectinload(User.company),
            selectinload(User.role).selectinload(Role.permissions),
        )
        .where(
            User.email == email.lower(),
            User.active.is_(True),
            User.deleted_at.is_(None),
            Company.status == "active",
            Company.deleted_at.is_(None),
        )
    )
    if company_id:
        query = query.where(User.company_id == company_id)
    query = query.order_by(User.company_id, User.id)
    users = (await session.execute(query)).scalars().all()
    return [map_user(u) for u in users]


async def find_active_user_by_id(
    session: AsyncSession,
    user_id: int,
    company_id: int,
) -> AuthenticatedUser | None:
    query = (
        select(User)
        .join(Company)
        .options(
            selectinload(User.company),
            selectinload(User.role).selectinload(Role.permissions),
        )
        .where(
            User.id == user_id,
            User.company_id == company_id,
            User.active.is_(True),
            User.deleted_at.is_(None),
            Company.status == "active",
            Company.deleted_at.is_(None),
        )
    )
    user = (await session.execute(query)).scalar_one_or_none()
    return map_user(user) if user else None
