from typing import Literal

from fastapi import APIRouter
from pydantic import BaseModel
from sqlalchemy import text

from app.core.config import get_settings
from app.core.database import engine

router = APIRouter(prefix="/health", tags=["health"])
settings = get_settings()


class HealthResponse(BaseModel):
    status: Literal["ok"] = "ok"
    service: str
    environment: str


class ReadinessResponse(BaseModel):
    status: Literal["ready", "not_configured"]
    database: Literal["connected", "not_configured"]


@router.get("", response_model=HealthResponse)
async def health() -> HealthResponse:
    return HealthResponse(service=settings.app_name, environment=settings.environment)


@router.get("/ready", response_model=ReadinessResponse)
async def readiness() -> ReadinessResponse:
    if engine is None:
        return ReadinessResponse(status="not_configured", database="not_configured")
    async with engine.connect() as connection:
        await connection.execute(text("SELECT 1"))
    return ReadinessResponse(status="ready", database="connected")
