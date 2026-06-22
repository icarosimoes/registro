"""Testes de CRUD de solicitações fiscais via API."""

import pytest

from tests.conftest import TENANT_A, auth_header

HEADERS_A = auth_header(TENANT_A, 1)
FR_URL = "/api/v1/fiscal-requests"


def _body(**overrides):
    base = {"request_type": "NF-e", "title": "Test", "requester": "R", "payload": {}}
    base.update(overrides)
    return base


@pytest.mark.asyncio
async def test_create_fiscal_request(client):
    body = _body(title="Emissão teste", requester="Hóspede X")
    r = await client.post(FR_URL, json=body, headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert data["protocol"].startswith("REG-")
    assert data["request_type"] == "NF-e"
    assert data["sla_deadline"] is not None
    assert data["sla_status"] in ("on_time", "warning")


@pytest.mark.asyncio
async def test_list_fiscal_requests(client):
    await client.post(FR_URL, json=_body(title="List test"), headers=HEADERS_A)
    r = await client.get(FR_URL, headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert data["total"] >= 1
    assert "items" in data
    assert data["page"] == 1


@pytest.mark.asyncio
async def test_list_with_search(client):
    body = _body(title="Busca test", requester="Hóspede Busca")
    resp = await client.post(FR_URL, json=body, headers=HEADERS_A)
    protocol = resp.json()["protocol"]

    r = await client.get(
        f"{FR_URL}?search={protocol}",
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    items = r.json()["items"]
    assert any(i["protocol"] == protocol for i in items)


@pytest.mark.asyncio
async def test_update_fiscal_request(client):
    resp = await client.post(FR_URL, json=_body(title="Update"), headers=HEADERS_A)
    req_id = resp.json()["id"]

    r = await client.patch(
        f"{FR_URL}/{req_id}",
        json={"status": "Concluído"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["status"] == "Concluído"
    assert r.json()["sla_status"] == "completed"


@pytest.mark.asyncio
async def test_update_nonexistent_returns_404(client):
    r = await client.patch(
        f"{FR_URL}/99999",
        json={"status": "Concluído"},
        headers=HEADERS_A,
    )
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_delete_fiscal_request(client):
    resp = await client.post(FR_URL, json=_body(title="Delete"), headers=HEADERS_A)
    req_id = resp.json()["id"]

    r = await client.delete(f"{FR_URL}/{req_id}", headers=HEADERS_A)
    assert r.status_code == 204

    r = await client.delete(f"{FR_URL}/{req_id}", headers=HEADERS_A)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_pause_and_resume_sla(client):
    resp = await client.post(FR_URL, json=_body(title="Pause"), headers=HEADERS_A)
    req_id = resp.json()["id"]

    r = await client.patch(
        f"{FR_URL}/{req_id}",
        json={"status": "Em espera"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["sla_status"] == "paused"

    r = await client.patch(
        f"{FR_URL}/{req_id}",
        json={"status": "Em andamento"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["sla_status"] in ("on_time", "warning")


@pytest.mark.asyncio
async def test_create_without_auth_returns_401(client):
    r = await client.post(FR_URL, json=_body())
    assert r.status_code == 401
