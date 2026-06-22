"""seed hotel roles: gerente, recepcao, governanca, manutencao, financeiro

Revision ID: 20260621_0039
Revises: 20260621_0038
Create Date: 2026-06-21
"""

import sqlalchemy as sa

from alembic import op

revision = "20260621_0039"
down_revision = "20260621_0038"
branch_labels = None
depends_on = None

ROLE_DEFINITIONS = {
    "gerente": {
        "name": "Gerente",
        "exclude": {"settings.edit", "user.delete"},
    },
    "recepcao": {
        "name": "Recepção",
        "include": {
            "occurrence.view",
            "occurrence.create",
            "occurrence.edit",
            "occurrence.delete",
            "fiscal_request.view",
            "fiscal_request.create",
            "fiscal_request.edit",
            "fiscal_request.delete",
            "registry.view",
            "meeting.view",
            "shift_report.view",
            "shift_report.create",
            "shift_report.edit",
            "shift_report.delete",
        },
    },
    "governanca": {
        "name": "Governança",
        "include": {
            "occurrence.view",
            "occurrence.create",
            "occurrence.edit",
            "occurrence.delete",
            "registry.view",
            "procedure.view",
            "shift_report.view",
        },
    },
    "manutencao": {
        "name": "Manutenção",
        "include": {
            "occurrence.view",
            "occurrence.create",
            "occurrence.edit",
            "registry.view",
            "procedure.view",
        },
    },
    "financeiro": {
        "name": "Financeiro",
        "include": {
            "fiscal_request.view",
            "fiscal_request.create",
            "fiscal_request.edit",
            "fiscal_request.delete",
            "settings.view",
            "occurrence.view",
        },
    },
}

SEEDED_CODES = set(ROLE_DEFINITIONS.keys())


def upgrade() -> None:
    conn = op.get_bind()

    all_perms = conn.execute(
        sa.text("SELECT id, code FROM permissions WHERE code != '*'")
    ).fetchall()
    perm_map = {code: pid for pid, code in all_perms}

    companies = conn.execute(sa.text("SELECT id FROM companies")).fetchall()

    for (company_id,) in companies:
        for code, defn in ROLE_DEFINITIONS.items():
            existing = conn.execute(
                sa.text("SELECT id FROM roles WHERE company_id = :cid AND code = :code"),
                {"cid": company_id, "code": code},
            ).scalar()

            if existing is None:
                result = conn.execute(
                    sa.text(
                        "INSERT INTO roles (company_id, code, name, created_at, updated_at) "
                        "VALUES (:cid, :code, :name, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) "
                        "RETURNING id"
                    ),
                    {"cid": company_id, "code": code, "name": defn["name"]},
                )
                role_id = result.scalar()
            else:
                role_id = existing

            if "include" in defn:
                perm_codes = defn["include"]
            else:
                perm_codes = set(perm_map.keys()) - defn["exclude"]

            for perm_code in perm_codes:
                pid = perm_map.get(perm_code)
                if pid is None:
                    continue
                conn.execute(
                    sa.text(
                        "INSERT INTO role_permissions (role_id, permission_id) "
                        "VALUES (:rid, :pid) ON CONFLICT DO NOTHING"
                    ),
                    {"rid": role_id, "pid": pid},
                )


def downgrade() -> None:
    conn = op.get_bind()
    for code in SEEDED_CODES:
        conn.execute(
            sa.text(
                "DELETE FROM role_permissions WHERE role_id IN "
                "(SELECT id FROM roles WHERE code = :code)"
            ),
            {"code": code},
        )
        conn.execute(
            sa.text("DELETE FROM roles WHERE code = :code"),
            {"code": code},
        )
