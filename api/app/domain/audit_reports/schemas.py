from datetime import date, datetime

from pydantic import BaseModel


class AuditReportItemInput(BaseModel):
    category: str | None = None
    description: str | None = None
    status: str = "ok"
    notes: str | None = None
    sort_order: int = 0


class AuditReportItemOut(BaseModel):
    id: int
    category: str | None
    description: str | None
    status: str
    notes: str | None
    sort_order: int


class AuditReportCreate(BaseModel):
    report_date: date
    shift_type: str | None = None
    auditor_user_id: int | None = None
    status: str = "Em andamento"
    notes: str | None = None
    items: list[AuditReportItemInput] | None = None


class AuditReportUpdate(BaseModel):
    report_date: date | None = None
    shift_type: str | None = None
    auditor_user_id: int | None = None
    status: str | None = None
    notes: str | None = None
    items: list[AuditReportItemInput] | None = None


class AuditReportSummary(BaseModel):
    id: int
    report_date: date
    shift_type: str | None
    status: str
    auditor: str
    notes: str | None
    updated_at: datetime


class AuditReportDetail(AuditReportSummary):
    items: list[AuditReportItemOut]


class AuditReportListResponse(BaseModel):
    items: list[AuditReportSummary]
    total: int
    page: int
    page_size: int
