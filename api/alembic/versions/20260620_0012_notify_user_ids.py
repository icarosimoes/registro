"""Adiciona notify_user_ids e created_by_user_id para notificações."""

from collections.abc import Sequence

import sqlalchemy as sa

from alembic import op

revision: str = "20260620_0012"
down_revision: str | None = "20260620_0011"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    op.add_column("occurrences", sa.Column("notify_user_ids", sa.JSON, nullable=True))
    op.add_column(
        "module_records",
        sa.Column("created_by_user_id", sa.Integer, sa.ForeignKey("users.id"), nullable=True),
    )
    op.add_column("module_records", sa.Column("notify_user_ids", sa.JSON, nullable=True))


def downgrade() -> None:
    op.drop_column("module_records", "notify_user_ids")
    op.drop_column("module_records", "created_by_user_id")
    op.drop_column("occurrences", "notify_user_ids")
