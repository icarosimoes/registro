"""preventive maintenance plans and recurring checklists

Revision ID: 20260621_0036
Revises: 20260621_0035
Create Date: 2026-06-21
"""

import sqlalchemy as sa

from alembic import op

revision = "20260621_0036"
down_revision = "20260621_0035"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "preventive_plans",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "company_id",
            sa.Integer,
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("description", sa.Text),
        sa.Column("recurrence", sa.String(20), nullable=False),
        sa.Column("category", sa.String(120)),
        sa.Column("priority", sa.String(20), server_default="media"),
        sa.Column("sla_hours", sa.Integer),
        sa.Column("location_id", sa.Integer, sa.ForeignKey("locations.id", ondelete="SET NULL")),
        sa.Column("assigned_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("active", sa.Boolean, server_default=sa.text("true"), nullable=False),
        sa.Column("next_due", sa.Date),
        sa.Column("last_generated_at", sa.DateTime),
        sa.Column("deleted_at", sa.DateTime),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index(
        "ix_preventive_plans_company_active", "preventive_plans", ["company_id", "active"]
    )

    op.create_table(
        "checklist_templates",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "company_id",
            sa.Integer,
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("description", sa.Text),
        sa.Column("recurrence", sa.String(20), nullable=False),
        sa.Column("category", sa.String(120)),
        sa.Column("assigned_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("active", sa.Boolean, server_default=sa.text("true"), nullable=False),
        sa.Column("next_due", sa.Date),
        sa.Column("last_generated_at", sa.DateTime),
        sa.Column("deleted_at", sa.DateTime),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )

    op.create_table(
        "checklist_template_items",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "template_id",
            sa.Integer,
            sa.ForeignKey("checklist_templates.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("label", sa.String(255), nullable=False),
        sa.Column("sort_order", sa.Integer, server_default="0"),
    )

    op.create_table(
        "checklist_executions",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "company_id",
            sa.Integer,
            sa.ForeignKey("companies.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column(
            "template_id",
            sa.Integer,
            sa.ForeignKey("checklist_templates.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("due_date", sa.Date, nullable=False),
        sa.Column("status", sa.String(40), server_default="pendente"),
        sa.Column("completed_at", sa.DateTime),
        sa.Column(
            "completed_by_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")
        ),
        sa.Column("notes", sa.Text),
        sa.Column("deleted_at", sa.DateTime),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index(
        "ix_checklist_exec_company_due", "checklist_executions", ["company_id", "due_date"]
    )

    op.create_table(
        "checklist_execution_items",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column(
            "execution_id",
            sa.Integer,
            sa.ForeignKey("checklist_executions.id", ondelete="CASCADE"),
            nullable=False,
        ),
        sa.Column("label", sa.String(255), nullable=False),
        sa.Column("sort_order", sa.Integer, server_default="0"),
        sa.Column("checked", sa.Boolean, server_default=sa.text("false")),
        sa.Column("checked_at", sa.DateTime),
    )

    # Permissions
    op.execute("""
        INSERT INTO permissions (code, name, module)
        VALUES
            ('preventive_plan.view', 'Visualizar planos preventivos', 'preventive_plan'),
            ('preventive_plan.create', 'Criar planos preventivos', 'preventive_plan'),
            ('preventive_plan.edit', 'Editar planos preventivos', 'preventive_plan'),
            ('preventive_plan.delete', 'Excluir planos preventivos', 'preventive_plan'),
            ('checklist.view', 'Visualizar checklists', 'checklist'),
            ('checklist.create', 'Criar checklists', 'checklist'),
            ('checklist.edit', 'Editar checklists', 'checklist'),
            ('checklist.delete', 'Excluir checklists', 'checklist')
        ON CONFLICT DO NOTHING
    """)

    # Grant to admin role
    op.execute("""
        INSERT INTO role_permissions (role_id, permission_id)
        SELECT r.id, p.id
        FROM roles r
        CROSS JOIN permissions p
        WHERE r.code = 'admin'
          AND p.code IN (
              'preventive_plan.view', 'preventive_plan.create',
              'preventive_plan.edit', 'preventive_plan.delete',
              'checklist.view', 'checklist.create',
              'checklist.edit', 'checklist.delete'
          )
        ON CONFLICT DO NOTHING
    """)


def downgrade() -> None:
    op.drop_table("checklist_execution_items")
    op.drop_table("checklist_executions")
    op.drop_table("checklist_template_items")
    op.drop_table("checklist_templates")
    op.drop_table("preventive_plans")
    op.execute("""
        DELETE FROM permissions
        WHERE code IN (
            'preventive_plan.view', 'preventive_plan.create',
            'preventive_plan.edit', 'preventive_plan.delete',
            'checklist.view', 'checklist.create',
            'checklist.edit', 'checklist.delete'
        )
    """)
