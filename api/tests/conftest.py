import asyncio

import pytest
from httpx import ASGITransport, AsyncClient
from sqlalchemy.ext.asyncio import async_sessionmaker, create_async_engine
from sqlalchemy.pool import StaticPool

from app.core.security import create_access_token
from app.models.base import Base

JWT_SECRET = "test-secret-with-at-least-32-characters-here"
TENANT_A = 1
TENANT_B = 2


@pytest.fixture(scope="session")
def event_loop():
    loop = asyncio.new_event_loop()
    yield loop
    loop.close()


@pytest.fixture(scope="session")
def test_engine():
    return create_async_engine(
        "sqlite+aiosqlite:///:memory:",
        connect_args={"check_same_thread": False},
        poolclass=StaticPool,
    )


@pytest.fixture(scope="session")
def test_session_factory(test_engine):
    return async_sessionmaker(test_engine, expire_on_commit=False)


@pytest.fixture(scope="session", autouse=True)
async def create_tables(test_engine):
    async with test_engine.begin() as conn:
        await conn.run_sync(Base.metadata.create_all)


@pytest.fixture(scope="session", autouse=True)
async def seed_data(test_session_factory, create_tables):
    from app.models import Company, Role, User

    async with test_session_factory() as s:
        s.add(Company(
            id=TENANT_A, name="Hotel A", slug="hotel-a",
            status="active", timezone="America/Sao_Paulo",
        ))
        s.add(Company(
            id=TENANT_B, name="Hotel B", slug="hotel-b",
            status="active", timezone="America/New_York",
        ))
        await s.flush()

        s.add(Role(id=1, company_id=TENANT_A, code="admin", name="Admin"))
        s.add(Role(id=2, company_id=TENANT_B, code="admin", name="Admin"))
        await s.flush()

        pw = "$2b$12$LJ3m4ys3Lf5UXOAZ3dDkheNPZ8XNfMsZFHmH7.KGZv6JqRiW8gzAi"
        s.add(User(
            id=1, company_id=TENANT_A, name="User A",
            email="a@test.com", password=pw, role_id=1, active=True,
        ))
        s.add(User(
            id=2, company_id=TENANT_B, name="User B",
            email="b@test.com", password=pw, role_id=2, active=True,
        ))
        await s.commit()


@pytest.fixture()
async def session(test_session_factory):
    async with test_session_factory() as s:
        yield s
        await s.rollback()


@pytest.fixture()
def app(test_session_factory):
    from app.core.config import Settings, get_settings
    from app.core.dependencies import require_session
    from app.main import app as fastapi_app

    test_settings = Settings(
        jwt_secret=JWT_SECRET,
        database_url="sqlite+aiosqlite:///:memory:",
    )

    async def _test_session():
        async with test_session_factory() as session:
            yield session

    fastapi_app.dependency_overrides[get_settings] = lambda: test_settings
    fastapi_app.dependency_overrides[require_session] = _test_session

    yield fastapi_app

    fastapi_app.dependency_overrides.clear()


@pytest.fixture()
async def client(app):
    transport = ASGITransport(app=app)
    async with AsyncClient(transport=transport, base_url="http://test") as c:
        yield c


def make_token(
    company_id: int, user_id: int = 1,
    permissions: list[str] | None = None,
) -> str:
    return create_access_token(
        subject=user_id,
        company_id=company_id,
        role_id=1,
        permissions=permissions or [],
        secret=JWT_SECRET,
        minutes=60,
    )


def auth_header(company_id: int, user_id: int = 1) -> dict[str, str]:
    return {"Authorization": f"Bearer {make_token(company_id, user_id)}"}
