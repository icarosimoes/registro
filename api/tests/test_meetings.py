"""Testes de CRUD de reuniões via API."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header, make_token

HEADERS_A = auth_header(TENANT_A, 1)
HEADERS_B = auth_header(TENANT_B, 2)
MTG_URL = "/api/v1/meetings"


def _body(**overrides):
    base = {
        "title": "Reunião teste",
        "description": "Pauta da reunião",
        "location": "Sala 1",
        "status": "Agendada",
    }
    base.update(overrides)
    return base


# ── Create ──


@pytest.mark.asyncio
async def test_create_meeting(client):
    r = await client.post(MTG_URL, json=_body(), headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert data["title"] == "Reunião teste"
    assert data["status"] == "Agendada"
    assert "participants" in data
    assert "subjects" in data


@pytest.mark.asyncio
async def test_create_meeting_with_subjects(client):
    body = _body(subjects=[{"title": "Ponto 1"}, {"title": "Ponto 2", "sort_order": 1}])
    r = await client.post(MTG_URL, json=body, headers=HEADERS_A)
    assert r.status_code == 201
    assert len(r.json()["subjects"]) == 2


# ── List ──


@pytest.mark.asyncio
async def test_list_meetings(client):
    await client.post(MTG_URL, json=_body(title="List meeting"), headers=HEADERS_A)
    r = await client.get(MTG_URL, headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert data["total"] >= 1
    assert "items" in data
    assert data["page"] == 1


@pytest.mark.asyncio
async def test_list_meetings_search(client):
    await client.post(MTG_URL, json=_body(title="BuscaReuniao999"), headers=HEADERS_A)
    r = await client.get(f"{MTG_URL}?search=BuscaReuniao999", headers=HEADERS_A)
    assert r.status_code == 200
    items = r.json()["items"]
    assert any(i["title"] == "BuscaReuniao999" for i in items)


# ── Get detail ──


@pytest.mark.asyncio
async def test_get_meeting(client):
    resp = await client.post(MTG_URL, json=_body(title="Detail mtg"), headers=HEADERS_A)
    mtg_id = resp.json()["id"]

    r = await client.get(f"{MTG_URL}/{mtg_id}", headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert data["title"] == "Detail mtg"
    assert "participants" in data
    assert "subjects" in data


@pytest.mark.asyncio
async def test_get_nonexistent_returns_404(client):
    r = await client.get(f"{MTG_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Update ──


@pytest.mark.asyncio
async def test_update_meeting(client):
    resp = await client.post(MTG_URL, json=_body(title="Before mtg"), headers=HEADERS_A)
    mtg_id = resp.json()["id"]

    r = await client.patch(
        f"{MTG_URL}/{mtg_id}",
        json={"title": "After mtg"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["title"] == "After mtg"


@pytest.mark.asyncio
async def test_update_nonexistent_returns_404(client):
    r = await client.patch(f"{MTG_URL}/99999", json={"title": "Nope"}, headers=HEADERS_A)
    assert r.status_code == 404


# ── Delete ──


@pytest.mark.asyncio
async def test_delete_meeting(client):
    resp = await client.post(MTG_URL, json=_body(title="Delete mtg"), headers=HEADERS_A)
    mtg_id = resp.json()["id"]

    r = await client.delete(f"{MTG_URL}/{mtg_id}", headers=HEADERS_A)
    assert r.status_code == 204

    r2 = await client.get(f"{MTG_URL}/{mtg_id}", headers=HEADERS_A)
    assert r2.status_code == 404


@pytest.mark.asyncio
async def test_delete_nonexistent_returns_404(client):
    r = await client.delete(f"{MTG_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Clone ──


@pytest.mark.asyncio
async def test_clone_meeting(client):
    resp = await client.post(MTG_URL, json=_body(title="Original mtg"), headers=HEADERS_A)
    mtg_id = resp.json()["id"]

    r = await client.post(f"{MTG_URL}/{mtg_id}/clone", headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert data["id"] != mtg_id


@pytest.mark.asyncio
async def test_clone_nonexistent_returns_404(client):
    r = await client.post(f"{MTG_URL}/99999/clone", headers=HEADERS_A)
    assert r.status_code == 404


# ── Subjects CRUD ──


@pytest.mark.asyncio
async def test_add_subject(client):
    resp = await client.post(MTG_URL, json=_body(title="Subject mtg"), headers=HEADERS_A)
    mtg_id = resp.json()["id"]

    r = await client.post(
        f"{MTG_URL}/{mtg_id}/subjects",
        json={"title": "Novo ponto"},
        headers=HEADERS_A,
    )
    assert r.status_code == 201
    data = r.json()
    assert data["title"] == "Novo ponto"
    assert data["resolved"] is False


@pytest.mark.asyncio
async def test_update_subject(client):
    resp = await client.post(
        MTG_URL,
        json=_body(title="SubjUpdate", subjects=[{"title": "Old subject"}]),
        headers=HEADERS_A,
    )
    mtg_id = resp.json()["id"]
    subj_id = resp.json()["subjects"][0]["id"]

    r = await client.patch(
        f"{MTG_URL}/{mtg_id}/subjects/{subj_id}",
        json={"title": "New subject", "resolved": True},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["title"] == "New subject"
    assert r.json()["resolved"] is True


@pytest.mark.asyncio
async def test_delete_subject(client):
    resp = await client.post(
        MTG_URL,
        json=_body(title="SubjDelete", subjects=[{"title": "Remove me"}]),
        headers=HEADERS_A,
    )
    mtg_id = resp.json()["id"]
    subj_id = resp.json()["subjects"][0]["id"]

    r = await client.delete(f"{MTG_URL}/{mtg_id}/subjects/{subj_id}", headers=HEADERS_A)
    assert r.status_code == 204


# ── Cross-tenant isolation ──


@pytest.mark.asyncio
async def test_cross_tenant_cannot_view(client):
    resp = await client.post(MTG_URL, json=_body(title="Tenant A mtg"), headers=HEADERS_A)
    mtg_id = resp.json()["id"]

    r = await client.get(f"{MTG_URL}/{mtg_id}", headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_delete(client):
    resp = await client.post(MTG_URL, json=_body(title="Tenant A mtg"), headers=HEADERS_A)
    mtg_id = resp.json()["id"]

    r = await client.delete(f"{MTG_URL}/{mtg_id}", headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_list_isolation(client):
    await client.post(MTG_URL, json=_body(title="MtgIsolation777"), headers=HEADERS_A)
    r = await client.get(f"{MTG_URL}?search=MtgIsolation777", headers=HEADERS_B)
    assert r.status_code == 200
    assert r.json()["total"] == 0


# ── Permissions ──


@pytest.mark.asyncio
async def test_view_permission_required(client):
    token = make_token(TENANT_A, 1, ["meeting.create"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.get(MTG_URL, headers=headers)
    assert r.status_code == 403


@pytest.mark.asyncio
async def test_create_permission_required(client):
    token = make_token(TENANT_A, 1, ["meeting.view"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.post(MTG_URL, json=_body(), headers=headers)
    assert r.status_code == 403


# ── Auth required ──


@pytest.mark.asyncio
async def test_list_requires_auth(client):
    r = await client.get(MTG_URL)
    assert r.status_code in (401, 403)
