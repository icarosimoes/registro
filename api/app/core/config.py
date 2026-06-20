from functools import lru_cache
from pathlib import Path
from typing import Annotated

from pydantic import field_validator
from pydantic_settings import BaseSettings, NoDecode, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8", extra="ignore")

    app_name: str = "Registro API"
    environment: str = "development"
    api_prefix: str = "/api/v1"
    web_origins: Annotated[list[str], NoDecode] = ["http://localhost:3000"]
    database_url: str | None = None
    database_url_file: str | None = None
    database_echo: bool = False
    jwt_secret: str = "registro-development-only-change-me"
    jwt_secret_file: str | None = None
    access_token_minutes: int = 30
    chess_hotel_integration_key: str = "chess-hotel-development"
    chess_hotel_integration_key_file: str | None = None
    chess_hotel_company_slug: str = "aero-hotel"
    registro_web_url: str = "http://localhost:3000"

    @field_validator("web_origins", mode="before")
    @classmethod
    def parse_origins(cls, value: str | list[str]) -> list[str]:
        if isinstance(value, str):
            return [origin.strip() for origin in value.split(",") if origin.strip()]
        return value

    @field_validator("database_url", mode="before")
    @classmethod
    def blank_database_url_is_unconfigured(cls, value: str | None) -> str | None:
        if isinstance(value, str) and not value.strip():
            return None
        return value


@lru_cache
def get_settings() -> Settings:
    settings = Settings()
    if settings.database_url is None and settings.database_url_file:
        settings.database_url = Path(settings.database_url_file).read_text(encoding="utf-8").strip()
    if settings.jwt_secret_file:
        settings.jwt_secret = Path(settings.jwt_secret_file).read_text(encoding="utf-8").strip()
    if settings.chess_hotel_integration_key_file:
        settings.chess_hotel_integration_key = Path(
            settings.chess_hotel_integration_key_file
        ).read_text(encoding="utf-8").strip()
    insecure_default = "registro-development-only-change-me"
    if settings.environment == "production" and (
        settings.jwt_secret == insecure_default or len(settings.jwt_secret) < 32
    ):
        raise RuntimeError("JWT_SECRET de produção deve ter pelo menos 32 caracteres")
    if settings.environment == "production" and (
        settings.chess_hotel_integration_key == "chess-hotel-development"
        or len(settings.chess_hotel_integration_key) < 32
    ):
        raise RuntimeError("CHESS_HOTEL_INTEGRATION_KEY de produção deve ter pelo menos 32 caracteres")
    return settings
