from sqlalchemy.ext.asyncio import AsyncSession

from app.models import Notification


async def create_notification(
    session: AsyncSession,
    *,
    company_id: int,
    user_id: int,
    title: str,
    body: str | None = None,
    category: str = "info",
    entity_type: str | None = None,
    entity_id: int | None = None,
) -> Notification:
    record = Notification(
        company_id=company_id,
        user_id=user_id,
        title=title,
        body=body,
        category=category,
        entity_type=entity_type,
        entity_id=entity_id,
    )
    session.add(record)
    return record
