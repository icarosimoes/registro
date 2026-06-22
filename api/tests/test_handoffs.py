"""Testes CRUD e isolamento cross-tenant para handoffs."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header

PREFIX = "/api/v1/handoffs"


def _body(title="Pendência teste", **kw):
    return {"title": title, **kw}


@pytest.mark.asyncio
async def test_create_and_list(client):
    r = await client.post(PREFIX, json=_body(), headers=auth_header(TENANT_A))
    assert r.status_code == 201
    data = r.json()
    assert data["title"] == "Pendência teste"
    assert data["status"] == "pendente"

    r = await client.get(PREFIX, headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["total"] >= 1


@pytest.mark.asyncio
async def test_get_by_id(client):
    r = await client.post(PREFIX, json=_body("Get by ID"), headers=auth_header(TENANT_A))
    hid = r.json()["id"]

    r = await client.get(f"{PREFIX}/{hid}", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["title"] == "Get by ID"


@pytest.mark.asyncio
async def test_update(client):
    r = await client.post(PREFIX, json=_body("Before"), headers=auth_header(TENANT_A))
    hid = r.json()["id"]

    r = await client.patch(
        f"{PREFIX}/{hid}", json={"title": "After"}, headers=auth_header(TENANT_A),
    )
    assert r.status_code == 200
    assert r.json()["title"] == "After"


@pytest.mark.asyncio
async def test_mark_read(client):
    r = await client.post(PREFIX, json=_body("Read me"), headers=auth_header(TENANT_A))
    hid = r.json()["id"]

    r = await client.post(f"{PREFIX}/{hid}/read", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["read_at"] is not None


@pytest.mark.asyncio
async def test_resolve(client):
    r = await client.post(PREFIX, json=_body("Resolve me"), headers=auth_header(TENANT_A))
    hid = r.json()["id"]

    r = await client.post(
        f"{PREFIX}/{hid}/resolve",
        json={"resolution_notes": "Resolvido"},
        headers=auth_header(TENANT_A),
    )
    assert r.status_code == 200
    assert r.json()["status"] == "resolvido"


@pytest.mark.asyncio
async def test_delete(client):
    r = await client.post(PREFIX, json=_body("To delete"), headers=auth_header(TENANT_A))
    hid = r.json()["id"]

    r = await client.delete(f"{PREFIX}/{hid}", headers=auth_header(TENANT_A))
    assert r.status_code == 204

    r = await client.get(f"{PREFIX}/{hid}", headers=auth_header(TENANT_A))
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_pending(client):
    r = await client.get(f"{PREFIX}/pending", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert isinstance(r.json(), list)


@pytest.mark.asyncio
async def test_cross_tenant_isolation(client):
    r = await client.post(PREFIX, json=_body("Tenant A"), headers=auth_header(TENANT_A))
    hid = r.json()["id"]

    r = await client.get(f"{PREFIX}/{hid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404

    r = await client.patch(
        f"{PREFIX}/{hid}", json={"title": "Hack"}, headers=auth_header(TENANT_B, 2),
    )
    assert r.status_code == 404

    r = await client.delete(f"{PREFIX}/{hid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404
