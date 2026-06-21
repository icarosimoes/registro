"""Dispara notificações por email (Brevo) e in-app ao criar/atualizar registros."""

import asyncio
import logging
from datetime import datetime

from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.domain.settings.router import get_company_setting, get_module_recipients
from app.integrations.brevo import send_email
from app.models import Notification, NotificationPreference, User

logger = logging.getLogger(__name__)

ENTITY_TO_MODULE: dict[str, str] = {
    "occurrence": "occurrences",
    "fiscal_request": "fiscal_requests",
    "meeting": "meetings",
    "shift_report": "shift_reports",
    "procedure": "procedures",
    "module_record": "modules",
}


async def _resolve_users(session: AsyncSession, user_ids: list[int]) -> list[dict]:
    if not user_ids:
        return []
    rows = (await session.execute(
        select(User.id, User.name, User.email).where(User.id.in_(user_ids), User.active.is_(True))
    )).all()
    return [{"id": r.id, "name": r.name, "email": r.email} for r in rows]


async def _load_preferences(
    session: AsyncSession, company_id: int, user_ids: list[int], module: str,
) -> dict[int, dict[str, bool]]:
    """Returns {user_id: {"in_app": bool, "email": bool}} for the given module."""
    if not user_ids or not module:
        return {}
    rows = (
        await session.scalars(
            select(NotificationPreference).where(
                NotificationPreference.company_id == company_id,
                NotificationPreference.user_id.in_(user_ids),
                NotificationPreference.module == module,
            )
        )
    ).all()
    prefs: dict[int, dict[str, bool]] = {}
    for r in rows:
        prefs[r.user_id] = {"in_app": r.in_app, "email": r.email}
    return prefs


def _build_html(action: str, title: str, module: str, actor: str, detail: str | None = None) -> str:
    detail_block = f"<p style='color:#555'>{detail}</p>" if detail else ""
    return f"""
    <div style="font-family:sans-serif;max-width:600px;margin:0 auto">
      <div style="background:#1e3a5f;padding:20px;border-radius:8px 8px 0 0">
        <h2 style="color:white;margin:0">Registro</h2>
      </div>
      <div style="padding:20px;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 8px 8px">
        <p style="color:#1e3a5f;font-weight:700;margin:0 0 8px">{action}</p>
        <h3 style="margin:0 0 12px">{title}</h3>
        <p style="color:#555;margin:0 0 4px"><strong>Módulo:</strong> {module}</p>
        <p style="color:#555;margin:0 0 4px"><strong>Por:</strong> {actor}</p>
        {detail_block}
        <hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0"/>
        <p style="font-size:12px;color:#999">Você recebeu este email porque é responsável ou está na lista de notificados.</p>
      </div>
    </div>
    """


async def create_notification(
    session: AsyncSession,
    *,
    company_id: int,
    user_id: int,
    title: str,
    body: str | None = None,
    category: str = "info",
    entity_type: str | None = None,
    entity_id: int | None = None,
) -> Notification:
    record = Notification(
        company_id=company_id,
        user_id=user_id,
        title=title,
        body=body,
        category=category,
        entity_type=entity_type,
        entity_id=entity_id,
    )
    session.add(record)
    return record


async def notify_record_event(
    session: AsyncSession,
    *,
    company_id: int,
    actor_name: str,
    actor_email: str,
    event: str,
    title: str,
    module: str,
    owner_user_id: int | None = None,
    created_by_user_id: int | None = None,
    notify_user_ids: list[int] | None = None,
    detail: str | None = None,
    entity_type: str | None = None,
    entity_id: int | None = None,
) -> None:
    action_labels = {
        "create": "Novo registro criado",
        "update": "Registro atualizado",
        "comment": "Novo comentário",
    }
    action_label = action_labels.get(event, event)

    recipient_ids: set[int] = set()
    if owner_user_id:
        recipient_ids.add(owner_user_id)
    if notify_user_ids:
        recipient_ids.update(notify_user_ids)
    if event in ("update", "comment") and created_by_user_id:
        recipient_ids.add(created_by_user_id)

    pref_module = ENTITY_TO_MODULE.get(entity_type or "", "")
    if pref_module:
        module_recipient_ids = await get_module_recipients(
            session, company_id, pref_module,
        )
        recipient_ids.update(module_recipient_ids)

    recipients = await _resolve_users(session, list(recipient_ids))

    prefs = await _load_preferences(
        session, company_id, [r["id"] for r in recipients], pref_module,
    )

    notification_body = f"{actor_name} · {module}"
    if detail:
        notification_body += f"\n{detail}"

    created_notifications: list[tuple[Notification, dict]] = []
    for r in recipients:
        if r["email"] == actor_email:
            continue
        user_pref = prefs.get(r["id"], {"in_app": True, "email": True})
        if user_pref["in_app"]:
            notif = await create_notification(
                session,
                company_id=company_id,
                user_id=r["id"],
                title=f"{action_label}: {title}",
                body=notification_body,
                category=event,
                entity_type=entity_type,
                entity_id=entity_id,
            )
            created_notifications.append((notif, r))
    await session.flush()

    brevo = await get_company_setting(session, company_id, "brevo")
    api_key = brevo.get("api_key")
    if not api_key:
        return

    from_address = brevo.get("from_address", "noreply@registro.app")
    from_name = brevo.get("from_name", "Registro")
    html = _build_html(action_label, title, module, actor_name, detail)
    subject = f"[Registro] {action_label}: {title}"

    email_tasks: list[tuple[Notification | None, asyncio.Task]] = []  # type: ignore[type-arg]
    for r in recipients:
        if r["email"] == actor_email:
            continue
        user_pref = prefs.get(r["id"], {"in_app": True, "email": True})
        if not user_pref["email"]:
            continue
        notif_record = next(
            (n for n, ri in created_notifications if ri["id"] == r["id"]), None,
        )
        email_tasks.append((notif_record, send_email(
            api_key=api_key,
            from_address=from_address,
            from_name=from_name,
            to_email=r["email"],
            to_name=r["name"],
            subject=subject,
            html=html,
            reply_to=actor_email,
        )))

    if email_tasks:
        results = await asyncio.gather(
            *[t for _, t in email_tasks], return_exceptions=True,
        )
        now = datetime.now()
        for (notif_record, _), result in zip(email_tasks, results, strict=True):
            if notif_record and not isinstance(result, BaseException):
                notif_record.email_sent_at = now
        await session.flush()

    evolution = await get_company_setting(session, company_id, "evolution")
    evo_key = evolution.get("api_key")
    if evo_key:
        from app.integrations.evolution import send_text

        whatsapp_text = f"*{action_label}*\n{title}\n\nMódulo: {module}\nPor: {actor_name}"
        if detail:
            whatsapp_text += f"\n{detail}"

        for r in recipients:
            if r["email"] == actor_email:
                continue
            user_pref = prefs.get(r["id"], {"in_app": True, "email": True})
            if not user_pref.get("whatsapp", True):
                continue
            phone = await session.scalar(
                select(User.phone).where(User.id == r["id"])
            )
            if not phone:
                continue
            try:
                await send_text(
                    api_url=evolution["api_url"],
                    api_key=evo_key,
                    instance=evolution["instance"],
                    to=phone,
                    text=whatsapp_text,
                )
            except Exception:
                logger.warning("WhatsApp send failed for user %s", r["id"])
