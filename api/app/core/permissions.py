from typing import Annotated

import structlog
from fastapi import Depends, HTTPException

from app.core.auth import current_user
from app.domain.auth.repository import AuthenticatedUser

logger = structlog.get_logger()


def require_permission(code: str):
    async def _check(
        user: Annotated[AuthenticatedUser, Depends(current_user)],
    ) -> AuthenticatedUser:
        if "*" in user.permissions:
            logger.debug("permission_check", required=code, granted_via="wildcard", user_id=user.id)
        elif code not in user.permissions:
            raise HTTPException(
                status_code=403,
                detail={"code": "forbidden", "required": code},
            )
        return user

    return Depends(_check)
