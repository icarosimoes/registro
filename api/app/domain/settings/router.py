from typing import Annotated

from fastapi import APIRouter, Depends
from pydantic import BaseModel
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.orm.attributes import flag_modified

from app.core.dependencies import require_session
from app.domain.auth.repository import AuthenticatedUser
from app.domain.users.router import current_user
from app.models import CompanySetting

router = APIRouter(prefix="/settings", tags=["settings"])


async def _get_setting(session: AsyncSession, company_id: int, key: str) -> CompanySetting | None:
    return (
        await session.execute(
            select(CompanySetting).where(
                CompanySetting.company_id == company_id,
                CompanySetting.key == key,
            )
        )
    ).scalar_one_or_none()


async def get_company_setting(session: AsyncSession, company_id: int, key: str) -> dict:
    row = await _get_setting(session, company_id, key)
    return row.value if row else {}


# ── Evolution API ──

class EvolutionConfig(BaseModel):
    api_url: str
    api_key: str
    instance: str


class EvolutionRead(BaseModel):
    has_credentials: bool
    api_url: str | None = None
    instance: str | None = None


@router.get("/evolution", response_model=EvolutionRead)
async def get_evolution(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> EvolutionRead:
    row = await _get_setting(session, user.company_id, "evolution")
    value = row.value if row else {}
    if not value.get("api_key"):
        return EvolutionRead(has_credentials=False)
    return EvolutionRead(
        has_credentials=True,
        api_url=value.get("api_url"),
        instance=value.get("instance"),
    )


@router.post("/evolution", response_model=EvolutionRead)
async def save_evolution(
    body: EvolutionConfig,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> EvolutionRead:
    row = await _get_setting(session, user.company_id, "evolution")
    new_value = {"api_url": body.api_url, "api_key": body.api_key, "instance": body.instance}
    if row:
        row.value = new_value
        flag_modified(row, "value")
    else:
        session.add(CompanySetting(company_id=user.company_id, key="evolution", value=new_value))
    await session.commit()
    return EvolutionRead(has_credentials=True, api_url=body.api_url, instance=body.instance)


# ── Brevo (E-mail transacional) ──

class BrevoConfig(BaseModel):
    api_key: str
    from_address: str
    from_name: str


class BrevoRead(BaseModel):
    has_credentials: bool
    from_address: str | None = None
    from_name: str | None = None


@router.get("/brevo", response_model=BrevoRead)
async def get_brevo(
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> BrevoRead:
    row = await _get_setting(session, user.company_id, "brevo")
    value = row.value if row else {}
    if not value.get("api_key"):
        return BrevoRead(has_credentials=False)
    return BrevoRead(
        has_credentials=True,
        from_address=value.get("from_address"),
        from_name=value.get("from_name"),
    )


@router.post("/brevo", response_model=BrevoRead)
async def save_brevo(
    body: BrevoConfig,
    user: Annotated[AuthenticatedUser, Depends(current_user)],
    session: Annotated[AsyncSession, Depends(require_session)],
) -> BrevoRead:
    row = await _get_setting(session, user.company_id, "brevo")
    new_value = {"api_key": body.api_key, "from_address": body.from_address, "from_name": body.from_name}
    if row:
        row.value = new_value
        flag_modified(row, "value")
    else:
        session.add(CompanySetting(company_id=user.company_id, key="brevo", value=new_value))
    await session.commit()
    return BrevoRead(has_credentials=True, from_address=body.from_address, from_name=body.from_name)
