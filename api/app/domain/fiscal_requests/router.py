from datetime import UTC, datetime, timedelta
from typing import Annotated
from uuid import uuid4

from fastapi import APIRouter, Depends, Header, HTTPException
from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.domain.auth.repository import AuthenticatedUser
from app.domain.fiscal_requests.schemas import (
    ChessUserResolve,
    ChessUserResolved,
    FiscalRequestCreate,
    FiscalRequestCreated,
    FiscalRequestTracking,
    FiscalRequestTrackingList,
    FiscalHistoryEntry,
    FiscalRequestListResponse,
    FiscalRequestSummary,
    FiscalRequestUpdate,
    FiscalRequestUserCreate,
)
from app.domain.occurrences.router import current_user
from app.models import AuditEvent, Company, FiscalRequest, User

router = APIRouter(tags=["fiscal-requests"])


def require_integration_key(integration_key: str | None, settings: Settings) -> None:
    if integration_key != settings.chess_hotel_integration_key:
        raise HTTPException(status_code=401, detail={"code": "invalid_integration_key"})


async def integration_company_id(session: AsyncSession, settings: Settings) -> int:
    company_id = await session.scalar(
        select(Company.id).where(
            Company.slug == settings.chess_hotel_company_slug,
            Company.status == "active",
            Company.deleted_at.is_(None),
        )
    )
    if company_id is None:
        raise HTTPException(status_code=503, detail={"code": "integration_company_not_found"})
    return company_id


async def resolve_chess_user(session: AsyncSession, company_id: int, email: str) -> User:
    normalized = email.strip().lower()
    user = await session.scalar(
        select(User).where(
            User.company_id == company_id,
            func.lower(User.email) == normalized,
            User.active.is_(True),
            User.deleted_at.is_(None),
        )
    )
    if user is None:
        raise HTTPException(status_code=404, detail={"code": "registro_user_not_found"})
    return user


async def tracking_item(
    session: AsyncSession,
    record: FiscalRequest,
    settings: Settings,
) -> FiscalRequestTracking:
    responsible = None
    if record.responsible_user_id:
        responsible = await session.scalar(select(User.name).where(User.id == record.responsible_user_id))
    history_rows = (
        await session.execute(
            select(AuditEvent, User.name)
            .join(User, User.id == AuditEvent.user_id)
            .where(
                AuditEvent.company_id == record.company_id,
                AuditEvent.entity_type == "fiscal_request",
                AuditEvent.entity_id == record.id,
            )
            .order_by(AuditEvent.created_at.asc(), AuditEvent.id.asc())
        )
    ).all()
    return FiscalRequestTracking(
        protocol=record.protocol,
        request_type=record.request_type,
        status=record.status,
        responsible=responsible,
        sla_deadline=record.sla_deadline,
        completed=record.status.casefold() in {"concluído", "concluido", "cancelado"},
        updated_at=record.updated_at,
        url=f"{settings.registro_web_url.rstrip('/')}/solicitacoes-fiscais?protocol={record.protocol}",
        history=[
            FiscalHistoryEntry(
                event=event.event_type,
                user=user_name,
                at=event.created_at,
                changes=event.diff,
            )
            for event, user_name in history_rows
        ],
    )


@router.post("/integrations/chess-hotel/users/resolve", response_model=ChessUserResolved)
async def resolve_user_from_chess_hotel(
    body: ChessUserResolve,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> ChessUserResolved:
    require_integration_key(integration_key, settings)
    company_id = await integration_company_id(session, settings)
    user = await resolve_chess_user(session, company_id, body.email)
    return ChessUserResolved(exists=True, id=user.id, name=user.name, email=user.email)


@router.post("/integrations/chess-hotel/tickets", response_model=FiscalRequestCreated)
async def create_from_chess_hotel(
    body: FiscalRequestCreate,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> FiscalRequestCreated:
    require_integration_key(integration_key, settings)
    if body.module != "solicitacoes-fiscais":
        raise HTTPException(status_code=422, detail={"code": "unsupported_module"})
    if body.hotel != settings.chess_hotel_company_slug:
        raise HTTPException(status_code=422, detail={"code": "invalid_hotel"})

    company_id = await integration_company_id(session, settings)
    registro_user = await resolve_chess_user(session, company_id, body.requester_email)
    sla_deadline = (datetime.now(UTC) + timedelta(hours=24)).replace(tzinfo=None)

    record = FiscalRequest(
        company_id=company_id,
        protocol=f"TMP-{uuid4().hex}",
        request_type=body.request_type,
        apartment=body.apartment,
        requester=body.requester,
        requester_email=registro_user.email.lower(),
        requester_user_id=registro_user.id,
        chess_user_id=body.chess_user_id,
        reservation_number=body.reservation_number,
        sla_deadline=sla_deadline,
        origin=body.origin,
        payload=body.model_dump(by_alias=True, exclude_none=True),
    )
    session.add(record)
    await session.flush()
    record.protocol = f"REG-{record.id:06d}"
    await record_event(
        session,
        company_id=company_id,
        user_id=registro_user.id,
        entity_type="fiscal_request",
        entity_id=record.id,
        event_type="create_from_chess",
        diff={"chess_user_id": body.chess_user_id, "hotel": body.hotel},
    )
    await session.commit()
    return FiscalRequestCreated(
        protocol=record.protocol,
        status=record.status,
        responsible=None,
        sla_deadline=sla_deadline,
        url=f"{settings.registro_web_url.rstrip('/')}/solicitacoes-fiscais?protocol={record.protocol}",
    )


@router.get("/integrations/chess-hotel/tickets", response_model=FiscalRequestTrackingList)
async def track_chess_hotel_tickets(
    email: str,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> FiscalRequestTrackingList:
    require_integration_key(integration_key, settings)
    company_id = await integration_company_id(session, settings)
    user = await resolve_chess_user(session, company_id, email)
    records = (
        await session.scalars(
            select(FiscalRequest)
            .where(
                FiscalRequest.company_id == company_id,
                FiscalRequest.requester_user_id == user.id,
            )
            .order_by(FiscalRequest.created_at.desc())
            .limit(50)
        )
    ).all()
    return FiscalRequestTrackingList(
        user=ChessUserResolved(exists=True, id=user.id, name=user.name, email=user.email),
        items=[await tracking_item(session, record, settings) for record in records],
    )


@router.get("/integrations/chess-hotel/tickets/{protocol}", response_model=FiscalRequestTracking)
async def track_chess_hotel_ticket(
    protocol: str,
    email: str,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> FiscalRequestTracking:
    require_integration_key(integration_key, settings)
    company_id = await integration_company_id(session, settings)
    user = await resolve_chess_user(session, company_id, email)
    record = await session.scalar(
        select(FiscalRequest).where(
            FiscalRequest.company_id == company_id,
            FiscalRequest.protocol == protocol,
            FiscalRequest.requester_user_id == user.id,
        )
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "ticket_not_found"})
    return await tracking_item(session, record, settings)


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
    await record_event(session, company_id=user.company_id, user_id=user.id,
                       entity_type="fiscal_request", entity_id=record.id, event_type="create")
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
    before = {k: str(getattr(record, k)) for k in updates}
    if record.responsible_user_id is None:
        record.responsible_user_id = user.id
        before["responsible_user_id"] = None
        updates["responsible_user_id"] = user.id
    for field, value in updates.items():
        setattr(record, field, value)
    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(session, company_id=user.company_id, user_id=user.id,
                           entity_type="fiscal_request", entity_id=record.id,
                           event_type="update", diff=diff)
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
    await record_event(session, company_id=user.company_id, user_id=user.id,
                       entity_type="fiscal_request", entity_id=record.id, event_type="delete")
    await session.delete(record)
    await session.commit()
