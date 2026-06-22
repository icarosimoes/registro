"""Cursor-based pagination utilities.

Encodes/decodes opaque cursors and builds SQLAlchemy filters
for keyset pagination by (id DESC).
"""

from __future__ import annotations

import base64
from typing import Any, NamedTuple

from pydantic import BaseModel
from sqlalchemy import Select
from sqlalchemy.ext.asyncio import AsyncSession

CURSOR_PREFIX = "c:"


def encode_cursor(record_id: int) -> str:
    raw = f"{CURSOR_PREFIX}{record_id}"
    return base64.urlsafe_b64encode(raw.encode()).decode()


def decode_cursor(cursor: str) -> int | None:
    try:
        raw = base64.urlsafe_b64decode(cursor.encode()).decode()
        if not raw.startswith(CURSOR_PREFIX):
            return None
        return int(raw[len(CURSOR_PREFIX) :])
    except (ValueError, Exception):
        return None


class CursorPage(NamedTuple):
    items: list[Any]
    next_cursor: str | None
    has_more: bool


class CursorPageResponse(BaseModel):
    items: list[Any]
    next_cursor: str | None
    has_more: bool


async def paginate_by_cursor(
    session: AsyncSession,
    stmt: Select,
    *,
    id_column,
    cursor: str | None = None,
    limit: int = 20,
) -> CursorPage:
    """Apply cursor-based pagination to a SELECT statement.

    Expects the statement to already have WHERE filters applied.
    Adds ORDER BY id DESC, cursor filter, and LIMIT.
    """
    if cursor:
        after_id = decode_cursor(cursor)
        if after_id is not None:
            stmt = stmt.where(id_column < after_id)

    stmt = stmt.order_by(id_column.desc()).limit(limit + 1)
    result = (await session.execute(stmt)).all()

    has_more = len(result) > limit
    items = list(result[:limit])

    next_cursor = None
    if has_more and items:
        last = items[-1]
        last_id = last[0].id if hasattr(last[0], "id") else last[0]
        next_cursor = encode_cursor(last_id)

    return CursorPage(items=items, next_cursor=next_cursor, has_more=has_more)
