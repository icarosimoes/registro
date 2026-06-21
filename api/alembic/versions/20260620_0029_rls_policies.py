"""row-level security policies for tenant isolation

Revision ID: 20260620_0029
Revises: 20260620_0028
Create Date: 2026-06-20
"""

from alembic import op
import sqlalchemy as sa

revision = "20260620_0029"
down_revision = "20260620_0028"
branch_labels = None
depends_on = None

TENANT_TABLES = [
    "users",
    "roles",
    "sectors",
    "locations",
    "functions",
    "procedures",
    "occurrences",
    "fiscal_requests",
    "audit_events",
    "module_records",
    "notifications",
    "notification_preferences",
    "attachments",
    "meetings",
    "shift_reports",
    "check_suites",
    "inspection_suites",
    "apartment_inspections",
    "audit_reports",
    "work_diaries",
    "subscriptions",
    "invoices",
    "company_settings",
]


def upgrade() -> None:
    conn = op.get_bind()

    for table in TENANT_TABLES:
        conn.execute(sa.text(
            f'ALTER TABLE "{table}" ENABLE ROW LEVEL SECURITY'
        ))
        conn.execute(sa.text(
            f'ALTER TABLE "{table}" FORCE ROW LEVEL SECURITY'
        ))
        conn.execute(sa.text(
            f'CREATE POLICY tenant_isolation ON "{table}" '
            f"USING (company_id = current_setting('app.current_company_id')::int)"
        ))

    conn.execute(sa.text(
        "ALTER DEFAULT PRIVILEGES GRANT ALL ON TABLES TO current_user"
    ))


def downgrade() -> None:
    conn = op.get_bind()

    for table in TENANT_TABLES:
        conn.execute(sa.text(
            f'DROP POLICY IF EXISTS tenant_isolation ON "{table}"'
        ))
        conn.execute(sa.text(
            f'ALTER TABLE "{table}" DISABLE ROW LEVEL SECURITY'
        ))
