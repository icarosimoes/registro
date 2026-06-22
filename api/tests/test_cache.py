from unittest.mock import AsyncMock, patch

import pytest

import app.core.cache as cache_mod
from app.core.cache import (
    cache_get,
    cache_set,
    cached,
    get_redis,
    invalidate,
    invalidate_dashboard,
    redis_healthy,
    start_redis,
    stop_redis,
)


@pytest.fixture(autouse=True)
def _reset_redis():
    original = cache_mod._redis
    cache_mod._redis = None
    yield
    cache_mod._redis = original


class TestRedisLifecycle:
    async def test_start_redis_success(self):
        mock_redis = AsyncMock()
        mock_redis.ping = AsyncMock(return_value=True)
        with patch("app.core.cache.Redis.from_url", return_value=mock_redis):
            await start_redis("redis://localhost:6379")
        assert get_redis() is mock_redis

    async def test_start_redis_failure(self):
        with patch("app.core.cache.Redis.from_url", side_effect=Exception("conn refused")):
            await start_redis("redis://localhost:6379")
        assert get_redis() is None

    async def test_stop_redis(self):
        mock_redis = AsyncMock()
        cache_mod._redis = mock_redis
        await stop_redis()
        assert get_redis() is None
        mock_redis.aclose.assert_awaited_once()

    async def test_stop_redis_when_none(self):
        await stop_redis()
        assert get_redis() is None


class TestCacheOps:
    async def test_cache_get_returns_none_without_redis(self):
        assert await cache_get("key") is None

    async def test_cache_set_noop_without_redis(self):
        await cache_set("key", {"a": 1}, 60)

    async def test_cache_get_hit(self):
        mock_redis = AsyncMock()
        mock_redis.get = AsyncMock(return_value='{"a": 1}')
        cache_mod._redis = mock_redis
        result = await cache_get("key")
        assert result == {"a": 1}

    async def test_cache_get_miss(self):
        mock_redis = AsyncMock()
        mock_redis.get = AsyncMock(return_value=None)
        cache_mod._redis = mock_redis
        assert await cache_get("key") is None

    async def test_cache_get_handles_error(self):
        mock_redis = AsyncMock()
        mock_redis.get = AsyncMock(side_effect=Exception("timeout"))
        cache_mod._redis = mock_redis
        assert await cache_get("key") is None

    async def test_cache_set_stores_value(self):
        mock_redis = AsyncMock()
        cache_mod._redis = mock_redis
        await cache_set("key", {"a": 1}, 60)
        mock_redis.set.assert_awaited_once()

    async def test_cache_set_handles_error(self):
        mock_redis = AsyncMock()
        mock_redis.set = AsyncMock(side_effect=Exception("timeout"))
        cache_mod._redis = mock_redis
        await cache_set("key", {"a": 1}, 60)


class TestInvalidate:
    async def test_invalidate_without_redis(self):
        assert await invalidate("pattern:*") == 0

    async def test_invalidate_deletes_keys(self):
        mock_redis = AsyncMock()

        async def fake_scan_iter(match, count):
            for key in ["key1", "key2"]:
                yield key

        mock_redis.scan_iter = fake_scan_iter
        mock_redis.delete = AsyncMock()
        cache_mod._redis = mock_redis

        deleted = await invalidate("pattern:*")
        assert deleted == 2

    async def test_invalidate_handles_error(self):
        mock_redis = AsyncMock()
        mock_redis.scan_iter = AsyncMock(side_effect=Exception("err"))
        cache_mod._redis = mock_redis
        assert await invalidate("pattern:*") == 0

    async def test_invalidate_dashboard(self):
        mock_redis = AsyncMock()

        async def fake_scan_iter(match, count):
            yield "registro:1:dashboard:summary"

        mock_redis.scan_iter = fake_scan_iter
        mock_redis.delete = AsyncMock()
        cache_mod._redis = mock_redis

        await invalidate_dashboard(1)
        mock_redis.delete.assert_awaited()


class TestCachedDecorator:
    async def test_calls_fn_without_redis(self):
        @cached(prefix="test", ttl=60, key_args=["company_id"])
        async def my_fn(company_id: int):
            return {"value": company_id}

        result = await my_fn(company_id=1)
        assert result == {"value": 1}

    async def test_returns_cached_value_on_hit(self):
        mock_redis = AsyncMock()
        mock_redis.get = AsyncMock(return_value='{"cached": true}')
        cache_mod._redis = mock_redis

        call_count = 0

        @cached(prefix="test", ttl=60, key_args=["company_id"])
        async def my_fn(company_id: int):
            nonlocal call_count
            call_count += 1
            return {"cached": False}

        result = await my_fn(company_id=1)
        assert result == {"cached": True}
        assert call_count == 0

    async def test_caches_result_on_miss(self):
        mock_redis = AsyncMock()
        mock_redis.get = AsyncMock(return_value=None)
        cache_mod._redis = mock_redis

        @cached(prefix="test", ttl=60, key_args=["company_id"])
        async def my_fn(company_id: int):
            return {"value": 42}

        result = await my_fn(company_id=1)
        assert result == {"value": 42}
        mock_redis.set.assert_awaited_once()


class TestRedisHealthy:
    async def test_healthy_without_redis(self):
        assert await redis_healthy() is False

    async def test_healthy_with_redis(self):
        mock_redis = AsyncMock()
        mock_redis.ping = AsyncMock(return_value=True)
        cache_mod._redis = mock_redis
        assert await redis_healthy() is True

    async def test_healthy_with_error(self):
        mock_redis = AsyncMock()
        mock_redis.ping = AsyncMock(side_effect=Exception("down"))
        cache_mod._redis = mock_redis
        assert await redis_healthy() is False
