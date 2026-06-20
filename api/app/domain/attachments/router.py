import re
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query, UploadFile
from fastapi.responses import StreamingResponse
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.auth import current_user
from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.storage import download_file
from app.domain.attachments.schemas import AttachmentListResponse, AttachmentOut
from app.domain.attachments.service import (
    create_attachment,
    delete_attachment,
    get_attachment,
    list_attachments,
)
from app.domain.auth.repository import AuthenticatedUser

_UNSAFE_FILENAME_RE = re.compile(r'[^\w\s\-\.\(\)]', re.UNICODE)


def _sanitize_filename(name: str) -> str:
    name = name.rsplit("/", 1)[-1].rsplit("\\", 1)[-1]
    name = _UNSAFE_FILENAME_RE.sub("_", name).strip(". ")
    return name or "download"

router = APIRouter(tags=["attachments"])


@router.post("/attachments", response_model=AttachmentOut, status_code=201)
async def upload_attachment(
    file: UploadFile,
    entity_type: Annotated[str, Query()],
    entity_id: Annotated[int, Query(ge=1)],
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
) -> AttachmentOut:
    data = await file.read()
    result = await create_attachment(
        session,
        user.company_id,
        user.id,
        entity_type=entity_type,
        entity_id=entity_id,
        filename=file.filename or "unnamed",
        content_type=file.content_type or "application/octet-stream",
        data=data,
        max_per_entity=settings.attachment_max_per_entity,
    )
    if isinstance(result, str):
        raise HTTPException(status_code=422, detail={"code": "invalid_file", "message": result})
    return AttachmentOut(
        id=result.id,
        entity_type=result.entity_type,
        entity_id=result.entity_id,
        filename=result.filename,
        content_type=result.content_type,
        size_bytes=result.size_bytes,
        uploaded_by_user_id=result.uploaded_by_user_id,
        created_at=result.created_at,
    )


@router.get("/attachments", response_model=AttachmentListResponse)
async def list_attachments_endpoint(
    entity_type: Annotated[str, Query()],
    entity_id: Annotated[int, Query(ge=1)],
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> AttachmentListResponse:
    records, total = await list_attachments(
        session, user.company_id, entity_type, entity_id,
    )
    return AttachmentListResponse(
        items=[
            AttachmentOut(
                id=r.id,
                entity_type=r.entity_type,
                entity_id=r.entity_id,
                filename=r.filename,
                content_type=r.content_type,
                size_bytes=r.size_bytes,
                uploaded_by_user_id=r.uploaded_by_user_id,
                created_at=r.created_at,
            )
            for r in records
        ],
        total=total,
    )


@router.get("/attachments/{attachment_id}/download")
async def download_attachment(
    attachment_id: int,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> StreamingResponse:
    record = await get_attachment(session, user.company_id, attachment_id)
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    buf, content_type = download_file(record.storage_key)
    safe_name = _sanitize_filename(record.filename)
    return StreamingResponse(
        buf,
        media_type=content_type,
        headers={
            "Content-Disposition": f'attachment; filename="{safe_name}"',
            "X-Content-Type-Options": "nosniff",
            "Content-Security-Policy": "default-src 'none'; style-src 'unsafe-inline'",
            "X-Frame-Options": "DENY",
            "Cache-Control": "no-store",
        },
    )


@router.delete("/attachments/{attachment_id}", status_code=204)
async def delete_attachment_endpoint(
    attachment_id: int,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_attachment(
        session, user.company_id, attachment_id,
    )
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
