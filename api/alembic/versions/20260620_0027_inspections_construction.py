"""inspections and construction tables

Revision ID: 20260620_0027
Revises: 20260620_0026
Create Date: 2026-06-20
"""

from alembic import op
import sqlalchemy as sa

revision = "20260620_0027"
down_revision = "20260620_0026"
branch_labels = None
depends_on = None


def upgrade() -> None:
    # ----- Check Suites -----
    op.create_table(
        "check_suites",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("company_id", sa.Integer, sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("description", sa.Text, nullable=True),
        sa.Column("status", sa.String(60), server_default="Ativo"),
        sa.Column("owner_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL"), nullable=True),
        sa.Column("deleted_at", sa.DateTime, nullable=True),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_check_suites_company", "check_suites", ["company_id"])

    op.create_table(
        "check_suite_items",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("suite_id", sa.Integer, sa.ForeignKey("check_suites.id", ondelete="CASCADE"), nullable=False),
        sa.Column("label", sa.String(255), nullable=False),
        sa.Column("sort_order", sa.Integer, server_default="0"),
        sa.Column("checked", sa.Boolean, server_default=sa.text("false")),
    )
    op.create_index("ix_check_suite_items_suite", "check_suite_items", ["suite_id"])

    # ----- Inspection Suites -----
    op.create_table(
        "inspection_suites",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("company_id", sa.Integer, sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("name", sa.String(255), nullable=False),
        sa.Column("description", sa.Text, nullable=True),
        sa.Column("type", sa.String(80), nullable=True),
        sa.Column("status", sa.String(60), server_default="Ativo"),
        sa.Column("owner_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL"), nullable=True),
        sa.Column("deleted_at", sa.DateTime, nullable=True),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_inspection_suites_company", "inspection_suites", ["company_id"])

    op.create_table(
        "inspection_suite_items",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("suite_id", sa.Integer, sa.ForeignKey("inspection_suites.id", ondelete="CASCADE"), nullable=False),
        sa.Column("area", sa.String(255), nullable=True),
        sa.Column("item_name", sa.String(255), nullable=False),
        sa.Column("expected_condition", sa.String(255), nullable=True),
        sa.Column("sort_order", sa.Integer, server_default="0"),
    )
    op.create_index("ix_inspection_suite_items_suite", "inspection_suite_items", ["suite_id"])

    # ----- Apartment Inspections -----
    op.create_table(
        "apartment_inspections",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("company_id", sa.Integer, sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("unit", sa.String(80), nullable=True),
        sa.Column("apartment", sa.String(80), nullable=True),
        sa.Column("inspection_type", sa.String(40), nullable=False),
        sa.Column("inspection_suite_id", sa.Integer, sa.ForeignKey("inspection_suites.id", ondelete="SET NULL"), nullable=True),
        sa.Column("inspector_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL"), nullable=True),
        sa.Column("scheduled_at", sa.DateTime, nullable=True),
        sa.Column("completed_at", sa.DateTime, nullable=True),
        sa.Column("status", sa.String(60), server_default="Pendente"),
        sa.Column("notes", sa.Text, nullable=True),
        sa.Column("deleted_at", sa.DateTime, nullable=True),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_apartment_inspections_company", "apartment_inspections", ["company_id"])
    op.create_index("ix_apartment_inspections_type", "apartment_inspections", ["company_id", "inspection_type"])

    op.create_table(
        "apartment_inspection_items",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("inspection_id", sa.Integer, sa.ForeignKey("apartment_inspections.id", ondelete="CASCADE"), nullable=False),
        sa.Column("suite_item_id", sa.Integer, sa.ForeignKey("inspection_suite_items.id", ondelete="SET NULL"), nullable=True),
        sa.Column("condition", sa.String(40), server_default="ok"),
        sa.Column("notes", sa.Text, nullable=True),
        sa.Column("sort_order", sa.Integer, server_default="0"),
    )
    op.create_index("ix_apt_inspection_items_inspection", "apartment_inspection_items", ["inspection_id"])

    # ----- Audit Reports -----
    op.create_table(
        "audit_reports",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("company_id", sa.Integer, sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("report_date", sa.Date, nullable=False),
        sa.Column("shift_type", sa.String(20), nullable=True),
        sa.Column("auditor_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL"), nullable=True),
        sa.Column("status", sa.String(60), server_default="Em andamento"),
        sa.Column("notes", sa.Text, nullable=True),
        sa.Column("deleted_at", sa.DateTime, nullable=True),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_audit_reports_company", "audit_reports", ["company_id"])

    op.create_table(
        "audit_report_items",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("report_id", sa.Integer, sa.ForeignKey("audit_reports.id", ondelete="CASCADE"), nullable=False),
        sa.Column("category", sa.String(120), nullable=True),
        sa.Column("description", sa.Text, nullable=True),
        sa.Column("status", sa.String(40), server_default="ok"),
        sa.Column("notes", sa.Text, nullable=True),
        sa.Column("sort_order", sa.Integer, server_default="0"),
    )
    op.create_index("ix_audit_report_items_report", "audit_report_items", ["report_id"])

    # ----- Work Diaries -----
    op.create_table(
        "work_diaries",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("company_id", sa.Integer, sa.ForeignKey("companies.id", ondelete="CASCADE"), nullable=False),
        sa.Column("diary_date", sa.Date, nullable=False),
        sa.Column("title", sa.String(255), nullable=False),
        sa.Column("description", sa.Text, nullable=True),
        sa.Column("weather", sa.String(60), nullable=True),
        sa.Column("status", sa.String(60), server_default="Em andamento"),
        sa.Column("owner_user_id", sa.Integer, sa.ForeignKey("users.id", ondelete="SET NULL"), nullable=True),
        sa.Column("deleted_at", sa.DateTime, nullable=True),
        sa.Column("created_at", sa.DateTime, server_default=sa.func.now()),
        sa.Column("updated_at", sa.DateTime, server_default=sa.func.now(), onupdate=sa.func.now()),
    )
    op.create_index("ix_work_diaries_company", "work_diaries", ["company_id"])

    op.create_table(
        "work_diary_activities",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("diary_id", sa.Integer, sa.ForeignKey("work_diaries.id", ondelete="CASCADE"), nullable=False),
        sa.Column("description", sa.Text, nullable=False),
        sa.Column("start_time", sa.DateTime, nullable=True),
        sa.Column("end_time", sa.DateTime, nullable=True),
        sa.Column("status", sa.String(60), server_default="Planejada"),
        sa.Column("sort_order", sa.Integer, server_default="0"),
    )

    op.create_table(
        "work_diary_teams",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("diary_id", sa.Integer, sa.ForeignKey("work_diaries.id", ondelete="CASCADE"), nullable=False),
        sa.Column("worker_name", sa.String(160), nullable=False),
        sa.Column("role", sa.String(120), nullable=True),
        sa.Column("hours_worked", sa.Numeric(5, 2), nullable=True),
        sa.Column("sort_order", sa.Integer, server_default="0"),
    )

    op.create_table(
        "work_diary_equipment",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("diary_id", sa.Integer, sa.ForeignKey("work_diaries.id", ondelete="CASCADE"), nullable=False),
        sa.Column("equipment_name", sa.String(160), nullable=False),
        sa.Column("quantity", sa.Integer, server_default="1"),
        sa.Column("hours_used", sa.Numeric(5, 2), nullable=True),
        sa.Column("sort_order", sa.Integer, server_default="0"),
    )

    op.create_table(
        "work_diary_observations",
        sa.Column("id", sa.Integer, primary_key=True, autoincrement=True),
        sa.Column("diary_id", sa.Integer, sa.ForeignKey("work_diaries.id", ondelete="CASCADE"), nullable=False),
        sa.Column("content", sa.Text, nullable=False),
        sa.Column("category", sa.String(80), nullable=True),
        sa.Column("sort_order", sa.Integer, server_default="0"),
    )


def downgrade() -> None:
    op.drop_table("work_diary_observations")
    op.drop_table("work_diary_equipment")
    op.drop_table("work_diary_teams")
    op.drop_table("work_diary_activities")
    op.drop_table("work_diaries")
    op.drop_table("audit_report_items")
    op.drop_table("audit_reports")
    op.drop_table("apartment_inspection_items")
    op.drop_table("apartment_inspections")
    op.drop_table("inspection_suite_items")
    op.drop_table("inspection_suites")
    op.drop_table("check_suite_items")
    op.drop_table("check_suites")
