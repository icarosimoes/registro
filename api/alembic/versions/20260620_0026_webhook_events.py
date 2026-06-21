"""webhook events for idempotent processing

Revision ID: 20260620_0026
Revises: 20260620_0025
Create Date: 2026-06-20
"""

from alembic import op
import sqlalchemy as sa

revision = "20260620_0026"
down_revision = "20260620_0025"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "webhook_events",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("provider", sa.String(40), nullable=False, index=True),
        sa.Column("external_id", sa.String(200), nullable=False),
        sa.Column("event_type", sa.String(120), nullable=False),
        sa.Column("payload", sa.JSON, nullable=True),
        sa.Column("processed_at", sa.DateTime, nullable=True),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
        sa.UniqueConstraint("provider", "external_id", name="uq_webhook_provider_ext"),
    )


def downgrade() -> None:
    op.drop_table("webhook_events")
