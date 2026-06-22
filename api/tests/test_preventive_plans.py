"""Testes CRUD e isolamento cross-tenant para preventive_plans."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header

PREFIX = "/api/v1/preventive-plans"


def _body(name="Plano teste", **kw):
    return {"name": name, "recurrence": "monthly", **kw}


@pytest.mark.asyncio
async def test_create_and_list(client):
    r = await client.post(PREFIX, json=_body(), headers=auth_header(TENANT_A))
    assert r.status_code == 201
    data = r.json()
    assert data["name"] == "Plano teste"

    r = await client.get(PREFIX, headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["total"] >= 1


@pytest.mark.asyncio
async def test_get_by_id(client):
    r = await client.post(PREFIX, json=_body("Get by ID"), headers=auth_header(TENANT_A))
    pid = r.json()["id"]

    r = await client.get(f"{PREFIX}/{pid}", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["name"] == "Get by ID"


@pytest.mark.asyncio
async def test_update(client):
    r = await client.post(PREFIX, json=_body("Before"), headers=auth_header(TENANT_A))
    pid = r.json()["id"]

    r = await client.patch(
        f"{PREFIX}/{pid}", json={"name": "After"}, headers=auth_header(TENANT_A),
    )
    assert r.status_code == 200
    assert r.json()["name"] == "After"


@pytest.mark.asyncio
async def test_delete(client):
    r = await client.post(PREFIX, json=_body("To delete"), headers=auth_header(TENANT_A))
    pid = r.json()["id"]

    r = await client.delete(f"{PREFIX}/{pid}", headers=auth_header(TENANT_A))
    assert r.status_code == 204

    r = await client.get(f"{PREFIX}/{pid}", headers=auth_header(TENANT_A))
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_generate_orders(client):
    r = await client.post(f"{PREFIX}/generate", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert "generated" in r.json()


@pytest.mark.asyncio
async def test_cross_tenant_isolation(client):
    r = await client.post(PREFIX, json=_body("Tenant A plan"), headers=auth_header(TENANT_A))
    pid = r.json()["id"]

    r = await client.get(f"{PREFIX}/{pid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404

    r = await client.patch(
        f"{PREFIX}/{pid}", json={"name": "Hack"}, headers=auth_header(TENANT_B, 2),
    )
    assert r.status_code == 404

    r = await client.delete(f"{PREFIX}/{pid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404
