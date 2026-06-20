"""Dispara notificações por email (Brevo) e in-app ao criar/atualizar registros."""

import asyncio
import logging

from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.domain.settings.router import get_company_setting
from app.integrations.brevo import send_email
from app.models import Notification, User

logger = logging.getLogger(__name__)


async def _resolve_users(session: AsyncSession, user_ids: list[int]) -> list[dict]:
    if not user_ids:
        return []
    rows = (await session.execute(
        select(User.id, User.name, User.email).where(User.id.in_(user_ids), User.active.is_(True))
    )).all()
    return [{"id": r.id, "name": r.name, "email": r.email} for r in rows]


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
) -> None:
    session.add(Notification(
        company_id=company_id,
        user_id=user_id,
        title=title,
        body=body,
        category=category,
        entity_type=entity_type,
        entity_id=entity_id,
    ))


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

    recipients = await _resolve_users(session, list(recipient_ids))

    notification_body = f"{actor_name} · {module}"
    if detail:
        notification_body += f"\n{detail}"
    for r in recipients:
        if r["email"] == actor_email:
            continue
        await create_notification(
            session,
            company_id=company_id,
            user_id=r["id"],
            title=f"{action_label}: {title}",
            body=notification_body,
            category=event,
            entity_type=entity_type,
            entity_id=entity_id,
        )
    await session.flush()

    brevo = await get_company_setting(session, company_id, "brevo")
    api_key = brevo.get("api_key")
    if not api_key:
        return

    from_address = brevo.get("from_address", "noreply@registro.app")
    from_name = brevo.get("from_name", "Registro")
    html = _build_html(action_label, title, module, actor_name, detail)
    subject = f"[Registro] {action_label}: {title}"

    email_tasks = []
    for r in recipients:
        if r["email"] == actor_email:
            continue
        email_tasks.append(send_email(
            api_key=api_key,
            from_address=from_address,
            from_name=from_name,
            to_email=r["email"],
            to_name=r["name"],
            subject=subject,
            html=html,
            reply_to=actor_email,
        ))

    if email_tasks:
        await asyncio.gather(*email_tasks, return_exceptions=True)
