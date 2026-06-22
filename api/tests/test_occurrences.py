"""Testes de CRUD de ocorrências via API."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header, make_token

HEADERS_A = auth_header(TENANT_A, 1)
HEADERS_B = auth_header(TENANT_B, 2)
OCC_URL = "/api/v1/occurrences"


def _body(**overrides):
    base = {"title": "Ocorrência teste", "description": "Descrição da ocorrência"}
    base.update(overrides)
    return base


# ── Create ──


@pytest.mark.asyncio
async def test_create_occurrence(client):
    r = await client.post(OCC_URL, json=_body(), headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert data["title"] == "Ocorrência teste"
    assert data["id"] is not None
    assert "status" in data
    assert "updated_at" in data


@pytest.mark.asyncio
async def test_create_occurrence_minimal(client):
    r = await client.post(OCC_URL, json={"title": "Mínima"}, headers=HEADERS_A)
    assert r.status_code == 201
    assert r.json()["title"] == "Mínima"


# ── List ──


@pytest.mark.asyncio
async def test_list_occurrences(client):
    await client.post(OCC_URL, json=_body(title="List test"), headers=HEADERS_A)
    r = await client.get(OCC_URL, headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert data["total"] >= 1
    assert "items" in data
    assert data["page"] == 1
    assert data["page_size"] == 20


@pytest.mark.asyncio
async def test_list_occurrences_with_search(client):
    await client.post(OCC_URL, json=_body(title="BuscaUnica123"), headers=HEADERS_A)
    r = await client.get(f"{OCC_URL}?search=BuscaUnica123", headers=HEADERS_A)
    assert r.status_code == 200
    items = r.json()["items"]
    assert any(i["title"] == "BuscaUnica123" for i in items)


@pytest.mark.asyncio
async def test_list_occurrences_pagination(client):
    for i in range(3):
        await client.post(OCC_URL, json=_body(title=f"Pag {i}"), headers=HEADERS_A)
    r = await client.get(f"{OCC_URL}?page=1&page_size=2", headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert len(data["items"]) <= 2
    assert data["page_size"] == 2


# ── Cursor pagination ──


@pytest.mark.asyncio
async def test_list_occurrences_cursor(client):
    await client.post(OCC_URL, json=_body(title="Cursor test"), headers=HEADERS_A)
    r = await client.get(f"{OCC_URL}/cursor?limit=5", headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert "items" in data
    assert "has_more" in data
    assert "next_cursor" in data


# ── Get detail ──


@pytest.mark.asyncio
async def test_get_occurrence(client):
    resp = await client.post(OCC_URL, json=_body(title="Detail"), headers=HEADERS_A)
    occ_id = resp.json()["id"]

    r = await client.get(f"{OCC_URL}/{occ_id}", headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert data["title"] == "Detail"
    assert "participants" in data
    assert "unit" in data


@pytest.mark.asyncio
async def test_get_nonexistent_returns_404(client):
    r = await client.get(f"{OCC_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Update ──


@pytest.mark.asyncio
async def test_update_occurrence(client):
    resp = await client.post(OCC_URL, json=_body(title="Before"), headers=HEADERS_A)
    occ_id = resp.json()["id"]

    r = await client.patch(
        f"{OCC_URL}/{occ_id}",
        json={"title": "After"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["title"] == "After"


@pytest.mark.asyncio
async def test_update_nonexistent_returns_404(client):
    r = await client.patch(
        f"{OCC_URL}/99999",
        json={"title": "Nope"},
        headers=HEADERS_A,
    )
    assert r.status_code == 404


# ── Delete ──


@pytest.mark.asyncio
async def test_delete_occurrence(client):
    resp = await client.post(OCC_URL, json=_body(title="Delete me"), headers=HEADERS_A)
    occ_id = resp.json()["id"]

    r = await client.delete(f"{OCC_URL}/{occ_id}", headers=HEADERS_A)
    assert r.status_code == 204

    r2 = await client.get(f"{OCC_URL}/{occ_id}", headers=HEADERS_A)
    assert r2.status_code == 404


@pytest.mark.asyncio
async def test_delete_nonexistent_returns_404(client):
    r = await client.delete(f"{OCC_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Clone ──


@pytest.mark.asyncio
async def test_clone_occurrence(client):
    resp = await client.post(OCC_URL, json=_body(title="Original"), headers=HEADERS_A)
    occ_id = resp.json()["id"]

    r = await client.post(f"{OCC_URL}/{occ_id}/clone", headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert data["id"] != occ_id
    assert "Original" in data["title"]


@pytest.mark.asyncio
async def test_clone_nonexistent_returns_404(client):
    r = await client.post(f"{OCC_URL}/99999/clone", headers=HEADERS_A)
    assert r.status_code == 404


# ── Cross-tenant isolation ──


@pytest.mark.asyncio
async def test_cross_tenant_cannot_view(client):
    resp = await client.post(OCC_URL, json=_body(title="Tenant A only"), headers=HEADERS_A)
    occ_id = resp.json()["id"]

    r = await client.get(f"{OCC_URL}/{occ_id}", headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_update(client):
    resp = await client.post(OCC_URL, json=_body(title="Tenant A only"), headers=HEADERS_A)
    occ_id = resp.json()["id"]

    r = await client.patch(
        f"{OCC_URL}/{occ_id}",
        json={"title": "Hacked"},
        headers=HEADERS_B,
    )
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_delete(client):
    resp = await client.post(OCC_URL, json=_body(title="Tenant A only"), headers=HEADERS_A)
    occ_id = resp.json()["id"]

    r = await client.delete(f"{OCC_URL}/{occ_id}", headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_list_isolation(client):
    await client.post(OCC_URL, json=_body(title="IsolationUnique789"), headers=HEADERS_A)

    r = await client.get(f"{OCC_URL}?search=IsolationUnique789", headers=HEADERS_B)
    assert r.status_code == 200
    assert r.json()["total"] == 0


# ── Permissions ──


@pytest.mark.asyncio
async def test_view_permission_required(client):
    token = make_token(TENANT_A, 1, ["occurrence.create"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.get(OCC_URL, headers=headers)
    assert r.status_code == 403


@pytest.mark.asyncio
async def test_create_permission_required(client):
    token = make_token(TENANT_A, 1, ["occurrence.view"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.post(OCC_URL, json=_body(), headers=headers)
    assert r.status_code == 403


# ── Auth required ──


@pytest.mark.asyncio
async def test_list_requires_auth(client):
    r = await client.get(OCC_URL)
    assert r.status_code in (401, 403)
