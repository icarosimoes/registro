from datetime import datetime

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import record_event
from app.integrations.notifications import notify_record_event
from app.models import BulletinPost, User


async def list_posts(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
    pinned_only: bool = False,
) -> tuple[list[tuple], int]:
    filters = [
        BulletinPost.company_id == company_id,
        BulletinPost.deleted_at.is_(None),
    ]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(or_(BulletinPost.title.ilike(pattern), BulletinPost.body.ilike(pattern)))
    if pinned_only:
        filters.append(BulletinPost.pinned.is_(True))
    total = await session.scalar(select(func.count(BulletinPost.id)).where(*filters)) or 0
    rows = (
        await session.execute(
            select(BulletinPost, User.name)
            .outerjoin(User, User.id == BulletinPost.author_user_id)
            .where(*filters)
            .order_by(BulletinPost.pinned.desc(), BulletinPost.created_at.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def create_post(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    user_name: str,
    user_email: str,
    *,
    title: str,
    body: str | None,
    pinned: bool,
    expires_at: datetime | None,
    notify_user_ids: list[int] | None,
) -> tuple[BulletinPost, str | None]:
    post = BulletinPost(
        company_id=company_id,
        title=title,
        body=body,
        pinned=pinned,
        expires_at=expires_at,
        author_user_id=user_id,
        notify_user_ids=notify_user_ids,
    )
    session.add(post)
    await session.commit()
    await session.refresh(post)
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="bulletin",
        entity_id=post.id,
        event_type="create",
    )
    await session.commit()
    await notify_record_event(
        session,
        company_id=company_id,
        actor_name=user_name,
        actor_email=user_email,
        event="create",
        title=post.title,
        module="Mural",
        notify_user_ids=notify_user_ids,
    )
    return post, user_name


async def update_post(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    post_id: int,
    updates: dict,
) -> BulletinPost | None:
    post = await session.scalar(
        select(BulletinPost).where(
            BulletinPost.id == post_id,
            BulletinPost.company_id == company_id,
            BulletinPost.deleted_at.is_(None),
        )
    )
    if post is None:
        return None
    for field, value in updates.items():
        setattr(post, field, value)
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="bulletin",
        entity_id=post.id,
        event_type="update",
        diff=updates,
    )
    await session.commit()
    await session.refresh(post)
    return post


async def delete_post(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    post_id: int,
) -> bool:
    post = await session.scalar(
        select(BulletinPost).where(
            BulletinPost.id == post_id,
            BulletinPost.company_id == company_id,
            BulletinPost.deleted_at.is_(None),
        )
    )
    if post is None:
        return False
    post.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="bulletin",
        entity_id=post.id,
        event_type="delete",
    )
    await session.commit()
    return True
