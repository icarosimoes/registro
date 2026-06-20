import re


def normalize_doc(value: str) -> str:
    return re.sub(r"\D", "", value)


def validate_cpf(digits: str) -> bool:
    if len(digits) != 11 or digits == digits[0] * 11:
        return False
    for i in (9, 10):
        total = sum(int(digits[j]) * ((i + 1) - j) for j in range(i))
        digit = (total * 10 % 11) % 10
        if digit != int(digits[i]):
            return False
    return True


def validate_cnpj(digits: str) -> bool:
    if len(digits) != 14 or digits == digits[0] * 14:
        return False
    for i, weights in ((12, (5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2)),
                       (13, (6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2))):
        total = sum(int(digits[j]) * weights[j] for j in range(i))
        digit = 11 - (total % 11)
        if digit >= 10:
            digit = 0
        if digit != int(digits[i]):
            return False
    return True


def validate_cpf_cnpj(value: str) -> str:
    digits = normalize_doc(value)
    if len(digits) == 11:
        if not validate_cpf(digits):
            raise ValueError("CPF invalido")
        return f"{digits[:3]}.{digits[3:6]}.{digits[6:9]}-{digits[9:]}"
    if len(digits) == 14:
        if not validate_cnpj(digits):
            raise ValueError("CNPJ invalido")
        return f"{digits[:2]}.{digits[2:5]}.{digits[5:8]}/{digits[8:12]}-{digits[12:]}"
    raise ValueError("CPF deve ter 11 digitos e CNPJ 14 digitos")


def validate_email_basic(value: str) -> str:
    value = value.strip().lower()
    if not re.match(r"^[^@\s]+@[^@\s]+\.[^@\s]+$", value):
        raise ValueError("E-mail invalido")
    return value
