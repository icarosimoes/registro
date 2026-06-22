from datetime import datetime

import bcrypt
from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.models import Role, User


async def _role_name(session: AsyncSession, role_id: int | None) -> str | None:
    if not role_id:
        return None
    return await session.scalar(select(Role.name).where(Role.id == role_id))


async def list_users(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list[tuple], int]:
    filters = [User.company_id == company_id, User.deleted_at.is_(None)]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(or_(User.name.ilike(pattern), User.email.ilike(pattern)))
    total = await session.scalar(select(func.count(User.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(User, Role.name)
            .outerjoin(Role, Role.id == User.role_id)
            .where(*filters)
            .order_by(User.name)
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def search_users(
    session: AsyncSession,
    company_id: int,
    q: str,
) -> list[User]:
    filters = [User.company_id == company_id, User.deleted_at.is_(None), User.active.is_(True)]
    if q.strip():
        pattern = f"%{q.strip()}%"
        filters.append(or_(User.name.ilike(pattern), User.email.ilike(pattern)))
    rows = (
        await session.execute(select(User).where(*filters).order_by(User.name).limit(10))
    ).scalars().all()
    return list(rows)


async def create_user(
    session: AsyncSession,
    company_id: int,
    actor_id: int,
    *,
    name: str,
    email: str,
    phone: str | None,
    password: str,
    role_id: int | None,
    active: bool,
) -> User | None:
    existing = await session.scalar(
        select(User.id).where(
            User.company_id == company_id,
            User.email == email.lower(),
            User.deleted_at.is_(None),
        )
    )
    if existing:
        return None

    password_hash = bcrypt.hashpw(password.encode(), bcrypt.gensalt()).decode()
    record = User(
        company_id=company_id,
        name=name,
        email=email.lower(),
        phone=phone,
        password=password_hash,
        role_id=role_id,
        active=active,
    )
    session.add(record)
    await session.flush()
    await record_event(
        session,
        company_id=company_id,
        user_id=actor_id,
        entity_type="user",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(record)
    return record


async def update_user(
    session: AsyncSession,
    company_id: int,
    actor_id: int,
    user_id: int,
    updates: dict,
) -> User | None:
    record = await session.scalar(
        select(User).where(
            User.id == user_id,
            User.company_id == company_id,
            User.deleted_at.is_(None),
        )
    )
    if record is None:
        return None

    if "password" in updates:
        updates["password"] = bcrypt.hashpw(updates["password"].encode(), bcrypt.gensalt()).decode()

    before = {}
    for k in updates:
        before[k] = "***" if k == "password" else str(getattr(record, k))

    for field, value in updates.items():
        setattr(record, field, value)

    after = {k: "***" if k == "password" else str(v) for k, v in updates.items()}
    diff = compute_diff(before, after)
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=actor_id,
            entity_type="user",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    await session.refresh(record)
    return record


async def update_profile(
    session: AsyncSession,
    user_id: int,
    company_id: int,
    updates: dict,
) -> User | None:
    record = await session.scalar(
        select(User).where(
            User.id == user_id,
            User.company_id == company_id,
            User.deleted_at.is_(None),
        )
    )
    if record is None:
        return None

    if "password" in updates:
        updates["password"] = bcrypt.hashpw(updates["password"].encode(), bcrypt.gensalt()).decode()

    before = {k: ("***" if k == "password" else str(getattr(record, k))) for k in updates}
    for field, value in updates.items():
        setattr(record, field, value)
    after = {k: ("***" if k == "password" else str(v)) for k, v in updates.items()}
    diff = compute_diff(before, after)
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="user",
            entity_id=record.id,
            event_type="update_profile",
            diff=diff,
        )
    await session.commit()
    await session.refresh(record)
    return record


async def delete_user(
    session: AsyncSession,
    company_id: int,
    actor_id: int,
    user_id: int,
) -> bool:
    record = await session.scalar(
        select(User).where(
            User.id == user_id,
            User.company_id == company_id,
            User.deleted_at.is_(None),
        )
    )
    if record is None:
        return False
    record.deleted_at = datetime.now()
    record.active = False
    await record_event(
        session,
        company_id=company_id,
        user_id=actor_id,
        entity_type="user",
        entity_id=record.id,
        event_type="delete",
    )
    await session.commit()
    return True


async def get_role_name(session: AsyncSession, role_id: int | None) -> str | None:
    return await _role_name(session, role_id)
