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
