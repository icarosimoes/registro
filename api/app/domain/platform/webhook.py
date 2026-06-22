from datetime import UTC, datetime
from typing import Any

import structlog
from sqlalchemy import select
from sqlalchemy.exc import IntegrityError
from sqlalchemy.ext.asyncio import AsyncSession

from app.models import Invoice, PlatformAuditLog, Subscription, WebhookEvent

logger = structlog.get_logger()

ASAAS_STATUS_MAP = {
    "CONFIRMED": "paid",
    "RECEIVED": "paid",
    "OVERDUE": "overdue",
    "REFUNDED": "refunded",
}


async def handle_asaas_webhook(
    session: AsyncSession,
    event_data: dict[str, Any],
) -> dict[str, Any]:
    event_type = event_data.get("event", "unknown")
    payment = event_data.get("payment", {})
    external_id = payment.get("id") or event_data.get("id") or ""

    if not external_id:
        return {"status": "ignored", "reason": "no_event_id"}

    we = WebhookEvent(
        provider="asaas",
        external_id=str(external_id),
        event_type=event_type,
        payload=event_data,
    )
    session.add(we)
    try:
        await session.flush()
    except IntegrityError:
        await session.rollback()
        return {"status": "already_processed", "event_type": event_type}

    now = datetime.now(UTC).replace(tzinfo=None)
    action_taken = "none"

    if event_type == "PAYMENT_CONFIRMED" or event_type == "PAYMENT_RECEIVED":
        action_taken = await _handle_payment_confirmed(session, payment, now)
    elif event_type == "PAYMENT_OVERDUE":
        action_taken = await _handle_payment_overdue(session, payment, now)
    elif event_type == "SUBSCRIPTION_DELETED":
        action_taken = await _handle_subscription_deleted(session, event_data, now)

    we.processed_at = now
    session.add(
        PlatformAuditLog(
            platform_user_id=None,
            action=f"webhook.asaas.{event_type}",
            target_type="webhook_event",
            target_id=str(we.id),
            payload={"external_id": external_id, "action_taken": action_taken},
            created_at=now,
        )
    )
    await session.commit()
    return {"status": "processed", "event_type": event_type, "action": action_taken}


async def _handle_payment_confirmed(
    session: AsyncSession,
    payment: dict,
    now: datetime,
) -> str:
    asaas_id = payment.get("id")
    if not asaas_id:
        return "no_payment_id"
    invoice = await session.scalar(select(Invoice).where(Invoice.external_payment_id == asaas_id))
    if invoice is None:
        return "invoice_not_found"
    invoice.status = "paid"
    invoice.payment_date = now.date()
    return "invoice_paid"


async def _handle_payment_overdue(
    session: AsyncSession,
    payment: dict,
    now: datetime,
) -> str:
    asaas_id = payment.get("id")
    if not asaas_id:
        return "no_payment_id"
    invoice = await session.scalar(select(Invoice).where(Invoice.external_payment_id == asaas_id))
    if invoice:
        invoice.status = "overdue"
    sub_id = payment.get("subscription")
    if sub_id:
        sub = await session.scalar(
            select(Subscription).where(Subscription.billing_provider_subscription_id == sub_id)
        )
        if sub and not sub.past_due_since:
            sub.past_due_since = now
            if sub.status == "active":
                sub.status = "past_due"
    return "payment_overdue_processed"


async def _handle_subscription_deleted(
    session: AsyncSession,
    event_data: dict,
    now: datetime,
) -> str:
    sub_id = event_data.get("subscription", {}).get("id") or event_data.get("id")
    if not sub_id:
        return "no_subscription_id"
    sub = await session.scalar(
        select(Subscription).where(Subscription.billing_provider_subscription_id == str(sub_id))
    )
    if sub is None:
        return "subscription_not_found"
    sub.status = "canceled"
    return "subscription_canceled"
