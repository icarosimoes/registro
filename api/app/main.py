from collections.abc import AsyncIterator
from contextlib import asynccontextmanager

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from slowapi import _rate_limit_exceeded_handler
from slowapi.errors import RateLimitExceeded

from app.core.config import get_settings
from app.core.database import engine
from app.core.rate_limit import limiter
from app.domain.apartment_inspections.router import router as apartment_inspections_router
from app.domain.attachments.router import router as attachments_router
from app.domain.audit_reports.router import router as audit_reports_router
from app.domain.auth.router import router as auth_router
from app.domain.check_suites.router import router as check_suites_router
from app.domain.dashboard.router import router as dashboard_router
from app.domain.fiscal_requests.router import router as fiscal_requests_router
from app.domain.health.router import router as health_router
from app.domain.inspection_suites.router import router as inspection_suites_router
from app.domain.meetings.router import router as meetings_router
from app.domain.modules.router import router as modules_router
from app.domain.notifications.router import router as notifications_router
from app.domain.occurrences.router import router as occurrences_router
from app.domain.platform.router import router as platform_router
from app.domain.platform.webhook_router import router as asaas_webhook_router
from app.domain.procedures.router import router as procedures_router
from app.domain.registries.router import router as registries_router
from app.domain.roles.router import router as roles_router
from app.domain.settings.router import router as settings_router
from app.domain.shift_reports.router import router as shift_reports_router
from app.domain.timeline.router import router as timeline_router
from app.domain.users.router import router as users_router
from app.domain.bulletin.router import router as bulletin_router
from app.domain.maintenance.router import router as maintenance_router
from app.domain.work_diaries.router import router as work_diaries_router

settings = get_settings()


@asynccontextmanager
async def lifespan(_: FastAPI) -> AsyncIterator[None]:
    try:
        from app.core.storage import ensure_bucket
        ensure_bucket()
    except Exception:
        pass
    yield
    if engine is not None:
        await engine.dispose()


app = FastAPI(
    title=settings.app_name,
    version="0.1.0",
    docs_url="/docs" if settings.environment != "production" else None,
    redoc_url=None,
    lifespan=lifespan,
)
app.state.limiter = limiter
app.add_exception_handler(RateLimitExceeded, _rate_limit_exceeded_handler)
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.web_origins,
    allow_credentials=True,
    allow_methods=["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"],
    allow_headers=["Authorization", "Content-Type", "X-Request-ID", "X-Registro-Key"],
)
app.include_router(health_router, prefix=settings.api_prefix)
app.include_router(auth_router, prefix=settings.api_prefix)
app.include_router(dashboard_router, prefix=settings.api_prefix)
app.include_router(occurrences_router, prefix=settings.api_prefix)
app.include_router(fiscal_requests_router, prefix=settings.api_prefix)
app.include_router(users_router, prefix=settings.api_prefix)
app.include_router(registries_router, prefix=settings.api_prefix)
app.include_router(modules_router, prefix=settings.api_prefix)
app.include_router(notifications_router, prefix=settings.api_prefix)
app.include_router(settings_router, prefix=settings.api_prefix)
app.include_router(timeline_router, prefix=settings.api_prefix)
app.include_router(procedures_router, prefix=settings.api_prefix)
app.include_router(roles_router, prefix=settings.api_prefix)
app.include_router(attachments_router, prefix=settings.api_prefix)
app.include_router(platform_router, prefix=settings.api_prefix)
app.include_router(shift_reports_router, prefix=settings.api_prefix)
app.include_router(meetings_router, prefix=settings.api_prefix)
app.include_router(check_suites_router, prefix=settings.api_prefix)
app.include_router(inspection_suites_router, prefix=settings.api_prefix)
app.include_router(apartment_inspections_router, prefix=settings.api_prefix)
app.include_router(audit_reports_router, prefix=settings.api_prefix)
app.include_router(work_diaries_router, prefix=settings.api_prefix)
app.include_router(maintenance_router, prefix=settings.api_prefix)
app.include_router(bulletin_router, prefix=settings.api_prefix)
app.include_router(asaas_webhook_router, prefix=settings.api_prefix)
