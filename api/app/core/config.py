from functools import lru_cache
from pathlib import Path

from pydantic import field_validator
from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8", extra="ignore")

    app_name: str = "Registro API"
    environment: str = "development"
    api_prefix: str = "/api/v1"
    web_origins: list[str] = ["http://localhost:3000"]
    database_url: str | None = None
    database_url_file: str | None = None
    database_echo: bool = False

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
    return settings
