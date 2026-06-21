"""seed permissions and admin roles

Revision ID: 20260620_0018
Revises: 20260620_0017
Create Date: 2026-06-20
"""

from alembic import op
import sqlalchemy as sa

revision = "20260620_0018"
down_revision = "20260620_0017"
branch_labels = None
depends_on = None

PERMISSIONS = [
    ("occurrence.view", "Ver ocorrências", "occurrence"),
    ("occurrence.create", "Criar ocorrências", "occurrence"),
    ("occurrence.edit", "Editar ocorrências", "occurrence"),
    ("occurrence.delete", "Excluir ocorrências", "occurrence"),
    ("fiscal_request.view", "Ver solicitações fiscais", "fiscal_request"),
    ("fiscal_request.create", "Criar solicitações fiscais", "fiscal_request"),
    ("fiscal_request.edit", "Editar solicitações fiscais", "fiscal_request"),
    ("fiscal_request.delete", "Excluir solicitações fiscais", "fiscal_request"),
    ("user.view", "Ver usuários", "user"),
    ("user.create", "Criar usuários", "user"),
    ("user.edit", "Editar usuários", "user"),
    ("user.delete", "Excluir usuários", "user"),
    ("registry.view", "Ver cadastros", "registry"),
    ("registry.create", "Criar cadastros", "registry"),
    ("registry.edit", "Editar cadastros", "registry"),
    ("registry.delete", "Excluir cadastros", "registry"),
    ("module.view", "Ver módulos genéricos", "module"),
    ("module.create", "Criar registros de módulo", "module"),
    ("module.edit", "Editar registros de módulo", "module"),
    ("module.delete", "Excluir registros de módulo", "module"),
    ("procedure.view", "Ver procedimentos", "procedure"),
    ("procedure.create", "Criar procedimentos", "procedure"),
    ("procedure.edit", "Editar procedimentos", "procedure"),
    ("procedure.delete", "Excluir procedimentos", "procedure"),
    ("settings.view", "Ver configurações", "settings"),
    ("settings.edit", "Editar configurações", "settings"),
    ("meeting.view", "Ver reuniões", "meeting"),
    ("meeting.create", "Criar reuniões", "meeting"),
    ("meeting.edit", "Editar reuniões", "meeting"),
    ("meeting.delete", "Excluir reuniões", "meeting"),
    ("shift_report.view", "Ver relatórios de turno", "shift_report"),
    ("shift_report.create", "Criar relatórios de turno", "shift_report"),
    ("shift_report.edit", "Editar relatórios de turno", "shift_report"),
    ("shift_report.delete", "Excluir relatórios de turno", "shift_report"),
    ("*", "Acesso total (administrador)", "system"),
]


def upgrade() -> None:
    conn = op.get_bind()

    for code, name, module in PERMISSIONS:
        conn.execute(
            sa.text(
                "INSERT INTO permissions (code, name, module, created_at, updated_at) "
                "VALUES (:code, :name, :module, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) "
                "ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name, module = EXCLUDED.module"
            ),
            {"code": code, "name": name, "module": module},
        )

    wildcard_id = conn.execute(
        sa.text("SELECT id FROM permissions WHERE code = '*'")
    ).scalar()

    companies = conn.execute(sa.text("SELECT id FROM companies")).fetchall()
    for (company_id,) in companies:
        existing_admin = conn.execute(
            sa.text("SELECT id FROM roles WHERE company_id = :cid AND code = 'admin'"),
            {"cid": company_id},
        ).scalar()

        if existing_admin is None:
            result = conn.execute(
                sa.text(
                    "INSERT INTO roles (company_id, code, name, created_at, updated_at) "
                    "VALUES (:cid, 'admin', 'Administrador', "
                    "CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id"
                ),
                {"cid": company_id},
            )
            role_id = result.scalar()
        else:
            role_id = existing_admin

        existing_rp = conn.execute(
            sa.text(
                "SELECT 1 FROM role_permissions "
                "WHERE role_id = :rid AND permission_id = :pid"
            ),
            {"rid": role_id, "pid": wildcard_id},
        ).scalar()
        if not existing_rp:
            conn.execute(
                sa.text(
                    "INSERT INTO role_permissions (role_id, permission_id) "
                    "VALUES (:rid, :pid)"
                ),
                {"rid": role_id, "pid": wildcard_id},
            )

        conn.execute(
            sa.text(
                "UPDATE users SET role_id = :rid "
                "WHERE company_id = :cid AND role_id IS NULL AND deleted_at IS NULL"
            ),
            {"rid": role_id, "cid": company_id},
        )


def downgrade() -> None:
    conn = op.get_bind()
    for code, _, _ in PERMISSIONS:
        conn.execute(
            sa.text("DELETE FROM permissions WHERE code = :code"),
            {"code": code},
        )
