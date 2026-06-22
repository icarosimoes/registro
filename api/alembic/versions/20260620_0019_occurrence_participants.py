"""occurrence participants junction table

Revision ID: 20260620_0019
Revises: 20260620_0018
Create Date: 2026-06-20
"""

import sqlalchemy as sa

from alembic import op

revision = "20260620_0019"
down_revision = "20260620_0018"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "occurrence_participants",
        sa.Column(
            "occurrence_id",
            sa.Integer,
            sa.ForeignKey("occurrences.id", ondelete="CASCADE"),
            primary_key=True,
        ),
        sa.Column(
            "user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="CASCADE"), primary_key=True
        ),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
    )


def downgrade() -> None:
    op.drop_table("occurrence_participants")
