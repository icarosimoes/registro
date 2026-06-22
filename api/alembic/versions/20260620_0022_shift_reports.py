"""shift_reports table

Revision ID: 20260620_0022
Revises: 20260620_0021
Create Date: 2026-06-20
"""

import sqlalchemy as sa

from alembic import op

revision = "20260620_0022"
down_revision = "20260620_0021"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "shift_reports",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "company_id",
            sa.Integer,
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("title", sa.String(255), nullable=False),
        sa.Column("description", sa.Text),
        sa.Column("shift_date", sa.Date),
        sa.Column("shift_type", sa.String(20)),
        sa.Column("status", sa.String(60), server_default="Em andamento"),
        sa.Column("started_at", sa.DateTime),
        sa.Column("ended_at", sa.DateTime),
        sa.Column("owner_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("created_by_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("notify_user_ids", sa.JSON),
        sa.Column("deleted_at", sa.DateTime),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_shift_reports_company", "shift_reports", ["company_id"])
    op.create_index("ix_shift_reports_company_date", "shift_reports", ["company_id", "shift_date"])


def downgrade() -> None:
    op.drop_index("ix_shift_reports_company_date", "shift_reports")
    op.drop_index("ix_shift_reports_company", "shift_reports")
    op.drop_table("shift_reports")
