from typing import Annotated, Literal

from fastapi import APIRouter, Depends
from pydantic import BaseModel
from sqlalchemy import text

from app.core.cache import redis_healthy
from app.core.config import Settings, get_settings
from app.core.database import engine

router = APIRouter(prefix="/health", tags=["health"])


class HealthResponse(BaseModel):
    status: Literal["ok"] = "ok"
    service: str
    environment: str


class ReadinessResponse(BaseModel):
    status: Literal["ready", "not_configured"]
    database: Literal["connected", "not_configured"]
    cache: Literal["connected", "unavailable"]


@router.get("", response_model=HealthResponse)
async def health(
    settings: Annotated[Settings, Depends(get_settings)],
) -> HealthResponse:
    return HealthResponse(service=settings.app_name, environment=settings.environment)


@router.get("/ready", response_model=ReadinessResponse)
async def readiness() -> ReadinessResponse:
    if engine is None:
        return ReadinessResponse(
            status="not_configured",
            database="not_configured",
            cache="unavailable",
        )
    async with engine.connect() as connection:
        await connection.execute(text("SELECT 1"))
    cache_status = "connected" if await redis_healthy() else "unavailable"
    return ReadinessResponse(status="ready", database="connected", cache=cache_status)
