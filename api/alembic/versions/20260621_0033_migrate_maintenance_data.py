"""maintenance_records starts empty — no data migration needed

The maintenance_records table is for real maintenance orders (new feature).
Auditorias noturnas remain in module_records with module='manutencao'.

Revision ID: 20260621_0033
Revises: 20260621_0032
Create Date: 2026-06-21
"""

revision = "20260621_0033"
down_revision = "20260621_0032"
branch_labels = None
depends_on = None


def upgrade() -> None:
    pass


def downgrade() -> None:
    pass
