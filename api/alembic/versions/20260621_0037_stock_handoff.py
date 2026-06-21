"""stock items, movements and shift handoffs

Revision ID: 20260621_0037
Revises: 20260621_0036
Create Date: 2026-06-21
"""

from alembic import op
import sqlalchemy as sa

revision = "20260621_0037"
down_revision = "20260621_0036"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "stock_items",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("company_id", sa.Integer, sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("category", sa.String(120)),
        sa.Column("unit", sa.String(40), server_default="un"),
        sa.Column("min_quantity", sa.Integer, server_default="0"),
        sa.Column("current_quantity", sa.Integer, server_default="0"),
        sa.Column("location_id", sa.Integer, sa.ForeignKey("locations.id", ondelete="SET NULL")),
        sa.Column("deleted_at", sa.DateTime),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_stock_items_company", "stock_items", ["company_id"])

    op.create_table(
        "stock_movements",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("company_id", sa.Integer, sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("item_id", sa.Integer, sa.ForeignKey("stock_items.id", ondelete="CASCADE"), nullable=False),
        sa.Column("movement_type", sa.String(20), nullable=False),
        sa.Column("quantity", sa.Integer, nullable=False),
        sa.Column("reason", sa.String(255)),
        sa.Column("work_order_id", sa.Integer, sa.ForeignKey("work_orders.id", ondelete="SET NULL")),
        sa.Column("occurrence_id", sa.Integer, sa.ForeignKey("occurrences.id", ondelete="SET NULL")),
        sa.Column("user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="CASCADE"), nullable=False),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_stock_movements_item", "stock_movements", ["item_id"])

    op.create_table(
        "shift_handoffs",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("company_id", sa.Integer, sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("shift_report_id", sa.Integer, sa.ForeignKey("shift_reports.id", ondelete="SET NULL")),
        sa.Column("title", sa.String(255), nullable=False),
        sa.Column("description", sa.Text),
        sa.Column("priority", sa.String(20), server_default="normal"),
        sa.Column("category", sa.String(120)),
        sa.Column("target_shift", sa.String(20)),
        sa.Column("target_date", sa.Date, nullable=False),
        sa.Column("status", sa.String(20), server_default="pendente"),
        sa.Column("read_at", sa.DateTime),
        sa.Column("read_by_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("resolved_at", sa.DateTime),
        sa.Column("resolved_by_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("resolution_notes", sa.Text),
        sa.Column("created_by_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL")),
        sa.Column("deleted_at", sa.DateTime),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_shift_handoffs_company_date", "shift_handoffs", ["company_id", "target_date"])

    op.execute("""
        INSERT INTO permissions (code, name, module)
        VALUES
            ('stock.view', 'Visualizar estoque', 'stock'),
            ('stock.create', 'Criar itens de estoque', 'stock'),
            ('stock.edit', 'Editar estoque e movimentações', 'stock'),
            ('stock.delete', 'Excluir itens de estoque', 'stock'),
            ('handoff.view', 'Visualizar pendências de turno', 'handoff'),
            ('handoff.create', 'Criar pendências de turno', 'handoff'),
            ('handoff.edit', 'Editar pendências de turno', 'handoff'),
            ('handoff.delete', 'Excluir pendências de turno', 'handoff')
        ON CONFLICT DO NOTHING
    """)

    op.execute("""
        INSERT INTO role_permissions (role_id, permission_id)
        SELECT r.id, p.id
        FROM roles r
        CROSS JOIN permissions p
        WHERE r.code = 'admin'
          AND p.code IN (
              'stock.view', 'stock.create', 'stock.edit', 'stock.delete',
              'handoff.view', 'handoff.create', 'handoff.edit', 'handoff.delete'
          )
        ON CONFLICT DO NOTHING
    """)


def downgrade() -> None:
    op.drop_table("shift_handoffs")
    op.drop_table("stock_movements")
    op.drop_table("stock_items")
    op.execute("""
        DELETE FROM permissions
        WHERE code IN (
            'stock.view', 'stock.create', 'stock.edit', 'stock.delete',
            'handoff.view', 'handoff.create', 'handoff.edit', 'handoff.delete'
        )
    """)
