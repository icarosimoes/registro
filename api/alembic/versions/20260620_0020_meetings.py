"""meetings, meeting_participants, meeting_subjects tables

Revision ID: 20260620_0020
Revises: 20260620_0019
Create Date: 2026-06-20
"""

import sqlalchemy as sa

from alembic import op

revision = "20260620_0020"
down_revision = "20260620_0019"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "meetings",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "company_id",
            sa.Integer,
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("title", sa.String(255), nullable=False),
        sa.Column("description", sa.Text),
        sa.Column("scheduled_at", sa.DateTime),
        sa.Column("location", sa.String(255)),
        sa.Column("status", sa.String(60), server_default="Agendada"),
        sa.Column("owner_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("created_by_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("notify_user_ids", sa.JSON),
        sa.Column("deleted_at", sa.DateTime),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_meetings_company", "meetings", ["company_id"])
    op.create_index("ix_meetings_company_status", "meetings", ["company_id", "status"])

    op.create_table(
        "meeting_participants",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "meeting_id",
            sa.Integer,
            sa.ForeignKey("meetings.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column(
            "user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="CASCADE"), nullable=False
        ),
        sa.Column("role", sa.String(20), server_default="attendee"),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.UniqueConstraint("meeting_id", "user_id", name="uq_meeting_participant"),
    )

    op.create_table(
        "meeting_subjects",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "meeting_id",
            sa.Integer,
            sa.ForeignKey("meetings.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("title", sa.String(255), nullable=False),
        sa.Column("description", sa.Text),
        sa.Column("sort_order", sa.Integer, server_default="0"),
        sa.Column("resolved", sa.Boolean, server_default=sa.text("false")),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
    )


def downgrade() -> None:
    op.drop_table("meeting_subjects")
    op.drop_table("meeting_participants")
    op.drop_index("ix_meetings_company_status", "meetings")
    op.drop_index("ix_meetings_company", "meetings")
    op.drop_table("meetings")
