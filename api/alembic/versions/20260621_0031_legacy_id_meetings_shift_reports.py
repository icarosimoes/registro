"""add legacy_id to meetings and shift_reports

Revision ID: 20260621_0031
Revises: 20260621_0030
Create Date: 2026-06-21
"""

from alembic import op
import sqlalchemy as sa

revision = "20260621_0031"
down_revision = "20260621_0030"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.add_column("meetings", sa.Column("legacy_id", sa.Integer(), nullable=True))
    op.add_column("shift_reports", sa.Column("legacy_id", sa.Integer(), nullable=True))


def downgrade() -> None:
    op.drop_column("shift_reports", "legacy_id")
    op.drop_column("meetings", "legacy_id")
