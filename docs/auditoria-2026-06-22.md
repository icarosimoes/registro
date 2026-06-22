# Auditoria de Sistema — 2026-06-22

Auditoria completa cobrindo backend, frontend, infraestrutura e testes.

## Resumo

| Severidade | Qtd | Exemplos principais |
|------------|-----|---------------------|
| **Critical** | 1 | Zero testes na integração Chess |
| **Critical corrigido** | 2 | ~~SQL injection no RLS~~, ~~credenciais hardcoded no admin~~ |
| **High** | 10 | SQL injection no import, missing soft-delete, N+1, sem CSP, sem CSRF |
| **High corrigido** | 2 | ~~access logs desabilitados~~, ~~rate limiter quebrado atrás de proxy~~ |
| **Medium** | 18 | Race condition em attachments, sem Sentry, Redis sem persistência, frontend sem testes |
| **Low** | 6 | Request ID, cookie secure em dev, i18n, image optimization |

---

## Critical

### ~~C1. SQL Injection no RLS Context~~ ✅ Corrigido 2026-06-22

**Arquivo:** `api/app/core/auth.py:31`

Corrigido: substituída f-string por query parametrizada `text("SET LOCAL ... = :cid"), {"cid": str(cid)}`.

### ~~C2. Credenciais Hardcoded no Admin Login~~ ✅ Corrigido 2026-06-22

**Arquivo:** `admin/app/(auth)/login/page.tsx`

Corrigido: removidos `defaultValue` com email e senha. Substituídos por `placeholder` genérico.

### C3. Zero Testes na Integração Chess Hotel

4 endpoints (`/integrations/chess-hotel/*`) sem nenhum teste:
- Autenticação por `X-Registro-Key`
- Resolução de usuário por email
- Criação de tickets com SLA
- Tracking por protocolo

---

## High

### H1. SQL Injection no Import V1

**Arquivo:** `api/app/import_v1.py:46`

```python
result = await session.execute(text(f"SELECT {columns} FROM {q}{table}{q}"))  # noqa: S608
```

Comentário `# noqa` indica consciência, mas ainda perigoso. Usar ORM reflection ou mapa de tabelas validado.

### H2. Missing Soft-Delete Filter em Lookups

**Arquivo:** `api/app/domain/users/service.py`

`_role_name()` busca Role por ID sem filtrar `company_id`. Risco de vazamento cross-tenant em lookups de nome usados para display. Mesmo padrão em lookups de setor.

### H3. N+1 Query em Notificações WhatsApp

**Arquivo:** `api/app/integrations/notifications.py:246`

```python
phone = await session.scalar(select(User.phone).where(User.id == r["id"]))
```

Chamado em loop para cada destinatário. Pre-fetch todos os telefones em query única.

### H4. Error Handling Inconsistente nos Routers

Services lançam `ValueError` que é capturado em alguns routers mas não em outros, gerando 500 ao invés de 422.

**Bom:** `stock/router.py` — try/except ValueError → HTTPException(422)
**Faltando:** work_orders, attachments, e outros domínios.

### H5. Sem Content Security Policy (CSP)

**Arquivos:** `web/next.config.ts`, `admin/next.config.ts`

Nenhum header de segurança configurado: CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy. Habilita ataques XSS mesmo com input sanitizado.

### H6. Sem CSRF Protection

Server Actions em ambas as apps aceitam requests sem token CSRF. Cookies usam `sameSite: "lax"` que é insuficiente para POST/PATCH/DELETE.

### H7. Sem Middleware de Proteção de Rotas

Proteção de rota feita em cada page individualmente via `currentTenantUser()` → `redirect()`. Página renderiza brevemente antes do redirect.

### H8. Admin Sem Token Refresh

**Arquivo:** `admin/lib/api.ts`

`platformFetch()` não implementa refresh no 401. Usuários admin perdem sessão sem retry.

### ~~H9. Access Logs Desabilitados em Produção~~ ✅ Corrigido 2026-06-22

**Arquivo:** `api/Dockerfile:35`

Corrigido: trocado `--no-access-log` por `--access-log`.

### ~~H10. Rate Limiter Quebrado Atrás de Proxy~~ ✅ Corrigido 2026-06-22

**Arquivo:** `api/app/core/rate_limit.py`

Corrigido: função `_get_client_ip` lê `X-Forwarded-For` + `ProxyHeadersMiddleware` adicionado em `main.py`.

### H11. Deploy Automático Sem Approval Gate

**Arquivo:** `.github/workflows/publish.yml`

Push no main faz deploy direto sem revisão humana. `StrictHostKeyChecking=no` no SSH desabilita verificação de host.

### H12. Frontend Zero Testes

Web e Admin não têm nenhum arquivo de teste. Nenhuma configuração de jest/vitest detectada.

---

## Medium

### M1. Race Condition em Attachments

**Arquivo:** `api/app/domain/attachments/service.py`

Validação de `max_per_entity` via COUNT pode ser burlada com uploads simultâneos. Usar constraint de banco.

### M2. Filename Sanitization Aceita Unicode

**Arquivo:** `api/app/domain/attachments/router.py:21-27`

Regex `[^\w\s\-\.\(\)]` com `re.UNICODE` permite caracteres unicode. Restringir a ASCII alphanumeric.

### M3. Broad Exception Handling na Integração Asaas

**Arquivo:** `api/app/domain/platform/service.py:569`

`except Exception` genérico mascara erros reais (network, auth, crashes). Capturar exceções específicas.

### M4. Paginação Mistura Dois Padrões

Endpoints misturam offset (`{items, total, page, page_size}`) e cursor (`{items, next_cursor, has_more}`) sem consistência documentada.

### M5. Request ID Não Gerado

**Arquivo:** `api/app/main.py:56`

Se header `X-Request-ID` ausente, fica string vazia. Gerar UUID quando não fornecido.

### M6. `dangerouslySetInnerHTML` no Service Worker

**Arquivo:** `web/app/layout.tsx:33-43`

Script de registro do SW via `dangerouslySetInnerHTML`. Mover para arquivo `.js` em `/public`.

### M7. Sem Refresh Token Rotation

Refresh tokens nunca são rotacionados. Token comprometido gera access tokens indefinidamente até expirar em 7 dias.

### M8. Auth Logic Duplicada

`tryRefresh()` duplicado entre `web/app/actions.ts` e `web/lib/api.ts`. Extrair para `lib/auth.ts`.

### M9. Sem Validação de Tipo nas Respostas da API

`response.json()` sem validação de shape em todo o frontend. Usar Zod para runtime validation.

### M10. Uso Excessivo de `"use client"`

Quase todos os componentes são client-side, reduzindo benefícios de Server Components e aumentando bundle size.

### M11. Sem Validação de Arquivo no Client

**Arquivo:** `web/components/fiscal-request-form.tsx`

Upload sem verificação de tipo/tamanho antes de enviar ao servidor.

### M12. Acessibilidade

Dropdowns sem navegação por teclado, botões sem `aria-label`, autocomplete sem `role` e `aria-haspopup`.

### M13. Sem Error Tracking Centralizado

Nenhum Sentry/DataDog configurado. Erros 500 em produção se perdem se o container crashar.

### M14. Redis Sem Persistência

Sem AOF ou RDB configurado. Perde rate limits e cache no restart.

### M15. PostgreSQL Single Replica

`docker-stack.yml` linha 20: `replicas: 1`. Sem failover se o node morrer.

### M16. Sem Log Rotation

Docker Compose/Stack sem `logging` driver configurado. Logs crescem indefinidamente.

### M17. Backup Sem Restore Testado

Backups são criados (diário, 14d retenção) mas sem procedimento de restore testado periodicamente.

### M18. MinIO Single Node

Storage em volume único sem replicação. Mirror diário não substitui redundância real.

---

## Low

### L1. Cookie `secure` Só em Produção

`secure: process.env.NODE_ENV === "production"` — em staging sem HTTPS, cookies interceptáveis.

### L2. Permission Wildcard Sem Detalhe

Wildcard `*` no role admin funciona mas não gera log de qual permissão específica foi usada.

### L3. Sem i18n

Texto em português hardcoded em todo o frontend. Não escalável para multi-idioma.

### L4. Image Optimization

Sem configuração de otimização de imagem no Next.js (webp, avif).

### L5. Schema Drift em Alembic

`alembic/env.py` filtra mudanças de índice e FK constraints. Drift nessas áreas não é detectado.

### L6. Error Response Inconsistente

Alguns endpoints retornam `{"code": "not_found"}`, outros `{"code": "not_found", "message": "..."}`.

---

## Cobertura de Testes

| Métrica | Valor |
|---------|-------|
| Arquivos de teste | 23 |
| Linhas de teste | ~2.456 |
| Domínios COM testes | 12 de 30 (40%) |
| Domínios SEM testes | occurrences, users, dashboard, notifications, platform, settings, roles, meetings, +10 |
| Frontend | 0 testes |
| Integração vs Unit | 90% / 10% |

### Domínios sem testes dedicados
apartment_inspections, check_suites, dashboard, inspection_suites, meetings, modules, notifications, occurrences, platform, procedures, registries, roles, settings, shift_reports, timeline, users, work_diaries

### Pontos fortes
- Isolamento cross-tenant: 60+ testes
- JWT/bcrypt/permissions: boa cobertura
- SLA: business days, timezone, pause/resume
- Dual-DB: SQLite para dev, PostgreSQL para CI

---

## Pontos Fortes do Sistema

- **Arquitetura limpa** — service layer, routers finos, audit trail com diff JSON
- **Tenant isolation** — `company_id` + RLS em 24+ tabelas
- **Auth robusto** — JWT access+refresh, bcrypt, rate limiting
- **Infra moderna** — Docker Swarm, rollback config, backups diários, Docker Secrets
- **CI completo** — ruff, mypy, pip-audit, alembic check, pytest 60% mínimo
- **Async end-to-end** — FastAPI + asyncpg + Redis async
- **Structured logging** — structlog com JSON em prod, contextvars por request
