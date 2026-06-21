from datetime import datetime

from pydantic import BaseModel


class PermissionSummary(BaseModel):
    id: int
    code: str
    name: str
    module: str


class PermissionGroup(BaseModel):
    module: str
    permissions: list[PermissionSummary]


class RoleSummary(BaseModel):
    id: int
    code: str
    name: str
    permission_codes: list[str]
    user_count: int
    updated_at: datetime


class RoleListResponse(BaseModel):
    items: list[RoleSummary]
    total: int
    page: int
    page_size: int


class RoleCreate(BaseModel):
    code: str
    name: str
    permission_codes: list[str]


class RoleUpdate(BaseModel):
    name: str | None = None
    permission_codes: list[str] | None = None
