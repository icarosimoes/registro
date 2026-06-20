import logging

import httpx

logger = logging.getLogger(__name__)

BREVO_URL = "https://api.brevo.com/v3/smtp/email"


async def send_email(
    *,
    api_key: str,
    from_address: str,
    from_name: str,
    to_email: str,
    to_name: str | None = None,
    subject: str,
    html: str,
    reply_to: str | None = None,
) -> dict:
    payload: dict = {
        "sender": {"email": from_address, "name": from_name},
        "to": [{"email": to_email, **({"name": to_name} if to_name else {})}],
        "subject": subject,
        "htmlContent": html,
    }
    if reply_to:
        payload["replyTo"] = {"email": reply_to}

    async with httpx.AsyncClient(timeout=30) as client:
        r = await client.post(
            BREVO_URL,
            headers={"api-key": api_key, "accept": "application/json", "content-type": "application/json"},
            json=payload,
        )
    if r.status_code >= 400:
        logger.error("brevo failed: %s %s", r.status_code, r.text[:500])
        return {"error": True, "status": r.status_code}
    data = r.json()
    logger.info("email sent to=%s subject=%s id=%s", to_email, subject, data.get("messageId"))
    return data
