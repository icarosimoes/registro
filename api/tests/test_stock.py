"""Testes CRUD e isolamento cross-tenant para stock."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header

PREFIX = "/api/v1/stock"


def _item_body(name="Item teste", **kw):
    return {"name": name, "unit": "un", "min_quantity": 5, **kw}


@pytest.mark.asyncio
async def test_create_and_list_items(client):
    r = await client.post(
        f"{PREFIX}/items", json=_item_body(), headers=auth_header(TENANT_A),
    )
    assert r.status_code == 201
    data = r.json()
    assert data["name"] == "Item teste"

    r = await client.get(f"{PREFIX}/items", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["total"] >= 1


@pytest.mark.asyncio
async def test_get_item_by_id(client):
    r = await client.post(
        f"{PREFIX}/items", json=_item_body("Get item"), headers=auth_header(TENANT_A),
    )
    item_id = r.json()["id"]

    r = await client.get(f"{PREFIX}/items/{item_id}", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["name"] == "Get item"


@pytest.mark.asyncio
async def test_update_item(client):
    r = await client.post(
        f"{PREFIX}/items", json=_item_body("Before"), headers=auth_header(TENANT_A),
    )
    item_id = r.json()["id"]

    r = await client.patch(
        f"{PREFIX}/items/{item_id}",
        json={"name": "After"},
        headers=auth_header(TENANT_A),
    )
    assert r.status_code == 200
    assert r.json()["name"] == "After"


@pytest.mark.asyncio
async def test_delete_item(client):
    r = await client.post(
        f"{PREFIX}/items", json=_item_body("To delete"), headers=auth_header(TENANT_A),
    )
    item_id = r.json()["id"]

    r = await client.delete(f"{PREFIX}/items/{item_id}", headers=auth_header(TENANT_A))
    assert r.status_code == 204

    r = await client.get(f"{PREFIX}/items/{item_id}", headers=auth_header(TENANT_A))
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_create_movement(client):
    r = await client.post(
        f"{PREFIX}/items", json=_item_body("Mov item"), headers=auth_header(TENANT_A),
    )
    item_id = r.json()["id"]

    r = await client.post(
        f"{PREFIX}/movements",
        json={"item_id": item_id, "movement_type": "in", "quantity": 10},
        headers=auth_header(TENANT_A),
    )
    assert r.status_code == 201
    assert r.json()["quantity"] == 10


@pytest.mark.asyncio
async def test_list_movements(client):
    r = await client.get(f"{PREFIX}/movements", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert "items" in r.json()


@pytest.mark.asyncio
async def test_cross_tenant_item_isolation(client):
    r = await client.post(
        f"{PREFIX}/items", json=_item_body("Tenant A item"), headers=auth_header(TENANT_A),
    )
    item_id = r.json()["id"]

    r = await client.get(f"{PREFIX}/items/{item_id}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404

    r = await client.patch(
        f"{PREFIX}/items/{item_id}",
        json={"name": "Hack"},
        headers=auth_header(TENANT_B, 2),
    )
    assert r.status_code == 404

    r = await client.delete(f"{PREFIX}/items/{item_id}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404
