"""Persiste solicitações fiscais recebidas do Chess Hotel."""

from collections.abc import Sequence

import sqlalchemy as sa
from alembic import op

revision: str = "20260620_0003"
down_revision: str | None = "20260619_0002"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    op.create_table(
        "fiscal_requests",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("company_id", sa.Integer(), sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("protocol", sa.String(40), nullable=False, unique=True),
        sa.Column("request_type", sa.String(120), nullable=False),
        sa.Column("title", sa.String(255)),
        sa.Column("apartment", sa.String(40)),
        sa.Column("requester", sa.String(160), nullable=False),
        sa.Column("description", sa.Text()),
        sa.Column("origin", sa.String(80), nullable=False, server_default="chess-hotel"),
        sa.Column("status", sa.String(40), nullable=False, server_default="Em andamento"),
        sa.Column("payload", sa.JSON(), nullable=False),
        sa.Column("created_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
    )
    op.create_index("ix_fiscal_requests_company_id", "fiscal_requests", ["company_id"])
    op.create_index("ix_fiscal_requests_request_type", "fiscal_requests", ["request_type"])
    op.create_index("ix_fiscal_requests_status", "fiscal_requests", ["status"])


def downgrade() -> None:
    op.drop_table("fiscal_requests")
