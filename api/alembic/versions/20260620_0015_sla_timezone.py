"""Adiciona timezone na company e sla_paused_at na fiscal_requests."""

from collections.abc import Sequence

import sqlalchemy as sa

from alembic import op

revision: str = "20260620_0015"
down_revision: str | None = "20260620_0014"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    op.add_column(
        "companies",
        sa.Column("timezone", sa.String(60), server_default="America/Sao_Paulo", nullable=False),
    )
    op.add_column("fiscal_requests", sa.Column("sla_paused_at", sa.DateTime(), nullable=True))
    op.add_column(
        "fiscal_requests",
        sa.Column("sla_paused_seconds", sa.Integer(), server_default="0", nullable=False),
    )


def downgrade() -> None:
    op.drop_column("fiscal_requests", "sla_paused_seconds")
    op.drop_column("fiscal_requests", "sla_paused_at")
    op.drop_column("companies", "timezone")
