"""Cria tabela company_settings para configurações por tenant."""

from collections.abc import Sequence

import sqlalchemy as sa

from alembic import op

revision: str = "20260620_0011"
down_revision: str | None = "20260620_0010"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    op.create_table(
        "company_settings",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "company_id",
            sa.Integer,
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
            index=True,
        ),
        sa.Column("key", sa.String(120), nullable=False, index=True),
        sa.Column("value", sa.JSON, nullable=False),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
        sa.UniqueConstraint("company_id", "key", name="uq_company_settings_company_key"),
    )


def downgrade() -> None:
    op.drop_table("company_settings")
