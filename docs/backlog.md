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
- [x] Reuniões: promovidas para tabela dedicada `meetings` + `meeting_participants` (com papel: organizer/attendee/optional) + `meeting_subjects` (pautas com resolved). Migration de dados de `module_records`. CRUD completo + clone + subjects CRUD + ata PDF.
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
- [ ] Atualizar `import_v1.py` para escrever diretamente nas tabelas dedicadas em futuras importações — atualmente a migration `0030` corrige o gap, mas o script deveria gravar direto.
- [ ] Executar corte final — puxar dump MySQL atualizado do V1, importar via `import_v1.py`, rodar migrations (incluindo 0030), validar dados.

## P6 — documentação e governança

- [x] Definir `/docs` como fonte de verdade técnica, funcional, operacional e histórica do Registro.
- [x] Registrar que toda informação pertinente ao desenvolvimento e ao sistema deve ser mantida em `/docs`.
- [x] Corrigir referências atuais que instruíam login por slug; o histórico cronológico preserva menções ao contrato antigo.
- [x] Documentar o módulo de solicitações fiscais em `web-rotas-ui.md`, `domain-model.md` e `api-reference.md` conforme o backend for implementado.
- [x] Documentar o módulo de ocorrências (CRUD, soft delete, `legacy_id` nullable) em `api-reference.md` e `domain-model.md`.
- [x] Atualizar `mapa.md`, contratos, runbooks, memória, backlog e registro de trabalho — atualização de 21/06/2026 corrigiu PostgreSQL como banco ativo, domínios P1/P4 implementados, bloqueios atuais revisados.
- [x] Criar ADR quando uma decisão alterar stack, isolamento, persistência, segurança, deploy, cobrança ou estratégia de migração — ADR-001 (MySQL→PostgreSQL) e ADR-002 (RLS multi-tenant) criados em `docs/adr/`.
- [ ] Manter documentação de estado atual separada de funcionalidades apenas planejadas.

## Correções de repositório

- [x] Restaurar `.idea/` e `.vscode/` no `.gitignore`.
- [ ] Manter `docs/v1/`, dumps SQL, credenciais, secrets e arquivos locais fora do Git e das imagens Docker.

## Próximos passos pendentes (prioridade)

### Alta — bloqueiam corte

1. **Atualizar `import_v1.py`** — reescrever para gravar diretamente nas tabelas dedicadas em futuras importações. A migration `0030` resolve o gap para dados já importados, mas o script deveria gravar direto sem depender da migration.
2. **Dump V1 atualizado** — puxar dump MySQL fresco do servidor V1 em produção. O dump local (`aero-2026-06-19.sql`) é snapshot de desenvolvimento.
3. **Inventário de anexos físicos** — mapear arquivos/volumes fora do banco na V1 (uploads, PDFs, imagens) para migração ao MinIO.
4. **Testes de cobertura** — expandir para anexos e auditoria (52 testes cobrem SLA, CRUD e cross-tenant, mas anexos e audit_events não têm cobertura).

### Média — valor operacional

5. **Integração Evolution (WhatsApp)** — credenciais são salvas via `/settings/evolution`, mas nenhum código envia mensagens. Implementar envio real ou remover a configuração.
6. **Separar estado atual de planejado na documentação** — item P6 aberto: garantir que docs não misturam features implementadas com funcionalidades futuras.
7. **Promover módulos genéricos remanescentes** — manutenção e mural ainda usam `module_records`. Promover quando precisarem de campos específicos.

### Baixa — preparação futura

8. **Corte do Laravel** — procedimento documentado em `docs/migracao-postgresql.md`. Depende dos itens 1-3 acima.
9. **Remover profile `mysql-import`** — após corte final em produção, eliminar MySQL do Docker Compose e dependência `asyncmy`.

## Definition of Done por módulo

Contrato, autorização, isolamento por empresa, estados de UI, CRUD necessário, anexos/exportações, testes, comparação de dados, observabilidade, documentação e rollback precisam estar aprovados antes do corte. Uma entrega não está concluída se a documentação pertinente em `/docs` estiver ausente ou desatualizada.
