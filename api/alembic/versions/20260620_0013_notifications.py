"""Cria tabela de notificações in-app."""

from collections.abc import Sequence

import sqlalchemy as sa

from alembic import op

revision: str = "20260620_0013"
down_revision: str | None = "20260620_0012"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    op.create_table(
        "notifications",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "company_id",
            sa.Integer,
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
            index=True,
        ),
        sa.Column("user_id", sa.Integer, sa.ForeignKey("users.id"), nullable=False, index=True),
        sa.Column("title", sa.String(255), nullable=False),
        sa.Column("body", sa.Text, nullable=True),
        sa.Column("category", sa.String(60), nullable=False, server_default="info", index=True),
        sa.Column("entity_type", sa.String(80), nullable=True),
        sa.Column("entity_id", sa.Integer, nullable=True),
        sa.Column("read_at", sa.DateTime, nullable=True),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now(), nullable=False),
    )


def downgrade() -> None:
    op.drop_table("notifications")
