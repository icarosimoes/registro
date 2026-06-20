# Registro de trabalho

## 2026-06-19

- Inventariado o legado: Laravel 7, PHP 7.2+, 131 migrations e 194 views Blade.
- Identificados os domínios principais e contratos de usuários/ACL.
- Confirmada a referência Jarvis em `/home/icarosimoes/dev/aloji/docs/agentes`.
- Definida migração incremental para FastAPI + Next.js, mantendo o MySQL.
- Iniciada a fundação paralela em `api/` e `web/`.
- Registrado o redesign inspirado na referência enviada: sidebar expansível, topbar, busca global, indicadores, tabelas densas e drawers contextuais.

## Pendências

- Obter acesso de desenvolvimento ou dump sem dados sensíveis do MySQL.
- Gerar inventário real de tabelas, volumes, constraints e inconsistências.
- Validar política de compatibilidade das senhas Laravel.
- Escolher o primeiro módulo funcional após autenticação/ACL.

## 2026-06-19 — Organização da versão legada

- Aplicação renomeada para **Registro**.
- Código, migrations, views, assets, testes e configuração Laravel movidos para `docs/v1/`.
- Banco MySQL legado mantido com o nome atual para evitar risco operacional.
- Referências da nova API, frontend e documentação atualizadas para Registro.

## 2026-06-19 — Fundação Docker/Swarm

- Criadas imagens Docker multi-stage para FastAPI e Next.js.
- Criado `docker-compose.yml` para desenvolvimento local.
- Criado `docker-stack.yml` para produção Swarm com duas réplicas, healthchecks, rolling update e rollback.
- Conexão de produção preparada para Docker Secret externo.
- Documentado o diretório `/opt/registro`, GHCR, deploy e rollback.

## 2026-06-19 — Fluxo Git simplificado

- Desenvolvimento passou a ocorrer diretamente na branch `main`.
- `docs/v1/` foi mantido no disco local e incluído no `.gitignore`.
- A aplicação Laravel legada foi removida do índice do Git para não ser enviada novamente ao GitHub.

## 2026-06-19 — Primeira fatia de autenticação

- Implementados `POST /api/v1/auth/login` e `GET /api/v1/auth/me`.
- Preservada compatibilidade com bcrypt, usuários ativos, soft delete, papéis, empresas e ACL do Laravel.
- A sessão inclui `company_id`; `/auth/me` revalida usuário e empresa no banco.
- Adicionado Docker Secret independente para a chave JWT no Swarm.
- A validação com usuários reais permanece pendente até configurar acesso seguro ao MySQL.

## 2026-06-19 — Documentação no padrão Aloji

- Inventariados stack atual, quatro endpoints, 60 tabelas legadas e 123 declarações de rota Laravel.
- Criados documentos de arquitetura, domínio, API, UI, desenvolvimento, segurança e backlog.
- Criados inventário V1, plano MySQL/PostgreSQL, runbook de produção e critérios de testes.
- Adaptados para o Registro os agentes Jarvis de engenharia, layout/CRUD, performance, segurança e multiempresa.
- Excluídos deliberadamente os padrões Aloji de reservas, Channex, Asaas, CRM e financeiro por falta de aderência ao domínio.

## 2026-06-19 — Base SaaS, MySQL e admin

- Adicionado MySQL 8.4 ao Compose, migration Alembic inicial e seed fictício com dois tenants.
- Criados modelos de empresas, usuários, papéis, permissões, planos, assinaturas, faturas, operadores e auditoria da plataforma.
- Separados JWT tenant e plataforma; login tenant aceita `company_slug` e revalida o tenant.
- Criada API administrativa de métricas, tenants e planos.
- Criado painel Next.js separado em `admin/`, com sessão em cookie `httpOnly`.
- Adicionado serviço admin à stack Swarm e mantido MySQL de produção externo.
- Adaptados os agentes Jarvis SaaS e Asaas; integração de cobrança continua desativada.
- Documentado o procedimento futuro de importação do dump Laravel.

## 2026-06-19 — Entrada autenticada do tenant

- A raiz do produto deixou de exibir diretamente o protótipo estático.
- Criados login tenant, cookie `httpOnly`, revalidação em `/auth/me`, dashboard protegido e logout.
- O protótipo visual foi preservado em `/design-preview`; seus indicadores continuam fictícios até os módulos operacionais serem conectados.

## 2026-06-19 — MVP funcional do portal

- Conectados todos os itens do menu do tenant a telas autenticadas.
- Implementados busca, filtro, paginação, detalhes, criação, edição, exclusão confirmada, restauração e exportação CSV.
- Criadas telas de ocorrências, reuniões, turno, inspeções, diário de obra, manutenção, cadastros, usuários, mural, configurações e conta.
- Dados operacionais de teste ficam no `localStorage`, isolados por `company_id`; a API continua sendo a próxima etapa para persistência e autorização reais.

## 2026-06-19 — Importação do dump V1

- Restaurado `aero-2026-06-19.sql` em staging MySQL separada com 66 tabelas.
- Identificado que `companies` está vazia e os usuários da V1 possuem `company_id` nulo.
- Criado tenant sintético `aero-hotel`, preservando hashes Laravel e IDs antigos em `legacy_id`.
- Importados 59 usuários, 17 setores, 69 locais, 13 funções, 6 procedimentos e 375 ocorrências.
- Criada migration `20260619_0002`, importador idempotente por checksum e `GET /occurrences`.
- Validada paridade de 375 ocorrências; a API retorna 317 registros não excluídos.

## 2026-06-19 — Tenant Aero Hotel e login sem slug

- Tenant V1 renomeado de `aero-v1` para `aero-hotel` (nome "Aero Hotel") no código, base e documentação.
- Documentado plano de produção: dump fresco da V1 em operação será reimportado pelo mesmo ETL idempotente.
- Login removeu campo `company_slug`; agora aceita apenas e-mail e senha.
- Se o e-mail pertence a um único tenant, entra direto. Se pertence a mais de um, API retorna `422 multi_tenant` com lista de empresas e o front exibe seletor.
- Front de login convertido para Client Component com seletor de tenant dinâmico.
- Padrão alinhado com o Aloji.

## 2026-06-19 — Tratativa (timeline de conversa)

- `HistoryEntry` agora possui `type` (`comment`, `change`, `create`) e campo `message` para comentários livres.
- Comentários podem ser adicionados diretamente no drawer de detalhes via campo de texto e botão enviar.
- Criações, edições e comentários aparecem em ordem cronológica como uma conversa de ticket.
- Avatares coloridos por tipo: azul (comentário), roxo (alteração de campos), verde (criação).
- Alterações exibem chips detalhando cada campo modificado com valor anterior e novo.
- Timeline visível tanto no drawer de detalhes (com campo de comentário) quanto no modal de edição (somente leitura).
- Modal de edição alarga automaticamente quando o registro possui histórico.
- Presente em todas as telas operacionais: ocorrências, reuniões, relatórios de turno, inspeções, diário de obra, manutenção, cadastros, usuários e mural.
- Dados persistidos no `localStorage` por tenant; futuramente serão gravados pela API com auditoria real.

## 2026-06-20 — Revisão técnica e governança documental

- Revisadas as alterações recentes de autenticação, tenant Aero Hotel, timeline e solicitações fiscais.
- Confirmados os quatro serviços locais ativos no Docker: API, web, admin e MySQL.
- Executados `npm run typecheck`, build de produção do Next.js e testes da API no container; frontend aprovado e 7 testes da API aprovados.
- Confirmados no banco local o tenant `aero-hotel`, 60 usuários vinculados e 375 ocorrências importadas.
- Identificado que o login multitenant revela a lista de empresas antes de validar a senha; correção e testes foram priorizados no backlog.
- Identificado que a interface carrega apenas 100 ocorrências e pode substituir os dados da API por uma cópia antiga do `localStorage`.
- Identificado que tratativas, edições, comentários e o módulo fiscal ainda não possuem persistência na API.
- Registradas pendências de anexos, SLA, notificações, validação fiscal, auditoria, cross-tenant, documentação e CI.
- Definido formalmente que toda informação pertinente ao desenvolvimento e ao sistema deve ser documentada em `/docs`.
- Atualizados `backlog.md`, `memoria-projeto.md` e o padrão documental com essa regra permanente.

## 2026-06-20 — Autenticação multitenant e ocorrências

- Corrigido o fluxo multitenant para validar hashes antes de retornar opções de empresa.
- Removida a segunda consulta que listava tenants apenas pelo e-mail; as opções agora derivam exclusivamente dos usuários autenticados.
- Adicionado `company_name` ao resultado interno de autenticação e ordenação determinística por empresa.
- Adicionada validação positiva para `company_id`.
- Criados cinco testes de serviço/contrato de autenticação; suíte total validada com 12 testes e Ruff sem erros.
- A página de ocorrências passou a buscar a primeira página e carregar em paralelo as páginas restantes da API.
- Ocorrências vindas da API não consultam nem gravam dados operacionais no `localStorage`.
- Ações de criação, edição, exclusão e comentário ficam ocultas para ocorrências reais até a API de mutações existir; a tela informa o modo leitura.
- Restaurados `.idea/` e `.vscode/` no `.gitignore`.
- Validação final: 12 testes da API, Ruff, TypeScript e build Next.js aprovados; os quatro serviços Docker permaneceram ativos e a API saudável.
- O mypy 1.20.2 da imagem encerrou com erro interno da própria ferramenta, sem produzir diagnóstico do código; estabilização registrada no backlog.

## 2026-06-20 — CRUD de solicitações fiscais e ocorrências

- Criado modelo `FiscalRequest` com `company_id`, `protocol`, `request_type`, `title`, `description`, `apartment`, `requester`, `origin`, `status` e `payload` JSON.
- Criadas migrations `0003` (tabela), `0004` (colunas `title`/`description`) e `0005` (`legacy_id` nullable em todas as tabelas legadas).
- Criada migration `0006` para renomear tenant `aero-v1` para `aero-hotel` sem duplicar, cobrindo cenário de dump antigo.
- Implementados endpoints `POST/GET/PATCH/DELETE /fiscal-requests` com Tenant Bearer e isolamento por `company_id`.
- Implementado `POST /integrations/chess-hotel/tickets` com autenticação por header `X-Registro-Key` e resolução de tenant por slug.
- Implementados endpoints `POST/PATCH/DELETE /occurrences` com soft delete, `created_by_user_id` e `updated_by_user_id`.
- Criadas server actions no frontend (`createFiscalRequestAction`, `updateFiscalRequestAction`, `deleteFiscalRequestAction`, `createOccurrenceAction`, `updateOccurrenceAction`, `deleteOccurrenceAction`).
- Frontend de ocorrências e solicitações fiscais agora permite criar, editar e excluir via API; mensagem de "modo leitura" removida.
- Frontend de módulos API-backed recarrega dados a cada 15 segundos e em eventos de foco/visibilidade.
- Componente `RegistroLauncher.vue` validado no Chess Hotel (localhost:8081) abrindo drawer de "Nova Solicitação Fiscal".
- Documentados os novos endpoints, modelo de domínio atualizado e rotas web revisadas em `api-reference.md`, `domain-model.md`, `web-rotas-ui.md` e `chess-hotel-implementacao.md`.
- Todos os endpoints testados end-to-end: create, update, delete, list, isolamento cross-tenant e integração Chess Hotel.

## 2026-06-20 — Auditoria, paginação, validação e CI

- Criada tabela `audit_events` (migration `0007`) com `company_id`, `user_id`, `entity_type`, `entity_id`, `event_type` e `diff` JSON.
- Criado service `app/core/audit.py` com `record_event` e `compute_diff`; integrado em todos os endpoints de mutação de ocorrências e solicitações fiscais.
- Diff registra campo a campo o valor anterior e novo, apenas quando há mudança; create e delete não possuem diff.
- Evoluído o frontend de ocorrências para paginação server-side (20 por página) com busca via query params na URL e debounce de 400ms.
- O server component busca uma única página da API em vez de carregar todas em paralelo.
- Criado `app/core/validators.py` com validação de CPF (dígitos verificadores), CNPJ (dígitos verificadores) e e-mail básico.
- CPF/CNPJ validados e normalizados no `payload` de solicitações fiscais (create e update); valores inválidos rejeitados com 422.
- E-mail do tomador normalizado para lowercase e trim.
- Adicionadas colunas `requester_email`, `requester_user_id`, `responsible_user_id`, `chess_user_id`, `reservation_number` e `sla_deadline` a `fiscal_requests` (migration `0008`).
- Integração Chess Hotel expandida: resolução de usuário por e-mail, cálculo de SLA (24h), tracking de solicitações com histórico de auditoria, e URL de acompanhamento.
- Criado CI mínimo em `.github/workflows/ci.yml` com 3 jobs: Ruff (lint + format), pytest (com MySQL service), TypeScript typecheck.
- Documentação atualizada em `api-reference.md`, `domain-model.md`, `web-rotas-ui.md`, `mapa.md`, `backlog.md` e `registro-trabalho.md`.
