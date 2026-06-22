from datetime import UTC, datetime, timedelta

import bcrypt
import jwt
import pytest

from app.core.security import (
    create_access_token,
    create_invite_token,
    create_platform_refresh_token,
    create_platform_token,
    create_refresh_token,
    decode_access_token,
    decode_invite_token,
    decode_platform_refresh_token,
    decode_platform_token,
    decode_refresh_token,
    verify_laravel_password,
)

TEST_SECRET = "test-secret-with-at-least-32-characters"


def test_verifies_laravel_bcrypt_hash() -> None:
    password_hash = bcrypt.hashpw(b"senha-segura", bcrypt.gensalt()).decode()
    password_hash = password_hash.replace("$2b$", "$2y$", 1)

    assert verify_laravel_password("senha-segura", password_hash)
    assert not verify_laravel_password("incorreta", password_hash)


def test_access_token_preserves_tenant_and_permissions() -> None:
    token = create_access_token(
        subject=7,
        company_id=3,
        role_id=2,
        permissions=["admin:users:list"],
        secret=TEST_SECRET,
        minutes=5,
    )

    claims = decode_access_token(token, TEST_SECRET)

    assert claims["sub"] == "7"
    assert claims["company_id"] == 3
    assert claims["permissions"] == ["admin:users:list"]


def test_rejects_token_with_unexpected_type() -> None:
    token = jwt.encode({"type": "refresh"}, TEST_SECRET, algorithm="HS256")

    with pytest.raises(jwt.InvalidTokenError):
        decode_access_token(token, TEST_SECRET)


def test_platform_token_has_separate_type() -> None:
    token = create_platform_token(
        subject=1,
        role="super_admin",
        secret=TEST_SECRET,
        minutes=5,
    )

    assert decode_platform_token(token, TEST_SECRET)["type"] == "platform_access"
    with pytest.raises(jwt.InvalidTokenError):
        decode_access_token(token, TEST_SECRET)


class TestRefreshToken:
    def test_create_and_decode(self):
        token = create_refresh_token(subject=5, company_id=2, secret=TEST_SECRET, days=7)
        claims = decode_refresh_token(token, TEST_SECRET)
        assert claims["sub"] == "5"
        assert claims["company_id"] == 2
        assert claims["type"] == "refresh"

    def test_decode_rejects_access_type(self):
        token = create_access_token(
            subject=1,
            company_id=1,
            role_id=1,
            permissions=[],
            secret=TEST_SECRET,
            minutes=5,
        )
        with pytest.raises(jwt.InvalidTokenError):
            decode_refresh_token(token, TEST_SECRET)

    def test_expired_refresh_rejected(self):
        payload = {
            "sub": "1",
            "company_id": 1,
            "type": "refresh",
            "iat": datetime.now(UTC) - timedelta(days=10),
            "exp": datetime.now(UTC) - timedelta(days=3),
        }
        token = jwt.encode(payload, TEST_SECRET, algorithm="HS256")
        with pytest.raises(jwt.ExpiredSignatureError):
            decode_refresh_token(token, TEST_SECRET)


class TestPlatformRefreshToken:
    def test_create_and_decode(self):
        token = create_platform_refresh_token(
            subject=1, role="super_admin", secret=TEST_SECRET, days=7
        )
        claims = decode_platform_refresh_token(token, TEST_SECRET)
        assert claims["sub"] == "1"
        assert claims["role"] == "super_admin"
        assert claims["type"] == "platform_refresh"

    def test_decode_rejects_platform_access_type(self):
        token = create_platform_token(subject=1, role="admin", secret=TEST_SECRET, minutes=5)
        with pytest.raises(jwt.InvalidTokenError):
            decode_platform_refresh_token(token, TEST_SECRET)


class TestInviteToken:
    def test_create_and_decode(self):
        token = create_invite_token(user_id=10, company_id=3, secret=TEST_SECRET, hours=48)
        claims = decode_invite_token(token, TEST_SECRET)
        assert claims["sub"] == "10"
        assert claims["company_id"] == 3
        assert claims["type"] == "invite"

    def test_decode_rejects_wrong_type(self):
        token = create_access_token(
            subject=1,
            company_id=1,
            role_id=1,
            permissions=[],
            secret=TEST_SECRET,
            minutes=5,
        )
        with pytest.raises(jwt.InvalidTokenError):
            decode_invite_token(token, TEST_SECRET)

    def test_expired_invite_rejected(self):
        payload = {
            "sub": "1",
            "company_id": 1,
            "type": "invite",
            "iat": datetime.now(UTC) - timedelta(hours=72),
            "exp": datetime.now(UTC) - timedelta(hours=24),
        }
        token = jwt.encode(payload, TEST_SECRET, algorithm="HS256")
        with pytest.raises(jwt.ExpiredSignatureError):
            decode_invite_token(token, TEST_SECRET)


class TestExpiredTokens:
    def test_expired_access_rejected(self):
        payload = {
            "sub": "1",
            "company_id": 1,
            "type": "access",
            "iat": datetime.now(UTC) - timedelta(hours=2),
            "exp": datetime.now(UTC) - timedelta(hours=1),
        }
        token = jwt.encode(payload, TEST_SECRET, algorithm="HS256")
        with pytest.raises(jwt.ExpiredSignatureError):
            decode_access_token(token, TEST_SECRET)

    def test_expired_platform_rejected(self):
        payload = {
            "sub": "1",
            "role": "admin",
            "type": "platform_access",
            "iat": datetime.now(UTC) - timedelta(hours=2),
            "exp": datetime.now(UTC) - timedelta(hours=1),
        }
        token = jwt.encode(payload, TEST_SECRET, algorithm="HS256")
        with pytest.raises(jwt.ExpiredSignatureError):
            decode_platform_token(token, TEST_SECRET)

    def test_wrong_secret_rejected(self):
        token = create_access_token(
            subject=1,
            company_id=1,
            role_id=1,
            permissions=[],
            secret=TEST_SECRET,
            minutes=5,
        )
        with pytest.raises(jwt.InvalidSignatureError):
            decode_access_token(token, "wrong-secret-that-is-long-enough")
