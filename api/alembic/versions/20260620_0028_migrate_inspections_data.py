"""migrate inspections from module_records to apartment_inspections

Revision ID: 20260620_0028
Revises: 20260620_0027
Create Date: 2026-06-20
"""

import json

from alembic import op
import sqlalchemy as sa

revision = "20260620_0028"
down_revision = "20260620_0027"
branch_labels = None
depends_on = None


def upgrade() -> None:
    conn = op.get_bind()

    records = conn.execute(
        sa.text(
            "SELECT id, company_id, title, description, category, status, "
            "owner_user_id, created_by_user_id, payload, "
            "created_at, updated_at "
            "FROM module_records WHERE module = 'inspections' AND deleted_at IS NULL"
        )
    ).fetchall()

    for r in records:
        old_id = r[0]
        payload = r[8]
        if isinstance(payload, str):
            try:
                payload = json.loads(payload)
            except (json.JSONDecodeError, TypeError):
                payload = {}
        if payload is None:
            payload = {}

        unit = payload.get("unit") or payload.get("unidade") or ""
        apartment = payload.get("apartment") or payload.get("apartamento") or ""
        inspection_type = (
            payload.get("inspection_type")
            or payload.get("tipo")
            or r[4]  # category as fallback
            or "periodic"
        )
        notes = r[3] or payload.get("notes") or payload.get("observacoes")

        result = conn.execute(
            sa.text(
                "INSERT INTO apartment_inspections "
                "(company_id, unit, apartment, inspection_type, "
                "inspector_user_id, status, notes, "
                "created_at, updated_at) "
                "VALUES (:company_id, :unit, :apartment, :inspection_type, "
                ":inspector, :status, :notes, "
                ":created_at, :updated_at) RETURNING id"
            ),
            {
                "company_id": r[1],
                "unit": unit[:80] if unit else None,
                "apartment": apartment[:80] if apartment else None,
                "inspection_type": inspection_type[:40],
                "inspector": r[6],
                "status": r[5] or "Pendente",
                "notes": notes,
                "created_at": r[9],
                "updated_at": r[10],
            },
        )
        new_id = result.scalar()

        items = payload.get("items") or payload.get("itens") or []
        for idx, item in enumerate(items):
            if isinstance(item, dict):
                conn.execute(
                    sa.text(
                        "INSERT INTO apartment_inspection_items "
                        '(inspection_id, "condition", notes, sort_order) '
                        "VALUES (:inspection_id, :cond, :notes, :sort_order)"
                    ),
                    {
                        "inspection_id": new_id,
                        "cond": item.get("condition", item.get("estado", "ok"))[:40],
                        "notes": item.get("notes") or item.get("observacao"),
                        "sort_order": idx,
                    },
                )

        conn.execute(
            sa.text(
                "UPDATE audit_events SET entity_type = 'apartment_inspection', "
                "entity_id = :new_id "
                "WHERE entity_type = 'inspections' AND entity_id = :old_id"
            ),
            {"new_id": new_id, "old_id": old_id},
        )
        conn.execute(
            sa.text(
                "UPDATE attachments SET entity_type = 'apartment_inspection', "
                "entity_id = :new_id "
                "WHERE entity_type = 'inspections' AND entity_id = :old_id"
            ),
            {"new_id": new_id, "old_id": old_id},
        )
        conn.execute(
            sa.text(
                "UPDATE notifications SET entity_type = 'apartment_inspection', "
                "entity_id = :new_id "
                "WHERE entity_type = 'inspections' AND entity_id = :old_id"
            ),
            {"new_id": new_id, "old_id": old_id},
        )

    if records:
        conn.execute(
            sa.text(
                "UPDATE module_records SET deleted_at = CURRENT_TIMESTAMP "
                "WHERE module = 'inspections' AND deleted_at IS NULL"
            )
        )


def downgrade() -> None:
    conn = op.get_bind()
    conn.execute(
        sa.text(
            "UPDATE module_records SET deleted_at = NULL "
            "WHERE module = 'inspections' AND deleted_at IS NOT NULL"
        )
    )
