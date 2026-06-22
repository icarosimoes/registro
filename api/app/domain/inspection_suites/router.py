from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.dependencies import require_session
from app.core.permissions import require_permission
from app.domain.auth.repository import AuthenticatedUser
from app.domain.inspection_suites import service
from app.domain.inspection_suites.schemas import (
    InspectionSuiteCreate,
    InspectionSuiteDetail,
    InspectionSuiteItemResponse,
    InspectionSuiteListResponse,
    InspectionSuiteSummary,
    InspectionSuiteUpdate,
)

router = APIRouter(prefix="/inspection-suites", tags=["inspection-suites"])


@router.get("", response_model=InspectionSuiteListResponse)
async def list_inspection_suites(
    user: Annotated[AuthenticatedUser, require_permission("inspection_suite.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
    page: Annotated[int, Query(ge=1)] = 1,
    page_size: Annotated[int, Query(ge=1, le=100)] = 20,
    search: str | None = None,
) -> InspectionSuiteListResponse:
    rows, total = await service.list_inspection_suites(
        session,
        user.company_id,
        page,
        page_size,
        search,
    )
    return InspectionSuiteListResponse(
        items=[
            InspectionSuiteSummary(
                id=suite.id,
                name=suite.name,
                description=suite.description,
                type=suite.type,
                status=suite.status,
                owner_user_id=suite.owner_user_id,
                owner_name=owner_name,
                item_count=item_count,
                updated_at=suite.updated_at,
            )
            for suite, owner_name, item_count in rows
        ],
        total=total,
        page=page,
        page_size=page_size,
    )


@router.get("/{suite_id}", response_model=InspectionSuiteDetail)
async def get_inspection_suite(
    suite_id: int,
    user: Annotated[AuthenticatedUser, require_permission("inspection_suite.view")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> InspectionSuiteDetail:
    detail = await service.get_inspection_suite(session, user.company_id, suite_id)
    if detail is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return _build_detail(detail)


@router.post("", response_model=InspectionSuiteDetail, status_code=201)
async def create_inspection_suite(
    user: Annotated[AuthenticatedUser, require_permission("inspection_suite.create")],
    payload: InspectionSuiteCreate,
    session: Annotated[AsyncSession, Depends(require_session)],
) -> InspectionSuiteDetail:
    detail = await service.create_inspection_suite(
        session,
        user.company_id,
        user.id,
        name=payload.name,
        description=payload.description,
        type=payload.type,
        status=payload.status,
        owner_user_id=payload.owner_user_id,
        items=[item.model_dump() for item in payload.items] if payload.items else None,
    )
    return _build_detail(detail)


@router.patch("/{suite_id}", response_model=InspectionSuiteDetail)
async def update_inspection_suite(
    suite_id: int,
    user: Annotated[AuthenticatedUser, require_permission("inspection_suite.edit")],
    payload: InspectionSuiteUpdate,
    session: Annotated[AsyncSession, Depends(require_session)],
) -> InspectionSuiteDetail:
    updates = payload.model_dump(exclude_unset=True)
    if "items" in updates and updates["items"] is not None:
        updates["items"] = [item.model_dump() for item in payload.items]  # type: ignore
    if not updates:
        raise HTTPException(status_code=422, detail={"code": "no_fields"})
    detail = await service.update_inspection_suite(
        session,
        user.company_id,
        user.id,
        suite_id,
        updates,
    )
    if detail is None:
        raise HTTPException(status_code=404, detail={"code": "not_found"})
    return _build_detail(detail)


@router.delete("/{suite_id}", status_code=204)
async def delete_inspection_suite(
    suite_id: int,
    user: Annotated[AuthenticatedUser, require_permission("inspection_suite.delete")],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> None:
    deleted = await service.delete_inspection_suite(
        session,
        user.company_id,
        user.id,
        suite_id,
    )
    if not deleted:
        raise HTTPException(status_code=404, detail={"code": "not_found"})


def _build_detail(detail: dict) -> InspectionSuiteDetail:
    suite = detail["suite"]
    return InspectionSuiteDetail(
        id=suite.id,
        name=suite.name,
        description=suite.description,
        type=suite.type,
        status=suite.status,
        owner_user_id=suite.owner_user_id,
        owner_name=detail["owner_name"],
        item_count=detail["item_count"],
        updated_at=suite.updated_at,
        created_at=suite.created_at,
        items=[
            InspectionSuiteItemResponse(
                id=item.id,
                area=item.area,
                item_name=item.item_name,
                expected_condition=item.expected_condition,
                sort_order=item.sort_order,
            )
            for item in detail["items"]
        ],
    )
