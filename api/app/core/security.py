from datetime import UTC, datetime, timedelta
from typing import Any

import bcrypt
import jwt

ALGORITHM = "HS256"


def verify_laravel_password(password: str, password_hash: str) -> bool:
    try:
        return bcrypt.checkpw(password.encode(), password_hash.encode())
    except (ValueError, TypeError):
        return False


def create_access_token(
    *, subject: int, company_id: int, role_id: int | None,
    permissions: list[str], secret: str, minutes: int,
) -> str:
    now = datetime.now(UTC)
    payload: dict[str, Any] = {
        "sub": str(subject), "company_id": company_id, "role_id": role_id,
        "permissions": permissions, "type": "access", "iat": now,
        "exp": now + timedelta(minutes=minutes),
    }
    return jwt.encode(payload, secret, algorithm=ALGORITHM)


def decode_access_token(token: str, secret: str) -> dict[str, Any]:
    payload: dict[str, Any] = jwt.decode(token, secret, algorithms=[ALGORITHM])
    if payload.get("type") != "access":
        raise jwt.InvalidTokenError("tipo de token inválido")
    return payload
