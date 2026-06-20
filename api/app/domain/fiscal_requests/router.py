from typing import Annotated
from uuid import uuid4

from fastapi import APIRouter, Depends, Header, HTTPException
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.domain.auth.repository import AuthenticatedUser
from app.domain.fiscal_requests.schemas import (
    FiscalRequestCreate,
    FiscalRequestCreated,
    FiscalRequestListResponse,
    FiscalRequestSummary,
    FiscalRequestUpdate,
    FiscalRequestUserCreate,
)
from app.domain.occurrences.router import current_user
from app.models import Company, FiscalRequest

router = APIRouter(tags=["fiscal-requests"])


@router.post("/integrations/chess-hotel/tickets", response_model=FiscalRequestCreated)
async def create_from_chess_hotel(
    body: FiscalRequestCreate,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> FiscalRequestCreated:
    if integration_key != settings.chess_hotel_integration_key:
        raise HTTPException(status_code=401, detail={"code": "invalid_integration_key"})
    if body.module != "solicitacoes-fiscais":
        raise HTTPException(status_code=422, detail={"code": "unsupported_module"})

    company_id = await session.scalar(
        select(Company.id).where(
            Company.slug == settings.chess_hotel_company_slug,
            Company.status == "active",
            Company.deleted_at.is_(None),
        )
    )
    if company_id is None:
        raise HTTPException(status_code=503, detail={"code": "integration_company_not_found"})

    record = FiscalRequest(
        company_id=company_id,
        protocol=f"TMP-{uuid4().hex}",
        request_type=body.request_type,
        apartment=body.apartment,
        requester=body.requester,
        origin=body.origin,
        payload=body.model_dump(by_alias=True, exclude_none=True),
    )
    session.add(record)
    await session.flush()
    record.protocol = f"REG-{record.id:06d}"
    await session.commit()
    return FiscalRequestCreated(protocol=record.protocol)


@router.get("/fiscal-requests", response_model=FiscalRequestListResponse)
async def list_fiscal_requests(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> FiscalRequestListResponse:
    records = (
        await session.scalars(
            select(FiscalRequest)
            .where(FiscalRequest.company_id == user.company_id)
            .order_by(FiscalRequest.created_at.desc(), FiscalRequest.id.desc())
        )
    ).all()
    return FiscalRequestListResponse(
        items=[
            FiscalRequestSummary(
                id=item.id,
                protocol=item.protocol,
                request_type=item.request_type,
                apartment=item.apartment,
                requester=item.requester,
                status=item.status,
                payload=item.payload,
                created_at=item.created_at,
                updated_at=item.updated_at,
            )
            for item in records
        ]
    )


@router.post("/fiscal-requests", response_model=FiscalRequestSummary, status_code=201)
async def create_fiscal_request(
    body: FiscalRequestUserCreate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> FiscalRequestSummary:
    record = FiscalRequest(
        company_id=user.company_id,
        protocol=f"TMP-{uuid4().hex}",
        request_type=body.request_type,
        title=body.title,
        apartment=body.apartment,
        requester=body.requester,
        description=body.description,
        origin="registro",
        status=body.status,
        payload=body.payload,
    )
    session.add(record)
    await session.flush()
    record.protocol = f"REG-{record.id:06d}"
    await session.commit()
    await session.refresh(record)
    return FiscalRequestSummary(
        id=record.id,
        protocol=record.protocol,
        request_type=record.request_type,
        apartment=record.apartment,
        requester=record.requester,
        status=record.status,
        payload=record.payload,
        created_at=record.created_at,
        updated_at=record.updated_at,
    )


@router.patch("/fiscal-requests/{request_id}", response_model=FiscalRequestSummary)
async def update_fiscal_request(
    request_id: int,
    body: FiscalRequestUpdate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> FiscalRequestSummary:
    record = await session.scalar(
        select(FiscalRequest).where(
            FiscalRequest.id == request_id,
            FiscalRequest.company_id == user.company_id,
        )
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})

    updates = body.model_dump(exclude_none=True)
    for field, value in updates.items():
        setattr(record, field, value)
    await session.commit()
    await session.refresh(record)
    return FiscalRequestSummary(
        id=record.id,
        protocol=record.protocol,
        request_type=record.request_type,
        apartment=record.apartment,
        requester=record.requester,
        status=record.status,
        payload=record.payload,
        created_at=record.created_at,
        updated_at=record.updated_at,
    )


@router.delete("/fiscal-requests/{request_id}", status_code=204)
async def delete_fiscal_request(
    request_id: int,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    record = await session.scalar(
        select(FiscalRequest).where(
            FiscalRequest.id == request_id,
            FiscalRequest.company_id == user.company_id,
        )
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    await session.delete(record)
    await session.commit()
