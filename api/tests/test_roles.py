"""Testes de CRUD de perfis/papéis via API."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, USE_POSTGRES, auth_header, make_token

pytestmark = pytest.mark.skipif(
    not USE_POSTGRES, reason="Roles tests need PostgreSQL for relationship refresh"
)

HEADERS_A = auth_header(TENANT_A, 1)
HEADERS_B = auth_header(TENANT_B, 2)
ROLES_URL = "/api/v1/roles"

_counter = 0


def _body(**overrides):
    global _counter
    _counter += 1
    base = {
        "code": f"test_role_{_counter}",
        "name": f"Test Role {_counter}",
        "permission_codes": [],
    }
    base.update(overrides)
    return base


# ── List permissions ──


@pytest.mark.asyncio
async def test_list_permissions(client):
    r = await client.get(f"{ROLES_URL}/permissions", headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert isinstance(data, list)
    if len(data) > 0:
        group = data[0]
        assert "module" in group
        assert "permissions" in group


# ── List roles ──


@pytest.mark.asyncio
async def test_list_roles(client):
    r = await client.get(ROLES_URL, headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert "items" in data
    assert "total" in data
    assert data["total"] >= 1  # admin role seeded


# ── Create ──


@pytest.mark.asyncio
async def test_create_role(client):
    r = await client.post(ROLES_URL, json=_body(), headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert "id" in data
    assert data["user_count"] == 0
    assert "permission_codes" in data


# ── Get ──


@pytest.mark.asyncio
async def test_get_role(client):
    resp = await client.post(ROLES_URL, json=_body(), headers=HEADERS_A)
    role_id = resp.json()["id"]

    r = await client.get(f"{ROLES_URL}/{role_id}", headers=HEADERS_A)
    assert r.status_code == 200
    assert r.json()["id"] == role_id


@pytest.mark.asyncio
async def test_get_nonexistent_returns_404(client):
    r = await client.get(f"{ROLES_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Update ──


@pytest.mark.asyncio
async def test_update_role(client):
    resp = await client.post(ROLES_URL, json=_body(), headers=HEADERS_A)
    role_id = resp.json()["id"]

    r = await client.patch(
        f"{ROLES_URL}/{role_id}",
        json={"name": "Updated Role"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["name"] == "Updated Role"


@pytest.mark.asyncio
async def test_update_nonexistent_returns_404(client):
    r = await client.patch(
        f"{ROLES_URL}/99999",
        json={"name": "Nope"},
        headers=HEADERS_A,
    )
    assert r.status_code == 404


# ── Delete ──


@pytest.mark.asyncio
async def test_delete_role(client):
    resp = await client.post(ROLES_URL, json=_body(), headers=HEADERS_A)
    role_id = resp.json()["id"]

    r = await client.delete(f"{ROLES_URL}/{role_id}", headers=HEADERS_A)
    assert r.status_code == 204


@pytest.mark.asyncio
async def test_delete_nonexistent_returns_404(client):
    r = await client.delete(f"{ROLES_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Cross-tenant isolation ──


@pytest.mark.asyncio
async def test_cross_tenant_cannot_view_role(client):
    resp = await client.post(ROLES_URL, json=_body(), headers=HEADERS_A)
    role_id = resp.json()["id"]

    r = await client.get(f"{ROLES_URL}/{role_id}", headers=HEADERS_B)
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_update_role(client):
    resp = await client.post(ROLES_URL, json=_body(), headers=HEADERS_A)
    role_id = resp.json()["id"]

    r = await client.patch(
        f"{ROLES_URL}/{role_id}",
        json={"name": "Hacked"},
        headers=HEADERS_B,
    )
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_delete_role(client):
    resp = await client.post(ROLES_URL, json=_body(), headers=HEADERS_A)
    role_id = resp.json()["id"]

    r = await client.delete(f"{ROLES_URL}/{role_id}", headers=HEADERS_B)
    assert r.status_code == 404


# ── Permissions ──


@pytest.mark.asyncio
async def test_view_permission_required(client):
    token = make_token(TENANT_A, 1, ["user.create"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.get(ROLES_URL, headers=headers)
    assert r.status_code == 403


@pytest.mark.asyncio
async def test_create_permission_required(client):
    token = make_token(TENANT_A, 1, ["user.view"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.post(ROLES_URL, json=_body(), headers=headers)
    assert r.status_code == 403


# ── Auth required ──


@pytest.mark.asyncio
async def test_list_requires_auth(client):
    r = await client.get(ROLES_URL)
    assert r.status_code in (401, 403)
