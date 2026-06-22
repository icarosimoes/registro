"""Torna legacy_id nullable para registros criados pelo Registro."""

from collections.abc import Sequence

import sqlalchemy as sa

from alembic import op

revision: str = "20260620_0005"
down_revision: str | None = "20260620_0004"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None

tables = ["sectors", "locations", "functions", "procedures", "occurrences"]


def upgrade() -> None:
    for table in tables:
        op.alter_column(table, "legacy_id", existing_type=sa.Integer(), nullable=True)


def downgrade() -> None:
    for table in tables:
        op.alter_column(table, "legacy_id", existing_type=sa.Integer(), nullable=False)
