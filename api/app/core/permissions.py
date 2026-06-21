from typing import Annotated

from fastapi import Depends, HTTPException

from app.core.auth import current_user
from app.domain.auth.repository import AuthenticatedUser


def require_permission(code: str):
    async def _check(
        user: Annotated[AuthenticatedUser, Depends(current_user)],
    ) -> AuthenticatedUser:
        if "*" not in user.permissions and code not in user.permissions:
            raise HTTPException(
                status_code=403,
                detail={"code": "forbidden", "required": code},
            )
        return user

    return Depends(_check)
