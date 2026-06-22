"""Testes unitarios para o modulo de storage (funcoes puras)."""

from unittest.mock import patch

from app.core.storage import (
    ALLOWED_EXTENSIONS,
    _check_magic,
    build_object_key,
    get_extension,
    validate_file,
)


class TestGetExtension:
    def test_simple_extension(self):
        assert get_extension("photo.jpg") == ".jpg"

    def test_multiple_dots(self):
        assert get_extension("report.v2.pdf") == ".pdf"

    def test_uppercase_normalized(self):
        assert get_extension("image.PNG") == ".png"

    def test_no_extension(self):
        assert get_extension("README") == ""

    def test_hidden_file(self):
        assert get_extension(".gitignore") == ".gitignore"

    def test_empty_string(self):
        assert get_extension("") == ""


class TestBuildObjectKey:
    def test_format_with_extension(self):
        key = build_object_key(1, "occurrence", 42, "photo.jpg")
        parts = key.split("/")
        assert parts[0] == "1"
        assert parts[1] == "occurrence"
        assert parts[2] == "42"
        assert parts[3].endswith(".jpg")
        assert len(parts[3]) == 32 + 4  # uuid hex + .jpg

    def test_format_without_extension(self):
        key = build_object_key(5, "meeting", 10, "README")
        parts = key.split("/")
        assert parts[0] == "5"
        assert parts[1] == "meeting"
        assert parts[2] == "10"
        assert "." not in parts[3]  # no extension

    def test_filename_with_multiple_dots(self):
        key = build_object_key(1, "doc", 1, "report.v2.pdf")
        assert key.endswith(".pdf")

    def test_unique_keys(self):
        key1 = build_object_key(1, "x", 1, "a.pdf")
        key2 = build_object_key(1, "x", 1, "a.pdf")
        assert key1 != key2  # uuid makes them unique


class TestCheckMagic:
    def test_pdf_magic_with_pdf_type(self):
        data = b"%PDF-1.4 rest of file"
        assert _check_magic(data, "application/pdf") is True

    def test_pdf_magic_with_wrong_type(self):
        data = b"%PDF-1.4 rest of file"
        assert _check_magic(data, "image/jpeg") is False

    def test_png_magic_with_png_type(self):
        data = b"\x89PNG\r\n\x1a\n rest of file"
        assert _check_magic(data, "image/png") is True

    def test_png_magic_with_wrong_type(self):
        data = b"\x89PNG\r\n\x1a\n rest of file"
        assert _check_magic(data, "application/pdf") is False

    def test_jpeg_magic(self):
        data = b"\xff\xd8\xff\xe0 rest of file"
        assert _check_magic(data, "image/jpeg") is True

    def test_gif87a_magic(self):
        data = b"GIF87a rest of file"
        assert _check_magic(data, "image/gif") is True

    def test_gif89a_magic(self):
        data = b"GIF89a rest of file"
        assert _check_magic(data, "image/gif") is True

    def test_zip_magic_accepts_docx(self):
        data = b"PK\x03\x04 rest of zip"
        assert _check_magic(
            data,
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        ) is True

    def test_zip_magic_accepts_xlsx(self):
        data = b"PK\x03\x04 rest of zip"
        assert _check_magic(
            data,
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ) is True

    def test_rar_magic(self):
        data = b"Rar!\x1a\x07\x00 rest of rar"
        assert _check_magic(data, "application/x-rar-compressed") is True

    def test_7z_magic(self):
        data = b"7z\xbc\xaf\x27\x1c rest of 7z"
        assert _check_magic(data, "application/x-7z-compressed") is True

    def test_unknown_magic_allows_any_type(self):
        data = b"\x00\x00\x00\x00 unknown content"
        assert _check_magic(data, "text/plain") is True

    def test_text_content_allows_any_type(self):
        data = b"Hello, this is a text file."
        assert _check_magic(data, "text/csv") is True


class TestValidateFile:
    """Testa validate_file mockando get_settings para isolar da config real."""

    def _mock_settings(self, max_mb=10):
        from types import SimpleNamespace

        return SimpleNamespace(attachment_max_size_mb=max_mb)

    @patch("app.core.storage.get_settings")
    def test_valid_file(self, mock_settings):
        mock_settings.return_value = self._mock_settings()
        result = validate_file("doc.pdf", "application/pdf", 1024)
        assert result is None

    @patch("app.core.storage.get_settings")
    def test_oversized_file(self, mock_settings):
        mock_settings.return_value = self._mock_settings(max_mb=5)
        result = validate_file("big.pdf", "application/pdf", 6 * 1024 * 1024)
        assert result is not None
        assert "5MB" in result

    @patch("app.core.storage.get_settings")
    def test_exactly_at_limit(self, mock_settings):
        mock_settings.return_value = self._mock_settings(max_mb=10)
        result = validate_file("ok.pdf", "application/pdf", 10 * 1024 * 1024)
        assert result is None

    @patch("app.core.storage.get_settings")
    def test_invalid_extension(self, mock_settings):
        mock_settings.return_value = self._mock_settings()
        result = validate_file("script.exe", "application/octet-stream", 100)
        assert result is not None
        assert ".exe" in result

    @patch("app.core.storage.get_settings")
    def test_invalid_content_type(self, mock_settings):
        mock_settings.return_value = self._mock_settings()
        result = validate_file("file", "application/octet-stream", 100)
        assert result is not None
        assert "application/octet-stream" in result

    @patch("app.core.storage.get_settings")
    def test_magic_byte_mismatch(self, mock_settings):
        mock_settings.return_value = self._mock_settings()
        # PDF magic bytes but declared as JPEG
        data = b"%PDF-1.4 fake jpeg"
        result = validate_file("photo.jpg", "image/jpeg", len(data), data=data)
        assert result is not None
        assert "tipo declarado" in result

    @patch("app.core.storage.get_settings")
    def test_magic_byte_match(self, mock_settings):
        mock_settings.return_value = self._mock_settings()
        data = b"\xff\xd8\xff\xe0 real jpeg data"
        result = validate_file("photo.jpg", "image/jpeg", len(data), data=data)
        assert result is None

    @patch("app.core.storage.get_settings")
    def test_no_extension_still_checks_content_type(self, mock_settings):
        mock_settings.return_value = self._mock_settings()
        result = validate_file("README", "text/plain", 100)
        assert result is None

    @patch("app.core.storage.get_settings")
    def test_all_allowed_extensions_accepted(self, mock_settings):
        mock_settings.return_value = self._mock_settings()
        # Map extensions to a valid content type
        ext_to_ct = {
            ".jpg": "image/jpeg", ".jpeg": "image/jpeg", ".png": "image/png",
            ".gif": "image/gif", ".webp": "image/webp", ".svg": "image/svg+xml",
            ".pdf": "application/pdf", ".doc": "application/msword",
            ".docx": "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            ".xls": "application/vnd.ms-excel",
            ".xlsx": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            ".csv": "text/csv", ".txt": "text/plain",
            ".zip": "application/zip", ".rar": "application/x-rar-compressed",
            ".7z": "application/x-7z-compressed",
        }
        for ext in ALLOWED_EXTENSIONS:
            ct = ext_to_ct.get(ext, "text/plain")
            result = validate_file(f"file{ext}", ct, 100)
            assert result is None, f"Extension {ext} with content type {ct} was rejected"
