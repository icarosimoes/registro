"""Auditoria do schema: índices compostos, remove redundantes, ondelete em FKs."""

from collections.abc import Sequence

from alembic import op

revision: str = "20260620_0017"
down_revision: str = "20260620_0016"
branch_labels: Sequence[str] | None = None
depends_on: Sequence[str] | None = None


def _drop_fk(table: str, constraint: str) -> None:
    op.drop_constraint(constraint, table, type_="foreignkey")


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
    # --- attachments: composto substitui individuais ---
    op.create_index(
        "ix_attachments_company_entity", "attachments",
        ["company_id", "entity_type", "entity_id"],
    )
    op.drop_index("ix_attachments_entity_type", table_name="attachments")
    op.drop_index("ix_attachments_entity_id", table_name="attachments")

    # --- audit_events: composto com company_id ---
    op.create_index(
        "ix_audit_events_company_entity", "audit_events",
        ["company_id", "entity_type", "entity_id"],
    )
    op.drop_index("ix_audit_events_entity", table_name="audit_events")

    # --- fiscal_requests: composto company+status ---
    op.create_index(
        "ix_fiscal_requests_company_status", "fiscal_requests",
        ["company_id", "status"],
    )

    # --- notifications: composto user+read_at ---
    op.create_index(
        "ix_notifications_user_unread", "notifications",
        ["user_id", "read_at"],
    )

    # --- FKs com ondelete ---

    # occurrences
    for constraint, col, ref in [
        ("occurrences_ibfk_2", "location_id", "locations"),
        ("occurrences_ibfk_3", "sector_id", "sectors"),
        ("occurrences_ibfk_4", "owner_user_id", "users"),
        ("occurrences_ibfk_5", "created_by_user_id", "users"),
        ("occurrences_ibfk_6", "updated_by_user_id", "users"),
    ]:
        _drop_fk("occurrences", constraint)
        _add_fk("occurrences", constraint, col, ref)

    # fiscal_requests
    for constraint, col in [
        ("fiscal_requests_ibfk_2", "requester_user_id"),
        ("fiscal_requests_ibfk_3", "responsible_user_id"),
    ]:
        _drop_fk("fiscal_requests", constraint)
        _add_fk("fiscal_requests", constraint, col, "users")

    # audit_events.user_id → CASCADE (se user é deletado, auditoria segue)
    _drop_fk("audit_events", "audit_events_ibfk_2")
    _add_fk("audit_events", "audit_events_ibfk_2", "user_id", "users", ondelete="CASCADE")

    # module_records
    for constraint, col in [
        ("module_records_ibfk_2", "owner_user_id"),
        ("module_records_ibfk_3", "created_by_user_id"),
    ]:
        _drop_fk("module_records", constraint)
        _add_fk("module_records", constraint, col, "users")

    # notifications.user_id → CASCADE
    _drop_fk("notifications", "notifications_ibfk_2")
    _add_fk("notifications", "notifications_ibfk_2", "user_id", "users", ondelete="CASCADE")


def downgrade() -> None:
    # --- Reverte FKs para sem ondelete ---
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
        _drop_fk(table, constraint)
        op.create_foreign_key(constraint, table, ref, [col], ["id"])

    # --- Reverte índices ---
    op.drop_index("ix_notifications_user_unread", table_name="notifications")
    op.drop_index("ix_fiscal_requests_company_status", table_name="fiscal_requests")
    op.drop_index("ix_audit_events_company_entity", table_name="audit_events")
    op.create_index("ix_audit_events_entity", "audit_events", ["entity_type", "entity_id"])
    op.drop_index("ix_attachments_company_entity", table_name="attachments")
    op.create_index("ix_attachments_entity_id", "attachments", ["entity_id"])
    op.create_index("ix_attachments_entity_type", "attachments", ["entity_type"])
