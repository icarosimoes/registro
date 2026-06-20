"""Vincula solicitações fiscais a usuários e adiciona acompanhamento."""

from collections.abc import Sequence

import sqlalchemy as sa
from alembic import op

revision: str = "20260620_0008"
down_revision: str | None = "20260620_0007"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def upgrade() -> None:
    op.add_column("fiscal_requests", sa.Column("requester_email", sa.String(255)))
    op.add_column("fiscal_requests", sa.Column("requester_user_id", sa.Integer(), sa.ForeignKey("users.id")))
    op.add_column("fiscal_requests", sa.Column("responsible_user_id", sa.Integer(), sa.ForeignKey("users.id")))
    op.add_column("fiscal_requests", sa.Column("chess_user_id", sa.String(80)))
    op.add_column("fiscal_requests", sa.Column("reservation_number", sa.String(80)))
    op.add_column("fiscal_requests", sa.Column("sla_deadline", sa.DateTime()))
    op.create_index("ix_fiscal_requests_requester_email", "fiscal_requests", ["requester_email"])
    op.create_index("ix_fiscal_requests_requester_user_id", "fiscal_requests", ["requester_user_id"])
    op.create_index("ix_fiscal_requests_responsible_user_id", "fiscal_requests", ["responsible_user_id"])


def downgrade() -> None:
    op.drop_index("ix_fiscal_requests_responsible_user_id", table_name="fiscal_requests")
    op.drop_index("ix_fiscal_requests_requester_user_id", table_name="fiscal_requests")
    op.drop_index("ix_fiscal_requests_requester_email", table_name="fiscal_requests")
    for column in ["sla_deadline", "reservation_number", "chess_user_id", "responsible_user_id", "requester_user_id", "requester_email"]:
        op.drop_column("fiscal_requests", column)
