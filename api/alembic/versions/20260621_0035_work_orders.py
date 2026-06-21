"""create work_orders table and permissions

Revision ID: 20260621_0035
Revises: 20260621_0034
Create Date: 2026-06-21
"""

from alembic import op
import sqlalchemy as sa

revision = "20260621_0035"
down_revision = "20260621_0034"
branch_labels = None
depends_on = None

PERMISSIONS = [
    ("work_order.view", "Ver ordens de serviço", "work_orders"),
    ("work_order.create", "Criar ordens de serviço", "work_orders"),
    ("work_order.edit", "Editar ordens de serviço", "work_orders"),
    ("work_order.delete", "Excluir ordens de serviço", "work_orders"),
]


def upgrade() -> None:
    op.create_table(
        "work_orders",
        sa.Column("id", sa.Integer(), nullable=False, autoincrement=True),
        sa.Column("company_id", sa.Integer(), nullable=False),
        sa.Column("title", sa.String(255), nullable=False),
        sa.Column("description", sa.Text(), nullable=True),
        sa.Column("status", sa.String(40), nullable=False, server_default="aberta"),
        sa.Column("priority", sa.String(20), nullable=True),
        sa.Column("category", sa.String(120), nullable=True),
        sa.Column("location_id", sa.Integer(), nullable=True),
        sa.Column("occurrence_id", sa.Integer(), nullable=True),
        sa.Column("maintenance_id", sa.Integer(), nullable=True),
        sa.Column("assigned_user_id", sa.Integer(), nullable=True),
        sa.Column("created_by_user_id", sa.Integer(), nullable=True),
        sa.Column("validated_by_user_id", sa.Integer(), nullable=True),
        sa.Column("notify_user_ids", sa.JSON(), nullable=True),
        sa.Column("sla_hours", sa.Integer(), nullable=True),
        sa.Column("sla_deadline", sa.DateTime(), nullable=True),
        sa.Column("started_at", sa.DateTime(), nullable=True),
        sa.Column("completed_at", sa.DateTime(), nullable=True),
        sa.Column("validated_at", sa.DateTime(), nullable=True),
        sa.Column("created_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
        sa.Column("updated_at", sa.DateTime(), server_default=sa.func.now(), nullable=False),
        sa.Column("deleted_at", sa.DateTime(), nullable=True),
        sa.ForeignKeyConstraint(["company_id"], ["companies.id"], ondelete="CASCADE"),
        sa.ForeignKeyConstraint(["location_id"], ["locations.id"], ondelete="SET NULL"),
        sa.ForeignKeyConstraint(["occurrence_id"], ["occurrences.id"], ondelete="SET NULL"),
        sa.ForeignKeyConstraint(["maintenance_id"], ["maintenance_records.id"], ondelete="SET NULL"),
        sa.ForeignKeyConstraint(["assigned_user_id"], ["users.id"], ondelete="SET NULL"),
        sa.ForeignKeyConstraint(["created_by_user_id"], ["users.id"], ondelete="SET NULL"),
        sa.ForeignKeyConstraint(["validated_by_user_id"], ["users.id"], ondelete="SET NULL"),
        sa.PrimaryKeyConstraint("id"),
    )
    op.create_index("ix_work_orders_company_id", "work_orders", ["company_id"])
    op.create_index("ix_work_orders_status", "work_orders", ["company_id", "status"])

    # RLS
    op.execute(
        "ALTER TABLE work_orders ENABLE ROW LEVEL SECURITY"
    )
    op.execute(
        "CREATE POLICY tenant_isolation ON work_orders "
        "USING (company_id = current_setting('app.current_company_id')::int)"
    )

    # Permissions
    conn = op.get_bind()
    for code, name, module in PERMISSIONS:
        conn.execute(
            sa.text(
                "INSERT INTO permissions (code, name, module) VALUES (:code, :name, :module) "
                "ON CONFLICT (code) DO NOTHING"
            ),
            {"code": code, "name": name, "module": module},
        )


def downgrade() -> None:
    op.execute("DROP POLICY IF EXISTS tenant_isolation ON work_orders")
    op.execute("ALTER TABLE work_orders DISABLE ROW LEVEL SECURITY")
    op.drop_index("ix_work_orders_status", table_name="work_orders")
    op.drop_index("ix_work_orders_company_id", table_name="work_orders")
    op.drop_table("work_orders")
    conn = op.get_bind()
    for code, _, _ in PERMISSIONS:
        conn.execute(sa.text("DELETE FROM permissions WHERE code = :code"), {"code": code})
