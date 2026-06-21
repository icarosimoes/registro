"""re-migrate module_records to dedicated tables after v1 import

The V1 import writes reunioes, relatorios-turno, inspecoes and manutencao
into module_records. The original data migrations (0021, 0023, 0028) ran
on an empty DB before the import, so the data was never moved. This
migration re-runs the same logic and also creates a demo user for
Aero Hotel so the imported data is accessible.

Revision ID: 20260621_0030
Revises: 20260620_0029
Create Date: 2026-06-21
"""

import json
import os
from datetime import datetime

import bcrypt
from alembic import op
import sqlalchemy as sa

revision = "20260621_0030"
down_revision = "20260620_0029"
branch_labels = None
depends_on = None

SHIFT_MAP = {"Manhã": "morning", "Tarde": "afternoon", "Noite": "night"}


def _parse_datetime(val):
    if not val or not str(val).strip():
        return None
    s = str(val).strip()[:19]
    for fmt in ("%Y-%m-%d %H:%M:%S", "%Y-%m-%d %H:%M", "%Y-%m-%dT%H:%M:%S", "%Y-%m-%dT%H:%M"):
        try:
            return datetime.strptime(s, fmt)
        except ValueError:
            continue
    return None


def _parse_date(val):
    if not val or not str(val).strip():
        return None
    s = str(val).strip()[:10]
    try:
        return datetime.strptime(s, "%Y-%m-%d").date()
    except ValueError:
        return None


def upgrade() -> None:
    conn = op.get_bind()

    # --- reunioes → meetings ---
    reunioes = conn.execute(
        sa.text(
            "SELECT id, company_id, title, description, category, status, "
            "owner_user_id, created_by_user_id, notify_user_ids, payload, "
            "created_at, updated_at "
            "FROM module_records WHERE module = 'reunioes' AND deleted_at IS NULL"
        )
    ).fetchall()

    for r in reunioes:
        old_id = r[0]
        payload = r[9]
        if isinstance(payload, str):
            try:
                payload = json.loads(payload)
            except (json.JSONDecodeError, TypeError):
                payload = {}
        if payload is None:
            payload = {}

        scheduled_at_raw = payload.get("datetime") or payload.get("scheduled_at")
        scheduled_at = _parse_datetime(scheduled_at_raw)
        location = payload.get("local") or payload.get("location") or r[3]

        result = conn.execute(
            sa.text(
                "INSERT INTO meetings "
                "(company_id, title, description, scheduled_at, location, status, "
                "owner_user_id, created_by_user_id, notify_user_ids, "
                "created_at, updated_at) "
                "VALUES (:company_id, :title, :description, "
                ":scheduled_at, :location, :status, :owner, "
                ":created_by, :notify, :created_at, :updated_at) RETURNING id"
            ),
            {
                "company_id": r[1], "title": r[2],
                "description": r[3],
                "scheduled_at": scheduled_at,
                "location": location,
                "status": r[5] or "Agendada",
                "owner": r[6], "created_by": r[7], "notify": r[8],
                "created_at": r[10], "updated_at": r[11],
            },
        )
        new_id = result.scalar()

        for table in ("audit_events", "attachments", "notifications"):
            conn.execute(
                sa.text(
                    f"UPDATE {table} SET entity_type = 'meeting', entity_id = :new_id "
                    f"WHERE entity_type = 'reunioes' AND entity_id = :old_id"
                ),
                {"new_id": new_id, "old_id": old_id},
            )

    if reunioes:
        conn.execute(
            sa.text(
                "UPDATE module_records SET deleted_at = CURRENT_TIMESTAMP "
                "WHERE module = 'reunioes' AND deleted_at IS NULL"
            )
        )

    # --- relatorios-turno → shift_reports ---
    turnos = conn.execute(
        sa.text(
            "SELECT id, company_id, title, description, category, status, "
            "owner_user_id, created_by_user_id, notify_user_ids, payload, "
            "created_at, updated_at "
            "FROM module_records WHERE module = 'relatorios-turno' AND deleted_at IS NULL"
        )
    ).fetchall()

    for r in turnos:
        old_id = r[0]
        category = r[4] or ""
        shift_type = SHIFT_MAP.get(category)

        payload = r[9]
        if isinstance(payload, str):
            try:
                payload = json.loads(payload)
            except (json.JSONDecodeError, TypeError):
                payload = {}
        if payload is None:
            payload = {}

        shift_date_raw = payload.get("beginning", "")[:10] if payload.get("beginning") else None
        shift_date = _parse_date(shift_date_raw)

        result = conn.execute(
            sa.text(
                "INSERT INTO shift_reports "
                "(company_id, title, description, shift_date, shift_type, status, "
                "owner_user_id, created_by_user_id, notify_user_ids, "
                "created_at, updated_at) "
                "VALUES (:company_id, :title, :description, "
                ":shift_date, :shift_type, :status, :owner, "
                ":created_by, :notify, :created_at, :updated_at) RETURNING id"
            ),
            {
                "company_id": r[1], "title": r[2], "description": r[3],
                "shift_date": shift_date,
                "shift_type": shift_type,
                "status": r[5] or "Em andamento",
                "owner": r[6], "created_by": r[7], "notify": r[8],
                "created_at": r[10], "updated_at": r[11],
            },
        )
        new_id = result.scalar()

        for table in ("audit_events", "attachments", "notifications"):
            conn.execute(
                sa.text(
                    f"UPDATE {table} SET entity_type = 'shift_report', entity_id = :new_id "
                    f"WHERE entity_type = 'relatorios-turno' AND entity_id = :old_id"
                ),
                {"new_id": new_id, "old_id": old_id},
            )

    if turnos:
        conn.execute(
            sa.text(
                "UPDATE module_records SET deleted_at = CURRENT_TIMESTAMP "
                "WHERE module = 'relatorios-turno' AND deleted_at IS NULL"
            )
        )

    # inspecoes (4497) e manutencao (104) permanecem em module_records —
    # o frontend usa /modules/inspecoes e /modules/manutencao (genérico).

    # --- Ensure legacy-admin role has wildcard permission ---
    aero = conn.execute(
        sa.text("SELECT id FROM companies WHERE slug = 'aero-hotel'")
    ).scalar()
    if aero:
        legacy_role = conn.execute(
            sa.text(
                "SELECT id FROM roles WHERE company_id = :cid AND code = 'legacy-admin'"
            ),
            {"cid": aero},
        ).scalar()
        wildcard = conn.execute(
            sa.text("SELECT id FROM permissions WHERE code = '*'")
        ).scalar()
        if legacy_role and wildcard:
            already = conn.execute(
                sa.text(
                    "SELECT 1 FROM role_permissions "
                    "WHERE role_id = :rid AND permission_id = :pid"
                ),
                {"rid": legacy_role, "pid": wildcard},
            ).scalar()
            if not already:
                conn.execute(
                    sa.text(
                        "INSERT INTO role_permissions (role_id, permission_id) "
                        "VALUES (:rid, :pid)"
                    ),
                    {"rid": legacy_role, "pid": wildcard},
                )

    # --- Demo user for Aero Hotel ---
    if aero:
        demo_email = "demo@aerohotel.local"
        existing = conn.execute(
            sa.text(
                "SELECT id FROM users WHERE company_id = :cid AND email = :email"
            ),
            {"cid": aero, "email": demo_email},
        ).scalar()
        if not existing:
            pwd = os.getenv("SEED_DEFAULT_PASSWORD", "Registro@123")
            hashed = bcrypt.hashpw(pwd.encode(), bcrypt.gensalt()).decode()
            admin_role = conn.execute(
                sa.text(
                    "SELECT id FROM roles WHERE company_id = :cid "
                    "AND code IN ('legacy-admin', 'admin') LIMIT 1"
                ),
                {"cid": aero},
            ).scalar()
            conn.execute(
                sa.text(
                    "INSERT INTO users (company_id, role_id, name, email, password, "
                    "active, email_verified_at, created_at, updated_at) "
                    "VALUES (:cid, :rid, :name, :email, :pwd, true, "
                    "CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)"
                ),
                {
                    "cid": aero, "rid": admin_role,
                    "name": "Demo Aero Hotel",
                    "email": demo_email, "pwd": hashed,
                },
            )


def downgrade() -> None:
    conn = op.get_bind()
    conn.execute(
        sa.text(
            "UPDATE module_records SET deleted_at = NULL "
            "WHERE module IN ('reunioes', 'relatorios-turno') "
            "AND deleted_at IS NOT NULL"
        )
    )
