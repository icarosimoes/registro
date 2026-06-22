"""add job_title, sector_id, avatar_url to users

Revision ID: 20260621_0038
Revises: 20260621_0037
Create Date: 2026-06-21
"""

from alembic import op
import sqlalchemy as sa

revision = "20260621_0038"
down_revision = "20260621_0037"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.add_column("users", sa.Column("job_title", sa.String(120), nullable=True))
    op.add_column(
        "users",
        sa.Column(
            "sector_id",
            sa.Integer(),
            sa.ForeignKey("sectors.id", ondelete="SET NULL"),
            nullable=True,
        ),
    )
    op.add_column("users", sa.Column("avatar_url", sa.String(500), nullable=True))
    op.create_index("ix_users_sector_id", "users", ["sector_id"])


def downgrade() -> None:
    op.drop_index("ix_users_sector_id", table_name="users")
    op.drop_column("users", "avatar_url")
    op.drop_column("users", "sector_id")
    op.drop_column("users", "job_title")
