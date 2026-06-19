from pydantic import BaseModel, Field, field_validator


class LoginRequest(BaseModel):
    email: str = Field(min_length=3, max_length=255)
    password: str = Field(min_length=1, max_length=72)
    company_slug: str | None = Field(default=None, min_length=2, max_length=100)

    @field_validator("email")
    @classmethod
    def normalize_email(cls, value: str) -> str:
        normalized = value.strip().lower()
        if "@" not in normalized:
            raise ValueError("e-mail inválido")
        return normalized

    @field_validator("company_slug")
    @classmethod
    def normalize_company_slug(cls, value: str | None) -> str | None:
        return value.strip().lower() if value else None


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
