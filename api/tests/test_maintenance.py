"""Testes CRUD e isolamento cross-tenant para maintenance."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header

PREFIX = "/api/v1/maintenance"


def _body(title="Manutenção teste", **kw):
    return {"title": title, **kw}


@pytest.mark.asyncio
async def test_create_and_list(client):
    r = await client.post(PREFIX, json=_body(), headers=auth_header(TENANT_A))
    assert r.status_code == 201
    data = r.json()
    assert data["title"] == "Manutenção teste"
    assert data["status"] == "Em andamento"

    r = await client.get(PREFIX, headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["total"] >= 1


@pytest.mark.asyncio
async def test_get_by_id(client):
    r = await client.post(PREFIX, json=_body("Get by ID"), headers=auth_header(TENANT_A))
    mid = r.json()["id"]

    r = await client.get(f"{PREFIX}/{mid}", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["title"] == "Get by ID"


@pytest.mark.asyncio
async def test_update(client):
    r = await client.post(PREFIX, json=_body("Before"), headers=auth_header(TENANT_A))
    mid = r.json()["id"]

    r = await client.patch(
        f"{PREFIX}/{mid}", json={"title": "After"}, headers=auth_header(TENANT_A),
    )
    assert r.status_code == 200
    assert r.json()["title"] == "After"


@pytest.mark.asyncio
async def test_delete(client):
    r = await client.post(PREFIX, json=_body("To delete"), headers=auth_header(TENANT_A))
    mid = r.json()["id"]

    r = await client.delete(f"{PREFIX}/{mid}", headers=auth_header(TENANT_A))
    assert r.status_code == 204

    r = await client.get(f"{PREFIX}/{mid}", headers=auth_header(TENANT_A))
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_isolation(client):
    r = await client.post(PREFIX, json=_body("Tenant A maint"), headers=auth_header(TENANT_A))
    mid = r.json()["id"]

    r = await client.get(f"{PREFIX}/{mid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404

    r = await client.patch(
        f"{PREFIX}/{mid}", json={"title": "Hack"}, headers=auth_header(TENANT_B, 2),
    )
    assert r.status_code == 404

    r = await client.delete(f"{PREFIX}/{mid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404
