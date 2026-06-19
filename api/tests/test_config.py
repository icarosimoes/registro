from app.core.config import Settings


def test_web_origins_accepts_comma_separated_environment_value(
    monkeypatch,
) -> None:
    monkeypatch.setenv(
        "WEB_ORIGINS",
        "http://localhost:3000, https://registro.example.com",
    )

    settings = Settings(_env_file=None)

    assert settings.web_origins == [
        "http://localhost:3000",
        "https://registro.example.com",
    ]
