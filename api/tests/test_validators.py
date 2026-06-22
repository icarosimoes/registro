import pytest

from app.core.validators import (
    normalize_doc,
    validate_cnpj,
    validate_cpf,
    validate_cpf_cnpj,
    validate_email_basic,
)


class TestNormalizeDoc:
    def test_strips_non_digits(self):
        assert normalize_doc("123.456.789-09") == "12345678909"

    def test_already_digits(self):
        assert normalize_doc("12345678909") == "12345678909"

    def test_empty(self):
        assert normalize_doc("") == ""


class TestValidateCpf:
    def test_valid_cpf(self):
        assert validate_cpf("52998224725") is True

    def test_invalid_cpf(self):
        assert validate_cpf("52998224720") is False

    def test_all_same_digits(self):
        assert validate_cpf("11111111111") is False

    def test_wrong_length(self):
        assert validate_cpf("123") is False


class TestValidateCnpj:
    def test_valid_cnpj(self):
        assert validate_cnpj("11222333000181") is True

    def test_invalid_cnpj(self):
        assert validate_cnpj("11222333000100") is False

    def test_all_same_digits(self):
        assert validate_cnpj("11111111111111") is False

    def test_wrong_length(self):
        assert validate_cnpj("123") is False


class TestValidateCpfCnpj:
    def test_valid_cpf_formats(self):
        result = validate_cpf_cnpj("529.982.247-25")
        assert result == "529.982.247-25"

    def test_valid_cnpj_formats(self):
        result = validate_cpf_cnpj("11.222.333/0001-81")
        assert result == "11.222.333/0001-81"

    def test_invalid_cpf_raises(self):
        with pytest.raises(ValueError, match="CPF invalido"):
            validate_cpf_cnpj("12345678900")

    def test_invalid_cnpj_raises(self):
        with pytest.raises(ValueError, match="CNPJ invalido"):
            validate_cpf_cnpj("11222333000100")

    def test_wrong_length_raises(self):
        with pytest.raises(ValueError, match="CPF deve ter 11"):
            validate_cpf_cnpj("12345")


class TestValidateEmailBasic:
    def test_valid_email(self):
        assert validate_email_basic("user@example.com") == "user@example.com"

    def test_strips_and_lowercases(self):
        assert validate_email_basic("  User@Example.COM  ") == "user@example.com"

    def test_invalid_email_raises(self):
        with pytest.raises(ValueError, match="E-mail invalido"):
            validate_email_basic("not-an-email")

    def test_missing_tld_raises(self):
        with pytest.raises(ValueError, match="E-mail invalido"):
            validate_email_basic("user@localhost")
