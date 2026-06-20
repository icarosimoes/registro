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

## 2026-06-19 — Timeline de alterações

- Adicionado tipo `HistoryEntry` ao `ModuleRecord`: registra ação, usuário, data/hora e diferenças campo a campo.
- Toda criação ou edição grava uma entrada automática no histórico do registro, com nome do usuário logado e timestamp.
- Edições registram as diferenças detalhadas (campo: valor anterior → valor novo) para título, categoria, responsável, status e descrição.
- Timeline exibida no drawer de detalhes de todas as telas operacionais: ocorrências, reuniões, relatórios de turno, inspeções, diário de obra, manutenção, cadastros, usuários e mural.
- Visual com linha vertical, dots azuis e cards de alteração, seguindo o padrão do sistema.
- Dados persistidos no `localStorage` por tenant; futuramente serão gravados pela API com auditoria real.
