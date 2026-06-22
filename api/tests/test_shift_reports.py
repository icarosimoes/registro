"""Testes de CRUD de passagens de turno via API."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header, make_token

HEADERS_A = auth_header(TENANT_A, 1)
HEADERS_B = auth_header(TENANT_B, 2)
SR_URL = "/api/v1/shift-reports"


def _body(**overrides):
    base = {
        "title": "Passagem manhã",
        "description": "Relatório do turno",
        "shift_type": "morning",
        "shift_date": "2026-06-22",
        "status": "Em andamento",
    }
    base.update(overrides)
    return base


# ── Create ──


@pytest.mark.asyncio
async def test_create_shift_report(client):
    r = await client.post(SR_URL, json=_body(), headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert data["title"] == "Passagem manhã"
    assert data["shift_type"] == "morning"
    assert data["shift_label"] == "Manhã"
    assert data["status"] == "Em andamento"


@pytest.mark.asyncio
async def test_create_shift_report_minimal(client):
    r = await client.post(SR_URL, json={"title": "Mínimo"}, headers=HEADERS_A)
    assert r.status_code == 201
    assert r.json()["title"] == "Mínimo"


# ── List ──


@pytest.mark.asyncio
async def test_list_shift_reports(client):
    await client.post(SR_URL, json=_body(title="List SR"), headers=HEADERS_A)
    r = await client.get(SR_URL, headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert data["total"] >= 1
    assert "items" in data
    assert data["page"] == 1


@pytest.mark.asyncio
async def test_list_shift_reports_search(client):
    await client.post(SR_URL, json=_body(title="BuscaSR555"), headers=HEADERS_A)
    r = await client.get(f"{SR_URL}?search=BuscaSR555", headers=HEADERS_A)
    assert r.status_code == 200
    items = r.json()["items"]
    assert any(i["title"] == "BuscaSR555" for i in items)


@pytest.mark.asyncio
async def test_list_shift_reports_date_filter(client):
    await client.post(
        SR_URL,
        json=_body(title="DateFilter", shift_date="2026-06-22"),
        headers=HEADERS_A,
    )
    r = await client.get(
        f"{SR_URL}?date_from=2026-06-22&date_to=2026-06-22",
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["total"] >= 1


# ── Get detail ──


@pytest.mark.asyncio
async def test_get_shift_report(client):
    resp = await client.post(SR_URL, json=_body(title="Detail SR"), headers=HEADERS_A)
    sr_id = resp.json()["id"]

    r = await client.get(f"{SR_URL}/{sr_id}", headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert data["title"] == "Detail SR"
    assert "observations" in data
    assert "notes_reception" in data
    assert "payload" in data


@pytest.mark.asyncio
async def test_get_nonexistent_returns_404(client):
    r = await client.get(f"{SR_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Update ──


@pytest.mark.asyncio
async def test_update_shift_report(client):
    resp = await client.post(SR_URL, json=_body(title="Before SR"), headers=HEADERS_A)
    sr_id = resp.json()["id"]

    r = await client.patch(
        f"{SR_URL}/{sr_id}",
        json={"title": "After SR", "observations": "Tudo tranquilo"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["title"] == "After SR"


@pytest.mark.asyncio
async def test_update_nonexistent_returns_404(client):
    r = await client.patch(f"{SR_URL}/99999", json={"title": "Nope"}, headers=HEADERS_A)
    assert r.status_code == 404


# ── Delete ──


@pytest.mark.asyncio
async def test_delete_shift_report(client):
    resp = await client.post(SR_URL, json=_body(title="Delete SR"), headers=HEADERS_A)
    sr_id = resp.json()["id"]

    r = await client.delete(f"{SR_URL}/{sr_id}", headers=HEADERS_A)
    assert r.status_code == 204

    r2 = await client.get(f"{SR_URL}/{sr_id}", headers=HEADERS_A)
    assert r2.status_code == 404


@pytest.mark.asyncio
async def test_delete_nonexistent_returns_404(client):
    r = await client.delete(f"{SR_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Cross-tenant isolation ──


@pytest.mark.asyncio
async def test_cross_tenant_cannot_view(client):
    resp = await client.post(SR_URL, json=_body(title="Tenant A SR"), headers=HEADERS_A)
    sr_id = resp.json()["id"]

    r = await client.get(f"{SR_URL}/{sr_id}", headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_update(client):
    resp = await client.post(SR_URL, json=_body(title="Tenant A SR"), headers=HEADERS_A)
    sr_id = resp.json()["id"]

    r = await client.patch(f"{SR_URL}/{sr_id}", json={"title": "Hacked"}, headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_delete(client):
    resp = await client.post(SR_URL, json=_body(title="Tenant A SR"), headers=HEADERS_A)
    sr_id = resp.json()["id"]

    r = await client.delete(f"{SR_URL}/{sr_id}", headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_list_isolation(client):
    await client.post(SR_URL, json=_body(title="SRIsolation333"), headers=HEADERS_A)
    r = await client.get(f"{SR_URL}?search=SRIsolation333", headers=HEADERS_B)
    assert r.status_code == 200
    assert r.json()["total"] == 0


# ── Permissions ──


@pytest.mark.asyncio
async def test_view_permission_required(client):
    token = make_token(TENANT_A, 1, ["shift_report.create"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.get(SR_URL, headers=headers)
    assert r.status_code == 403


@pytest.mark.asyncio
async def test_create_permission_required(client):
    token = make_token(TENANT_A, 1, ["shift_report.view"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.post(SR_URL, json=_body(), headers=headers)
    assert r.status_code == 403


# ── Auth required ──


@pytest.mark.asyncio
async def test_list_requires_auth(client):
    r = await client.get(SR_URL)
    assert r.status_code in (401, 403)
