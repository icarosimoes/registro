from datetime import date
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.audit_reports.schemas import (
    AuditReportCreate,
    AuditReportDetail,
    AuditReportItemOut,
    AuditReportListResponse,
    AuditReportSummary,
    AuditReportUpdate,
)
from app.domain.audit_reports.service import (
    create_audit_report,
    delete_audit_report,
    get_audit_report,
    list_audit_reports,
    update_audit_report,
)
from app.domain.auth.repository import AuthenticatedUser

router = APIRouter(prefix="/audit-reports", tags=["audit-reports"])


def _summary(report, auditor_name: str | None) -> AuditReportSummary:
    return AuditReportSummary(
        id=report.id,
        report_date=report.report_date,
        shift_type=report.shift_type,
        status=report.status,
        auditor=auditor_name or "Não atribuído",
        notes=report.notes,
        updated_at=report.updated_at,
    )


def _detail(report, auditor_name: str | None, items) -> AuditReportDetail:
    return AuditReportDetail(
        id=report.id,
        report_date=report.report_date,
        shift_type=report.shift_type,
        status=report.status,
        auditor=auditor_name or "Não atribuído",
        notes=report.notes,
        updated_at=report.updated_at,
        items=[
            AuditReportItemOut(
                id=item.id,
                category=item.category,
                description=item.description,
                status=item.status,
                notes=item.notes,
                sort_order=item.sort_order,
            )
            for item in items
        ],
    )


@router.get("", response_model=AuditReportListResponse)
async def list_audit_reports_endpoint(
    user: Annotated[
        AuthenticatedUser, require_permission("audit_report.view")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    date_from: date | None = None,
    date_to: date | None = None,
) -> AuditReportListResponse:
    rows, total = await list_audit_reports(
        session,
        user.company_id,
        page,
        page_size,
        date_from,
        date_to,
    )
    return AuditReportListResponse(
        items=[_summary(report, auditor_name) for report, auditor_name in rows],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/{report_id}", response_model=AuditReportDetail)
async def get_audit_report_endpoint(
    report_id: int,
    user: Annotated[
        AuthenticatedUser, require_permission("audit_report.view")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> AuditReportDetail:
    result = await get_audit_report(
        session, user.company_id, report_id
    )
    if result is None:
        raise HTTPException(
            status_code=404, detail={"code": "not_found"}
        )
    report, auditor_name, items = result
    return _detail(report, auditor_name, items)


@router.post("", response_model=AuditReportDetail, status_code=201)
async def create_audit_report_endpoint(
    body: AuditReportCreate,
    user: Annotated[
        AuthenticatedUser, require_permission("audit_report.create")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> AuditReportDetail:
    items_dicts = (
        [item.model_dump() for item in body.items] if body.items else None
    )
    report, auditor_name, items = await create_audit_report(
        session,
        user.company_id,
        user.id,
        report_date=body.report_date,
        shift_type=body.shift_type,
        auditor_user_id=body.auditor_user_id,
        status=body.status,
        notes=body.notes,
        items=items_dicts,
    )
    return _detail(report, auditor_name, items)


@router.patch("/{report_id}", response_model=AuditReportDetail)
async def update_audit_report_endpoint(
    report_id: int,
    body: AuditReportUpdate,
    user: Annotated[
        AuthenticatedUser, require_permission("audit_report.edit")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> AuditReportDetail:
    updates = body.model_dump(exclude_none=True)
    if "items" in updates:
        updates["items"] = [
            item.model_dump() for item in body.items  # type: ignore[union-attr]
        ]
    result = await update_audit_report(
        session,
        user.company_id,
        user.id,
        report_id,
        updates,
    )
    if result is None:
        raise HTTPException(
            status_code=404, detail={"code": "not_found"}
        )
    report, auditor_name, items = result
    return _detail(report, auditor_name, items)


@router.delete("/{report_id}", status_code=204)
async def delete_audit_report_endpoint(
    report_id: int,
    user: Annotated[
        AuthenticatedUser, require_permission("audit_report.delete")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_audit_report(
        session, user.company_id, user.id, report_id
    )
    if not deleted:
        raise HTTPException(
            status_code=404, detail={"code": "not_found"}
        )
