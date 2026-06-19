import pytest
from fastapi import HTTPException
from httpx import ASGITransport, AsyncClient

from app.core.dependencies import require_session
from app.main import app


@pytest.mark.asyncio
async def test_health() -> None:
    transport = ASGITransport(app=app)
    async with AsyncClient(transport=transport, base_url="http://test") as client:
        response = await client.get("/api/v1/health")

    assert response.status_code == 200
    assert response.json()["status"] == "ok"


@pytest.mark.asyncio
async def test_login_reports_unconfigured_database() -> None:
    async def unavailable_session():
        raise HTTPException(
            status_code=503,
            detail={"code": "database_unavailable", "message": "Banco não configurado"},
        )

    app.dependency_overrides[require_session] = unavailable_session
    transport = ASGITransport(app=app)
    try:
        async with AsyncClient(transport=transport, base_url="http://test") as client:
            response = await client.post(
                "/api/v1/auth/login",
                json={"email": "usuario@example.com", "password": "senha"},
            )
    finally:
        app.dependency_overrides.clear()

    assert response.status_code == 503
    assert response.json()["detail"]["code"] == "database_unavailable"
