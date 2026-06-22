"""notification preferences and delivery tracking

Revision ID: 20260620_0024
Revises: 20260620_0023
Create Date: 2026-06-20
"""

import sqlalchemy as sa

from alembic import op

revision = "20260620_0024"
down_revision = "20260620_0023"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "notification_preferences",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "company_id",
            sa.Integer,
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column(
            "user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="CASCADE"), nullable=False
        ),
        sa.Column("module", sa.String(80), nullable=False),
        sa.Column("in_app", sa.Boolean, nullable=False, server_default=sa.text("true")),
        sa.Column("email", sa.Boolean, nullable=False, server_default=sa.text("true")),
        sa.UniqueConstraint("user_id", "company_id", "module", name="uq_notif_pref_user_module"),
    )
    op.create_index(
        "ix_notif_pref_company_user", "notification_preferences", ["company_id", "user_id"]
    )

    op.add_column("notifications", sa.Column("email_sent_at", sa.DateTime, nullable=True))


def downgrade() -> None:
    op.drop_column("notifications", "email_sent_at")
    op.drop_index("ix_notif_pref_company_user", table_name="notification_preferences")
    op.drop_table("notification_preferences")
