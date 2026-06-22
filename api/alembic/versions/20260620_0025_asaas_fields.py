"""add asaas_customer_id to companies

Revision ID: 20260620_0025
Revises: 20260620_0024
Create Date: 2026-06-20
"""

import sqlalchemy as sa

from alembic import op

revision = "20260620_0025"
down_revision = "20260620_0024"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.add_column("companies", sa.Column("asaas_customer_id", sa.String(120), nullable=True))


def downgrade() -> None:
    op.drop_column("companies", "asaas_customer_id")
