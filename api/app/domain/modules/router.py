from datetime import datetime
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from pydantic import BaseModel
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.modules.service import (
    VALID_MODULES,
    create_record,
    delete_record,
    list_records,
    update_record,
)

router = APIRouter(prefix="/modules", tags=["modules"])


class ModuleRecordSummary(BaseModel):
    id: int
    title: str
    description: str | None
    category: str | None
    owner: str
    status: str
    updated_at: datetime


class ModuleRecordListResponse(BaseModel):
    items: list[ModuleRecordSummary]
    total: int
    page: int
    page_size: int


class ModuleRecordCreate(BaseModel):
    title: str
    description: str | None = None
    category: str | None = None
    status: str = "Em andamento"
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None


class ModuleRecordUpdate(BaseModel):
    title: str | None = None
    description: str | None = None
    category: str | None = None
    status: str | None = None
    owner_user_id: int | None = None
    notify_user_ids: list[int] | None = None


def _validate_module(module_slug: str) -> None:
    if module_slug not in VALID_MODULES:
        raise HTTPException(status_code=404, detail={"code": "invalid_module"})


@router.get("/{module_slug}", response_model=ModuleRecordListResponse)
async def list_records_endpoint(
    module_slug: str,
    user: Annotated[AuthenticatedUser, require_permission("module.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> ModuleRecordListResponse:
    _validate_module(module_slug)
    rows, total = await list_records(session, user.company_id, module_slug, page, page_size, search)
    return ModuleRecordListResponse(
        items=[
            ModuleRecordSummary(
                id=rec.id,
                title=rec.title,
                description=rec.description,
                category=rec.category,
                owner=owner_name or "Não atribuído",
                status=rec.status,
                updated_at=rec.updated_at,
            )
            for rec, owner_name in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.post("/{module_slug}", response_model=ModuleRecordSummary, status_code=201)
async def create_record_endpoint(
    module_slug: str,
    body: ModuleRecordCreate,
    user: Annotated[AuthenticatedUser, require_permission("module.create")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ModuleRecordSummary:
    _validate_module(module_slug)
    record, owner_name = await create_record(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        module_slug,
        title=body.title,
        description=body.description,
        category=body.category,
        status=body.status,
        owner_user_id=body.owner_user_id,
        notify_user_ids=body.notify_user_ids,
    )
    return ModuleRecordSummary(
        id=record.id,
        title=record.title,
        description=record.description,
        category=record.category,
        owner=owner_name or "Não atribuído",
        status=record.status,
        updated_at=record.updated_at,
    )


@router.patch("/{module_slug}/{record_id}", response_model=ModuleRecordSummary)
async def update_record_endpoint(
    module_slug: str,
    record_id: int,
    body: ModuleRecordUpdate,
    user: Annotated[AuthenticatedUser, require_permission("module.edit")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ModuleRecordSummary:
    _validate_module(module_slug)
    updates = body.model_dump(exclude_none=True)
    result = await update_record(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        module_slug,
        record_id,
        updates,
    )
    if result is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    record, owner_name = result
    return ModuleRecordSummary(
        id=record.id,
        title=record.title,
        description=record.description,
        category=record.category,
        owner=owner_name or "Não atribuído",
        status=record.status,
        updated_at=record.updated_at,
    )


@router.delete("/{module_slug}/{record_id}", status_code=204)
async def delete_record_endpoint(
    module_slug: str,
    record_id: int,
    user: Annotated[AuthenticatedUser, require_permission("module.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    _validate_module(module_slug)
    deleted = await delete_record(session, user.company_id, user.id, module_slug, record_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
