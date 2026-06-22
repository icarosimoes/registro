import io
from uuid import uuid4

import boto3
from botocore.config import Config as BotoConfig
from botocore.exceptions import ClientError

from app.core.config import get_settings

ALLOWED_EXTENSIONS = {
    ".jpg", ".jpeg", ".png", ".gif", ".webp", ".svg",
    ".pdf", ".doc", ".docx", ".xls", ".xlsx", ".csv",
    ".txt", ".zip", ".rar", ".7z",
}

ALLOWED_CONTENT_TYPES = {
    "image/jpeg", "image/png", "image/gif", "image/webp", "image/svg+xml",
    "application/pdf",
    "application/msword",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "application/vnd.ms-excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "text/csv", "text/plain",
    "application/zip", "application/x-rar-compressed",
    "application/x-7z-compressed",
}


def _get_client():
    settings = get_settings()
    return boto3.client(
        "s3",
        endpoint_url=settings.s3_endpoint_url,
        aws_access_key_id=settings.s3_access_key,
        aws_secret_access_key=settings.s3_secret_key,
        config=BotoConfig(signature_version="s3v4"),
        region_name="us-east-1",
    )


def ensure_bucket() -> None:
    settings = get_settings()
    client = _get_client()
    try:
        client.head_bucket(Bucket=settings.s3_bucket)
    except ClientError:
        client.create_bucket(Bucket=settings.s3_bucket)


def build_object_key(
    company_id: int, entity_type: str, entity_id: int, filename: str,
) -> str:
    ext = ""
    if "." in filename:
        ext = "." + filename.rsplit(".", 1)[-1].lower()
    return f"{company_id}/{entity_type}/{entity_id}/{uuid4().hex}{ext}"


def upload_file(
    data: bytes, key: str, content_type: str,
) -> str:
    settings = get_settings()
    client = _get_client()
    client.upload_fileobj(
        io.BytesIO(data),
        settings.s3_bucket,
        key,
        ExtraArgs={"ContentType": content_type},
    )
    return key


def download_file(key: str) -> tuple[io.BytesIO, str]:
    settings = get_settings()
    client = _get_client()
    response = client.get_object(Bucket=settings.s3_bucket, Key=key)
    content_type = response.get("ContentType", "application/octet-stream")
    buf = io.BytesIO(response["Body"].read())
    buf.seek(0)
    return buf, content_type


def delete_file(key: str) -> None:
    settings = get_settings()
    client = _get_client()
    client.delete_object(Bucket=settings.s3_bucket, Key=key)


def get_extension(filename: str) -> str:
    if "." in filename:
        return "." + filename.rsplit(".", 1)[-1].lower()
    return ""


MAGIC_SIGNATURES: dict[bytes, set[str]] = {
    b"%PDF": {"application/pdf"},
    b"\x89PNG": {"image/png"},
    b"\xff\xd8\xff": {"image/jpeg"},
    b"GIF87a": {"image/gif"},
    b"GIF89a": {"image/gif"},
    b"PK\x03\x04": {
        "application/zip",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    },
    b"Rar!\x1a\x07": {"application/x-rar-compressed"},
    b"7z\xbc\xaf\x27\x1c": {"application/x-7z-compressed"},
}


def _check_magic(data: bytes, content_type: str) -> bool:
    for signature, allowed_types in MAGIC_SIGNATURES.items():
        if data[:len(signature)] == signature:
            return content_type in allowed_types
    return True


def validate_file(
    filename: str, content_type: str, size: int, data: bytes | None = None,
) -> str | None:
    settings = get_settings()
    max_bytes = settings.attachment_max_size_mb * 1024 * 1024
    if size > max_bytes:
        return f"Arquivo excede {settings.attachment_max_size_mb}MB"
    ext = get_extension(filename)
    if ext and ext not in ALLOWED_EXTENSIONS:
        return f"Extensão {ext} não permitida"
    if content_type not in ALLOWED_CONTENT_TYPES:
        return f"Tipo {content_type} não permitido"
    if data and not _check_magic(data, content_type):
        return "Conteúdo do arquivo não corresponde ao tipo declarado"
    return None
