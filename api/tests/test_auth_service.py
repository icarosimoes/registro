import bcrypt
import pytest
from pydantic import ValidationError

from app.core.config import Settings
from app.domain.auth import service
from app.domain.auth.repository import AuthenticatedUser
from app.domain.auth.schemas import LoginRequest
from app.domain.auth.service import MultiTenantResult

TEST_SECRET = "test-secret-with-at-least-32-characters"


def password_hash(password: str) -> str:
    return bcrypt.hashpw(password.encode(), bcrypt.gensalt()).decode()


def user(*, user_id: int, company_id: int, company_name: str, password: str) -> AuthenticatedUser:
    return AuthenticatedUser(
        id=user_id,
        name=f"Usuário {user_id}",
        email="multi@example.com",
        phone=None,
        password_hash=password_hash(password),
        company_id=company_id,
        company_name=company_name,
        role_id=1,
        role_name="Administrador",
        permissions=["occurrences:list"],
    )


def settings() -> Settings:
    return Settings(_env_file=None, jwt_secret=TEST_SECRET)


@pytest.mark.asyncio
async def test_unique_tenant_authenticates(monkeypatch: pytest.MonkeyPatch) -> None:
    candidate = user(user_id=1, company_id=10, company_name="Aero Hotel", password="correta")

    async def find_users(*_args, **_kwargs):
        return [candidate]

    monkeypatch.setattr(service, "find_active_users_by_email", find_users)
    result = await service.authenticate(object(), candidate.email, "correta", settings())

    assert result is not None
    assert not isinstance(result, MultiTenantResult)
    assert result.user.company_id == 10


@pytest.mark.asyncio
async def test_wrong_password_does_not_reveal_tenants(monkeypatch: pytest.MonkeyPatch) -> None:
    candidates = [
        user(user_id=1, company_id=10, company_name="Aero Hotel", password="correta"),
        user(user_id=2, company_id=20, company_name="Outro Hotel", password="correta"),
    ]

    async def find_users(*_args, **_kwargs):
        return candidates

    monkeypatch.setattr(service, "find_active_users_by_email", find_users)
    result = await service.authenticate(object(), "multi@example.com", "incorreta", settings())

    assert result is None


@pytest.mark.asyncio
async def test_valid_password_lists_only_matching_tenants(monkeypatch: pytest.MonkeyPatch) -> None:
    candidates = [
        user(user_id=1, company_id=10, company_name="Aero Hotel", password="compartilhada"),
        user(user_id=2, company_id=20, company_name="Outro Hotel", password="compartilhada"),
        user(user_id=3, company_id=30, company_name="Tenant com outra senha", password="diferente"),
    ]

    async def find_users(*_args, **_kwargs):
        return candidates

    monkeypatch.setattr(service, "find_active_users_by_email", find_users)
    result = await service.authenticate(object(), "multi@example.com", "compartilhada", settings())

    assert isinstance(result, MultiTenantResult)
    assert [(tenant.id, tenant.name) for tenant in result.tenants] == [
        (10, "Aero Hotel"),
        (20, "Outro Hotel"),
    ]


@pytest.mark.asyncio
async def test_selected_tenant_is_forwarded_to_repository(monkeypatch: pytest.MonkeyPatch) -> None:
    candidate = user(user_id=2, company_id=20, company_name="Outro Hotel", password="correta")
    received_company_id = None

    async def find_users(_session, _email, company_id=None):
        nonlocal received_company_id
        received_company_id = company_id
        return [candidate]

    monkeypatch.setattr(service, "find_active_users_by_email", find_users)
    result = await service.authenticate(
        object(), candidate.email, "correta", settings(), company_id=20
    )

    assert received_company_id == 20
    assert result is not None
    assert not isinstance(result, MultiTenantResult)
    assert result.user.company_id == 20


def test_login_rejects_non_positive_company_id() -> None:
    with pytest.raises(ValidationError):
        LoginRequest(email="usuario@example.com", password="senha", company_id=0)
