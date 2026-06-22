"""Testes de CRUD de procedimentos via API."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header, make_token

HEADERS_A = auth_header(TENANT_A, 1)
HEADERS_B = auth_header(TENANT_B, 2)
PROC_URL = "/api/v1/procedures"


def _body(**overrides):
    base = {
        "name": "Procedimento teste",
        "link": "https://example.com/proc",
    }
    base.update(overrides)
    return base


# ── Create ──


@pytest.mark.asyncio
async def test_create_procedure(client):
    r = await client.post(PROC_URL, json=_body(), headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert data["name"] == "Procedimento teste"
    assert data["link"] == "https://example.com/proc"
    assert "id" in data


@pytest.mark.asyncio
async def test_create_procedure_minimal(client):
    r = await client.post(PROC_URL, json={"name": "Mínimo"}, headers=HEADERS_A)
    assert r.status_code == 201
    assert r.json()["name"] == "Mínimo"
    assert r.json()["link"] is None
    assert r.json()["file"] is None


# ── List ──


@pytest.mark.asyncio
async def test_list_procedures(client):
    await client.post(PROC_URL, json=_body(name="List proc"), headers=HEADERS_A)
    r = await client.get(PROC_URL, headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert data["total"] >= 1
    assert "items" in data
    assert data["page"] == 1


@pytest.mark.asyncio
async def test_list_procedures_search(client):
    await client.post(PROC_URL, json=_body(name="BuscaProc888"), headers=HEADERS_A)
    r = await client.get(f"{PROC_URL}?search=BuscaProc888", headers=HEADERS_A)
    assert r.status_code == 200
    items = r.json()["items"]
    assert any(i["name"] == "BuscaProc888" for i in items)


@pytest.mark.asyncio
async def test_list_procedures_pagination(client):
    for i in range(3):
        await client.post(PROC_URL, json=_body(name=f"PagProc {i}"), headers=HEADERS_A)
    r = await client.get(f"{PROC_URL}?page=1&page_size=2", headers=HEADERS_A)
    assert r.status_code == 200
    assert len(r.json()["items"]) <= 2


# ── Update ──


@pytest.mark.asyncio
async def test_update_procedure(client):
    resp = await client.post(PROC_URL, json=_body(name="Before proc"), headers=HEADERS_A)
    proc_id = resp.json()["id"]

    r = await client.patch(
        f"{PROC_URL}/{proc_id}",
        json={"name": "After proc", "link": "https://new.com"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["name"] == "After proc"
    assert r.json()["link"] == "https://new.com"


@pytest.mark.asyncio
async def test_update_nonexistent_returns_404(client):
    r = await client.patch(
        f"{PROC_URL}/99999",
        json={"name": "Nope"},
        headers=HEADERS_A,
    )
    assert r.status_code == 404


# ── Delete ──


@pytest.mark.asyncio
async def test_delete_procedure(client):
    resp = await client.post(PROC_URL, json=_body(name="Delete proc"), headers=HEADERS_A)
    proc_id = resp.json()["id"]

    r = await client.delete(f"{PROC_URL}/{proc_id}", headers=HEADERS_A)
    assert r.status_code == 204


@pytest.mark.asyncio
async def test_delete_nonexistent_returns_404(client):
    r = await client.delete(f"{PROC_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Cross-tenant isolation ──


@pytest.mark.asyncio
async def test_cross_tenant_cannot_update(client):
    resp = await client.post(PROC_URL, json=_body(name="Tenant A proc"), headers=HEADERS_A)
    proc_id = resp.json()["id"]

    r = await client.patch(
        f"{PROC_URL}/{proc_id}",
        json={"name": "Hacked"},
        headers=HEADERS_B,
    )
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_delete(client):
    resp = await client.post(PROC_URL, json=_body(name="Tenant A proc"), headers=HEADERS_A)
    proc_id = resp.json()["id"]

    r = await client.delete(f"{PROC_URL}/{proc_id}", headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_list_isolation(client):
    await client.post(PROC_URL, json=_body(name="ProcIsolation444"), headers=HEADERS_A)
    r = await client.get(f"{PROC_URL}?search=ProcIsolation444", headers=HEADERS_B)
    assert r.status_code == 200
    assert r.json()["total"] == 0


# ── Permissions ──


@pytest.mark.asyncio
async def test_view_permission_required(client):
    token = make_token(TENANT_A, 1, ["procedure.create"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.get(PROC_URL, headers=headers)
    assert r.status_code == 403


@pytest.mark.asyncio
async def test_create_permission_required(client):
    token = make_token(TENANT_A, 1, ["procedure.view"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.post(PROC_URL, json=_body(), headers=headers)
    assert r.status_code == 403


# ── Auth required ──


@pytest.mark.asyncio
async def test_list_requires_auth(client):
    r = await client.get(PROC_URL)
    assert r.status_code in (401, 403)
