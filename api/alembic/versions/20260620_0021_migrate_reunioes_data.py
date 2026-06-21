"""migrate reunioes from module_records to meetings

Revision ID: 20260620_0021
Revises: 20260620_0020
Create Date: 2026-06-20
"""

from alembic import op
import sqlalchemy as sa

revision = "20260620_0021"
down_revision = "20260620_0020"
branch_labels = None
depends_on = None


def upgrade() -> None:
    conn = op.get_bind()

    records = conn.execute(
        sa.text(
            "SELECT id, company_id, title, description, category, status, "
            "owner_user_id, created_by_user_id, notify_user_ids, "
            "created_at, updated_at "
            "FROM module_records WHERE module = 'reunioes' AND deleted_at IS NULL"
        )
    ).fetchall()

    for r in records:
        old_id = r[0]
        result = conn.execute(
            sa.text(
                "INSERT INTO meetings "
                "(company_id, title, description, status, owner_user_id, "
                "created_by_user_id, notify_user_ids, created_at, updated_at) "
                "VALUES (:company_id, :title, :description, :status, :owner, "
                ":created_by, :notify, :created_at, :updated_at) RETURNING id"
            ),
            {
                "company_id": r[1], "title": r[2], "description": r[3],
                "status": r[5] or "Agendada", "owner": r[6],
                "created_by": r[7], "notify": r[8],
                "created_at": r[9], "updated_at": r[10],
            },
        )
        new_id = result.scalar()

        conn.execute(
            sa.text(
                "UPDATE audit_events SET entity_type = 'meeting', entity_id = :new_id "
                "WHERE entity_type = 'reunioes' AND entity_id = :old_id"
            ),
            {"new_id": new_id, "old_id": old_id},
        )
        conn.execute(
            sa.text(
                "UPDATE attachments SET entity_type = 'meeting', entity_id = :new_id "
                "WHERE entity_type = 'reunioes' AND entity_id = :old_id"
            ),
            {"new_id": new_id, "old_id": old_id},
        )
        conn.execute(
            sa.text(
                "UPDATE notifications SET entity_type = 'meeting', entity_id = :new_id "
                "WHERE entity_type = 'reunioes' AND entity_id = :old_id"
            ),
            {"new_id": new_id, "old_id": old_id},
        )

    conn.execute(
        sa.text(
            "UPDATE module_records SET deleted_at = CURRENT_TIMESTAMP "
            "WHERE module = 'reunioes' AND deleted_at IS NULL"
        )
    )


def downgrade() -> None:
    conn = op.get_bind()
    conn.execute(
        sa.text(
            "UPDATE module_records SET deleted_at = NULL "
            "WHERE module = 'reunioes' AND deleted_at IS NOT NULL"
        )
    )
