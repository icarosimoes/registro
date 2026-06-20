"""Adiciona title e description a fiscal_requests."""

from collections.abc import Sequence

import sqlalchemy as sa
from alembic import op

revision: str = "20260620_0004"
down_revision: str | None = "20260620_0003"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    pass


def downgrade() -> None:
    pass
