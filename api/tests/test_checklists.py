"""Testes CRUD e isolamento cross-tenant para checklists."""

import pytest

from tests.conftest import TENANT_A, TENANT_B, auth_header

PREFIX = "/api/v1/checklists"


def _template_body(name="Check teste", **kw):
    return {
        "name": name,
        "recurrence": "daily",
        "items": [{"label": "Item 1"}, {"label": "Item 2"}],
        **kw,
    }


@pytest.mark.asyncio
async def test_create_and_list_templates(client):
    r = await client.post(
        f"{PREFIX}/templates",
        json=_template_body(),
        headers=auth_header(TENANT_A),
    )
    assert r.status_code == 201
    data = r.json()
    assert data["name"] == "Check teste"
    assert data["item_count"] == 2

    r = await client.get(f"{PREFIX}/templates", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["total"] >= 1


@pytest.mark.asyncio
async def test_get_template_by_id(client):
    r = await client.post(
        f"{PREFIX}/templates",
        json=_template_body("Get tmpl"),
        headers=auth_header(TENANT_A),
    )
    tid = r.json()["id"]

    r = await client.get(f"{PREFIX}/templates/{tid}", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert r.json()["name"] == "Get tmpl"


@pytest.mark.asyncio
async def test_update_template(client):
    r = await client.post(
        f"{PREFIX}/templates",
        json=_template_body("Before"),
        headers=auth_header(TENANT_A),
    )
    tid = r.json()["id"]

    r = await client.patch(
        f"{PREFIX}/templates/{tid}",
        json={"name": "After"},
        headers=auth_header(TENANT_A),
    )
    assert r.status_code == 200
    assert r.json()["name"] == "After"


@pytest.mark.asyncio
async def test_delete_template(client):
    r = await client.post(
        f"{PREFIX}/templates",
        json=_template_body("To delete"),
        headers=auth_header(TENANT_A),
    )
    tid = r.json()["id"]

    r = await client.delete(f"{PREFIX}/templates/{tid}", headers=auth_header(TENANT_A))
    assert r.status_code == 204

    r = await client.get(f"{PREFIX}/templates/{tid}", headers=auth_header(TENANT_A))
    assert r.status_code == 404


@pytest.mark.asyncio
async def test_generate_executions(client):
    r = await client.post(f"{PREFIX}/generate", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert "generated" in r.json()


@pytest.mark.asyncio
async def test_list_executions(client):
    r = await client.get(f"{PREFIX}/executions", headers=auth_header(TENANT_A))
    assert r.status_code == 200
    assert "items" in r.json()


@pytest.mark.asyncio
async def test_cross_tenant_template_isolation(client):
    r = await client.post(
        f"{PREFIX}/templates",
        json=_template_body("Tenant A tmpl"),
        headers=auth_header(TENANT_A),
    )
    tid = r.json()["id"]

    r = await client.get(f"{PREFIX}/templates/{tid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404

    r = await client.patch(
        f"{PREFIX}/templates/{tid}",
        json={"name": "Hack"},
        headers=auth_header(TENANT_B, 2),
    )
    assert r.status_code == 404

    r = await client.delete(f"{PREFIX}/templates/{tid}", headers=auth_header(TENANT_B, 2))
    assert r.status_code == 404
