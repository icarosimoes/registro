from collections.abc import AsyncIterator
from typing import Annotated

import jwt
from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings, get_settings
from app.core.database import SessionLocal
from app.core.security import decode_access_token
from app.domain.auth.repository import find_active_user_by_id
from app.domain.auth.schemas import LoginRequest, TokenResponse, UserResponse
from app.domain.auth.service import authenticate, to_response

router = APIRouter(prefix="/auth", tags=["auth"])
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/login")


async def require_session() -> AsyncIterator[AsyncSession]:
    if SessionLocal is None:
        raise HTTPException(
            status_code=503,
            detail={"code": "database_unavailable", "message": "Banco não configurado"},
        )
    async with SessionLocal() as session:
        yield session


@router.post("/login", response_model=TokenResponse)
async def login(
    payload: LoginRequest,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> TokenResponse:
    result = await authenticate(session, payload.email, payload.password, settings)
    if result is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail={"code": "invalid_credentials", "message": "E-mail ou senha inválidos"},
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
