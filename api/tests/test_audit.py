"""Testes de cobertura para auditoria (audit_events)."""

import pytest
from sqlalchemy import select

from app.core.audit import compute_diff, record_event
from app.models import AuditEvent
from tests.conftest import TENANT_A, TENANT_B


@pytest.mark.asyncio
async def test_record_event_create(session):
    await record_event(
        session,
        company_id=TENANT_A,
        user_id=1,
        entity_type="occurrence",
        entity_id=1,
        event_type="create",
    )
    await session.flush()

    row = await session.scalar(
        select(AuditEvent).where(
            AuditEvent.company_id == TENANT_A,
            AuditEvent.entity_type == "occurrence",
            AuditEvent.event_type == "create",
        )
    )
    assert row is not None
    assert row.user_id == 1
    assert row.entity_id == 1
    assert row.diff is None


@pytest.mark.asyncio
async def test_record_event_update_with_diff(session):
    diff = {"status": {"from": "Em andamento", "to": "Concluído"}}
    await record_event(
        session,
        company_id=TENANT_A,
        user_id=1,
        entity_type="occurrence",
        entity_id=2,
        event_type="update",
        diff=diff,
    )
    await session.flush()

    row = await session.scalar(
        select(AuditEvent).where(
            AuditEvent.company_id == TENANT_A,
            AuditEvent.entity_id == 2,
            AuditEvent.event_type == "update",
        )
    )
    assert row is not None
    assert row.diff == diff


@pytest.mark.asyncio
async def test_record_event_update_no_diff_skipped(session):
    await record_event(
        session,
        company_id=TENANT_A,
        user_id=1,
        entity_type="occurrence",
        entity_id=3,
        event_type="update",
        diff=None,
    )
    await session.flush()

    row = await session.scalar(
        select(AuditEvent).where(
            AuditEvent.company_id == TENANT_A,
            AuditEvent.entity_id == 3,
            AuditEvent.event_type == "update",
        )
    )
    assert row is None


@pytest.mark.asyncio
async def test_record_event_delete(session):
    await record_event(
        session,
        company_id=TENANT_A,
        user_id=1,
        entity_type="meeting",
        entity_id=10,
        event_type="delete",
    )
    await session.flush()

    row = await session.scalar(
        select(AuditEvent).where(
            AuditEvent.company_id == TENANT_A,
            AuditEvent.entity_type == "meeting",
            AuditEvent.event_type == "delete",
        )
    )
    assert row is not None


@pytest.mark.asyncio
async def test_record_event_attachment_add(session):
    diff = {"filename": "doc.pdf", "content_type": "application/pdf", "size_bytes": 1234}
    await record_event(
        session,
        company_id=TENANT_A,
        user_id=1,
        entity_type="occurrence",
        entity_id=5,
        event_type="attachment_add",
        diff=diff,
    )
    await session.flush()

    row = await session.scalar(
        select(AuditEvent).where(
            AuditEvent.company_id == TENANT_A,
            AuditEvent.entity_id == 5,
            AuditEvent.event_type == "attachment_add",
        )
    )
    assert row is not None
    assert row.diff["filename"] == "doc.pdf"


@pytest.mark.asyncio
async def test_audit_tenant_isolation(session):
    await record_event(
        session,
        company_id=TENANT_A,
        user_id=1,
        entity_type="occurrence",
        entity_id=100,
        event_type="create",
    )
    await session.flush()

    row = await session.scalar(
        select(AuditEvent).where(
            AuditEvent.company_id == TENANT_B,
            AuditEvent.entity_id == 100,
        )
    )
    assert row is None


def test_compute_diff_detects_changes():
    before = {"status": "Em andamento", "title": "Test"}
    after = {"status": "Concluído", "title": "Test"}
    result = compute_diff(before, after)
    assert result is not None
    assert "status" in result
    assert result["status"]["from"] == "Em andamento"
    assert result["status"]["to"] == "Concluído"
    assert "title" not in result


def test_compute_diff_returns_none_when_no_changes():
    before = {"status": "Em andamento", "title": "Test"}
    after = {"status": "Em andamento", "title": "Test"}
    result = compute_diff(before, after)
    assert result is None


def test_compute_diff_multiple_changes():
    before = {"status": "1", "title": "A", "desc": "X"}
    after = {"status": "2", "title": "B", "desc": "X"}
    result = compute_diff(before, after)
    assert result is not None
    assert len(result) == 2
    assert "status" in result
    assert "title" in result


def test_compute_diff_added_field():
    """Campo presente apenas no after deve aparecer no diff."""
    before = {"status": "Em andamento"}
    after = {"status": "Em andamento", "priority": "alta"}
    result = compute_diff(before, after)
    assert result is not None
    assert "priority" in result
    assert result["priority"]["from"] is None
    assert result["priority"]["to"] == "alta"


def test_compute_diff_none_to_value():
    """Transicao de None para um valor deve ser detectada."""
    before = {"notes": None}
    after = {"notes": "observacao"}
    result = compute_diff(before, after)
    assert result is not None
    assert result["notes"]["from"] is None
    assert result["notes"]["to"] == "observacao"


def test_compute_diff_value_to_none():
    """Transicao de um valor para None deve ser detectada."""
    before = {"notes": "observacao"}
    after = {"notes": None}
    result = compute_diff(before, after)
    assert result is not None
    assert result["notes"]["from"] == "observacao"
    assert result["notes"]["to"] is None


def test_compute_diff_both_none_no_change():
    """Dois None devem ser considerados iguais (sem diff)."""
    before = {"notes": None}
    after = {"notes": None}
    result = compute_diff(before, after)
    assert result is None


def test_compute_diff_empty_dicts():
    before = {}
    after = {}
    result = compute_diff(before, after)
    assert result is None


@pytest.mark.asyncio
async def test_record_event_update_empty_dict_skipped(session):
    """Update com diff={} (dict vazio) deve ser tratado como sem diff."""
    await record_event(
        session,
        company_id=TENANT_A,
        user_id=1,
        entity_type="test_entity",
        entity_id=999,
        event_type="update",
        diff={},
    )
    await session.flush()

    row = await session.scalar(
        select(AuditEvent).where(
            AuditEvent.company_id == TENANT_A,
            AuditEvent.entity_id == 999,
            AuditEvent.event_type == "update",
        )
    )
    assert row is None
