from typing import Any

from sqlalchemy.ext.asyncio import AsyncSession

from app.models import AuditEvent


async def record_event(
    session: AsyncSession,
    *,
    company_id: int,
    user_id: int,
    entity_type: str,
    entity_id: int,
    event_type: str,
    diff: dict[str, Any] | None = None,
) -> None:
    if event_type == "update" and not diff:
        return
    session.add(AuditEvent(
        company_id=company_id,
        user_id=user_id,
        entity_type=entity_type,
        entity_id=entity_id,
        event_type=event_type,
        diff=diff,
    ))


def compute_diff(before: dict[str, Any], after: dict[str, Any]) -> dict[str, Any] | None:
    changes: dict[str, Any] = {}
    for key in after:
        old = before.get(key)
        new = after[key]
        if str(old) != str(new):
            changes[key] = {"from": old, "to": new}
    return changes or None
