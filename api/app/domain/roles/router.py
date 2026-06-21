from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.roles.schemas import (
    PermissionGroup,
    PermissionSummary,
    RoleCreate,
    RoleListResponse,
    RoleSummary,
    RoleUpdate,
)
from app.domain.roles.service import (
    create_role,
    delete_role,
    get_role,
    list_permissions,
    list_roles,
    update_role,
)

router = APIRouter(prefix="/roles", tags=["roles"])


def _to_summary(role, user_count: int) -> RoleSummary:
    return RoleSummary(
        id=role.id,
        code=role.code,
        name=role.name,
        permission_codes=sorted(p.code for p in role.permissions),
        user_count=user_count,
        updated_at=role.updated_at,
    )


@router.get("/permissions", response_model=list[PermissionGroup])
async def list_permissions_endpoint(
    user: Annotated[AuthenticatedUser, require_permission("user.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> list[PermissionGroup]:
    perms = await list_permissions(session)
    groups: dict[str, list[PermissionSummary]] = {}
    for p in perms:
        groups.setdefault(p.module, []).append(
            PermissionSummary(id=p.id, code=p.code, name=p.name, module=p.module)
        )
    return [PermissionGroup(module=m, permissions=ps) for m, ps in groups.items()]


@router.get("", response_model=RoleListResponse)
async def list_roles_endpoint(
    user: Annotated[AuthenticatedUser, require_permission("user.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
) -> RoleListResponse:
    rows, total = await list_roles(session, user.company_id, page, page_size)
    return RoleListResponse(
        items=[_to_summary(role, uc) for role, uc in rows],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/{role_id}", response_model=RoleSummary)
async def get_role_endpoint(
    role_id: int,
    user: Annotated[AuthenticatedUser, require_permission("user.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> RoleSummary:
    role = await get_role(session, user.company_id, role_id)
    if role is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    from sqlalchemy import func, select

    from app.models import User

    uc = await session.scalar(
        select(func.count(User.id)).where(
            User.role_id == role_id, User.deleted_at.is_(None)
        )
    ) or 0
    return _to_summary(role, uc)


@router.post("", response_model=RoleSummary, status_code=201)
async def create_role_endpoint(
    body: RoleCreate,
    user: Annotated[AuthenticatedUser, require_permission("user.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> RoleSummary:
    role = await create_role(
        session,
        user.company_id,
        user.id,
        code=body.code,
        name=body.name,
        permission_codes=body.permission_codes,
    )
    return _to_summary(role, 0)


@router.patch("/{role_id}", response_model=RoleSummary)
async def update_role_endpoint(
    role_id: int,
    body: RoleUpdate,
    user: Annotated[AuthenticatedUser, require_permission("user.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> RoleSummary:
    role = await update_role(
        session,
        user.company_id,
        user.id,
        role_id,
        name=body.name,
        permission_codes=body.permission_codes,
    )
    if role is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    from sqlalchemy import func, select

    from app.models import User

    uc = await session.scalar(
        select(func.count(User.id)).where(
            User.role_id == role_id, User.deleted_at.is_(None)
        )
    ) or 0
    return _to_summary(role, uc)


@router.delete("/{role_id}", status_code=204)
async def delete_role_endpoint(
    role_id: int,
    user: Annotated[AuthenticatedUser, require_permission("user.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    result = await delete_role(session, user.company_id, user.id, role_id)
    if result is False:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    if result == "has_users":
        raise HTTPException(
            status_code=409,
            detail={"code": "role_has_users"},
        )
