# Jarvis — Multiempresa

O Registro não é uma plataforma de assinatura neste momento, mas o schema possui empresas e exige isolamento equivalente a tenant.

## Agora — MySQL

- Toda entidade de negócio é analisada para `company_id`.
- Repository recebe a empresa autenticada e filtra explicitamente.
- Operação cross-company é administrativa, explícita e auditável.
- ACL não substitui isolamento por empresa.

## Futuro — PostgreSQL

- Models novos usarão um padrão comum de tenant.
- Filtro ORM/aplicação permanece obrigatório.
- RLS será a terceira camada, ativada somente após saneamento.
- Bypass de RLS será restrito a rotinas administrativas nomeadas e testadas.

Não copiar planos, billing, marketplace ou usuários de plataforma do Aloji sem decisão de produto específica para o Registro.
