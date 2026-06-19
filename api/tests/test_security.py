import bcrypt
import jwt
import pytest

from app.core.security import (
    create_access_token,
    create_platform_token,
    decode_access_token,
    decode_platform_token,
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
