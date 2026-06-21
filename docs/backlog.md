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

- [ ] CRUD auditado de tenants, planos e assinaturas.
- [ ] Definir trial, tolerância, suspensão e reativação.
- [ ] Configurar Asaas sandbox e segredos no Swarm.
- [ ] Implementar webhook autenticado, idempotente e com replay.
- [ ] Implementar reconciliação periódica de cobranças.

## P4 — inspeções e obra

- [ ] Check suites e inspection suites.
- [ ] Vistorias V2 e migração controlada da V1.
- [ ] Auditorias e relatórios.
- [ ] Diário de obra.

## P5 — corte e banco

- [ ] Retirar Laravel domínio a domínio.
- [ ] Congelar mudanças estruturais no MySQL.
- [ ] Ensaiar carga e validação no PostgreSQL.
- [ ] Executar corte sem dual-write e ativar isolamento PostgreSQL/RLS após saneamento.

## P6 — documentação e governança

- [x] Definir `/docs` como fonte de verdade técnica, funcional, operacional e histórica do Registro.
- [x] Registrar que toda informação pertinente ao desenvolvimento e ao sistema deve ser mantida em `/docs`.
- [x] Corrigir referências atuais que instruíam login por slug; o histórico cronológico preserva menções ao contrato antigo.
- [x] Documentar o módulo de solicitações fiscais em `web-rotas-ui.md`, `domain-model.md` e `api-reference.md` conforme o backend for implementado.
- [x] Documentar o módulo de ocorrências (CRUD, soft delete, `legacy_id` nullable) em `api-reference.md` e `domain-model.md`.
- [ ] Atualizar `mapa.md`, contratos, runbooks, memória, backlog e registro de trabalho continuamente, junto da mudança correspondente.
- [ ] Criar ADR quando uma decisão alterar stack, isolamento, persistência, segurança, deploy, cobrança ou estratégia de migração.
- [ ] Manter documentação de estado atual separada de funcionalidades apenas planejadas.

## Correções de repositório

- [x] Restaurar `.idea/` e `.vscode/` no `.gitignore`.
- [ ] Manter `docs/v1/`, dumps SQL, credenciais, secrets e arquivos locais fora do Git e das imagens Docker.

## Sugestões de próximos passos (prioridade)

### 🔴 Alta — bloqueiam uso real

1. ~~**Armazenamento de anexos**~~ — concluído: MinIO (S3-compatible) com tabela `attachments`, endpoints multipart upload/download/delete, frontend integrado em solicitações fiscais. Validação de tamanho (10MB), quantidade (20/registro), extensão e content-type.

2. ~~**SLA com timezone e calendário útil**~~ — concluído: `app/core/sla.py` com dias úteis (seg-sex 8h-18h), timezone por tenant (`companies.timezone`), feriados configuráveis, pausa/resume via status "Em espera" e acúmulo de segundos pausados.

3. ~~**Testes de cobertura**~~ — concluído: 52 testes (era 12). Cobertura de SLA (22 testes), CRUD fiscal_requests via API (8 testes), isolamento cross-tenant com DB (4 testes). Pendente: anexos e auditoria.

### 🟡 Média — valor operacional

4. ~~**Componentes reutilizáveis**~~ — concluído: ACL integrado ao OperationalModule com botões condicionais.

5. **Integração Evolution (WhatsApp)** — credenciais são salvas via `/settings/evolution`, mas nenhum código envia mensagens. Implementar envio real ou remover a configuração.

6. ~~**Promover módulos genéricos**~~ — concluído: reuniões e relatórios de turno promovidos para tabelas dedicadas com migrations de dados.

7. **Preferências de notificação** — destinatários por módulo, frequência e registro de entrega.

### 🟢 Baixa — preparação futura

8. **Comercial e cobrança** (P1) — CRUD de tenants/planos, Asaas sandbox, webhook e reconciliação.

9. **Corte do Laravel** (P5) — retirar domínio a domínio, congelar MySQL, ensaiar PostgreSQL.

### Já concluídos (removidos das sugestões)

- ~~Persistir tratativas na API~~ — endpoints `GET/POST /timeline/{entity_type}/{entity_id}`
- ~~Notificações Chess Hotel~~ — `create_notification()` + `notify_record_event`
- ~~Testes cross-tenant~~ — suite `test_cross_tenant.py`
- ~~Extrair `current_user`~~ — movido para `app/core/auth.py`
- ~~Service layer~~ — `service.py` por domínio
- ~~Rate limiting~~ — slowapi nos endpoints sensíveis
- ~~Refresh token~~ — JWT refresh + auto-refresh no frontend

## Definition of Done por módulo

Contrato, autorização, isolamento por empresa, estados de UI, CRUD necessário, anexos/exportações, testes, comparação de dados, observabilidade, documentação e rollback precisam estar aprovados antes do corte. Uma entrega não está concluída se a documentação pertinente em `/docs` estiver ausente ou desatualizada.
