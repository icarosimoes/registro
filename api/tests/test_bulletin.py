"""Testes CRUD e isolamento cross-tenant para bulletin."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header

PREFIX = "/api/v1/bulletin"


def _body(title="Aviso teste", **kw):
    return {"title": title, **kw}


@pytest.mark.asyncio
async def test_create_and_list(client):
    r = await client.post(PREFIX, json=_body(), headers=auth_header(TENANT_A))
    assert r.status_code == 201
    data = r.json()
    assert data["title"] == "Aviso teste"
    assert data["pinned"] is False

    r = await client.get(PREFIX, headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["total"] >= 1


@pytest.mark.asyncio
async def test_update(client):
    r = await client.post(PREFIX, json=_body("Before"), headers=auth_header(TENANT_A))
    pid = r.json()["id"]

    r = await client.patch(
        f"{PREFIX}/{pid}",
        json={"title": "After", "pinned": True},
        headers=auth_header(TENANT_A),
    )
    assert r.status_code == 200
    assert r.json()["title"] == "After"
    assert r.json()["pinned"] is True


@pytest.mark.asyncio
async def test_delete(client):
    r = await client.post(PREFIX, json=_body("To delete"), headers=auth_header(TENANT_A))
    pid = r.json()["id"]

    r = await client.delete(f"{PREFIX}/{pid}", headers=auth_header(TENANT_A))
    assert r.status_code == 204


@pytest.mark.asyncio
async def test_cross_tenant_isolation(client):
    r = await client.post(PREFIX, json=_body("Tenant A post"), headers=auth_header(TENANT_A))
    pid = r.json()["id"]

    r = await client.patch(
        f"{PREFIX}/{pid}", json={"title": "Hack"}, headers=auth_header(TENANT_B, 2),
    )
    assert r.status_code == 404

    r = await client.delete(f"{PREFIX}/{pid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404
