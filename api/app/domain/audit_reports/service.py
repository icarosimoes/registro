from datetime import date, datetime

from sqlalchemy import delete as sa_delete
from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.audit import compute_diff, record_event
from app.models import AuditReport, AuditReportItem, User


async def list_audit_reports(
    session: AsyncSession,
    company_id: int,
    page: int,
    page_size: int,
    date_from: date | None = None,
    date_to: date | None = None,
) -> tuple[list[tuple], int]:
    filters = [
        AuditReport.company_id == company_id,
        AuditReport.deleted_at.is_(None),
    ]
    if date_from:
        filters.append(AuditReport.report_date >= date_from)
    if date_to:
        filters.append(AuditReport.report_date <= date_to)
    total = (
        await session.scalar(
            select(func.count(AuditReport.id)).where(*filters)
        )
        or 0
    )
    rows = (
        await session.execute(
            select(AuditReport, User.name)
            .outerjoin(User, User.id == AuditReport.auditor_user_id)
            .where(*filters)
            .order_by(
                AuditReport.report_date.desc(),
                AuditReport.id.desc(),
            )
            .offset((page - 1) * page_size)
            .limit(page_size)
        )
    ).all()
    return rows, total


async def get_audit_report(
    session: AsyncSession,
    company_id: int,
    report_id: int,
) -> tuple[AuditReport, str | None, list[AuditReportItem]] | None:
    row = (
        await session.execute(
            select(AuditReport, User.name)
            .outerjoin(User, User.id == AuditReport.auditor_user_id)
            .where(
                AuditReport.id == report_id,
                AuditReport.company_id == company_id,
                AuditReport.deleted_at.is_(None),
            )
        )
    ).first()
    if row is None:
        return None
    report = row[0]
    auditor_name = row[1]
    items = (
        await session.execute(
            select(AuditReportItem)
            .where(AuditReportItem.report_id == report.id)
            .order_by(AuditReportItem.sort_order, AuditReportItem.id)
        )
    ).scalars().all()
    return report, auditor_name, list(items)


async def _sync_items(
    session: AsyncSession,
    report_id: int,
    items_input: list[dict],
) -> None:
    await session.execute(
        sa_delete(AuditReportItem).where(
            AuditReportItem.report_id == report_id
        )
    )
    for item_data in items_input:
        session.add(AuditReportItem(report_id=report_id, **item_data))


async def create_audit_report(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    *,
    report_date: date,
    shift_type: str | None,
    auditor_user_id: int | None,
    status: str,
    notes: str | None,
    items: list[dict] | None,
) -> tuple[AuditReport, str | None, list[AuditReportItem]]:
    report = AuditReport(
        company_id=company_id,
        report_date=report_date,
        shift_type=shift_type,
        auditor_user_id=auditor_user_id,
        status=status,
        notes=notes,
    )
    session.add(report)
    await session.flush()
    if items:
        await _sync_items(session, report.id, items)
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="audit_report",
        entity_id=report.id,
        event_type="create",
    )
    await session.commit()
    await session.refresh(report)
    auditor_name = (
        await session.scalar(
            select(User.name).where(User.id == report.auditor_user_id)
        )
        if report.auditor_user_id
        else None
    )
    report_items = (
        await session.execute(
            select(AuditReportItem)
            .where(AuditReportItem.report_id == report.id)
            .order_by(AuditReportItem.sort_order, AuditReportItem.id)
        )
    ).scalars().all()
    return report, auditor_name, list(report_items)


async def update_audit_report(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    report_id: int,
    updates: dict,
) -> tuple[AuditReport, str | None, list[AuditReportItem]] | None:
    report = await session.scalar(
        select(AuditReport).where(
            AuditReport.id == report_id,
            AuditReport.company_id == company_id,
            AuditReport.deleted_at.is_(None),
        )
    )
    if report is None:
        return None
    items_input = updates.pop("items", None)
    before = {k: str(getattr(report, k)) for k in updates}
    for field, value in updates.items():
        setattr(report, field, value)
    if items_input is not None:
        await _sync_items(session, report.id, items_input)
    diff = compute_diff(before, {k: str(v) for k, v in updates.items()})
    if diff:
        await record_event(
            session,
            company_id=company_id,
            user_id=user_id,
            entity_type="audit_report",
            entity_id=report.id,
            event_type="update",
            diff=diff,
        )
    await session.commit()
    await session.refresh(report)
    auditor_name = (
        await session.scalar(
            select(User.name).where(User.id == report.auditor_user_id)
        )
        if report.auditor_user_id
        else None
    )
    report_items = (
        await session.execute(
            select(AuditReportItem)
            .where(AuditReportItem.report_id == report.id)
            .order_by(AuditReportItem.sort_order, AuditReportItem.id)
        )
    ).scalars().all()
    return report, auditor_name, list(report_items)


async def delete_audit_report(
    session: AsyncSession,
    company_id: int,
    user_id: int,
    report_id: int,
) -> bool:
    report = await session.scalar(
        select(AuditReport).where(
            AuditReport.id == report_id,
            AuditReport.company_id == company_id,
            AuditReport.deleted_at.is_(None),
        )
    )
    if report is None:
        return False
    report.deleted_at = datetime.now()
    await record_event(
        session,
        company_id=company_id,
        user_id=user_id,
        entity_type="audit_report",
        entity_id=report.id,
        event_type="delete",
    )
    await session.commit()
    return True
