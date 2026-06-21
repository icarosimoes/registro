from collections.abc import AsyncIterator

from fastapi import HTTPException
from sqlalchemy import text
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.database import SessionLocal


async def require_session() -> AsyncIterator[AsyncSession]:
    if SessionLocal is None:
        raise HTTPException(
            status_code=503,
            detail={"code": "database_unavailable", "message": "Banco não configurado"},
        )
    async with SessionLocal() as session:
        try:
            yield session
        finally:
            await session.execute(text("RESET app.current_company_id"))
