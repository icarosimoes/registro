from datetime import UTC, datetime, timedelta
from uuid import uuid4

from sqlalchemy import func, or_, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.core.config import Settings
from app.integrations.notifications import create_notification
from app.models import AuditEvent, Company, FiscalRequest, User

SLA_HOURS = 24


def compute_sla_status(deadline: datetime | None, status: str) -> str | None:
    if deadline is None:
        return None
    if status.casefold() in {"concluído", "concluido", "cancelado"}:
        return "completed"
    now = datetime.now(UTC)
    dl = deadline.replace(tzinfo=UTC) if deadline.tzinfo is None else deadline
    remaining = (dl - now).total_seconds()
    if remaining <= 0:
        return "overdue"
    if remaining <= 3600 * 4:
        return "warning"
    return "on_time"


async def get_integration_company_id(session: AsyncSession, settings: Settings) -> int | None:
    return await session.scalar(
        select(Company.id).where(
            Company.slug == settings.chess_hotel_company_slug,
            Company.status == "active",
            Company.deleted_at.is_(None),
        )
    )


async def resolve_chess_user(session: AsyncSession, company_id: int, email: str) -> User | None:
    normalized = email.strip().lower()
    return await session.scalar(
        select(User).where(
            User.company_id == company_id,
            func.lower(User.email) == normalized,
            User.active.is_(True),
            User.deleted_at.is_(None),
        )
    )


async def build_tracking_item(
    session: AsyncSession,
    record: FiscalRequest,
    settings: Settings,
) -> dict:
    responsible = None
    if record.responsible_user_id:
        responsible = await session.scalar(
            select(User.name).where(User.id == record.responsible_user_id)
        )
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
    return {
        "protocol": record.protocol,
        "request_type": record.request_type,
        "status": record.status,
        "responsible": responsible,
        "sla_deadline": record.sla_deadline,
        "completed": record.status.casefold() in {"concluído", "concluido", "cancelado"},
        "updated_at": record.updated_at,
        "url": (
            f"{settings.registro_web_url.rstrip('/')}"
            f"/solicitacoes-fiscais?protocol={record.protocol}"
        ),
        "history": [
            {
                "event": event.event_type,
                "user": user_name,
                "at": event.created_at,
                "changes": event.diff,
            }
            for event, user_name in history_rows
        ],
    }


async def create_from_chess(
    session: AsyncSession,
    settings: Settings,
    *,
    company_id: int,
    registro_user: User,
    request_type: str,
    apartment: str | None,
    requester: str,
    chess_user_id: str,
    reservation_number: str | None,
    origin: str,
    payload: dict,
) -> FiscalRequest:
    sla_deadline = (datetime.now(UTC) + timedelta(hours=24)).replace(tzinfo=None)
    record = FiscalRequest(
        company_id=company_id,
        protocol=f"TMP-{uuid4().hex}",
        request_type=request_type,
        apartment=apartment,
        requester=requester,
        requester_email=registro_user.email.lower(),
        requester_user_id=registro_user.id,
        chess_user_id=chess_user_id,
        reservation_number=reservation_number,
        sla_deadline=sla_deadline,
        origin=origin,
        payload=payload,
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
        diff={"chess_user_id": chess_user_id, "hotel": settings.chess_hotel_company_slug},
    )
    active_users = (
        await session.scalars(
            select(User.id).where(
                User.company_id == company_id,
                User.active.is_(True),
                User.deleted_at.is_(None),
                User.id != registro_user.id,
            )
        )
    ).all()
    title_text = request_type
    if apartment:
        title_text += f" · UH {apartment}"
    for uid in active_users:
        await create_notification(
            session,
            company_id=company_id,
            user_id=uid,
            title=f"Nova solicitação fiscal do Chess Hotel: {title_text}",
            body=f"Solicitante: {requester}\nProtocolo: {record.protocol}",
            category="create",
            entity_type="fiscal_request",
            entity_id=record.id,
        )
    await session.commit()
    return record


async def list_fiscal_requests(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    search: str | None = None,
) -> tuple[list[FiscalRequest], int]:
    filters = [FiscalRequest.company_id == company_id]
    if search:
        pattern = f"%{search.strip()}%"
        filters.append(
            or_(
                FiscalRequest.protocol.ilike(pattern),
                FiscalRequest.requester.ilike(pattern),
                FiscalRequest.request_type.ilike(pattern),
            )
        )
    total = await session.scalar(select(func.count(FiscalRequest.id)).where(*filters)) or 0
    records = (
        await session.scalars(
            select(FiscalRequest)
            .where(*filters)
            .order_by(FiscalRequest.created_at.desc(), FiscalRequest.id.desc())
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return list(records), total


async def create_fiscal_request(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    *,
    request_type: str,
    title: str,
    apartment: str | None,
    requester: str,
    description: str | None,
    status: str,
    payload: dict,
) -> FiscalRequest:
    sla_deadline = (datetime.now(UTC) + timedelta(hours=SLA_HOURS)).replace(tzinfo=None)
    record = FiscalRequest(
        company_id=company_id,
        protocol=f"TMP-{uuid4().hex}",
        request_type=request_type,
        title=title,
        apartment=apartment,
        requester=requester,
        description=description,
        origin="registro",
        status=status,
        sla_deadline=sla_deadline,
        payload=payload,
    )
    session.add(record)
    await session.flush()
    record.protocol = f"REG-{record.id:06d}"
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="fiscal_request",
        entity_id=record.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(record)
    return record


async def update_fiscal_request(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    request_id: int,
    updates: dict,
) -> FiscalRequest | None:
    record = await session.scalar(
        select(FiscalRequest).where(
            FiscalRequest.id == request_id,
            FiscalRequest.company_id == company_id,
        )
    )
    if record is None:
        return None
    before = {k: str(getattr(record, k)) for k in updates}
    if record.responsible_user_id is None:
        record.responsible_user_id = user_id
        before["responsible_user_id"] = None
        updates["responsible_user_id"] = user_id
    for field, value in updates.items():
        setattr(record, field, value)
    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="fiscal_request",
            entity_id=record.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    await session.refresh(record)
    return record


async def delete_fiscal_request(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    request_id: int,
) -> bool:
    record = await session.scalar(
        select(FiscalRequest).where(
            FiscalRequest.id == request_id,
            FiscalRequest.company_id == company_id,
        )
    )
    if record is None:
        return False
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="fiscal_request",
        entity_id=record.id,
        event_type="delete",
    )
    await session.delete(record)
    await session.commit()
    return True


async def list_chess_tickets(
    session: AsyncSession,
    company_id: int,
    user_id: int,
) -> list[FiscalRequest]:
    records = (
        await session.scalars(
            select(FiscalRequest)
            .where(
                FiscalRequest.company_id == company_id,
                FiscalRequest.requester_user_id == user_id,
            )
            .order_by(FiscalRequest.created_at.desc())
            .limit(50)
        )
    ).all()
    return list(records)


async def get_chess_ticket(
    session: AsyncSession,
    company_id: int,
    protocol: str,
    user_id: int,
) -> FiscalRequest | None:
    return await session.scalar(
        select(FiscalRequest).where(
            FiscalRequest.company_id == company_id,
            FiscalRequest.protocol == protocol,
            FiscalRequest.requester_user_id == user_id,
        )
    )
