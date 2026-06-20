from typing import Annotated

import jwt
from fastapi import APIRouter, Depends, HTTPException, Request, status
from fastapi.security import OAuth2PasswordBearer
from pydantic import BaseModel
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.rate_limit import limiter
from app.core.security import (
    create_access_token,
    create_refresh_token,
    decode_access_token,
    decode_refresh_token,
)
from app.domain.auth.repository import find_active_user_by_id
from app.domain.auth.schemas import LoginRequest, TokenResponse, UserResponse
from app.domain.auth.service import MultiTenantResult, authenticate, to_response

router = APIRouter(prefix="/auth", tags=["auth"])
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/login")


@router.post("/login")
@limiter.limit("10/minute")
async def login(
    request: Request,
    payload: LoginRequest,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> TokenResponse:
    result = await authenticate(
        session,
        payload.email,
        payload.password,
        settings,
        company_id=payload.company_id,
    )
    if result is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail={"code": "invalid_credentials", "message": "E-mail ou senha inválidos"},
        )
    if isinstance(result, MultiTenantResult):
        raise HTTPException(
            status_code=422,
            detail={
                "code": "multi_tenant",
                "message": "Selecione a empresa",
                "tenants": [{"id": t.id, "name": t.name} for t in result.tenants],
            },
        )
    return result


@router.get("/me", response_model=UserResponse)
async def me(
    token: Annotated[str, Depends(oauth2_scheme)],
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> UserResponse:
    try:
        claims = decode_access_token(token, settings.jwt_secret)
        user_id, company_id = int(claims["sub"]), int(claims["company_id"])
    except (jwt.InvalidTokenError, KeyError, TypeError, ValueError) as exc:
        raise HTTPException(
            status_code=401,
            detail={"code": "invalid_token", "message": "Sessão inválida"},
        ) from exc
    user = await find_active_user_by_id(session, user_id, company_id)
    if user is None:
        raise HTTPException(
            status_code=401,
            detail={"code": "inactive_user", "message": "Usuário indisponível"},
        )
    return to_response(user)


class RefreshRequest(BaseModel):
    refresh_token: str


@router.post("/refresh")
@limiter.limit("20/minute")
async def refresh(
    request: Request,
    body: RefreshRequest,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> TokenResponse:
    try:
        claims = decode_refresh_token(body.refresh_token, settings.jwt_secret)
        user_id, company_id = int(claims["sub"]), int(claims["company_id"])
    except (jwt.InvalidTokenError, KeyError, TypeError, ValueError) as exc:
        raise HTTPException(
            status_code=401,
            detail={"code": "invalid_refresh_token", "message": "Refresh token inválido"},
        ) from exc
    user = await find_active_user_by_id(session, user_id, company_id)
    if user is None:
        raise HTTPException(
            status_code=401,
            detail={"code": "inactive_user", "message": "Usuário indisponível"},
        )
    access = create_access_token(
        subject=user.id,
        company_id=user.company_id,
        role_id=user.role_id,
        permissions=user.permissions,
        secret=settings.jwt_secret,
        minutes=settings.access_token_minutes,
    )
    refresh_tok = create_refresh_token(
        subject=user.id,
        company_id=user.company_id,
        secret=settings.jwt_secret,
        days=settings.refresh_token_days,
    )
    return TokenResponse(
        access_token=access,
        refresh_token=refresh_tok,
        expires_in=settings.access_token_minutes * 60,
        user=to_response(user),
    )
