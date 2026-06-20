"""Testes de isolamento cross-tenant com banco real.

Garante que tenant A não consegue ver, editar ou deletar dados do tenant B.
"""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header


@pytest.mark.asyncio
async def test_tenant_b_cannot_see_tenant_a_fiscal_requests(client):
    body = {"request_type": "NF-e", "title": "Tenant A only", "requester": "R", "payload": {}}
    r = await client.post("/api/v1/fiscal-requests", json=body, headers=auth_header(TENANT_A, 1))
    assert r.status_code == 201

    r = await client.get("/api/v1/fiscal-requests", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 200
    items = r.json()["items"]
    assert not any("Tenant A" in i.get("title", "") for i in items)


@pytest.mark.asyncio
async def test_tenant_b_cannot_update_tenant_a_record(client):
    body = {"request_type": "NF-e", "title": "Cross update", "requester": "R", "payload": {}}
    r = await client.post("/api/v1/fiscal-requests", json=body, headers=auth_header(TENANT_A, 1))
    req_id = r.json()["id"]

    r = await client.patch(
        f"/api/v1/fiscal-requests/{req_id}",
        json={"status": "Concluído"},
        headers=auth_header(TENANT_B, 2),
    )
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_tenant_b_cannot_delete_tenant_a_record(client):
    body = {"request_type": "NF-e", "title": "Cross delete", "requester": "R", "payload": {}}
    r = await client.post("/api/v1/fiscal-requests", json=body, headers=auth_header(TENANT_A, 1))
    req_id = r.json()["id"]

    r = await client.delete(
        f"/api/v1/fiscal-requests/{req_id}",
        headers=auth_header(TENANT_B, 2),
    )
    assert r.status_code == 404

    r = await client.get("/api/v1/fiscal-requests", headers=auth_header(TENANT_A, 1))
    assert any(i["id"] == req_id for i in r.json()["items"])


@pytest.mark.asyncio
async def test_each_tenant_sees_only_own_data(client):
    body_a = {"request_type": "NF-e", "title": "A record", "requester": "A", "payload": {}}
    body_b = {"request_type": "NF-e", "title": "B record", "requester": "B", "payload": {}}
    await client.post("/api/v1/fiscal-requests", json=body_a, headers=auth_header(TENANT_A, 1))
    await client.post("/api/v1/fiscal-requests", json=body_b, headers=auth_header(TENANT_B, 2))

    r_a = await client.get("/api/v1/fiscal-requests", headers=auth_header(TENANT_A, 1))
    r_b = await client.get("/api/v1/fiscal-requests", headers=auth_header(TENANT_B, 2))

    titles_a = {i["title"] for i in r_a.json()["items"]}
    titles_b = {i["title"] for i in r_b.json()["items"]}

    assert "B record" not in titles_a
    assert "A record" not in titles_b
