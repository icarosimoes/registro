from pydantic import BaseModel, Field, field_validator


class LoginRequest(BaseModel):
    email: str = Field(min_length=3, max_length=255)
    password: str = Field(min_length=1, max_length=72)
    company_id: int | None = None

    @field_validator("email")
    @classmethod
    def normalize_email(cls, value: str) -> str:
        normalized = value.strip().lower()
        if "@" not in normalized:
            raise ValueError("e-mail inválido")
        return normalized


class TenantOption(BaseModel):
    id: int
    name: str


class UserResponse(BaseModel):
    id: int
    name: str
    email: str
    company_id: int
    role_id: int | None
    role_name: str | None
    permissions: list[str]


class TokenResponse(BaseModel):
    access_token: str
    token_type: str = "bearer"
    expires_in: int
    user: UserResponse
