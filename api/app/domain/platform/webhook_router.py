import hmac
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Request
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.rate_limit import limiter
from app.domain.platform.webhook import handle_asaas_webhook

router = APIRouter(prefix="/integrations/asaas", tags=["asaas-webhook"])


@router.post("/webhook")
@limiter.limit("60/minute")
async def asaas_webhook(
    request: Request,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> dict:
    token = request.headers.get("asaas-access-token", "")
    if not settings.asaas_webhook_token or not hmac.compare_digest(
        token, settings.asaas_webhook_token
    ):
        raise HTTPException(status_code=401, detail={"code": "invalid_webhook_token"})
    body = await request.json()
    return await handle_asaas_webhook(session, body)
