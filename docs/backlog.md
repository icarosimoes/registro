# Backlog da modernização

## P0 — consolidar SaaS e receber dados reais

- [x] Criar MySQL local, migrations e seed fictício com dois tenants.
- [x] Separar autenticação tenant e plataforma.
- [x] Criar painel administrativo inicial.
- [x] Testar bloqueio de token com `company_id` divergente.
- [x] Receber dump de desenvolvimento e restaurar/importar em staging temporária.
- [x] Inventariar as 66 tabelas do dump V1 e importar o núcleo normalizado.
- [x] Importar todos os dados restantes do dump V1: reuniões (meetings + subjects + participants), relatórios de turno (shift_reports + filhas), comentários e participantes de ocorrências, conferências de suítes (check_suites + items), auditorias noturnas (audit_reports + itens), arquivos de procedimentos e notificações legadas.
- [x] Validar login compatível com hashes bcrypt Laravel.
- [x] Implementar página `/login` do produto tenant com cookie `httpOnly`.
- [x] Corrigir o login multitenant para validar a senha antes de revelar a lista de empresas.
- [x] Adicionar testes de login para e-mail único, senha inválida, e-mail em múltiplos tenants e seleção explícita de tenant.
- [x] Validar `company_id` positivo no contrato de login e documentar integralmente a resposta `422 multi_tenant`.
- [x] Criar migration de upgrade que renomeie com segurança um eventual tenant antigo `aero-v1` para `aero-hotel`, sem criar tenant duplicado.
- [x] Criar CI com Ruff, pytest e typecheck (GitHub Actions); mypy excluído até estabilizar.
- [x] Concluir inventário de índices, constraints e órfãos — migration `0017_schema_audit` corrige índices compostos, remove redundantes e adiciona `ondelete` em 10 FKs.
- [x] Testes cross-tenant: suite `test_cross_tenant.py` com validação de tokens por tenant, rejeição de token expirado e sem token em todos os endpoints autenticados.
- [x] Separar service layer dos routers — cada domínio possui `service.py` com lógica de negócio extraída; routers delegam para services.
- [x] Rate limiting com slowapi nos endpoints sensíveis: login (10/min), refresh (20/min), integração Chess (30/min).
- [x] Refresh token JWT (type=refresh, 7 dias) com endpoint `POST /auth/refresh` e auto-refresh transparente no frontend via cookie httpOnly.
- [x] Estabilizar mypy na imagem de desenvolvimento — causa era `PermissionError` no `.mypy_cache`; resolvido com `MYPY_CACHE_DIR=/tmp/.mypy_cache` no Dockerfile.

## P2 — identidade, ACL e cadastros

- [x] Usuários: CRUD via API com listagem, criação (bcrypt), atualização, soft delete e paginação server-side.
- [x] Setores, locais e funções: CRUD unificado via `/registries` com paginação e busca.
- [x] Procedimentos: CRUD via API (`/procedures`) com listagem paginada, busca, criação, atualização e soft delete.
- [x] Endpoint `PATCH /users/me` para edição de perfil pelo próprio usuário (nome, telefone, senha).
- [x] Anexos de procedimentos (upload, metadados e download) — página `/procedimentos` no frontend com upload via MinIO, listagem e download; eventos de auditoria `attachment_add`/`attachment_remove` registrados na timeline.
- [x] Timeline de alterações nos registros operacionais (front local, todas as telas).
- [x] Tabela de auditoria na API (`audit_events`) para persistir o histórico de alterações com `user_id`, `company_id` e diff JSON.
- [x] Padronizar design tokens no `globals.css` (espaçamento, cores, raios, sombras, tipografia, transições).
- [x] Unificar `DashboardShell` e `OperationalModule` em um `AppLayout` compartilhado, eliminando sidebar/topbar/navegação duplicados.
- [x] Componentes reutilizáveis de lista, formulário, estado vazio e confirmação — permissões ACL integradas ao `OperationalModule` (canView/canCreate/canEdit/canDelete); botões condicionados por `user.permissions`.
- [x] Persistir comentários e alterações da tratativa na API (`GET/POST /timeline/{entity_type}/{entity_id}`); frontend consome timeline da API para módulos conectados.
- [x] Tornar a auditoria imutável, com ator, tenant, data UTC, tipo de evento e diferenças estruturadas — `AuditEvent` já é imutável por design (sem `updated_at`/`deleted_at`, `created_at` com `server_default=func.now()`).
- [x] Registrar na timeline todos os campos específicos do domínio, inclusive os campos fiscais e anexos — timeline agora renderiza eventos `attachment_add`/`attachment_remove` com nome do arquivo; `procedure` adicionado como entity type válido; campos fiscais já eram capturados via `compute_diff` no payload.
- [x] Impedir a criação de eventos de alteração vazios: `record_event` retorna sem inserir quando `event_type == "update"` e `diff` é vazio.

## P3 — operação

- [x] Remover o corte nos primeiros 100 registros, carregando todas as páginas disponíveis da API de ocorrências.
- [x] Evoluir ocorrências para paginação e busca server-side sob demanda, com navegação via query params na URL.
- [x] Remover a precedência de dados antigos do `localStorage` sobre ocorrências retornadas pela API.
- [x] Implementar mutações de ocorrências na API com autorização e isolamento por empresa.
- [x] Dashboard com métricas reais agregadas do banco (ocorrências, fiscais, equipe, atividades recentes).
- [x] Todos os módulos operacionais conectados à API com CRUD completo e paginação server-side.
- [x] Tabela genérica `module_records` para módulos sem tabela própria (reuniões, inspeções, turnos, obra, manutenção, mural).
- [x] Ocorrências: participantes (tabela junction `occurrence_participants`), clone (`POST /occurrences/{id}/clone`), PDF (`GET /occurrences/{id}/pdf` via reportlab). Comentários e anexos já existiam via timeline/attachments.
- [x] Reuniões: promovidas para tabela dedicada `meetings` + `meeting_participants` (com papel: organizer/attendee/optional) + `meeting_subjects` (pautas com resolved). Migration de dados de `module_records`. CRUD completo + clone + subjects CRUD + ata PDF (`GET /meetings/{id}/pdf`).
- [x] Relatórios de turno: promovidos para tabela dedicada `shift_reports` com `shift_date`, `shift_type` (morning/afternoon/night), `status`. Migration de dados de `module_records`. CRUD completo com filtro por data.
- [x] Sistema ACL: `require_permission()` em todos os routers, seed de 35 permissões, role "Administrador" com wildcard `*`. CRUD de roles via `/roles`. Frontend condiciona ações por `user.permissions`.

## P3B — solicitações fiscais

- [x] Criar protótipo funcional no frontend com tipos de solicitação, campos condicionais, SLA, anexos e tratativa.
- [x] Criar modelo, migration, schemas, service, autorização e endpoints FastAPI para solicitações fiscais.
- [x] Integrar Chess Hotel via `POST /integrations/chess-hotel/tickets` com autenticação por header, resolução de usuário e SLA inicial.
- [x] Vincular solicitações fiscais a usuários do Registro (`requester_user_id`, `responsible_user_id`) e adicionar tracking/histórico para o Chess.
- [x] Validar e normalizar CPF/CNPJ e e-mail do tomador nos endpoints de solicitações fiscais.
- [x] Adicionar paginação server-side ao endpoint `GET /fiscal-requests` com busca por protocolo, solicitante e tipo.
- [x] Definir SLA no servidor: `sla_deadline` definido server-side (24h) ao criar solicitações fiscais do Registro e Chess; `sla_status` computado no servidor (on_time/warning/overdue/completed).
- [x] Evoluir SLA com timezone explícito, calendário útil, pausa e política de vencimento.
- [x] Substituir anexos Base64 no `localStorage` por armazenamento via MinIO (S3-compatible), com metadados no banco (`attachments`).
- [x] Validar tamanho (10MB), quantidade (20/registro), extensão e content-type dos anexos.
- [x] Restringir previews e downloads — CSP `default-src 'none'`, `nosniff`, `X-Frame-Options: DENY`, sanitização de filename no endpoint de download.
- [x] Notificações in-app: `create_notification()` dispara para responsáveis e notificados em todo `notify_record_event`; Chess Hotel notifica todos os usuários ativos ao criar solicitação fiscal.
- [x] Implementar preferências de notificação, destinatários por módulo e registro de entrega.
- [x] Backend de notificações in-app: model `Notification`, migration, endpoints de listagem paginada, marcar como lida e marcar todas como lidas.
- [x] Cobrir CRUD, SLA e isolamento cross-tenant com testes (52 testes; anexos e auditoria pendentes).

## P1 — comercial e cobrança

- [x] CRUD auditado de tenants, planos e assinaturas — endpoints platform com POST/GET/PATCH/DELETE para tenants, plans e subscriptions, todos auditados via `PlatformAuditLog`.
- [x] Definir trial, tolerância, suspensão e reativação — trial 14 dias, expiração para past_due, suspensão após 7 dias de tolerância (Company.status="suspended" bloqueia login), reativação via endpoint admin.
- [x] Configurar Asaas sandbox e segredos — `AsaasClient` com httpx async, config com `asaas_api_key`/`asaas_api_url`/`asaas_webhook_token` e variantes `_file` para produção.
- [x] Implementar webhook autenticado, idempotente e com replay — `POST /integrations/asaas/webhook` com dedup via tabela `webhook_events` (provider + external_id unique), header token auth, rate limit 60/min.
- [x] Implementar reconciliação periódica de cobranças — `POST /platform/billing/reconcile` compara status local vs Asaas API, loga discrepâncias, auto_correct opcional.

## P4 — inspeções e obra

- [x] Check suites e inspection suites — CRUD completo com items inline, auditoria, soft delete, tenant isolation.
- [x] Vistorias V2 e migração controlada da V1 — `apartment_inspections` + `apartment_inspection_items` com CRUD e migration de dados de `module_records`.
- [x] Auditorias e relatórios — `audit_reports` + `audit_report_items` com CRUD completo.
- [x] Diário de obra — `work_diaries` + 4 tabelas filhas (activities, teams, equipment, observations) com CRUD completo.

## P5 — corte e banco

- [x] Migrar infra de MySQL para PostgreSQL — Docker Compose com postgres:17-alpine, asyncpg como driver, MySQL mantido com profile `mysql-import` para dump V1.
- [x] Corrigir código MySQL-specific — `LAST_INSERT_ID()` → `RETURNING id`, `NOW()` → `CURRENT_TIMESTAMP`, boolean defaults `"1"`/`"0"` → `"true"`/`"false"`, backticks → double quotes.
- [x] Implementar RLS (Row-Level Security) — policies `tenant_isolation` em 24 tabelas com `company_id`, GUC `app.current_company_id` setado via `SET LOCAL` na dependency `current_user`.
- [x] Re-migrar dados de `module_records` para tabelas dedicadas — migration `0030` move reuniões (72) → `meetings` e relatórios de turno (1165) → `shift_reports`; inspeções (4497) e manutenção (104) permanecem em `module_records` (frontend usa `/modules/{slug}`).
- [x] Criar demo user para Aero Hotel — `demo@aerohotel.local` / `Registro@123` com role `legacy-admin` + permissão wildcard `*`.
- [x] Corrigir permissões do role `legacy-admin` — adicionada permissão `*` para que os endpoints do Registro funcionem (V1 usava códigos `legacy.controller.action`, API nova usa `module.action`).
- [x] Atualizar `import_v1.py` para escrever diretamente nas tabelas dedicadas em futuras importações — `import_meetings` grava em `meetings`/`meeting_participants`/`meeting_subjects` e `import_shift_reports` grava em `shift_reports`.
- [ ] Executar corte final — puxar dump MySQL atualizado do V1, importar via `import_v1.py`, rodar migrations (incluindo 0030), validar dados.

## P6 — documentação e governança

- [x] Definir `/docs` como fonte de verdade técnica, funcional, operacional e histórica do Registro.
- [x] Registrar que toda informação pertinente ao desenvolvimento e ao sistema deve ser mantida em `/docs`.
- [x] Corrigir referências atuais que instruíam login por slug; o histórico cronológico preserva menções ao contrato antigo.
- [x] Documentar o módulo de solicitações fiscais em `web-rotas-ui.md`, `domain-model.md` e `api-reference.md` conforme o backend for implementado.
- [x] Documentar o módulo de ocorrências (CRUD, soft delete, `legacy_id` nullable) em `api-reference.md` e `domain-model.md`.
- [x] Atualizar `mapa.md`, contratos, runbooks, memória, backlog e registro de trabalho — atualização de 21/06/2026 corrigiu PostgreSQL como banco ativo, domínios P1/P4 implementados, bloqueios atuais revisados.
- [x] Criar ADR quando uma decisão alterar stack, isolamento, persistência, segurança, deploy, cobrança ou estratégia de migração — ADR-001 (MySQL→PostgreSQL) e ADR-002 (RLS multi-tenant) criados em `docs/adr/`.
- [x] Manter documentação de estado atual separada de funcionalidades apenas planejadas — `docs/mapa.md` separado em seções "Implementado e operacional", "Planejado/pendente de produção" e "Limitações conhecidas".

## Correções de repositório

- [x] Restaurar `.idea/` e `.vscode/` no `.gitignore`.
- [x] Manter `docs/v1/`, dumps SQL, credenciais, secrets e arquivos locais fora do Git — `.gitignore` cobre `docs/v1/`, `*.sql`, `*.sql.gz`, `.env*`, `secrets/`, `backups/`.
- [x] Reforçar `.dockerignore` de api, web e admin — adicionados `docs/`, `*.sql`, `*.dump`, `secrets/`, `.mypy_cache`, `.coverage`, `tests/` para não entrarem nas imagens Docker.

## Próximos passos pendentes (prioridade)

### Alta — bloqueiam corte

1. ~~**Atualizar `import_v1.py`**~~ — ✅ reescrito para gravar diretamente em `meetings`, `meeting_participants`, `meeting_subjects` e `shift_reports`.
2. **Dump V1 atualizado** — puxar dump MySQL fresco do servidor V1 em produção. O dump local (`aero-2026-06-19.sql`) é snapshot de desenvolvimento.
3. **Inventário de anexos físicos** — mapear arquivos/volumes fora do banco na V1 (uploads, PDFs, imagens) para migração ao MinIO.
4. ~~**Testes de cobertura**~~ — ✅ expandido: 70 testes cobrindo SLA, CRUD, cross-tenant, anexos (9 testes) e auditoria (9 testes).

### Média — valor operacional

5. ~~**Integração Evolution (WhatsApp)**~~ — ✅ implementado: `app/integrations/evolution.py` com `send_text`, `send_media`, `check_connection`; endpoints `GET /settings/evolution/status` e `POST /settings/evolution/test`; envio automático via `notify_record_event` para usuários com telefone cadastrado.
6. ~~**Separar estado atual de planejado na documentação**~~ — ✅ `docs/mapa.md` reestruturado com seções explícitas.
7. ~~**Promover módulos genéricos remanescentes**~~ — ✅ manutenção promovida para `maintenance_records` (com priority, location_id) e mural promovido para `bulletin_posts` (com pinned, expires_at, author). Migration de dados inclusa. Endpoints dedicados `/maintenance` e `/bulletin`.
8. ~~**Ata PDF de reuniões**~~ — ✅ endpoint `GET /meetings/{id}/pdf` com participantes, pautas e timeline (reportlab). Padrão idêntico ao PDF de ocorrências.
9. ~~**Higiene dos .dockerignore**~~ — ✅ adicionados `docs/`, `*.sql`, `*.dump`, `secrets/`, `.mypy_cache`, `.coverage` e `tests/` aos .dockerignore de api, web e admin.
10. ~~**Testes dos novos módulos**~~ — ✅ 7 arquivos de teste: work_orders, stock, handoffs, checklists, preventive_plans, maintenance e bulletin. CRUD + isolamento cross-tenant.

### Baixa — preparação futura

11. **Corte do Laravel** — procedimento documentado em `docs/migracao-postgresql.md`. Depende dos itens 2-3 acima.
12. **Remover profile `mysql-import`** — após corte final em produção, eliminar MySQL do Docker Compose e dependência `asyncmy`.

## P6 — evolução operacional

### Alta — valor operacional imediato

1. ~~**Ordens de Serviço (OS) com workflow**~~ — ✅ modelo `work_orders` com fluxo de estados (aberta → em andamento → aguardando material → concluída → validada), atribuição de responsável, SLA, vínculo com ocorrências e manutenção. Migration com RLS e permissões. Endpoints CRUD + transições de estado auditadas + summary. Server actions no frontend.
2. ~~**Kanban visual**~~ — ✅ componente `KanbanBoard` com drag-and-drop HTML5 para transição de status, modal de criação de OS, busca, badges de prioridade, SLA e exclusão. CSS responsivo com estados visuais de drag.

### Média — automação e controle

3. ~~**Manutenção preventiva com agendamento**~~ — ✅ modelo `preventive_plans` com recorrência (daily→annual), geração automática de OS via `POST /preventive-plans/generate`, avanço de `next_due`, CRUD completo com permissões. Frontend em `/preventivas`.
4. ~~**Controle de materiais e estoque operacional**~~ — ✅ modelo `stock_items` + `stock_movements` com entrada/saída/ajuste, vínculo com OS e ocorrências, alerta de estoque mínimo. CRUD completo com permissões `stock.*`. Frontend em `/estoque`.
5. ~~**Checklists operacionais recorrentes**~~ — ✅ templates com itens (`checklist_templates` + `checklist_template_items`), execuções automáticas por agenda (`checklist_executions` + `checklist_execution_items`), toggle individual de itens, conclusão com notas. CRUD completo com permissões. Frontend em `/checklists`.

### Média — visibilidade e comunicação

6. ~~**Dashboard com KPIs operacionais avançados**~~ — ✅ endpoint `/dashboard/metrics` expandido com `kpis`: OS (total, por status/prioridade/categoria, tempo médio resolução, SLA compliance %, atrasadas, semana), ocorrências (por status, taxa conclusão, por setor, atrasadas), fiscais (por status/tipo, SLA compliance %, atrasadas), tendência 7 dias. Frontend com painéis de indicadores, gráficos de barras e tendência semanal.
7. ~~**Comunicação entre turnos aprimorada**~~ — ✅ modelo `shift_handoffs` com pendências direcionadas por turno/data, fluxo pendente → lido → resolvido com timestamps e responsáveis, endpoint `GET /handoffs/pending` para pendências não resolvidas do turno. CRUD completo com permissões `handoff.*`. Frontend em `/pendencias`.

### Baixa — alcance e adoção

8. ~~**App mobile / PWA**~~ — ✅ PWA implementado com manifest, service worker (network-first para navegação, cache-first para assets), ícones SVG, meta tags Apple, safe-area-inset e display standalone.

## P7 — melhorias de engenharia

### Alta — bloqueiam produção

1. ~~**Structured logging com structlog**~~ — ✅ `app/core/logging.py` configura structlog com contextvars (company_id, user_id, request_id). Middleware em `main.py` limpa/vincula contexto por request. `auth.py` vincula company_id/user_id ao autenticar. Todos os 5 módulos que usavam `logging.getLogger` migrados para `structlog.get_logger()`. JSON em produção, console colorido em dev.
2. ~~**Testes de integração contra PostgreSQL real**~~ — ✅ `conftest.py` detecta `DATABASE_URL` ou `TEST_DATABASE_URL` e usa PostgreSQL quando disponível (SQLite como fallback local). Em PostgreSQL, `_current_user_test` seta `SET LOCAL app.current_company_id` para exercitar RLS. CI já roda com PostgreSQL 17 + Alembic migrations. `docker-compose.test.yml` com PostgreSQL tmpfs para testes locais. `aiosqlite` adicionado a dev dependencies.
3. ~~**CI mais robusto**~~ — ✅ CI reforçado: `pip-audit --strict` para CVEs em dependências, cobertura mínima subida para 60%, `alembic heads` para detectar branches divergentes, coverage XML como artifact.

### Média — valor operacional

4. ~~**Cache e performance**~~ — ✅ Redis com cache por tenant no dashboard, cache global de permissões, invalidação nas mutações e readiness.
5. ~~**Background tasks**~~ — ✅ `notify_record_event` refatorado: criação de registros in-app é síncrona (com commit), envio de email (Brevo) e WhatsApp (Evolution) é disparado em background via `asyncio.create_task`. PDF mantido inline (rápido e necessário na resposta). Evolução para Celery/ARQ se necessário.
6. ~~**Exportação em lote**~~ — ✅ utilitário genérico `generate_xlsx()` em `app/core/export.py` (openpyxl, header estilizado, auto-width, limite 10k linhas). Endpoints `GET /export` em ocorrências, manutenção, checklists (execuções) e cadastros. Testes de permissão e validação de xlsx inclusos.
7. ~~**Versionamento da API**~~ — ✅ Routers agrupados em `v1_router` (APIRouter com prefix `/api/v1`) em `main.py`. Estratégia documentada em `docs/api-versionamento.md`: versionamento por prefixo de URL, regras de deprecação, exemplo de coexistência v1/v2.

### Média — qualidade de código

8. ~~**Schemas inline no router**~~ — ✅ `maintenance/schemas.py` e `bulletin/schemas.py` criados. Routers importam de schemas ao invés de definir Pydantic models inline. Consistente com o padrão dos outros domínios.
9. ~~**Tipagem dos retornos de service**~~ — ✅ Todos os 19 services tipados com NamedTuples nomeados (ex: `OccurrenceRow`, `WorkOrderRow`, `HandoffRow`). Retornos de funções anotados com tipos concretos ao invés de `tuple` genérico. Routers continuam funcionando via unpacking posicional.
10. ~~**Testes de permissão**~~ — ✅ `test_permissions.py` com 20 testes cobrindo 4 domínios (occurrences, bulletin, maintenance, checklists). Valida 403 sem permissão, 403 com permissão errada e 200/201 com permissão específica. Suite completa: 147/147 passando.

### Baixa — preparação para escala

11. ~~**Paginação por cursor**~~ — ✅ Utilitário genérico `app/core/pagination.py` com encode/decode de cursor opaco (base64). Endpoints `/cursor` adicionados em ocorrências, OS e timeline como alternativa aos endpoints offset existentes. Response: `{items, next_cursor, has_more}`.
12. ~~**Backup e deploy**~~ — ✅ Service `backup` melhorado com validação `pg_restore --list`. Novo service `backup-minio` com `mc mirror` diário. Script `scripts/backup-restore.sh` para backup/restore manual. Documentação completa em `docs/infra/backup-restore.md` com RTO/RPO, procedimento de restore, checklist pós-restore e estratégia off-site.

## P8 — auditoria de segurança e qualidade (2026-06-22)

Itens identificados na auditoria completa de sistema. Documentação detalhada em `docs/auditoria-2026-06-22.md`.

### Critical — corrigir imediatamente

- [ ] **[C1] Fix SQL injection no RLS context** — `auth.py:31` usa f-string em `SET LOCAL`. Trocar por binding parametrizado. (~1h)
- [ ] **[C2] Remover credenciais hardcoded do admin login** — `admin/app/(auth)/login/page.tsx` tem email e senha em `defaultValue`. (~30min)
- [ ] **[C3] Testes para integração Chess Hotel** — 4 endpoints sem nenhum teste: auth por header, resolução de email, criação de tickets, tracking por protocolo. (~4h)

### High — corrigir em breve

- [ ] **[H1] Fix SQL injection no import V1** — `import_v1.py:46` usa f-string para SELECT. Usar ORM reflection ou mapa validado. (~2h)
- [ ] **[H2] Adicionar soft-delete filter em lookups de Role/Sector** — `users/service.py` `_role_name()` sem filtro `company_id`. (~2h)
- [ ] **[H3] Fix N+1 query em notificações WhatsApp** — `notifications.py:246` busca telefone em loop. Pre-fetch em query única. (~1h)
- [ ] **[H4] Padronizar error handling nos routers** — `ValueError` de services não capturado em todos os routers. Gera 500 ao invés de 422. (~4h)
- [ ] **[H5] Adicionar CSP headers no Next.js** — configurar Content-Security-Policy, X-Frame-Options, X-Content-Type-Options, Referrer-Policy em `web/next.config.ts` e `admin/next.config.ts`. (~2h)
- [ ] **[H6] Implementar CSRF protection** — Server Actions sem token CSRF. Implementar geração e validação. (~4h)
- [ ] **[H7] Criar middleware de proteção de rotas** — ambas as apps fazem redirect em cada page. Centralizar em `middleware.ts`. (~3h)
- [ ] **[H8] Implementar token refresh no admin** — `admin/lib/api.ts` `platformFetch()` sem retry no 401. (~2h)
- [ ] **[H9] Habilitar access logs em produção** — remover `--no-access-log` do Uvicorn no Dockerfile. (~30min)
- [ ] **[H10] Fix rate limiter para X-Forwarded-For** — `rate_limit.py` usa `get_remote_address`, quebrado atrás de Traefik. (~2h)
- [ ] **[H11] Adicionar approval gate no deploy** — `publish.yml` faz deploy automático no push. Adicionar environment protection rule ou manual approval. (~2h)
- [ ] **[H12] Adicionar testes no frontend** — configurar vitest no web e admin, cobrir fluxos críticos (login, CRUD, forms). (~8h+)

### Medium — planejar correção

- [ ] **[M1] Fix race condition em attachments** — validação de `max_per_entity` via COUNT sem lock. Usar constraint de banco ou SELECT FOR UPDATE. (~2h)
- [ ] **[M2] Restringir filename sanitization a ASCII** — regex aceita unicode. (~30min)
- [ ] **[M3] Substituir `except Exception` na integração Asaas** — capturar exceções específicas (`httpx.HTTPError`, `KeyError`). (~1h)
- [ ] **[M4] Documentar padrão de paginação** — offset vs cursor usados sem regra clara. Definir quando usar cada um. (~1h)
- [ ] **[M5] Gerar Request ID quando ausente** — `main.py:56` deixa string vazia. Gerar UUID. (~30min)
- [ ] **[M6] Mover script do Service Worker para arquivo** — `dangerouslySetInnerHTML` em `web/app/layout.tsx`. Criar `/public/sw-loader.js`. (~30min)
- [ ] **[M7] Implementar refresh token rotation** — emitir novo refresh token a cada refresh, revogar o anterior. (~4h)
- [ ] **[M8] Extrair auth logic duplicada** — `tryRefresh()` duplicado entre `actions.ts` e `api.ts`. Criar `lib/auth.ts`. (~2h)
- [ ] **[M9] Validação de tipo nas respostas da API** — `response.json()` sem validação. Avaliar Zod para runtime validation. (~8h+)
- [ ] **[M10] Reduzir uso de `"use client"`** — extrair partes server-side de componentes grandes. (~4h)
- [ ] **[M11] Validação de arquivo no client antes do upload** — tipo e tamanho no `fiscal-request-form.tsx`. (~1h)
- [ ] **[M12] Acessibilidade** — keyboard navigation em dropdowns, ARIA labels, `role` em autocomplete. (~4h)
- [ ] **[M13] Configurar error tracking centralizado** — Sentry ou similar para capturar erros 500 em produção. (~4h)
- [ ] **[M14] Habilitar persistência no Redis** — configurar AOF ou RDB no docker-stack.yml. (~1h)
- [ ] **[M15] Avaliar PostgreSQL com failover** — single replica em produção. Considerar streaming replication ou managed DB. (~8h+)
- [ ] **[M16] Configurar log rotation no Docker** — adicionar `logging` driver com `max-size` e `max-file`. (~1h)
- [ ] **[M17] Testar procedimento de restore** — agendar teste mensal de restore de backup. Documentar. (~2h)
- [ ] **[M18] Avaliar replicação do MinIO** — storage single node. Considerar clustering ou S3 externo. (~4h)

### Low — quando houver oportunidade

- [ ] **[L1] Cookie `secure` explícito por ambiente** — usar env var ao invés de `NODE_ENV`. (~30min)
- [ ] **[L2] Log de permissão específica para wildcard** — registrar qual permissão foi checada mesmo com `*`. (~1h)
- [ ] **[L3] Avaliar i18n** — texto hardcoded em português. Considerar `next-intl` se multi-idioma necessário. (~8h+)
- [ ] **[L4] Configurar image optimization no Next.js** — habilitar webp/avif em `next.config.ts`. (~30min)
- [ ] **[L5] Revisar filtros do Alembic env.py** — filtra mudanças de índice/FK, pode mascarar drift. (~1h)
- [ ] **[L6] Padronizar error response** — definir schema único para erros (`{code, message}`). (~2h)

### Testes — gaps de cobertura

- [ ] **[T1] Testes para domínio occurrences** — domínio core sem testes dedicados. (~4h)
- [ ] **[T2] Testes para domínio users** — CRUD de usuários sem testes. (~3h)
- [ ] **[T3] Testes para domínio dashboard** — métricas e KPIs sem testes. (~3h)
- [ ] **[T4] Testes para domínio notifications** — lógica de service sem testes isolados. (~3h)
- [ ] **[T5] Testes para domínios restantes** — meetings, platform, settings, roles, procedures, timeline, shift_reports, work_diaries, apartment_inspections, check_suites, inspection_suites, modules, registries. (~16h+)
- [ ] **[T6] Testes unitários de service layer** — 90% são integração. Adicionar testes isolados de business logic. (~8h+)
- [ ] **[T7] Testes de negative path** — ~70% dos domínios testam só happy path. Cobrir validações e erros. (~8h+)

## Definition of Done por módulo

Contrato, autorização, isolamento por empresa, estados de UI, CRUD necessário, anexos/exportações, testes, comparação de dados, observabilidade, documentação e rollback precisam estar aprovados antes do corte. Uma entrega não está concluída se a documentação pertinente em `/docs` estiver ausente ou desatualizada.
