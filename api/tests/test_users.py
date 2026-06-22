"""Testes de CRUD de usuários via API."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header, make_token

HEADERS_A = auth_header(TENANT_A, 1)
HEADERS_B = auth_header(TENANT_B, 2)
USERS_URL = "/api/v1/users"


def _body(**overrides):
    base = {
        "name": "Novo Usuário",
        "email": f"novo_{id(overrides)}@test.com",
        "password": "SenhaForte!123",
    }
    base.update(overrides)
    return base


# ── List ──


@pytest.mark.asyncio
async def test_list_users(client):
    r = await client.get(USERS_URL, headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert "items" in data
    assert "total" in data
    assert data["page"] == 1
    assert data["total"] >= 1


@pytest.mark.asyncio
async def test_list_users_pagination(client):
    r = await client.get(f"{USERS_URL}?page=1&page_size=1", headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert len(data["items"]) <= 1
    assert data["page_size"] == 1


# ── Search ──


@pytest.mark.asyncio
async def test_search_users(client):
    r = await client.get(f"{USERS_URL}/search?q=User", headers=HEADERS_A)
    assert r.status_code == 200
    data = r.json()
    assert isinstance(data, list)
    assert len(data) >= 1
    assert "id" in data[0]
    assert "name" in data[0]
    assert "email" in data[0]


@pytest.mark.asyncio
async def test_search_users_empty(client):
    r = await client.get(f"{USERS_URL}/search?q=NaoExiste99999", headers=HEADERS_A)
    assert r.status_code == 200
    assert r.json() == []


# ── Create ──


@pytest.mark.asyncio
async def test_create_user(client):
    body = _body(name="Criado Teste", email="criado_teste@test.com")
    r = await client.post(USERS_URL, json=body, headers=HEADERS_A)
    assert r.status_code == 201
    data = r.json()
    assert data["name"] == "Criado Teste"
    assert data["email"] == "criado_teste@test.com"
    assert data["active"] is True
    assert "id" in data


@pytest.mark.asyncio
async def test_create_user_weak_password(client):
    body = _body(email="weak@test.com", password="123")
    r = await client.post(USERS_URL, json=body, headers=HEADERS_A)
    assert r.status_code == 422


# ── Update ──


@pytest.mark.asyncio
async def test_update_user(client):
    body = _body(name="Before Update", email="before_update@test.com")
    resp = await client.post(USERS_URL, json=body, headers=HEADERS_A)
    user_id = resp.json()["id"]

    r = await client.patch(
        f"{USERS_URL}/{user_id}",
        json={"name": "After Update"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["name"] == "After Update"


@pytest.mark.asyncio
async def test_update_nonexistent_returns_404(client):
    r = await client.patch(
        f"{USERS_URL}/99999",
        json={"name": "Nope"},
        headers=HEADERS_A,
    )
    assert r.status_code == 404


# ── Delete ──


@pytest.mark.asyncio
async def test_delete_user(client):
    body = _body(name="Delete Me", email="delete_me@test.com")
    resp = await client.post(USERS_URL, json=body, headers=HEADERS_A)
    user_id = resp.json()["id"]

    r = await client.delete(f"{USERS_URL}/{user_id}", headers=HEADERS_A)
    assert r.status_code == 204


@pytest.mark.asyncio
async def test_delete_self_returns_400(client):
    # user_id=1 is the authenticated user
    r = await client.delete(f"{USERS_URL}/1", headers=HEADERS_A)
    assert r.status_code == 400


@pytest.mark.asyncio
async def test_delete_nonexistent_returns_404(client):
    r = await client.delete(f"{USERS_URL}/99999", headers=HEADERS_A)
    assert r.status_code == 404


# ── Profile update ──


@pytest.mark.asyncio
async def test_update_profile(client):
    r = await client.patch(
        f"{USERS_URL}/me",
        json={"name": "Meu Nome Atualizado"},
        headers=HEADERS_A,
    )
    assert r.status_code == 200
    assert r.json()["name"] == "Meu Nome Atualizado"


@pytest.mark.asyncio
async def test_update_profile_no_fields(client):
    r = await client.patch(f"{USERS_URL}/me", json={}, headers=HEADERS_A)
    assert r.status_code == 422


# ── Cross-tenant isolation ──


@pytest.mark.asyncio
async def test_cross_tenant_cannot_update_user(client):
    body = _body(name="Tenant A User", email="tenant_a_user@test.com")
    resp = await client.post(USERS_URL, json=body, headers=HEADERS_A)
    user_id = resp.json()["id"]

    r = await client.patch(
        f"{USERS_URL}/{user_id}",
        json={"name": "Hacked"},
        headers=HEADERS_B,
    )
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_cross_tenant_cannot_delete_user(client):
    body = _body(name="Tenant A User 2", email="tenant_a_user2@test.com")
    resp = await client.post(USERS_URL, json=body, headers=HEADERS_A)
    user_id = resp.json()["id"]

    r = await client.delete(f"{USERS_URL}/{user_id}", headers=HEADERS_B)
    assert r.status_code == 404


# ── Permissions ──


@pytest.mark.asyncio
async def test_view_permission_required(client):
    token = make_token(TENANT_A, 1, ["user.create"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.get(USERS_URL, headers=headers)
    assert r.status_code == 403


@pytest.mark.asyncio
async def test_create_permission_required(client):
    token = make_token(TENANT_A, 1, ["user.view"])
    headers = {"Authorization": f"Bearer {token}"}
    r = await client.post(USERS_URL, json=_body(email="perm@test.com"), headers=headers)
    assert r.status_code == 403


# ── Auth required ──


@pytest.mark.asyncio
async def test_list_requires_auth(client):
    r = await client.get(USERS_URL)
    assert r.status_code in (401, 403)
