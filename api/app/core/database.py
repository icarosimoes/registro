from collections.abc import AsyncIterator

from sqlalchemy.ext.asyncio import AsyncSession, async_sessionmaker, create_async_engine

from app.core.config import get_settings
from app.models.base import Base

settings = get_settings()

engine = (
    create_async_engine(
        settings.database_url,
        echo=settings.database_echo,
        pool_pre_ping=True,
        pool_recycle=1800,
    )
    if settings.database_url
    else None
)

SessionLocal = async_sessionmaker(engine, expire_on_commit=False) if engine else None

__all__ = ["Base", "SessionLocal", "engine", "get_session"]


async def get_session() -> AsyncIterator[AsyncSession]:
    if SessionLocal is None:
        raise RuntimeError("DATABASE_URL não configurada")
    async with SessionLocal() as session:
        yield session
