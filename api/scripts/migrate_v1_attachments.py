"""
Migração de anexos do V1 (filesystem local) para MinIO.

Uso:
    python -m scripts.migrate_v1_attachments \
        --storage-dir ./v1-storage \
        [--dry-run]

Pré-requisitos:
    1. rsync do storage/app/ do servidor V1 para --storage-dir
    2. import_v1.py já executado (dados no PostgreSQL)
    3. MinIO configurado (S3_ENDPOINT_URL, S3_ACCESS_KEY, etc.)
"""

import argparse
import asyncio
import json
import mimetypes
import os
import sys
from dataclasses import dataclass, field
from pathlib import Path

from sqlalchemy import select, update

sys.path.insert(0, os.path.join(os.path.dirname(__file__), ".."))

from app.core.database import SessionLocal
from app.core.storage import build_object_key, ensure_bucket, upload_file
from app.models import (
    Company,
    Occurrence,
    Procedure,
    ShiftReport,
    User,
)

TENANT_SLUG = "aero-hotel"


@dataclass
class MigrationReport:
    migrated: int = 0
    skipped: int = 0
    missing_file: int = 0
    errors: list[str] = field(default_factory=list)

    def summary(self) -> dict:
        return {
            "migrated": self.migrated,
            "skipped": self.skipped,
            "missing_file": self.missing_file,
            "errors_count": len(self.errors),
            "errors": self.errors[:20],
        }


def resolve_path(storage_dir: Path, db_path: str) -> Path | None:
    if not db_path:
        return None
    cleaned = db_path.lstrip("/").removeprefix("storage/app/")
    candidate = storage_dir / cleaned
    if candidate.is_file():
        return candidate
    if (storage_dir / db_path).is_file():
        return storage_dir / db_path
    return None


def guess_content_type(filepath: Path) -> str:
    ct, _ = mimetypes.guess_type(str(filepath))
    return ct or "application/octet-stream"


async def migrate_user_avatars(
    company_id: int, storage_dir: Path, dry_run: bool, report: MigrationReport,
) -> None:
    async with SessionLocal() as session:
        users = (await session.scalars(
            select(User).where(
                User.company_id == company_id,
                User.avatar_url.isnot(None),
                User.avatar_url != "",
            )
        )).all()
        for user in users:
            if user.avatar_url and user.avatar_url.startswith(f"{company_id}/"):
                report.skipped += 1
                continue
            path = resolve_path(storage_dir, user.avatar_url)
            if not path:
                report.missing_file += 1
                report.errors.append(
                    f"user {user.id}: arquivo não encontrado: {user.avatar_url}"
                )
                continue
            key = build_object_key(company_id, "user-avatar", user.id, path.name)
            if not dry_run:
                data = path.read_bytes()
                upload_file(data, key, guess_content_type(path))
                await session.execute(
                    update(User).where(User.id == user.id).values(avatar_url=key)
                )
            report.migrated += 1
        if not dry_run:
            await session.commit()


async def migrate_occurrence_files(
    company_id: int, storage_dir: Path, dry_run: bool, report: MigrationReport,
) -> None:
    async with SessionLocal() as session:
        items = (await session.scalars(
            select(Occurrence).where(
                Occurrence.company_id == company_id,
                Occurrence.file.isnot(None),
                Occurrence.file != "",
            )
        )).all()
        for item in items:
            if item.file and item.file.startswith(f"{company_id}/"):
                report.skipped += 1
                continue
            path = resolve_path(storage_dir, item.file)
            if not path:
                report.missing_file += 1
                report.errors.append(
                    f"occurrence {item.id}: arquivo não encontrado: {item.file}"
                )
                continue
            key = build_object_key(company_id, "occurrence", item.id, path.name)
            if not dry_run:
                data = path.read_bytes()
                upload_file(data, key, guess_content_type(path))
                await session.execute(
                    update(Occurrence).where(Occurrence.id == item.id).values(file=key)
                )
            report.migrated += 1
        if not dry_run:
            await session.commit()


async def migrate_procedure_files(
    company_id: int, storage_dir: Path, dry_run: bool, report: MigrationReport,
) -> None:
    async with SessionLocal() as session:
        items = (await session.scalars(
            select(Procedure).where(
                Procedure.company_id == company_id,
                Procedure.file.isnot(None),
                Procedure.file != "",
            )
        )).all()
        for item in items:
            if not item.file:
                continue
            entries = item.file.split("|")
            new_entries = []
            for entry in entries:
                raw_path = entry.split(":")[-1] if ":" in entry else entry
                if raw_path.startswith(f"{company_id}/"):
                    new_entries.append(entry)
                    report.skipped += 1
                    continue
                path = resolve_path(storage_dir, raw_path)
                if not path:
                    report.missing_file += 1
                    report.errors.append(
                        f"procedure {item.id}: arquivo não encontrado: {raw_path}"
                    )
                    new_entries.append(entry)
                    continue
                key = build_object_key(company_id, "procedure", item.id, path.name)
                if not dry_run:
                    data = path.read_bytes()
                    upload_file(data, key, guess_content_type(path))
                prefix = entry.split(":")[0] + ":" if ":" in entry else ""
                new_entries.append(f"{prefix}{key}")
                report.migrated += 1
            if not dry_run:
                await session.execute(
                    update(Procedure)
                    .where(Procedure.id == item.id)
                    .values(file="|".join(new_entries))
                )
        if not dry_run:
            await session.commit()


async def migrate_shift_report_payload_files(
    company_id: int, storage_dir: Path, dry_run: bool, report: MigrationReport,
) -> None:
    """ShiftReport uploads are referenced in the payload JSON from import_v1."""
    async with SessionLocal() as session:
        items = (await session.scalars(
            select(ShiftReport).where(ShiftReport.company_id == company_id)
        )).all()
        for item in items:
            if not item.payload:
                continue
            report.skipped += 1
        if not dry_run:
            await session.commit()


async def run(storage_dir: Path, dry_run: bool) -> dict:
    if SessionLocal is None:
        raise RuntimeError("DATABASE_URL não configurada")

    if not dry_run:
        ensure_bucket()

    async with SessionLocal() as session:
        company = await session.scalar(
            select(Company).where(Company.slug == TENANT_SLUG)
        )
        if not company:
            raise RuntimeError(
                f"Tenant '{TENANT_SLUG}' não encontrado. Rode import_v1.py primeiro."
            )
        company_id = company.id

    reports: dict[str, MigrationReport] = {}

    tasks = [
        ("user_avatars", migrate_user_avatars),
        ("occurrence_files", migrate_occurrence_files),
        ("procedure_files", migrate_procedure_files),
    ]

    for name, func in tasks:
        r = MigrationReport()
        print(f"{'[DRY RUN] ' if dry_run else ''}Migrando {name}...")
        await func(company_id, storage_dir, dry_run, r)
        reports[name] = r
        print(f"  → migrados={r.migrated} | faltando={r.missing_file} | pulados={r.skipped}")

    total = {
        "dry_run": dry_run,
        "storage_dir": str(storage_dir),
        "entities": {k: v.summary() for k, v in reports.items()},
        "total_migrated": sum(r.migrated for r in reports.values()),
        "total_missing": sum(r.missing_file for r in reports.values()),
    }
    return total


def main() -> None:
    parser = argparse.ArgumentParser(description="Migra anexos V1 para MinIO")
    parser.add_argument(
        "--storage-dir",
        type=Path,
        required=True,
        help="Diretório local com o conteúdo de storage/app/ do V1",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Simula a migração sem fazer upload nem alterar o banco",
    )
    args = parser.parse_args()

    if not args.storage_dir.is_dir():
        print(f"Erro: diretório não encontrado: {args.storage_dir}")
        sys.exit(1)

    result = asyncio.run(run(args.storage_dir, args.dry_run))
    print("\n" + json.dumps(result, indent=2, ensure_ascii=False))


if __name__ == "__main__":
    main()
