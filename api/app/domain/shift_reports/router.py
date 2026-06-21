from datetime import date
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.shift_reports.schemas import (
    SHIFT_LABELS,
    ShiftReportCreate,
    ShiftReportDetail,
    ShiftReportListResponse,
    ShiftReportSummary,
    ShiftReportUpdate,
)
from app.domain.shift_reports.service import (
    create_shift_report,
    delete_shift_report,
    get_shift_report,
    list_shift_reports,
    update_shift_report,
)

router = APIRouter(prefix="/shift-reports", tags=["shift-reports"])


@router.get("", response_model=ShiftReportListResponse)
async def list_shift_reports_endpoint(
    user: Annotated[
        AuthenticatedUser, require_permission("shift_report.view")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
    date_from: date | None = None,
    date_to: date | None = None,
) -> ShiftReportListResponse:
    rows, total = await list_shift_reports(
        session,
        user.company_id,
        page,
        page_size,
        search,
        date_from,
        date_to,
    )
    return ShiftReportListResponse(
        items=[
            ShiftReportSummary(
                id=report.id,
                title=report.title,
                description=report.description,
                shift_date=report.shift_date,
                shift_type=report.shift_type,
                shift_label=SHIFT_LABELS.get(report.shift_type or ""),
                status=report.status,
                owner=owner_name or "Não atribuído",
                updated_at=report.updated_at,
            )
            for report, owner_name in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/{report_id}", response_model=ShiftReportDetail)
async def get_shift_report_endpoint(
    report_id: int,
    user: Annotated[
        AuthenticatedUser, require_permission("shift_report.view")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ShiftReportDetail:
    result = await get_shift_report(
        session, user.company_id, report_id
    )
    if result is None:
        raise HTTPException(
            status_code=404, detail={"code": "not_found"}
        )
    report, owner_name = result
    return ShiftReportDetail(
        id=report.id,
        title=report.title,
        description=report.description,
        shift_date=report.shift_date,
        shift_type=report.shift_type,
        shift_label=SHIFT_LABELS.get(report.shift_type or ""),
        status=report.status,
        owner=owner_name or "Não atribuído",
        started_at=report.started_at,
        ended_at=report.ended_at,
        supervisor=report.supervisor,
        occupation=report.occupation,
        average_daily=report.average_daily,
        guests=report.guests,
        uhs=report.uhs,
        maintenance_count=report.maintenance_count,
        cleaning=report.cleaning,
        walk_in=report.walk_in,
        input_quantity=report.input_quantity,
        output_quantity=report.output_quantity,
        return_of_customers=report.return_of_customers,
        observations=report.observations,
        notes_ab=report.notes_ab,
        notes_reception=report.notes_reception,
        notes_reservations=report.notes_reservations,
        notes_governance=report.notes_governance,
        notes_maintenance=report.notes_maintenance,
        notes_ti=report.notes_ti,
        notes_security=report.notes_security,
        payload=report.payload,
        updated_at=report.updated_at,
    )


@router.post(
    "", response_model=ShiftReportSummary, status_code=201
)
async def create_shift_report_endpoint(
    body: ShiftReportCreate,
    user: Annotated[
        AuthenticatedUser, require_permission("shift_report.create")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ShiftReportSummary:
    record, owner_name = await create_shift_report(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        title=body.title,
        description=body.description,
        shift_date=body.shift_date,
        shift_type=body.shift_type,
        started_at=body.started_at,
        ended_at=body.ended_at,
        status=body.status,
        owner_user_id=body.owner_user_id,
        notify_user_ids=body.notify_user_ids,
    )
    return ShiftReportSummary(
        id=record.id,
        title=record.title,
        description=record.description,
        shift_date=record.shift_date,
        shift_type=record.shift_type,
        shift_label=SHIFT_LABELS.get(record.shift_type or ""),
        status=record.status,
        owner=owner_name or "Não atribuído",
        updated_at=record.updated_at,
    )


@router.patch(
    "/{report_id}", response_model=ShiftReportSummary
)
async def update_shift_report_endpoint(
    report_id: int,
    body: ShiftReportUpdate,
    user: Annotated[
        AuthenticatedUser, require_permission("shift_report.edit")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> ShiftReportSummary:
    updates = body.model_dump(exclude_none=True)
    result = await update_shift_report(
        session,
        user.company_id,
        user.id,
        user.name,
        user.email,
        report_id,
        updates,
    )
    if result is None:
        raise HTTPException(
            status_code=404, detail={"code": "not_found"}
        )
    record, owner_name = result
    return ShiftReportSummary(
        id=record.id,
        title=record.title,
        description=record.description,
        shift_date=record.shift_date,
        shift_type=record.shift_type,
        shift_label=SHIFT_LABELS.get(record.shift_type or ""),
        status=record.status,
        owner=owner_name or "Não atribuído",
        updated_at=record.updated_at,
    )


@router.delete("/{report_id}", status_code=204)
async def delete_shift_report_endpoint(
    report_id: int,
    user: Annotated[
        AuthenticatedUser, require_permission("shift_report.delete")
    ],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await delete_shift_report(
        session, user.company_id, user.id, report_id
    )
    if not deleted:
        raise HTTPException(
            status_code=404, detail={"code": "not_found"}
        )
