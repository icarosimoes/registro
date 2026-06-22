"""Auditoria do schema: índices compostos, remove redundantes, ondelete em FKs."""

from collections.abc import Sequence

import sqlalchemy as sa

from alembic import op

revision: str = "20260620_0017"
down_revision: str = "20260620_0016"
branch_labels: Sequence[str] | None = None
depends_on: Sequence[str] | None = None


def _drop_fk_safe(table: str, constraint: str) -> None:
    conn = op.get_bind()
    result = conn.execute(
        sa.text(
            "SELECT 1 FROM information_schema.table_constraints "
            "WHERE constraint_name = :name AND table_name = :table "
            "AND constraint_type = 'FOREIGN KEY'"
        ),
        {"name": constraint, "table": table},
    ).scalar()
    if result:
        op.drop_constraint(constraint, table, type_="foreignkey")


def _drop_index_safe(index_name: str, table_name: str) -> None:
    conn = op.get_bind()
    result = conn.execute(
        sa.text("SELECT 1 FROM pg_indexes WHERE indexname = :name AND tablename = :table"),
        {"name": index_name, "table": table_name},
    ).scalar()
    if result:
        op.drop_index(index_name, table_name=table_name)


def _add_fk(
    table: str,
    constraint: str,
    local_col: str,
    ref: str,
    ref_col: str = "id",
    ondelete: str = "SET NULL",
) -> None:
    op.create_foreign_key(constraint, table, ref, [local_col], [ref_col], ondelete=ondelete)


def upgrade() -> None:
    conn = op.get_bind()

    def _index_exists(name: str) -> bool:
        return bool(
            conn.execute(
                sa.text("SELECT 1 FROM pg_indexes WHERE indexname = :name"),
                {"name": name},
            ).scalar()
        )

    if not _index_exists("ix_attachments_company_entity"):
        op.create_index(
            "ix_attachments_company_entity",
            "attachments",
            ["company_id", "entity_type", "entity_id"],
        )
    _drop_index_safe("ix_attachments_entity_type", "attachments")
    _drop_index_safe("ix_attachments_entity_id", "attachments")

    if not _index_exists("ix_audit_events_company_entity"):
        op.create_index(
            "ix_audit_events_company_entity",
            "audit_events",
            ["company_id", "entity_type", "entity_id"],
        )
    _drop_index_safe("ix_audit_events_entity", "audit_events")

    if not _index_exists("ix_fiscal_requests_company_status"):
        op.create_index(
            "ix_fiscal_requests_company_status",
            "fiscal_requests",
            ["company_id", "status"],
        )

    if not _index_exists("ix_notifications_user_unread"):
        op.create_index(
            "ix_notifications_user_unread",
            "notifications",
            ["user_id", "read_at"],
        )

    for constraint, col, ref in [
        ("occurrences_ibfk_2", "location_id", "locations"),
        ("occurrences_ibfk_3", "sector_id", "sectors"),
        ("occurrences_ibfk_4", "owner_user_id", "users"),
        ("occurrences_ibfk_5", "created_by_user_id", "users"),
        ("occurrences_ibfk_6", "updated_by_user_id", "users"),
    ]:
        _drop_fk_safe("occurrences", constraint)
        _add_fk("occurrences", constraint, col, ref)

    for constraint, col in [
        ("fiscal_requests_ibfk_2", "requester_user_id"),
        ("fiscal_requests_ibfk_3", "responsible_user_id"),
    ]:
        _drop_fk_safe("fiscal_requests", constraint)
        _add_fk("fiscal_requests", constraint, col, "users")

    _drop_fk_safe("audit_events", "audit_events_ibfk_2")
    _add_fk("audit_events", "audit_events_ibfk_2", "user_id", "users", ondelete="CASCADE")

    for constraint, col in [
        ("module_records_ibfk_2", "owner_user_id"),
        ("module_records_ibfk_3", "created_by_user_id"),
    ]:
        _drop_fk_safe("module_records", constraint)
        _add_fk("module_records", constraint, col, "users")

    _drop_fk_safe("notifications", "notifications_ibfk_2")
    _add_fk("notifications", "notifications_ibfk_2", "user_id", "users", ondelete="CASCADE")


def downgrade() -> None:
    for table, constraint, col, ref in [
        ("notifications", "notifications_ibfk_2", "user_id", "users"),
        ("module_records", "module_records_ibfk_3", "created_by_user_id", "users"),
        ("module_records", "module_records_ibfk_2", "owner_user_id", "users"),
        ("audit_events", "audit_events_ibfk_2", "user_id", "users"),
        ("fiscal_requests", "fiscal_requests_ibfk_3", "responsible_user_id", "users"),
        ("fiscal_requests", "fiscal_requests_ibfk_2", "requester_user_id", "users"),
        ("occurrences", "occurrences_ibfk_6", "updated_by_user_id", "users"),
        ("occurrences", "occurrences_ibfk_5", "created_by_user_id", "users"),
        ("occurrences", "occurrences_ibfk_4", "owner_user_id", "users"),
        ("occurrences", "occurrences_ibfk_3", "sector_id", "sectors"),
        ("occurrences", "occurrences_ibfk_2", "location_id", "locations"),
    ]:
        _drop_fk_safe(table, constraint)
        op.create_foreign_key(constraint, table, ref, [col], ["id"])

    _drop_index_safe("ix_notifications_user_unread", "notifications")
    _drop_index_safe("ix_fiscal_requests_company_status", "fiscal_requests")
    _drop_index_safe("ix_audit_events_company_entity", "audit_events")
    _drop_index_safe("ix_attachments_company_entity", "attachments")
