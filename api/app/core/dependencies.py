from collections.abc import AsyncIterator

from fastapi import HTTPException
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.database import SessionLocal


async def require_session() -> AsyncIterator[AsyncSession]:
    if SessionLocal is None:
        raise HTTPException(
            status_code=503,
            detail={"code": "database_unavailable", "message": "Banco não configurado"},
        )
    async with SessionLocal() as session:
        yield session
