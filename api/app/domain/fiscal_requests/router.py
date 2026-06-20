from typing import Annotated

from fastapi import APIRouter, Depends, Header, HTTPException, Query, Request
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.auth import current_user
from app.core.config import Settings, get_settings
from app.core.dependencies import require_session
from app.core.rate_limit import limiter
from app.core.sla import compute_sla_status
from app.domain.auth.repository import AuthenticatedUser
from app.domain.fiscal_requests.schemas import (
    ChessUserResolve,
    ChessUserResolved,
    FiscalHistoryEntry,
    FiscalRequestCreate,
    FiscalRequestCreated,
    FiscalRequestListResponse,
    FiscalRequestSummary,
    FiscalRequestTracking,
    FiscalRequestTrackingList,
    FiscalRequestUpdate,
    FiscalRequestUserCreate,
)
from app.domain.fiscal_requests.service import (
    build_tracking_item,
    create_fiscal_request,
    create_from_chess,
    delete_fiscal_request,
    get_chess_ticket,
    get_integration_company_id,
    list_chess_tickets,
    list_fiscal_requests,
    resolve_chess_user,
    update_fiscal_request,
)

router = APIRouter(tags=["fiscal-requests"])


def _require_integration_key(integration_key: str | None, settings: Settings) -> None:
    if integration_key != settings.chess_hotel_integration_key:
        raise HTTPException(status_code=401, detail={"code": "invalid_integration_key"})


async def _require_company(session: AsyncSession, settings: Settings) -> int:
    company_id = await get_integration_company_id(session, settings)
    if company_id is None:
        raise HTTPException(status_code=503, detail={"code": "integration_company_not_found"})
    return company_id


async def _require_chess_user(session: AsyncSession, company_id: int, email: str):
    user = await resolve_chess_user(session, company_id, email)
    if user is None:
        raise HTTPException(status_code=404, detail={"code": "registro_user_not_found"})
    return user


def _to_summary(record) -> FiscalRequestSummary:
    return FiscalRequestSummary(
        id=record.id,
        protocol=record.protocol,
        request_type=record.request_type,
        title=record.title,
        apartment=record.apartment,
        requester=record.requester,
        description=record.description,
        reservation_number=record.reservation_number,
        sla_deadline=record.sla_deadline,
        sla_status=compute_sla_status(
            record.sla_deadline, record.status, record.sla_paused_at, record.sla_paused_seconds
        ),
        status=record.status,
        payload=record.payload,
        created_at=record.created_at,
        updated_at=record.updated_at,
    )


def _to_tracking(data: dict) -> FiscalRequestTracking:
    return FiscalRequestTracking(
        protocol=data["protocol"],
        request_type=data["request_type"],
        status=data["status"],
        responsible=data["responsible"],
        sla_deadline=data["sla_deadline"],
        completed=data["completed"],
        updated_at=data["updated_at"],
        url=data["url"],
        history=[
            FiscalHistoryEntry(event=h["event"], user=h["user"], at=h["at"], changes=h["changes"])
            for h in data["history"]
        ],
    )


@router.post("/integrations/chess-hotel/users/resolve", response_model=ChessUserResolved)
@limiter.limit("30/minute")
async def resolve_user_from_chess_hotel(
    request: Request,
    body: ChessUserResolve,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> ChessUserResolved:
    _require_integration_key(integration_key, settings)
    company_id = await _require_company(session, settings)
    user = await _require_chess_user(session, company_id, body.email)
    return ChessUserResolved(exists=True, id=user.id, name=user.name, email=user.email)


@router.post("/integrations/chess-hotel/tickets", response_model=FiscalRequestCreated)
@limiter.limit("30/minute")
async def create_from_chess_hotel(
    request: Request,
    body: FiscalRequestCreate,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> FiscalRequestCreated:
    _require_integration_key(integration_key, settings)
    if body.module != "solicitacoes-fiscais":
        raise HTTPException(status_code=422, detail={"code": "unsupported_module"})
    if body.hotel != settings.chess_hotel_company_slug:
        raise HTTPException(status_code=422, detail={"code": "invalid_hotel"})

    company_id = await _require_company(session, settings)
    registro_user = await _require_chess_user(session, company_id, body.requester_email)

    record = await create_from_chess(
        session,
        settings,
        company_id=company_id,
        registro_user=registro_user,
        request_type=body.request_type,
        apartment=body.apartment,
        requester=body.requester,
        chess_user_id=body.chess_user_id,
        reservation_number=body.reservation_number,
        origin=body.origin,
        payload=body.model_dump(by_alias=True, exclude_none=True),
    )
    return FiscalRequestCreated(
        protocol=record.protocol,
        status=record.status,
        responsible=None,
        sla_deadline=record.sla_deadline,
        url=f"{settings.registro_web_url.rstrip('/')}/solicitacoes-fiscais?protocol={record.protocol}",
    )


@router.get("/integrations/chess-hotel/tickets", response_model=FiscalRequestTrackingList)
async def track_chess_hotel_tickets(
    email: str,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> FiscalRequestTrackingList:
    _require_integration_key(integration_key, settings)
    company_id = await _require_company(session, settings)
    user = await _require_chess_user(session, company_id, email)
    records = await list_chess_tickets(session, company_id, user.id)
    return FiscalRequestTrackingList(
        user=ChessUserResolved(exists=True, id=user.id, name=user.name, email=user.email),
        items=[_to_tracking(await build_tracking_item(session, r, settings)) for r in records],
    )


@router.get(
    "/integrations/chess-hotel/tickets/{protocol}", response_model=FiscalRequestTracking
)
async def track_chess_hotel_ticket(
    protocol: str,
    email: str,
    session: Annotated[AsyncSession, Depends(require_session)],
    settings: Annotated[Settings, Depends(get_settings)],
    integration_key: Annotated[str | None, Header(alias="X-Registro-Key")] = None,
) -> FiscalRequestTracking:
    _require_integration_key(integration_key, settings)
    company_id = await _require_company(session, settings)
    user = await _require_chess_user(session, company_id, email)
    record = await get_chess_ticket(session, company_id, protocol, user.id)
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "ticket_not_found"})
    return _to_tracking(await build_tracking_item(session, record, settings))


@router.get("/fiscal-requests", response_model=FiscalRequestListResponse)
async def list_fiscal_requests_endpoint(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> FiscalRequestListResponse:
    records, total = await list_fiscal_requests(
        session, user.company_id, page, page_size, search
    )
    return FiscalRequestListResponse(
        items=[_to_summary(item) for item in records],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.post("/fiscal-requests", response_model=FiscalRequestSummary, status_code=201)
async def create_fiscal_request_endpoint(
    body: FiscalRequestUserCreate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> FiscalRequestSummary:
    record = await create_fiscal_request(
        session,
        user.company_id,
        user.id,
        request_type=body.request_type,
        title=body.title,
        apartment=body.apartment,
        requester=body.requester,
        description=body.description,
        status=body.status,
        payload=body.payload,
    )
    return _to_summary(record)


@router.patch("/fiscal-requests/{request_id}", response_model=FiscalRequestSummary)
async def update_fiscal_request_endpoint(
    request_id: int,
    body: FiscalRequestUpdate,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> FiscalRequestSummary:
    updates = body.model_dump(exclude_none=True)
    record = await update_fiscal_request(
        session, user.company_id, user.id, request_id, updates
    )
    if record is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return _to_summary(record)


@router.delete("/fiscal-requests/{request_id}", status_code=204)
async def delete_fiscal_request_endpoint(
    request_id: int,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_fiscal_request(session, user.company_id, user.id, request_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
