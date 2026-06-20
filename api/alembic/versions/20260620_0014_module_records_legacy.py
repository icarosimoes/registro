"""Adiciona legacy_id e payload a module_records para importação V1."""

from collections.abc import Sequence

import sqlalchemy as sa
from alembic import op

revision: str = "20260620_0014"
down_revision: str | None = "20260620_0013"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    op.add_column("module_records", sa.Column("legacy_id", sa.Integer, nullable=True))
    op.add_column("module_records", sa.Column("payload", sa.JSON, nullable=True))


def downgrade() -> None:
    op.drop_column("module_records", "payload")
    op.drop_column("module_records", "legacy_id")
