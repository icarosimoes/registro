# Jarvis — SaaS e multiempresa

O Registro é preparado para comercialização como SaaS. Empresa cliente, usuário da empresa e operador da plataforma são contextos distintos e nunca compartilham uma sessão implicitamente.

## Agora — MySQL

- Toda entidade de negócio é analisada para `company_id`.
- Repository recebe a empresa autenticada e filtra explicitamente.
- Operação cross-company é administrativa, explícita e auditável.
- ACL não substitui isolamento por empresa.
- JWT tenant usa `type=access`; JWT administrativo usa `type=platform_access`.
- Usuário da plataforma vive em `platform_users`, fora de `users`.
- Preços são armazenados em centavos; estado de assinatura não é inferido da interface.
- O painel administrativo usa cookie `httpOnly` criado no servidor Next.js.

## Núcleo da plataforma

- `companies`: tenants.
- `plans`: catálogo comercial versionável.
- `subscriptions`: plano e estado comercial do tenant.
- `invoices`: espelho local das cobranças externas.
- `platform_users`: operadores internos.
- `platform_audit_logs`: ações cross-tenant e administrativas.

Estados iniciais de assinatura: `trial`, `active`, `past_due`, `paused` e `canceled`. Bloqueio de acesso por inadimplência só será ativado com regra de negócio documentada, período de tolerância e suporte operacional.

## Futuro — PostgreSQL

- Models novos usarão um padrão comum de tenant.
- Filtro ORM/aplicação permanece obrigatório.
- RLS será a terceira camada, ativada somente após saneamento.
- Bypass de RLS será restrito a rotinas administrativas nomeadas e testadas.

## Critérios obrigatórios

- Teste negativo prova que token de uma empresa não lê dados de outra.
- Endpoint administrativo revalida operador ativo e registra mutações em auditoria.
- Migração e seed são repetíveis; seed de demonstração nunca usa senha padrão em produção.
- Integração de cobrança é adaptador externo: o domínio não depende do SDK do provedor.
