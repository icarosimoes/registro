"""Testes dos endpoints de integração Chess Hotel."""

import pytest
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.models import Company, Role, User

INTEGRATION_KEY = "chess-hotel-development"
CHESS_SLUG = "aero-hotel"
BASE = "/api/v1/integrations/chess-hotel"


def chess_headers(key: str | None = INTEGRATION_KEY) -> dict[str, str]:
    headers: dict[str, str] = {}
    if key is not None:
        headers["X-Registro-Key"] = key
    return headers


@pytest.fixture()
async def chess_company(session: AsyncSession) -> Company:
    """Cria a company com slug 'aero-hotel' que a integração espera."""
    existing = await session.scalar(
        select(Company).where(Company.slug == CHESS_SLUG)
    )
    if existing:
        await session.commit()
        return existing

    company = Company(
        name="Aero Hotel",
        slug=CHESS_SLUG,
        status="active",
        timezone="America/Sao_Paulo",
    )
    session.add(company)
    await session.commit()
    await session.refresh(company)
    return company


@pytest.fixture()
async def chess_role(session: AsyncSession, chess_company: Company) -> Role:
    """Cria um role para a company de integração."""
    existing = await session.scalar(
        select(Role).where(
            Role.company_id == chess_company.id,
            Role.code == "admin",
        )
    )
    if existing:
        await session.commit()
        return existing

    role = Role(
        company_id=chess_company.id,
        code="admin",
        name="Admin",
    )
    session.add(role)
    await session.commit()
    await session.refresh(role)
    return role


@pytest.fixture()
async def chess_user(
    session: AsyncSession, chess_company: Company, chess_role: Role
) -> User:
    """Cria um usuario ativo na company de integração."""
    existing = await session.scalar(
        select(User).where(
            User.company_id == chess_company.id,
            User.email == "chess@test.com",
        )
    )
    if existing:
        await session.commit()
        return existing

    pw = "$2b$12$LJ3m4ys3Lf5UXOAZ3dDkheNPZ8XNfMsZFHmH7.KGZv6JqRiW8gzAi"
    user = User(
        company_id=chess_company.id,
        name="Chess User",
        email="chess@test.com",
        password=pw,
        role_id=chess_role.id,
        active=True,
    )
    session.add(user)
    await session.commit()
    await session.refresh(user)
    return user


# ---------------------------------------------------------------------------
# 1. Autenticação — X-Registro-Key
# ---------------------------------------------------------------------------


class TestIntegrationAuth:
    """Todos os endpoints devem rejeitar requests sem chave ou com chave errada."""

    @pytest.mark.asyncio
    async def test_missing_key_resolve_returns_401(self, client):
        r = await client.post(
            f"{BASE}/users/resolve",
            json={"email": "any@test.com"},
        )
        assert r.status_code == 401
        assert r.json()["detail"]["code"] == "invalid_integration_key"

    @pytest.mark.asyncio
    async def test_wrong_key_resolve_returns_401(self, client):
        r = await client.post(
            f"{BASE}/users/resolve",
            json={"email": "any@test.com"},
            headers=chess_headers("wrong-key"),
        )
        assert r.status_code == 401
        assert r.json()["detail"]["code"] == "invalid_integration_key"

    @pytest.mark.asyncio
    async def test_missing_key_create_ticket_returns_401(self, client):
        r = await client.post(f"{BASE}/tickets", json=_ticket_body())
        assert r.status_code == 401

    @pytest.mark.asyncio
    async def test_wrong_key_list_tickets_returns_401(self, client):
        r = await client.get(
            f"{BASE}/tickets",
            params={"email": "any@test.com"},
            headers=chess_headers("bad-key"),
        )
        assert r.status_code == 401

    @pytest.mark.asyncio
    async def test_wrong_key_track_ticket_returns_401(self, client):
        r = await client.get(
            f"{BASE}/tickets/REG-000001",
            params={"email": "any@test.com"},
            headers=chess_headers("bad-key"),
        )
        assert r.status_code == 401


# ---------------------------------------------------------------------------
# 2. POST /users/resolve
# ---------------------------------------------------------------------------


class TestResolveUser:
    @pytest.mark.asyncio
    async def test_resolve_known_user(self, client, chess_user):
        r = await client.post(
            f"{BASE}/users/resolve",
            json={"email": chess_user.email},
            headers=chess_headers(),
        )
        assert r.status_code == 200
        data = r.json()
        assert data["exists"] is True
        assert data["id"] == chess_user.id
        assert data["name"] == chess_user.name
        assert data["email"] == chess_user.email

    @pytest.mark.asyncio
    async def test_resolve_case_insensitive(self, client, chess_user):
        r = await client.post(
            f"{BASE}/users/resolve",
            json={"email": "CHESS@TEST.COM"},
            headers=chess_headers(),
        )
        assert r.status_code == 200
        assert r.json()["exists"] is True

    @pytest.mark.asyncio
    async def test_resolve_unknown_email_returns_404(self, client, chess_company):
        r = await client.post(
            f"{BASE}/users/resolve",
            json={"email": "nobody@test.com"},
            headers=chess_headers(),
        )
        assert r.status_code == 404
        assert r.json()["detail"]["code"] == "registro_user_not_found"

    @pytest.mark.asyncio
    async def test_resolve_invalid_email_returns_422(self, client, chess_company):
        r = await client.post(
            f"{BASE}/users/resolve",
            json={"email": "not-an-email"},
            headers=chess_headers(),
        )
        assert r.status_code == 422


# ---------------------------------------------------------------------------
# 3. POST /tickets — criar ticket
# ---------------------------------------------------------------------------


def _ticket_body(email: str = "chess@test.com", **overrides) -> dict:
    base = {
        "module": "solicitacoes-fiscais",
        "requestType": "NF-e",
        "solicitante": "Hospede Teste",
        "solicitanteEmail": email,
        "chessUserId": "chess-42",
        "hotel": CHESS_SLUG,
    }
    base.update(overrides)
    return base


class TestCreateTicket:
    @pytest.mark.asyncio
    async def test_create_valid_ticket(self, client, chess_user):
        body = _ticket_body(email=chess_user.email, apartment="101")
        r = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert r.status_code == 200
        data = r.json()
        assert data["protocol"].startswith("REG-")
        assert data["status"] == "Em andamento"
        assert data["sla_deadline"] is not None
        assert "url" in data
        assert data["attachments_count"] == 0

    @pytest.mark.asyncio
    async def test_create_ticket_with_reservation(self, client, chess_user):
        body = _ticket_body(
            email=chess_user.email,
            reservationNumber="RES-2024-001",
            apartment="202",
        )
        r = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert r.status_code == 200
        assert r.json()["protocol"].startswith("REG-")

    @pytest.mark.asyncio
    async def test_create_ticket_unsupported_module_returns_422(
        self, client, chess_user
    ):
        body = _ticket_body(email=chess_user.email, module="outro-modulo")
        r = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert r.status_code == 422
        assert r.json()["detail"]["code"] == "unsupported_module"

    @pytest.mark.asyncio
    async def test_create_ticket_invalid_hotel_returns_422(
        self, client, chess_user
    ):
        body = _ticket_body(email=chess_user.email, hotel="wrong-hotel")
        r = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert r.status_code == 422
        assert r.json()["detail"]["code"] == "invalid_hotel"

    @pytest.mark.asyncio
    async def test_create_ticket_unknown_email_returns_404(
        self, client, chess_company
    ):
        body = _ticket_body(email="nobody@test.com")
        r = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert r.status_code == 404
        assert r.json()["detail"]["code"] == "registro_user_not_found"

    @pytest.mark.asyncio
    async def test_create_ticket_invalid_email_format_returns_422(
        self, client, chess_company
    ):
        body = _ticket_body(email="bad-email")
        r = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert r.status_code == 422


# ---------------------------------------------------------------------------
# 4. GET /tickets — listar tickets por email
# ---------------------------------------------------------------------------


class TestListTickets:
    @pytest.mark.asyncio
    async def test_list_tickets_returns_created(self, client, chess_user):
        # Cria um ticket primeiro
        body = _ticket_body(email=chess_user.email, apartment="301")
        create_resp = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert create_resp.status_code == 200
        protocol = create_resp.json()["protocol"]

        # Lista tickets
        r = await client.get(
            f"{BASE}/tickets",
            params={"email": chess_user.email},
            headers=chess_headers(),
        )
        assert r.status_code == 200
        data = r.json()
        assert data["user"]["exists"] is True
        assert data["user"]["id"] == chess_user.id
        assert isinstance(data["items"], list)
        assert len(data["items"]) >= 1
        protocols = [item["protocol"] for item in data["items"]]
        assert protocol in protocols

    @pytest.mark.asyncio
    async def test_list_tickets_item_structure(self, client, chess_user):
        # Garante ao menos um ticket
        await client.post(
            f"{BASE}/tickets",
            json=_ticket_body(email=chess_user.email),
            headers=chess_headers(),
        )

        r = await client.get(
            f"{BASE}/tickets",
            params={"email": chess_user.email},
            headers=chess_headers(),
        )
        assert r.status_code == 200
        item = r.json()["items"][0]
        assert "protocol" in item
        assert "request_type" in item
        assert "status" in item
        assert "sla_deadline" in item
        assert "completed" in item
        assert "updated_at" in item
        assert "url" in item
        assert "history" in item

    @pytest.mark.asyncio
    async def test_list_tickets_unknown_email_returns_404(
        self, client, chess_company
    ):
        r = await client.get(
            f"{BASE}/tickets",
            params={"email": "nobody@test.com"},
            headers=chess_headers(),
        )
        assert r.status_code == 404
        assert r.json()["detail"]["code"] == "registro_user_not_found"


# ---------------------------------------------------------------------------
# 5. GET /tickets/{protocol} — rastrear ticket por protocolo
# ---------------------------------------------------------------------------


class TestTrackTicket:
    @pytest.mark.asyncio
    async def test_track_existing_ticket(self, client, chess_user):
        # Cria ticket
        body = _ticket_body(email=chess_user.email, apartment="401")
        create_resp = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert create_resp.status_code == 200
        protocol = create_resp.json()["protocol"]

        # Rastreia
        r = await client.get(
            f"{BASE}/tickets/{protocol}",
            params={"email": chess_user.email},
            headers=chess_headers(),
        )
        assert r.status_code == 200
        data = r.json()
        assert data["protocol"] == protocol
        assert data["request_type"] == "NF-e"
        assert data["status"] == "Em andamento"
        assert data["completed"] is False
        assert data["url"] is not None
        assert isinstance(data["history"], list)

    @pytest.mark.asyncio
    async def test_track_unknown_protocol_returns_404(self, client, chess_user):
        r = await client.get(
            f"{BASE}/tickets/REG-999999",
            params={"email": chess_user.email},
            headers=chess_headers(),
        )
        assert r.status_code == 404
        assert r.json()["detail"]["code"] == "ticket_not_found"

    @pytest.mark.asyncio
    async def test_track_ticket_unknown_email_returns_404(
        self, client, chess_company
    ):
        r = await client.get(
            f"{BASE}/tickets/REG-000001",
            params={"email": "nobody@test.com"},
            headers=chess_headers(),
        )
        assert r.status_code == 404
        assert r.json()["detail"]["code"] == "registro_user_not_found"

    @pytest.mark.asyncio
    async def test_track_ticket_wrong_user_returns_404(
        self, client, session, chess_company, chess_role, chess_user
    ):
        """Ticket criado por um usuario nao aparece para outro."""
        # Cria ticket com chess_user
        body = _ticket_body(email=chess_user.email)
        create_resp = await client.post(
            f"{BASE}/tickets",
            json=body,
            headers=chess_headers(),
        )
        assert create_resp.status_code == 200
        protocol = create_resp.json()["protocol"]

        # Cria outro usuario na mesma company
        pw = "$2b$12$LJ3m4ys3Lf5UXOAZ3dDkheNPZ8XNfMsZFHmH7.KGZv6JqRiW8gzAi"
        other_user = User(
            company_id=chess_company.id,
            name="Other User",
            email="other@test.com",
            password=pw,
            role_id=chess_role.id,
            active=True,
        )
        session.add(other_user)
        await session.commit()
        await session.refresh(other_user)

        # Tenta rastrear com email do outro usuario
        r = await client.get(
            f"{BASE}/tickets/{protocol}",
            params={"email": "other@test.com"},
            headers=chess_headers(),
        )
        assert r.status_code == 404
        assert r.json()["detail"]["code"] == "ticket_not_found"
