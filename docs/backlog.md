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
- [ ] Concluir inventário de índices, constraints, collations, órfãos, soft deletes, anexos e volumes fora do banco.
- [x] Testes cross-tenant: suite `test_cross_tenant.py` com validação de tokens por tenant, rejeição de token expirado e sem token em todos os endpoints autenticados.
- [ ] Estabilizar a execução do mypy na imagem de desenvolvimento; a versão 1.20.2 encerrou com erro interno em 20/06/2026.

## P2 — identidade, ACL e cadastros

- [x] Usuários: CRUD via API com listagem, criação (bcrypt), atualização, soft delete e paginação server-side.
- [x] Setores, locais e funções: CRUD unificado via `/registries` com paginação e busca.
- [x] Procedimentos: CRUD via API (`/procedures`) com listagem paginada, busca, criação, atualização e soft delete.
- [x] Endpoint `PATCH /users/me` para edição de perfil pelo próprio usuário (nome, telefone, senha).
- [ ] Anexos de procedimentos (upload, metadados e download).
- [x] Timeline de alterações nos registros operacionais (front local, todas as telas).
- [x] Tabela de auditoria na API (`audit_events`) para persistir o histórico de alterações com `user_id`, `company_id` e diff JSON.
- [x] Padronizar design tokens no `globals.css` (espaçamento, cores, raios, sombras, tipografia, transições).
- [x] Unificar `DashboardShell` e `OperationalModule` em um `AppLayout` compartilhado, eliminando sidebar/topbar/navegação duplicados.
- [ ] Componentes reutilizáveis de lista, formulário, estado vazio e confirmação.
- [x] Persistir comentários e alterações da tratativa na API (`GET/POST /timeline/{entity_type}/{entity_id}`); frontend consome timeline da API para módulos conectados.
- [ ] Tornar a auditoria imutável, com ator, tenant, data UTC, tipo de evento e diferenças estruturadas.
- [ ] Registrar na timeline todos os campos específicos do domínio, inclusive os campos fiscais e anexos.
- [x] Impedir a criação de eventos de alteração vazios: `record_event` retorna sem inserir quando `event_type == "update"` e `diff` é vazio.

## P3 — operação

- [x] Remover o corte nos primeiros 100 registros, carregando todas as páginas disponíveis da API de ocorrências.
- [x] Evoluir ocorrências para paginação e busca server-side sob demanda, com navegação via query params na URL.
- [x] Remover a precedência de dados antigos do `localStorage` sobre ocorrências retornadas pela API.
- [x] Implementar mutações de ocorrências na API com autorização e isolamento por empresa.
- [x] Dashboard com métricas reais agregadas do banco (ocorrências, fiscais, equipe, atividades recentes).
- [x] Todos os módulos operacionais conectados à API com CRUD completo e paginação server-side.
- [x] Tabela genérica `module_records` para módulos sem tabela própria (reuniões, inspeções, turnos, obra, manutenção, mural).
- [ ] Ocorrências: comentários, participantes, anexos, clone e PDF.
- [ ] Reuniões: promover para tabela própria com participantes, assuntos, anexos, início e ata PDF.
- [ ] Relatórios de turno: promover para tabela própria com Excel.

## P3B — solicitações fiscais

- [x] Criar protótipo funcional no frontend com tipos de solicitação, campos condicionais, SLA, anexos e tratativa.
- [x] Criar modelo, migration, schemas, service, autorização e endpoints FastAPI para solicitações fiscais.
- [x] Integrar Chess Hotel via `POST /integrations/chess-hotel/tickets` com autenticação por header, resolução de usuário e SLA inicial.
- [x] Vincular solicitações fiscais a usuários do Registro (`requester_user_id`, `responsible_user_id`) e adicionar tracking/histórico para o Chess.
- [x] Validar e normalizar CPF/CNPJ e e-mail do tomador nos endpoints de solicitações fiscais.
- [x] Adicionar paginação server-side ao endpoint `GET /fiscal-requests` com busca por protocolo, solicitante e tipo.
- [x] Definir SLA no servidor: `sla_deadline` definido server-side (24h) ao criar solicitações fiscais do Registro e Chess; `sla_status` computado no servidor (on_time/warning/overdue/completed).
- [ ] Evoluir SLA com timezone explícito, calendário útil, pausa e política de vencimento.
- [ ] Substituir anexos Base64 no `localStorage` por armazenamento próprio, com metadados no banco.
- [ ] Validar tamanho, quantidade, extensão, MIME real, nome e autorização de download dos anexos.
- [ ] Restringir previews e downloads para impedir conteúdo ativo ou arquivo malicioso.
- [x] Notificações in-app: `create_notification()` dispara para responsáveis e notificados em todo `notify_record_event`; Chess Hotel notifica todos os usuários ativos ao criar solicitação fiscal.
- [ ] Implementar preferências de notificação, destinatários por módulo e registro de entrega.
- [x] Backend de notificações in-app: model `Notification`, migration, endpoints de listagem paginada, marcar como lida e marcar todas como lidas.
- [ ] Cobrir CRUD, SLA, anexos, auditoria e isolamento cross-tenant com testes.

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

## Sugestões de próximos passos

### Valor operacional

1. **Armazenamento de anexos** (P3B) — substituir Base64/localStorage por upload para disco/S3 com metadados no banco. Necessário antes de uso real das solicitações fiscais.

2. **SLA no servidor** (P3B) — mover o cálculo de prazo do cliente para a API, com timezone e calendário útil. A integração Chess Hotel já envia `sla_deadline` de 24h; o servidor precisa de política real.

3. ~~**Persistir tratativas na API** (P2)~~ — feito: endpoints `GET/POST /timeline/{entity_type}/{entity_id}`, frontend consome da API.

4. ~~**Notificações da integração Chess Hotel**~~ — feito: `create_notification()` dispara para todos os usuários ativos ao criar solicitação fiscal via Chess; `notify_record_event` agora cria notificações in-app além de e-mail.

4b. **Integração Evolution (WhatsApp)** — credenciais são salvas via `/settings/evolution`, mas nenhum código consome essas credenciais para enviar mensagens. Implementar envio real ou remover a configuração.

5. **Promover módulos genéricos** — quando reuniões, inspeções ou diário de obra precisarem de campos específicos, criar tabelas dedicadas preservando os dados da `module_records`.

### Estruturais

6. ~~**Testes cross-tenant** (P0)~~ — feito: suite `test_cross_tenant.py` validando token isolation, rejeição sem auth e token expirado.

7. ~~**Extrair `current_user` para dependência compartilhada**~~ — feito: movido para `app/core/auth.py`, removidas 5 cópias e 4 imports cruzados.

## Definition of Done por módulo

Contrato, autorização, isolamento por empresa, estados de UI, CRUD necessário, anexos/exportações, testes, comparação de dados, observabilidade, documentação e rollback precisam estar aprovados antes do corte. Uma entrega não está concluída se a documentação pertinente em `/docs` estiver ausente ou desatualizada.
