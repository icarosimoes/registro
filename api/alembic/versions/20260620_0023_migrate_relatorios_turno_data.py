"""migrate relatorios-turno from module_records to shift_reports

Revision ID: 20260620_0023
Revises: 20260620_0022
Create Date: 2026-06-20
"""

from alembic import op
import sqlalchemy as sa

revision = "20260620_0023"
down_revision = "20260620_0022"
branch_labels = None
depends_on = None

SHIFT_MAP = {"Manhã": "morning", "Tarde": "afternoon", "Noite": "night"}


def upgrade() -> None:
    conn = op.get_bind()

    records = conn.execute(
        sa.text(
            "SELECT id, company_id, title, description, category, status, "
            "owner_user_id, created_by_user_id, notify_user_ids, "
            "created_at, updated_at "
            "FROM module_records WHERE module = 'relatorios-turno' AND deleted_at IS NULL"
        )
    ).fetchall()

    for r in records:
        old_id = r[0]
        category = r[4] or ""
        shift_type = SHIFT_MAP.get(category)

        result = conn.execute(
            sa.text(
                "INSERT INTO shift_reports "
                "(company_id, title, description, shift_type, status, owner_user_id, "
                "created_by_user_id, notify_user_ids, created_at, updated_at) "
                "VALUES (:company_id, :title, :description, :shift_type, :status, :owner, "
                ":created_by, :notify, :created_at, :updated_at) RETURNING id"
            ),
            {
                "company_id": r[1], "title": r[2], "description": r[3],
                "shift_type": shift_type, "status": r[5] or "Em andamento",
                "owner": r[6], "created_by": r[7], "notify": r[8],
                "created_at": r[9], "updated_at": r[10],
            },
        )
        new_id = result.scalar()

        for table in ("audit_events", "attachments", "notifications"):
            conn.execute(
                sa.text(
                    f"UPDATE {table} SET entity_type = 'shift_report', entity_id = :new_id "
                    f"WHERE entity_type = 'relatorios-turno' AND entity_id = :old_id"
                ),
                {"new_id": new_id, "old_id": old_id},
            )

    conn.execute(
        sa.text(
            "UPDATE module_records SET deleted_at = CURRENT_TIMESTAMP "
            "WHERE module = 'relatorios-turno' AND deleted_at IS NULL"
        )
    )


def downgrade() -> None:
    conn = op.get_bind()
    conn.execute(
        sa.text(
            "UPDATE module_records SET deleted_at = NULL "
            "WHERE module = 'relatorios-turno' AND deleted_at IS NOT NULL"
        )
    )
