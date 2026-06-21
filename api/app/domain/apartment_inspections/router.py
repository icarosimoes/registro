from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.apartment_inspections.schemas import (
    ApartmentInspectionCreate,
    ApartmentInspectionDetail,
    ApartmentInspectionItemSummary,
    ApartmentInspectionListResponse,
    ApartmentInspectionSummary,
    ApartmentInspectionUpdate,
)
from app.domain.apartment_inspections.service import (
    create_apartment_inspection,
    delete_apartment_inspection,
    get_apartment_inspection,
    list_apartment_inspections,
    update_apartment_inspection,
)
from app.domain.auth.repository import AuthenticatedUser

router = APIRouter(prefix="/apartment-inspections", tags=["apartment-inspections"])

ViewUser = Annotated[
    AuthenticatedUser,
    require_permission("apartment_inspection.view"),
]
CreateUser = Annotated[
    AuthenticatedUser,
    require_permission("apartment_inspection.create"),
]
EditUser = Annotated[
    AuthenticatedUser,
    require_permission("apartment_inspection.edit"),
]
DeleteUser = Annotated[
    AuthenticatedUser,
    require_permission("apartment_inspection.delete"),
]
Session = Annotated[AsyncSession, Depends(require_session)]


def _to_summary(record, inspector_name: str | None) -> ApartmentInspectionSummary:
    return ApartmentInspectionSummary(
        id=record.id,
        unit=record.unit,
        apartment=record.apartment,
        inspection_type=record.inspection_type,
        inspector_name=inspector_name,
        scheduled_at=record.scheduled_at,
        completed_at=record.completed_at,
        status=record.status,
        updated_at=record.updated_at,
    )


def _to_detail(record, inspector_name: str | None, items) -> ApartmentInspectionDetail:
    return ApartmentInspectionDetail(
        id=record.id,
        unit=record.unit,
        apartment=record.apartment,
        inspection_type=record.inspection_type,
        inspection_suite_id=record.inspection_suite_id,
        inspector_user_id=record.inspector_user_id,
        inspector_name=inspector_name,
        scheduled_at=record.scheduled_at,
        completed_at=record.completed_at,
        status=record.status,
        updated_at=record.updated_at,
        notes=record.notes,
        items=[
            ApartmentInspectionItemSummary(
                id=item.id,
                suite_item_id=item.suite_item_id,
                condition=item.condition,
                notes=item.notes,
                sort_order=item.sort_order,
            )
            for item in items
        ],
    )


@router.get("", response_model=ApartmentInspectionListResponse)
async def list_inspections_endpoint(
    user: ViewUser,
    session: Session,
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> ApartmentInspectionListResponse:
    rows, total = await list_apartment_inspections(
        session, user.company_id, page, page_size, search
    )
    return ApartmentInspectionListResponse(
        items=[_to_summary(record, inspector_name) for record, inspector_name in rows],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/{inspection_id}", response_model=ApartmentInspectionDetail)
async def get_inspection_endpoint(
    inspection_id: int,
    user: ViewUser,
    session: Session,
) -> ApartmentInspectionDetail:
    result = await get_apartment_inspection(session, user.company_id, inspection_id)
    if result is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    record, inspector_name, items = result
    return _to_detail(record, inspector_name, items)


@router.post("", response_model=ApartmentInspectionDetail, status_code=201)
async def create_inspection_endpoint(
    body: ApartmentInspectionCreate,
    user: CreateUser,
    session: Session,
) -> ApartmentInspectionDetail:
    items_data = [item.model_dump() for item in body.items] if body.items else None
    record, inspector_name, items = await create_apartment_inspection(
        session,
        user.company_id,
        user.id,
        unit=body.unit,
        apartment=body.apartment,
        inspection_type=body.inspection_type,
        inspection_suite_id=body.inspection_suite_id,
        inspector_user_id=body.inspector_user_id,
        scheduled_at=body.scheduled_at,
        completed_at=body.completed_at,
        status=body.status,
        notes=body.notes,
        items=items_data,
    )
    return _to_detail(record, inspector_name, items)


@router.patch("/{inspection_id}", response_model=ApartmentInspectionDetail)
async def update_inspection_endpoint(
    inspection_id: int,
    body: ApartmentInspectionUpdate,
    user: EditUser,
    session: Session,
) -> ApartmentInspectionDetail:
    updates = body.model_dump(exclude_none=True)
    if "items" in updates:
        updates["items"] = [item.model_dump() for item in body.items] if body.items else []
    result = await update_apartment_inspection(
        session, user.company_id, user.id, inspection_id, updates
    )
    if result is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    record, inspector_name, items = result
    return _to_detail(record, inspector_name, items)


@router.delete("/{inspection_id}", status_code=204)
async def delete_inspection_endpoint(
    inspection_id: int,
    user: DeleteUser,
    session: Session,
) -> None:
    deleted = await delete_apartment_inspection(session, user.company_id, user.id, inspection_id)
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
