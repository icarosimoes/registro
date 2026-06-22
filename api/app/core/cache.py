from __future__ import annotations

import json
from collections.abc import Awaitable, Callable
from functools import wraps
from typing import Any

import structlog
from redis.asyncio import Redis

logger = structlog.get_logger()

_redis: Redis | None = None


async def start_redis(url: str) -> None:
    global _redis
    try:
        _redis = Redis.from_url(url, decode_responses=True)
        await _redis.ping()
        logger.info("redis_connected", url=url.split("@")[-1])
    except Exception:
        logger.warning("redis_unavailable", url=url.split("@")[-1])
        _redis = None


async def stop_redis() -> None:
    global _redis
    if _redis is not None:
        await _redis.aclose()
        _redis = None


def get_redis() -> Redis | None:
    return _redis


async def cache_get(key: str) -> Any | None:
    if _redis is None:
        return None
    try:
        raw = await _redis.get(key)
        if raw is not None:
            return json.loads(raw)
    except Exception:
        logger.warning("cache_get_error", key=key)
    return None


async def cache_set(key: str, value: Any, ttl: int) -> None:
    if _redis is None:
        return
    try:
        await _redis.set(key, json.dumps(value, default=str), ex=ttl)
    except Exception:
        logger.warning("cache_set_error", key=key)


async def invalidate(pattern: str) -> int:
    if _redis is None:
        return 0
    try:
        deleted = 0
        async for key in _redis.scan_iter(match=pattern, count=100):
            await _redis.delete(key)
            deleted += 1
        if deleted:
            logger.info("cache_invalidated", pattern=pattern, keys=deleted)
        return deleted
    except Exception:
        logger.warning("cache_invalidate_error", pattern=pattern)
        return 0


async def invalidate_dashboard(company_id: int) -> None:
    await invalidate(f"registro:{company_id}:dashboard:*")


def cached(
    prefix: str,
    ttl: int,
    key_args: list[str],
) -> Callable:
    def decorator(fn: Callable[..., Awaitable]) -> Callable[..., Awaitable]:
        @wraps(fn)
        async def wrapper(*args: Any, **kwargs: Any) -> Any:
            if _redis is None:
                return await fn(*args, **kwargs)

            parts = [str(kwargs.get(k, "")) for k in key_args]
            cache_key = f"registro:{':'.join(parts)}:{prefix}"

            hit = await cache_get(cache_key)
            if hit is not None:
                return hit

            result = await fn(*args, **kwargs)
            await cache_set(cache_key, result, ttl)
            return result

        return wrapper

    return decorator


async def redis_healthy() -> bool:
    if _redis is None:
        return False
    try:
        return await _redis.ping()
    except Exception:
        return False
