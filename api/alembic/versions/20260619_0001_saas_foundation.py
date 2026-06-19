"""Cria a fundação SaaS multiempresa."""

from collections.abc import Sequence

import sqlalchemy as sa

from alembic import op

revision: str = "20260619_0001"
down_revision: str | None = None
branch_labels: str | Sequence[str] | None = None
depends_on: str | Sequence[str] | None = None


def timestamps() -> list[sa.Column]:
    return [
        sa.Column("created_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
    ]


def upgrade() -> None:
    op.create_table(
        "companies",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("name", sa.String(160), nullable=False),
        sa.Column("slug", sa.String(100), nullable=False, unique=True),
        sa.Column("email", sa.String(255)),
        sa.Column("document", sa.String(20), unique=True),
        sa.Column("status", sa.String(20), nullable=False, server_default="active"),
        sa.Column("deleted_at", sa.DateTime()),
        *timestamps(),
    )
    op.create_index("ix_companies_status", "companies", ["status"])
    op.create_table(
        "permissions",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("code", sa.String(120), nullable=False, unique=True),
        sa.Column("name", sa.String(160), nullable=False),
        sa.Column("module", sa.String(80), nullable=False),
        *timestamps(),
    )
    op.create_index("ix_permissions_module", "permissions", ["module"])
    op.create_table(
        "roles",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column(
            "company_id",
            sa.Integer(),
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("code", sa.String(80), nullable=False),
        sa.Column("name", sa.String(120), nullable=False),
        *timestamps(),
        sa.UniqueConstraint("company_id", "code", name="uq_roles_company_code"),
    )
    op.create_index("ix_roles_company_id", "roles", ["company_id"])
    op.create_table(
        "role_permissions",
        sa.Column(
            "role_id", sa.Integer(), sa.ForeignKey("roles.id", ondelete="CASCADE"), primary_key=True
        ),
        sa.Column(
            "permission_id",
            sa.Integer(),
            sa.ForeignKey("permissions.id", ondelete="CASCADE"),
            primary_key=True,
        ),
    )
    op.create_table(
        "users",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column(
            "company_id",
            sa.Integer(),
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("role_id", sa.Integer(), sa.ForeignKey("roles.id", ondelete="SET NULL")),
        sa.Column("name", sa.String(160), nullable=False),
        sa.Column("email", sa.String(255), nullable=False),
        sa.Column("password", sa.String(255), nullable=False),
        sa.Column("active", sa.Boolean(), nullable=False, server_default=sa.true()),
        sa.Column("email_verified_at", sa.DateTime()),
        sa.Column("deleted_at", sa.DateTime()),
        *timestamps(),
        sa.UniqueConstraint("company_id", "email", name="uq_users_company_email"),
    )
    op.create_index("ix_users_company_id", "users", ["company_id"])
    op.create_index("ix_users_email", "users", ["email"])
    op.create_index("ix_users_active", "users", ["active"])
    op.create_table(
        "platform_users",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("email", sa.String(255), nullable=False, unique=True),
        sa.Column("name", sa.String(160), nullable=False),
        sa.Column("password_hash", sa.String(255), nullable=False),
        sa.Column("role", sa.String(30), nullable=False, server_default="read_only"),
        sa.Column("active", sa.Boolean(), nullable=False, server_default=sa.true()),
        sa.Column("last_login_at", sa.DateTime()),
        sa.Column("deleted_at", sa.DateTime()),
        *timestamps(),
    )
    op.create_table(
        "plans",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("code", sa.String(60), nullable=False, unique=True),
        sa.Column("name", sa.String(120), nullable=False),
        sa.Column("price_cents", sa.Integer(), nullable=False, server_default="0"),
        sa.Column("currency", sa.String(3), nullable=False, server_default="BRL"),
        sa.Column("billing_period", sa.String(20), nullable=False, server_default="monthly"),
        sa.Column("features", sa.JSON(), nullable=False),
        sa.Column("limits", sa.JSON(), nullable=False),
        sa.Column("active", sa.Boolean(), nullable=False, server_default=sa.true()),
        sa.Column("public", sa.Boolean(), nullable=False, server_default=sa.true()),
        *timestamps(),
    )
    op.create_table(
        "subscriptions",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column(
            "company_id",
            sa.Integer(),
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("plan_id", sa.Integer(), sa.ForeignKey("plans.id"), nullable=False),
        sa.Column("status", sa.String(20), nullable=False, server_default="trial"),
        sa.Column("trial_ends_at", sa.DateTime()),
        sa.Column("current_period_start", sa.DateTime()),
        sa.Column("current_period_end", sa.DateTime()),
        sa.Column("billing_provider_subscription_id", sa.String(120)),
        sa.Column("past_due_since", sa.DateTime()),
        sa.Column("overdue_warned_at", sa.DateTime()),
        sa.Column("suspended_at", sa.DateTime()),
        *timestamps(),
    )
    op.create_index("ix_subscriptions_company_id", "subscriptions", ["company_id"])
    op.create_index("ix_subscriptions_status", "subscriptions", ["status"])
    op.create_table(
        "invoices",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column(
            "company_id",
            sa.Integer(),
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column(
            "subscription_id", sa.Integer(), sa.ForeignKey("subscriptions.id"), nullable=False
        ),
        sa.Column("external_payment_id", sa.String(120), unique=True),
        sa.Column("value_cents", sa.Integer(), nullable=False),
        sa.Column("status", sa.String(20), nullable=False, server_default="pending"),
        sa.Column("due_date", sa.Date(), nullable=False),
        sa.Column("payment_date", sa.Date()),
        *timestamps(),
    )
    op.create_index("ix_invoices_company_id", "invoices", ["company_id"])
    op.create_index("ix_invoices_status", "invoices", ["status"])
    op.create_table(
        "platform_audit_logs",
        sa.Column("id", sa.Integer(), primary_key=True),
        sa.Column("platform_user_id", sa.Integer(), sa.ForeignKey("platform_users.id")),
        sa.Column("action", sa.String(120), nullable=False),
        sa.Column("target_type", sa.String(80), nullable=False),
        sa.Column("target_id", sa.String(80)),
        sa.Column("payload", sa.JSON()),
        sa.Column("ip_address", sa.String(45)),
        sa.Column("created_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
    )
    op.create_index("ix_platform_audit_logs_action", "platform_audit_logs", ["action"])


def downgrade() -> None:
    for table in [
        "platform_audit_logs",
        "invoices",
        "subscriptions",
        "plans",
        "platform_users",
        "users",
        "role_permissions",
        "roles",
        "permissions",
        "companies",
    ]:
        op.drop_table(table)
