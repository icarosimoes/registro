"""Integração com Evolution API para envio de mensagens via WhatsApp."""

import httpx
import structlog

logger = structlog.get_logger()


async def send_text(
    *,
    api_url: str,
    api_key: str,
    instance: str,
    to: str,
    text: str,
) -> dict | None:
    url = f"{api_url.rstrip('/')}/message/sendText/{instance}"
    headers = {"apikey": api_key, "Content-Type": "application/json"}
    payload = {
        "number": _normalize_phone(to),
        "text": text,
    }
    try:
        async with httpx.AsyncClient(timeout=15) as client:
            resp = await client.post(url, json=payload, headers=headers)
            resp.raise_for_status()
            return resp.json()
    except httpx.HTTPStatusError as exc:
        logger.error(
            "evolution_http_error",
            status=exc.response.status_code,
            body=exc.response.text,
        )
        return None
    except httpx.RequestError as exc:
        logger.error("evolution_request_error", error=str(exc))
        return None


async def send_media(
    *,
    api_url: str,
    api_key: str,
    instance: str,
    to: str,
    media_url: str,
    media_type: str = "document",
    caption: str | None = None,
    filename: str | None = None,
) -> dict | None:
    url = f"{api_url.rstrip('/')}/message/sendMedia/{instance}"
    headers = {"apikey": api_key, "Content-Type": "application/json"}
    payload = {
        "number": _normalize_phone(to),
        "mediatype": media_type,
        "media": media_url,
    }
    if caption:
        payload["caption"] = caption
    if filename:
        payload["fileName"] = filename
    try:
        async with httpx.AsyncClient(timeout=30) as client:
            resp = await client.post(url, json=payload, headers=headers)
            resp.raise_for_status()
            return resp.json()
    except httpx.HTTPStatusError as exc:
        logger.error(
            "evolution_http_error",
            status=exc.response.status_code,
            body=exc.response.text,
        )
        return None
    except httpx.RequestError as exc:
        logger.error("evolution_request_error", error=str(exc))
        return None


async def check_connection(
    *,
    api_url: str,
    api_key: str,
    instance: str,
) -> dict:
    url = f"{api_url.rstrip('/')}/instance/connectionState/{instance}"
    headers = {"apikey": api_key}
    try:
        async with httpx.AsyncClient(timeout=10) as client:
            resp = await client.get(url, headers=headers)
            resp.raise_for_status()
            return resp.json()
    except httpx.HTTPStatusError as exc:
        return {"state": "error", "status_code": exc.response.status_code}
    except httpx.RequestError as exc:
        return {"state": "error", "detail": str(exc)}


def _normalize_phone(phone: str) -> str:
    digits = "".join(c for c in phone if c.isdigit())
    if digits.startswith("0"):
        digits = "55" + digits[1:]
    elif not digits.startswith("55") and len(digits) <= 11:
        digits = "55" + digits
    return digits
