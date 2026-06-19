"""Adiciona o núcleo operacional para importação da V1."""

from collections.abc import Sequence

import sqlalchemy as sa

from alembic import op

revision: str = "20260619_0002"
down_revision: str | None = "20260619_0001"
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def timestamps() -> list[sa.Column]:
    return [
        sa.Column("created_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
    ]


def legacy_catalog(name: str) -> None:
    op.create_table(
        name,
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column(
            "company_id",
            sa.Integer(),
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("legacy_id", sa.Integer(), nullable=False),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("deleted_at", sa.DateTime()),
        *timestamps(),
        sa.UniqueConstraint("company_id", "legacy_id", name=f"uq_{name}_legacy"),
    )
    op.create_index(f"ix_{name}_company_id", name, ["company_id"])


def upgrade() -> None:
    op.add_column("users", sa.Column("legacy_id", sa.Integer()))
    op.create_index("ix_users_legacy_id", "users", ["legacy_id"])
    op.create_unique_constraint("uq_users_company_legacy", "users", ["company_id", "legacy_id"])

    legacy_catalog("sectors")
    legacy_catalog("locations")
    legacy_catalog("functions")
    op.create_table(
        "procedures",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column(
            "company_id",
            sa.Integer(),
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("legacy_id", sa.Integer(), nullable=False),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("link", sa.String(255)),
        sa.Column("file", sa.String(255)),
        sa.Column("deleted_at", sa.DateTime()),
        *timestamps(),
        sa.UniqueConstraint("company_id", "legacy_id", name="uq_procedures_legacy"),
    )
    op.create_index("ix_procedures_company_id", "procedures", ["company_id"])
    op.create_table(
        "occurrences",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column(
            "company_id",
            sa.Integer(),
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("legacy_id", sa.Integer(), nullable=False),
        sa.Column("title", sa.String(255), nullable=False),
        sa.Column("description", sa.Text()),
        sa.Column("comments", sa.Text()),
        sa.Column("unit", sa.String(255)),
        sa.Column("deadline", sa.Date()),
        sa.Column("status", sa.Integer(), nullable=False, server_default="1"),
        sa.Column("legacy_type_id", sa.Integer()),
        sa.Column("legacy_receiver_user_id", sa.Integer()),
        sa.Column("location_id", sa.Integer(), sa.ForeignKey("locations.id")),
        sa.Column("sector_id", sa.Integer(), sa.ForeignKey("sectors.id")),
        sa.Column("owner_user_id", sa.Integer(), sa.ForeignKey("users.id")),
        sa.Column("created_by_user_id", sa.Integer(), sa.ForeignKey("users.id")),
        sa.Column("updated_by_user_id", sa.Integer(), sa.ForeignKey("users.id")),
        sa.Column("file", sa.Text()),
        sa.Column("deleted_at", sa.DateTime()),
        *timestamps(),
        sa.UniqueConstraint("company_id", "legacy_id", name="uq_occurrences_legacy"),
    )
    op.create_index("ix_occurrences_company_id", "occurrences", ["company_id"])
    op.create_index("ix_occurrences_status", "occurrences", ["status"])
    op.create_table(
        "legacy_import_runs",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("source", sa.String(80), nullable=False),
        sa.Column("checksum_sha256", sa.String(64), nullable=False, unique=True),
        sa.Column("status", sa.String(20), nullable=False),
        sa.Column("report", sa.Text()),
        sa.Column("started_at", sa.DateTime(), nullable=False),
        sa.Column("finished_at", sa.DateTime()),
    )


def downgrade() -> None:
    for table in [
        "legacy_import_runs",
        "occurrences",
        "procedures",
        "functions",
        "locations",
        "sectors",
    ]:
        op.drop_table(table)
    op.drop_constraint("uq_users_company_legacy", "users", type_="unique")
    op.drop_index("ix_users_legacy_id", table_name="users")
    op.drop_column("users", "legacy_id")
