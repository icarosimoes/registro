"""add structured fields to shift_reports

Revision ID: 20260621_0034
Revises: 20260621_0033
Create Date: 2026-06-21
"""

import sqlalchemy as sa

from alembic import op

revision = "20260621_0034"
down_revision = "20260621_0033"
branch_labels = None
depends_on = None

NEW_COLUMNS = [
    ("supervisor", sa.String(120)),
    ("occupation", sa.String(20)),
    ("average_daily", sa.String(20)),
    ("guests", sa.Integer()),
    ("uhs", sa.Integer()),
    ("maintenance_count", sa.Integer()),
    ("cleaning", sa.Integer()),
    ("walk_in", sa.Integer()),
    ("input_quantity", sa.Integer()),
    ("output_quantity", sa.Integer()),
    ("return_of_customers", sa.Integer()),
    ("observations", sa.Text()),
    ("notes_ab", sa.Text()),
    ("notes_reception", sa.Text()),
    ("notes_reservations", sa.Text()),
    ("notes_governance", sa.Text()),
    ("notes_maintenance", sa.Text()),
    ("notes_ti", sa.Text()),
    ("notes_security", sa.Text()),
    ("payload", sa.JSON()),
]


def upgrade() -> None:
    for name, col_type in NEW_COLUMNS:
        op.add_column("shift_reports", sa.Column(name, col_type, nullable=True))

    conn = op.get_bind()
    conn.execute(
        sa.text("""
        UPDATE shift_reports sr
        SET
            supervisor = mr.payload->>'supervisor',
            return_of_customers = NULLIF(mr.payload->>'return_of_customers', '')::int,
            input_quantity = NULLIF(mr.payload->>'inputQuantity', '')::int,
            output_quantity = NULLIF(mr.payload->>'outputQuantity', '')::int,
            payload = mr.payload,
            legacy_id = mr.legacy_id
        FROM module_records mr
        WHERE mr.module = 'relatorios-turno'
          AND mr.company_id = sr.company_id
          AND mr.title = sr.title
    """)
    )


def downgrade() -> None:
    for name, _ in reversed(NEW_COLUMNS):
        op.drop_column("shift_reports", name)
