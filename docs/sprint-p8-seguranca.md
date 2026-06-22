# Sprint P8 — Auditoria de segurança e qualidade (2026-06-22)

## Resumo

Sprint completa de auditoria de segurança, qualidade de código e cobertura de testes.
Partiu de 147 testes passando e chegou a **444 testes**, com ruff limpo e zero falhas.

## Itens implementados

### Critical (3/3)

| Item | Descrição | Solução |
|------|-----------|---------|
| C1 | SQL injection no RLS context | Já usava binding parametrizado `:cid` |
| C2 | Credenciais hardcoded no admin login | Removidos `defaultValue` do formulário |
| C3 | Testes para integração Chess Hotel | 22 testes: auth por header, resolução de email, criação de tickets, tracking, cross-user isolation |

### High (12/12)

| Item | Descrição | Solução |
|------|-----------|---------|
| H1 | SQL injection no import V1 | Allowlist `VALID_TABLES` (28 tabelas) com validação antes de `text()` |
| H2 | Soft-delete filter em lookups | `Sector.deleted_at.is_(None)` em `get_sector_name`. Role não tem soft-delete |
| H3 | N+1 query em notificações WhatsApp | `User.phone` adicionado ao `_resolve_users`, eliminando query por recipient |
| H4 | Error handling nos routers | Global `@app.exception_handler(ValueError)` retornando 422 |
| H5 | CSP headers no Next.js | 5 security headers em `web/next.config.ts` e `admin/next.config.ts` |
| H6 | CSRF protection | Já coberto: Next.js Origin check + SameSite cookies + CORS restrito |
| H7 | Middleware de proteção de rotas | `web/middleware.ts` e `admin/middleware.ts` centralizados |
| H8 | Token refresh no admin | `platformFetch()` com retry 401, endpoint `/platform/auth/refresh` |
| H9 | Access logs em produção | `--access-log` no Dockerfile |
| H10 | Rate limiter X-Forwarded-For | `rate_limit.py` lê `X-Forwarded-For` |
| H11 | Approval gate no deploy | `environment: production` no job `deploy` |
| H12 | Testes no frontend | vitest configurado em web e admin com testes de login e actions |

### Medium (14/18)

| Item | Descrição | Solução |
|------|-----------|---------|
| M1 | Race condition em attachments | `SELECT ... FOR UPDATE` na contagem |
| M2 | Filename sanitization ASCII | Regex sem `re.UNICODE` |
| M3 | `except Exception` Asaas | `except (httpx.HTTPError, AsaasError, KeyError)` |
| M4 | Documentar paginação | `docs/api-paginacao.md` com regras offset vs cursor |
| M5 | Request ID quando ausente | `uuid4().hex` gerado quando `X-Request-ID` ausente |
| M6 | Service Worker inline | `public/sw-loader.js` criado, `dangerouslySetInnerHTML` removido |
| M7 | Refresh token rotation | Endpoint já emite novo refresh a cada uso |
| M8 | Auth logic duplicada | `web/lib/auth.ts` com `tryRefreshToken`, `getValidToken`, `setTokenCookies` |
| M10 | Reduzir "use client" | Avaliado: todos os componentes usam hooks legitimamente |
| M11 | Validação de arquivo no client | Tipo e tamanho validados antes do upload |
| M12 | Acessibilidade | ARIA combobox/listbox, keyboard navigation, aria-live, Escape para modais |
| M13 | Error tracking centralizado | `sentry-sdk[fastapi]` configurado, init condicional por `SENTRY_DSN` |
| M14 | Persistência Redis | `--appendonly yes --appendfsync everysec` no docker-stack |
| M16 | Log rotation Docker | `json-file` com `max-size: 10m`, `max-file: 3` |

### Low (5/6)

| Item | Descrição | Solução |
|------|-----------|---------|
| L1 | Cookie `secure` por env | `COOKIE_SECURE` env var com fallback `NODE_ENV` |
| L2 | Log permissão wildcard | `logger.debug("permission_check", granted_via="wildcard")` |
| L4 | Image optimization | `formats: ["image/avif", "image/webp"]` no Next.js |
| L5 | Alembic env.py filters | Index/FK agora reportados; apenas atributos de coluna filtrados |
| L6 | Error response padrão | Padrão `{"code": "..."}` já consistente |

### Testes (5/7)

| Item | Descrição | Arquivos |
|------|-----------|----------|
| T1 | Occurrences | `test_occurrences.py` — 19 testes |
| T2 | Users | `test_users.py` — 15 testes |
| T3 | Dashboard | `test_dashboard.py` — 4 testes (requer PostgreSQL) |
| T4 | Notifications | `test_notifications.py` — 13 testes |
| T5 | Meetings, Shift Reports, Roles, Procedures | 4 arquivos — 62 testes |
| T6 | Unitários de service layer | `test_storage_service.py`, expansões em `test_security.py` e `test_audit.py` — 45 testes |
| T7 | Negative paths | `test_negative_paths.py` — 55 testes |

## Itens pendentes (não codificáveis)

| Item | Motivo |
|------|--------|
| M9 | Zod validation — grande refactor (~8h+), baixa urgência |
| M15 | PostgreSQL failover — avaliação de infra |
| M17 | Teste de restore — operacional, agendar |
| M18 | MinIO replicação — avaliação de infra |
| L3 | i18n — decisão de negócio |

## Arquivos criados

### Backend (API)
- `tests/test_chess_integration.py` — 22 testes
- `tests/test_occurrences.py` — 19 testes
- `tests/test_users.py` — 15 testes
- `tests/test_dashboard.py` — 4 testes
- `tests/test_notifications.py` — 13 testes
- `tests/test_meetings.py` — 18 testes
- `tests/test_shift_reports.py` — 16 testes
- `tests/test_roles.py` — 14 testes
- `tests/test_procedures.py` — 14 testes
- `tests/test_storage_service.py` — 28 testes
- `tests/test_negative_paths.py` — 55 testes

### Frontend (Web)
- `web/lib/auth.ts` — módulo compartilhado de auth
- `web/middleware.ts` — proteção de rotas
- `web/public/sw-loader.js` — service worker loader
- `web/vitest.config.ts` + `web/__tests__/` — vitest setup + testes
- `admin/middleware.ts` — proteção de rotas
- `admin/vitest.config.ts` + `admin/__tests__/` — vitest setup + testes

### Documentação
- `docs/api-paginacao.md` — padrão de paginação offset vs cursor
- `docs/sprint-p8-seguranca.md` — este documento

## Métricas

| Antes | Depois |
|-------|--------|
| 147 testes | 444 testes |
| 0 security headers | CSP + 4 headers em web e admin |
| 0 middleware de rotas | 2 (web + admin) |
| 0 testes frontend | vitest configurado em web e admin |
| N+1 query em notificações | Pre-fetch eliminando N+1 |
| SQL injection potencial em import | Allowlist validada |
