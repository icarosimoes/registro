# Referência da API

Base local: `http://localhost:8000/api/v1`. OpenAPI: `http://localhost:8000/docs` fora de produção.

## Endpoints implementados

| Método | Rota | Autenticação | Resultado |
| --- | --- | --- | --- |
| `GET` | `/health` | pública | processo FastAPI está vivo |
| `GET` | `/health/ready` | pública | conexão do banco pronta ou não configurada |
| `POST` | `/auth/login` | pública (10/min) | JWT access + refresh e perfil |
| `POST` | `/auth/refresh` | pública (20/min) | renova tokens via refresh token |
| `GET` | `/auth/me` | Bearer | perfil revalidado no MySQL |
| `GET` | `/occurrences` | `occurrence.view` | ocorrências paginadas e isoladas por empresa |
| `GET` | `/occurrences/{id}` | `occurrence.view` | detalhe com participantes |
| `POST` | `/occurrences` | `occurrence.create` | cria ocorrência |
| `PATCH` | `/occurrences/{id}` | `occurrence.edit` | atualiza ocorrência |
| `DELETE` | `/occurrences/{id}` | `occurrence.delete` | soft delete de ocorrência |
| `POST` | `/occurrences/{id}/clone` | `occurrence.create` | duplica ocorrência com participantes |
| `GET` | `/occurrences/{id}/pdf` | `occurrence.view` | exporta PDF da ocorrência |
| `POST` | `/integrations/chess-hotel/users/resolve` | `X-Registro-Key` (30/min) | resolve usuário Chess no Registro por e-mail |
| `POST` | `/integrations/chess-hotel/tickets` | `X-Registro-Key` (30/min) | cria solicitação fiscal via integração Chess Hotel |
| `GET` | `/integrations/chess-hotel/tickets` | `X-Registro-Key` | lista solicitações do usuário Chess com tracking |
| `GET` | `/fiscal-requests` | `fiscal_request.view` | solicitações fiscais paginadas do tenant |
| `POST` | `/fiscal-requests` | `fiscal_request.create` | cria solicitação fiscal |
| `PATCH` | `/fiscal-requests/{id}` | `fiscal_request.edit` | atualiza solicitação fiscal |
| `DELETE` | `/fiscal-requests/{id}` | `fiscal_request.delete` | exclui solicitação fiscal |
| `GET` | `/dashboard/metrics` | Tenant Bearer | métricas agregadas do dashboard |
| `PATCH` | `/users/me` | Tenant Bearer | edição de perfil do próprio usuário |
| `GET` | `/users` | `user.view` | usuários paginados do tenant |
| `GET` | `/users/search?q=` | `user.view` | autocomplete de usuários ativos (max 10) |
| `POST` | `/users` | `user.create` | cria usuário |
| `PATCH` | `/users/{id}` | `user.edit` | atualiza usuário |
| `DELETE` | `/users/{id}` | `user.delete` | soft delete de usuário |
| `GET` | `/roles` | `user.view` | lista roles do tenant com contagem de usuários |
| `GET` | `/roles/{id}` | `user.view` | detalhe do role com permissões |
| `POST` | `/roles` | `user.edit` | cria role com permissões |
| `PATCH` | `/roles/{id}` | `user.edit` | atualiza role/permissões |
| `DELETE` | `/roles/{id}` | `user.edit` | exclui role (só se sem usuários) |
| `GET` | `/roles/permissions` | `user.view` | lista todas as permissões agrupadas por módulo |
| `GET` | `/registries` | `registry.view` | cadastros (setores, locais e funções) |
| `POST` | `/registries` | `registry.create` | cria cadastro |
| `PATCH` | `/registries/{id}?category=` | `registry.edit` | atualiza cadastro |
| `DELETE` | `/registries/{id}?category=` | `registry.delete` | soft delete de cadastro |
| `GET` | `/meetings` | `meeting.view` | reuniões paginadas do tenant |
| `GET` | `/meetings/{id}` | `meeting.view` | detalhe com participantes e pautas |
| `POST` | `/meetings` | `meeting.create` | cria reunião com participantes e pautas |
| `PATCH` | `/meetings/{id}` | `meeting.edit` | atualiza reunião |
| `DELETE` | `/meetings/{id}` | `meeting.delete` | soft delete de reunião |
| `POST` | `/meetings/{id}/clone` | `meeting.create` | duplica reunião |
| `POST` | `/meetings/{id}/subjects` | `meeting.edit` | adiciona pauta |
| `PATCH` | `/meetings/{id}/subjects/{sid}` | `meeting.edit` | atualiza pauta (toggle resolved) |
| `DELETE` | `/meetings/{id}/subjects/{sid}` | `meeting.edit` | remove pauta |
| `GET` | `/shift-reports` | `shift_report.view` | relatórios de turno paginados (filtro por data) |
| `GET` | `/shift-reports/{id}` | `shift_report.view` | detalhe do relatório |
| `POST` | `/shift-reports` | `shift_report.create` | cria relatório de turno |
| `PATCH` | `/shift-reports/{id}` | `shift_report.edit` | atualiza relatório |
| `DELETE` | `/shift-reports/{id}` | `shift_report.delete` | soft delete de relatório |
| `GET` | `/modules/{slug}` | `module.view` | registros genéricos paginados |
| `POST` | `/modules/{slug}` | `module.create` | cria registro genérico |
| `PATCH` | `/modules/{slug}/{id}` | `module.edit` | atualiza registro genérico |
| `DELETE` | `/modules/{slug}/{id}` | `module.delete` | soft delete de registro genérico |
| `GET` | `/procedures` | `procedure.view` | procedimentos paginados do tenant |
| `POST` | `/procedures` | `procedure.create` | cria procedimento |
| `PATCH` | `/procedures/{id}` | `procedure.edit` | atualiza procedimento |
| `DELETE` | `/procedures/{id}` | `procedure.delete` | soft delete de procedimento |
| `GET` | `/notifications` | Tenant Bearer | notificações in-app paginadas do usuário |
| `PATCH` | `/notifications/{id}/read` | Tenant Bearer | marca notificação como lida |
| `POST` | `/notifications/read-all` | Tenant Bearer | marca todas as notificações como lidas |
| `GET` | `/notifications/preferences` | Tenant Bearer | preferências de notificação do usuário |
| `PUT` | `/notifications/preferences/{module}` | Tenant Bearer | atualiza preferência de um módulo |
| `GET` | `/settings/evolution` | `settings.view` | configuração da Evolution API |
| `POST` | `/settings/evolution` | `settings.edit` | salva configuração da Evolution API |
| `GET` | `/settings/brevo` | `settings.view` | configuração do Brevo (e-mail) |
| `POST` | `/settings/brevo` | `settings.edit` | salva configuração do Brevo |
| `GET` | `/settings/notification-recipients` | `settings.view` | destinatários por módulo |
| `PUT` | `/settings/notification-recipients/{module}` | `settings.edit` | define destinatários de um módulo |
| `POST` | `/attachments` | Tenant Bearer (multipart) | upload de anexo para entidade |
| `GET` | `/attachments?entity_type=&entity_id=` | Tenant Bearer | lista anexos de uma entidade |
| `GET` | `/attachments/{id}/download` | Tenant Bearer | download do arquivo |
| `DELETE` | `/attachments/{id}` | Tenant Bearer | exclui anexo (S3 + banco) |
| `GET` | `/timeline/{entity_type}/{entity_id}` | Tenant Bearer | timeline de alterações do registro |
| `POST` | `/timeline/{entity_type}/{entity_id}/comment` | Tenant Bearer | adiciona comentário ao registro |
| `POST` | `/platform/auth/login` | pública | JWT administrativo isolado |
| `GET` | `/platform/metrics` | Platform Bearer | métricas SaaS agregadas |
| `GET` | `/platform/tenants` | Platform Bearer | empresas e assinatura |
| `GET` | `/platform/plans` | Platform Bearer | catálogo de planos |

### Login

```json
{
  "email": "usuario@empresa.com.br",
  "password": "senha",
  "company_id": 1
}
```

`company_id` é opcional. Se o e-mail pertencer a um único tenant, o login resolve automaticamente. Se pertencer a mais de um, a API retorna `422` com `code: "multi_tenant"` e a lista de empresas disponíveis; o front exibe um seletor e reenvia com `company_id`. O access token expõe `sub`, `company_id`, `role_id`, `permissions`, `type=access`, `iat` e `exp`. O algoritmo aceito é exclusivamente HS256.

No fluxo multitenant, a senha é validada antes da resposta de seleção. A API retorna somente os tenants cujos usuários possuem credencial compatível; senha inválida responde `401` sem revelar empresas. `company_id`, quando informado, deve ser um inteiro positivo.

O token da plataforma contém `type=platform_access` e não é aceito nas rotas tenant. O painel admin o mantém em cookie `httpOnly`; a API continua recebendo Bearer pela conexão server-side.

### Refresh token

O login retorna `access_token` (curta duração, padrão 30min) e `refresh_token` (longa duração, padrão 7 dias, `type=refresh`). O frontend armazena ambos em cookies `httpOnly`. Quando o access token expira (401), o frontend chama `POST /auth/refresh` com o refresh token para obter novos tokens sem pedir a senha novamente. O refresh token contém apenas `sub` e `company_id` (sem permissions) — ao renovar, a API revalida o usuário no banco e gera tokens atualizados.

### Rate limiting

Endpoints sensíveis possuem rate limiting por IP via slowapi:

| Endpoint | Limite |
| --- | --- |
| `POST /auth/login` | 10 req/min |
| `POST /auth/refresh` | 20 req/min |
| `POST /integrations/chess-hotel/users/resolve` | 30 req/min |
| `POST /integrations/chess-hotel/tickets` | 30 req/min |

Exceder o limite retorna `429 Too Many Requests`.

### Arquitetura: service layer

Cada domínio possui um `service.py` com a lógica de negócio separada do router. Os routers lidam apenas com parsing HTTP, validação de input e mapeamento de resposta. Os services recebem session e parâmetros tipados, facilitando reuso (ex: `fiscal_requests.service.create_from_chess()` pode ser chamado por qualquer integração) e testes unitários sem dependência de FastAPI.

### Erros estruturados

```json
{
  "detail": {
    "code": "invalid_credentials",
    "message": "E-mail ou senha inválidos"
  }
}
```

| Código | HTTP | Significado |
| --- | --- | --- |
| `database_unavailable` | 503 | `DATABASE_URL` ausente |
| `invalid_credentials` | 401 | usuário ou senha inválidos |
| `invalid_token` | 401 | JWT inválido, expirado ou de tipo incorreto |
| `inactive_user` | 401 | usuário removido, inativo ou fora da empresa do token |

## Contrato de listas

Todas as listas paginadas respondem `{items, total, page, page_size}` e aceitam `page`, `page_size` e `search` (quando aplicável). Endpoints que seguem este contrato: `/occurrences`, `/fiscal-requests`, `/users`, `/registries`, `/modules/{slug}`, `/procedures` e `/notifications`.

### Ocorrências

O CRUD de ocorrências está operacional. Todas as rotas exigem Tenant Bearer e isolam por `company_id`.

#### `POST /occurrences`

Cria uma ocorrência. O campo `legacy_id` não é enviado — registros criados pelo Registro ficam com `legacy_id` null. Os campos `created_by_user_id` e `updated_by_user_id` são preenchidos automaticamente com o usuário autenticado.

```json
{
  "title": "Revisar vistoria do apartamento 302",
  "description": "Pendência identificada na inspeção de ontem",
  "status": 1,
  "sector_id": 1,
  "location_id": 5,
  "owner_user_id": 2,
  "deadline": "2026-06-25"
}
```

Campos opcionais: `description`, `unit`, `deadline`, `sector_id`, `location_id`, `owner_user_id`. O `status` é inteiro: `1` = Em andamento, `2` = Concluído, `3` = Aguardando (padrão: `1`). Responde `201` com o registro criado, resolvendo `category` (nome do setor), `owner` (nome do usuário) e `location` (nome do local).

#### `PATCH /occurrences/{id}`

Atualiza campos da ocorrência. Aceita qualquer subconjunto de `title`, `description`, `unit`, `deadline`, `status`, `sector_id`, `location_id` e `owner_user_id`. Registra `updated_by_user_id` automaticamente.

#### `DELETE /occurrences/{id}`

Exclusão lógica — preenche `deleted_at` sem destruir o registro. Responde `204`. Retorna `404` se o registro não existir, já estiver excluído ou pertencer a outro tenant.

### Solicitações fiscais

O CRUD de solicitações fiscais está operacional. Todas as rotas exigem Tenant Bearer e isolam por `company_id`.

#### `POST /fiscal-requests`

Cria uma solicitação fiscal autenticada.

```json
{
  "request_type": "Dados do tomador incorretos",
  "title": "Correção CPF hóspede UH 305",
  "apartment": "305",
  "requester": "Ícaro Simoes",
  "description": "CPF informado está incorreto na NF",
  "status": "Em andamento",
  "payload": { "taxpayerDoc": "123.456.789-00" }
}
```

Responde `201` com o registro criado, incluindo `id`, `protocol` (`REG-{id:06d}`), `created_at` e `updated_at`. O campo `payload` armazena campos adicionais específicos do tipo (tomador, reserva, nota, etc.) como JSON.

#### `PATCH /fiscal-requests/{id}`

Atualiza campos da solicitação. Aceita qualquer subconjunto de `request_type`, `title`, `apartment`, `requester`, `description`, `status` e `payload`. Campos não enviados permanecem inalterados.

#### `DELETE /fiscal-requests/{id}`

Exclui a solicitação. Responde `204` sem corpo. Retorna `404` se o registro não existir ou pertencer a outro tenant.

#### `POST /integrations/chess-hotel/tickets`

Endpoint de integração para o Chess Hotel. Autenticado por header `X-Registro-Key` (variável `CHESS_HOTEL_INTEGRATION_KEY`). Resolve o tenant pelo slug configurado em `CHESS_HOTEL_COMPANY_SLUG` e o usuário Registro pelo e-mail do solicitante. Calcula `sla_deadline` (24h corridas) e vincula `requester_user_id`. Retorna protocolo, status, responsável, SLA e URL de acompanhamento.

O payload do Chess Hotel agora inclui `solicitanteEmail`, `chessUserId` e `hotel` além dos campos originais. O campo `reservationNumber` é promovido a coluna própria.

#### SLA inteligente

O `sla_deadline` é calculado em **dias úteis** (seg-sex, 8h-18h) na timezone do tenant (`companies.timezone`). O SLA padrão é 24h úteis (≈2,4 dias corridos). O campo `sla_status` é computado a cada consulta:

| Status | Condição |
| --- | --- |
| `on_time` | mais de 4h úteis restantes |
| `warning` | ≤4h restantes |
| `overdue` | deadline ultrapassado |
| `paused` | registro em status "Em espera" |
| `completed` | status "Concluído" ou "Cancelado" |

**Pausa**: quando o status muda para "Em espera", o SLA é congelado. Ao retomar (qualquer outro status), os segundos pausados são acumulados e descontados do deadline efetivo. Múltiplas pausas são somadas.

**Feriados**: `calculate_business_deadline()` aceita um set de datas (`YYYY-MM-DD`) como feriados. Integração com `CompanySetting` para configuração por tenant está preparada.

#### `POST /integrations/chess-hotel/users/resolve`

Verifica se um e-mail do Chess corresponde a um usuário ativo no tenant do Registro. Retorna `{ "exists": true, "id": 1, "name": "...", "email": "..." }` ou `404` se não encontrar.

#### `GET /integrations/chess-hotel/tickets?email=...`

Lista as últimas 50 solicitações do usuário (por `requester_user_id`) com histórico de auditoria e status de tracking. Retorna o perfil do usuário e array de `FiscalRequestTracking` com protocolo, status, responsável, SLA, flag de conclusão, URL e histórico de eventos.

### Timeline

A timeline agrega eventos de auditoria de um registro e os apresenta como thread de conversa. Lê da tabela `audit_events` e resolve o nome do ator via join com `users`.

#### `GET /timeline/{entity_type}/{entity_id}`

Retorna todos os eventos do registro em ordem cronológica. Cada item inclui `id`, `event_type`, `user` (nome), `message` (para comentários e anexos), `changes` (para updates) e `created_at`.

Entity types válidos: `occurrence`, `fiscal_request`, `procedure`, `meeting`, `shift_report`, `inspecoes`, `diarios-obra`, `manutencao`, `mural`.

Tipos de evento renderizados:

| `event_type` | Exibição |
| --- | --- |
| `create` | "Criou o registro" |
| `update` | Diff campo a campo |
| `delete` | "Excluiu o registro" |
| `comment` | Mensagem livre do usuário |
| `attachment_add` | "Anexou \"{filename}\"" |
| `attachment_remove` | "Removeu anexo \"{filename}\"" |

#### `POST /timeline/{entity_type}/{entity_id}/comment`

Adiciona comentário ao registro. Body: `{ "message": "texto" }`. Dispara notificação para o dono e criador do registro (ocorrências e solicitações fiscais). Responde `201` com o comentário criado.

### Auditoria

Toda mutação em ocorrências, solicitações fiscais, procedimentos e anexos gera um `AuditEvent` com `company_id`, `user_id`, `entity_type`, `entity_id`, `event_type` e `diff` JSON. O diff registra campo a campo o valor anterior e o novo, apenas quando há mudança. Eventos de create e delete não possuem diff. A tabela `audit_events` é imutável por design (sem `updated_at`/`deleted_at`, `created_at` com `server_default=func.now()`).

| `entity_type` | `event_type` | Quando |
| --- | --- | --- |
| `occurrence` | `create` | POST /occurrences |
| `occurrence` | `update` | PATCH /occurrences/{id}, apenas se houve diff |
| `occurrence` | `delete` | DELETE /occurrences/{id} |
| `fiscal_request` | `create` | POST /fiscal-requests |
| `fiscal_request` | `create_from_chess` | POST /integrations/chess-hotel/tickets |
| `fiscal_request` | `update` | PATCH /fiscal-requests/{id}, apenas se houve diff |
| `fiscal_request` | `delete` | DELETE /fiscal-requests/{id} |
| `{entity_type}` | `attachment_add` | POST /attachments — registra filename, content_type e size_bytes |
| `{entity_type}` | `attachment_remove` | DELETE /attachments/{id} — registra filename |

### Validação de campos fiscais

O `payload` de solicitações fiscais valida automaticamente:

| Campo | Regra |
| --- | --- |
| `taxpayerDoc` | CPF (11 dígitos) ou CNPJ (14 dígitos) com verificação de dígitos; normalizado para formato com pontuação |
| `taxpayerEmail` | formato básico de e-mail; normalizado para lowercase e trim |

Valores inválidos retornam `422`.

### Dashboard

`GET /dashboard/metrics` retorna indicadores agregados em tempo real do tenant:

```json
{
  "open_occurrences": 12,
  "my_occurrences": 3,
  "open_fiscal": 4,
  "completed_month": 28,
  "active_users": 26,
  "active_sectors": 5,
  "recent": [
    { "id": 1048, "title": "...", "area": "Governança", "owner": "Marina Costa", "status": "Em andamento", "updated_at": "..." }
  ]
}
```

| Campo | Descrição |
| --- | --- |
| `open_occurrences` | ocorrências com status 1 (em andamento) ou 3 (aguardando) |
| `my_occurrences` | subconjunto de `open_occurrences` atribuídas ao usuário logado |
| `open_fiscal` | solicitações fiscais com status diferente de "Concluído" |
| `completed_month` | ocorrências concluídas (status 2) no mês corrente |
| `active_users` | usuários ativos e não excluídos do tenant |
| `active_sectors` | setores não excluídos do tenant |
| `recent` | últimas 10 ocorrências ordenadas por `updated_at` |

### Usuários

O CRUD de usuários está operacional. Todas as rotas exigem Tenant Bearer e isolam por `company_id`.

#### `GET /users`

Lista usuários do tenant com paginação e busca. Aceita `page`, `page_size` e `search` (busca por nome ou e-mail). Responde `{items, total, page, page_size}`. Cada item inclui `id`, `name`, `email`, `role_name`, `active` e `updated_at`.

#### `POST /users`

Cria um usuário. Requer `name`, `email` e `password`. Opcionais: `role_id` e `active`. A senha é armazenada com hash bcrypt. Retorna `409` se o e-mail já existir no tenant.

#### `PATCH /users/{id}`

Atualiza campos do usuário. Aceita qualquer subconjunto de `name`, `email`, `password`, `role_id` e `active`. Se `password` for enviada, gera novo hash bcrypt.

#### `DELETE /users/{id}`

Exclusão lógica — preenche `deleted_at` e desativa o usuário. Retorna `400` se o usuário tentar excluir a si mesmo.

### Cadastros (Registries)

Endpoint unificado para setores, locais e funções. O campo `category` identifica o tipo: `"Setor"`, `"Local"` ou `"Função"`.

#### `GET /registries`

Lista todos os cadastros do tenant combinados em uma única listagem, com paginação e busca por nome. Responde `{items, total, page, page_size}`.

#### `POST /registries`

Cria um cadastro. Requer `name` e `category`. A `category` deve ser `"Setor"`, `"Local"` ou `"Função"`; valores inválidos retornam `400`.

#### `PATCH /registries/{id}?category=Setor`

Atualiza o nome do cadastro. O `category` é obrigatório como query parameter para identificar a tabela correta.

#### `DELETE /registries/{id}?category=Setor`

Exclusão lógica do cadastro. O `category` é obrigatório como query parameter.

### Módulos genéricos

Endpoint unificado para módulos operacionais que compartilham a mesma estrutura: reuniões, relatórios de turno, inspeções, diário de obra, manutenção e mural. O `{slug}` identifica o módulo.

Slugs válidos: `inspecoes`, `diarios-obra`, `manutencao`, `mural`. (Reuniões e relatórios de turno foram promovidos para tabelas dedicadas — ver `/meetings` e `/shift-reports`.)

Registros importados da V1 possuem `legacy_id` e `payload` JSON com dados ricos (subjects, participants, frequencies, items de conferência, etc.) preservados da estrutura original.

#### `GET /modules/{slug}`

Lista registros do módulo com paginação e busca. Aceita `page`, `page_size` e `search`. Responde `{items, total, page, page_size}`. Cada item inclui `id`, `title`, `description`, `category`, `owner` (nome do usuário), `status` e `updated_at`.

#### `POST /modules/{slug}`

Cria um registro. Requer `title`. Opcionais: `description`, `category`, `status` (padrão: "Em andamento") e `owner_user_id` (padrão: usuário logado).

#### `PATCH /modules/{slug}/{id}`

Atualiza campos do registro. Aceita qualquer subconjunto de `title`, `description`, `category`, `status` e `owner_user_id`.

#### `DELETE /modules/{slug}/{id}`

Exclusão lógica — preenche `deleted_at`. Retorna `404` se o registro não existir, já estiver excluído ou pertencer a outro tenant/módulo.

### Auditoria de novos endpoints

Toda mutação nos novos endpoints (usuários, cadastros e módulos genéricos) gera `AuditEvent`:

| `entity_type` | `event_type` | Quando |
| --- | --- | --- |
| `user` | `create` / `update` / `delete` | CRUD de usuários |
| `registry` | `create` / `delete` | CRUD de cadastros |
| `{module_slug}` | `create` / `update` / `delete` | CRUD de módulos genéricos |

### Perfil do usuário

#### `PATCH /users/me`

Permite ao usuário autenticado editar seu próprio perfil. Aceita `name`, `phone` e `password`. Não permite alterar `email`, `role_id` ou `active` — essas alterações exigem o endpoint administrativo `PATCH /users/{id}`. Se `password` for enviada, gera novo hash bcrypt. Retorna `422` se nenhum campo for enviado.

### Procedimentos

O CRUD de procedimentos está operacional. Todas as rotas exigem Tenant Bearer e isolam por `company_id`.

#### `GET /procedures`

Lista procedimentos do tenant com paginação e busca por nome. Aceita `page`, `page_size` e `search`. Responde `{items, total, page, page_size}`. Cada item inclui `id`, `name`, `link`, `file` e `updated_at`.

#### `POST /procedures`

Cria um procedimento. Requer `name`. Opcionais: `link` e `file`.

#### `PATCH /procedures/{id}`

Atualiza campos do procedimento. Aceita qualquer subconjunto de `name`, `link` e `file`.

#### `DELETE /procedures/{id}`

Exclusão lógica — preenche `deleted_at`. Retorna `404` se o registro não existir, já estiver excluído ou pertencer a outro tenant.

### Notificações in-app

Notificações persistentes por usuário com suporte a leitura e contagem de não lidas.

#### `GET /notifications`

Lista notificações do usuário autenticado com paginação. Aceita `page`, `page_size` e `unread_only` (boolean). Responde `{items, total, unread, page, page_size}`. Cada item inclui `id`, `title`, `body`, `category`, `entity_type`, `entity_id`, `read_at` e `created_at`. O campo `unread` sempre indica o total de não lidas (independente do filtro).

#### `PATCH /notifications/{id}/read`

Marca uma notificação como lida. Idempotente — se já lida, retorna sem alterar. Retorna `404` se a notificação não pertencer ao usuário.

#### `POST /notifications/read-all`

Marca todas as notificações não lidas do usuário como lidas. Responde `204`.

#### `GET /notifications/preferences`

Lista preferências de notificação do usuário autenticado para todos os módulos válidos. Módulos sem preferência salva retornam `in_app: true, email: true` (default). Responde `[{module, in_app, email}]`.

Módulos válidos: `occurrences`, `fiscal_requests`, `meetings`, `shift_reports`, `procedures`, `inspections`, `maintenance`, `modules`.

#### `PUT /notifications/preferences/{module}`

Atualiza preferência de notificação do usuário para um módulo. Body: `{in_app: bool, email: bool}`. Retorna `{module, in_app, email}`. Retorna `400` se o módulo for inválido.

### Destinatários por módulo

Configuração a nível de empresa — define quais usuários recebem notificações de cada módulo por padrão (além dos envolvidos diretos no registro). Armazenado em `company_settings` com chave `notification_recipients`.

#### `GET /settings/notification-recipients`

Lista destinatários configurados para todos os módulos. Requer `settings.view`. Responde `[{module, user_ids}]`.

#### `PUT /settings/notification-recipients/{module}`

Define a lista de `user_ids` que recebem notificações de um módulo. Requer `settings.edit`. Body: `{user_ids: [int]}`. Retorna `{module, user_ids}`. Retorna `400` se o módulo for inválido.

### Registro de entrega

Cada notificação inclui o campo `email_sent_at` (datetime ou null) que indica quando o e-mail correspondente foi enviado com sucesso via Brevo. O campo é preenchido automaticamente pelo sistema — não há endpoint para alterá-lo.

### Auditoria dos novos endpoints

| `entity_type` | `event_type` | Quando |
| --- | --- | --- |
| `procedure` | `create` / `update` / `delete` | CRUD de procedimentos |
| `user` | `update_profile` | PATCH /users/me |

### Anexos

Anexos são armazenados no MinIO (S3-compatible) e referenciados na tabela `attachments` com vínculo polimórfico por `entity_type`/`entity_id`.

#### `POST /attachments?entity_type=fiscal_request&entity_id=42`

Upload multipart. Envia o arquivo no campo `file` do form-data. Query params obrigatórios: `entity_type` e `entity_id`.

Entity types válidos: `fiscal_request`, `occurrence`, `procedure`, `module_record`.

Validações:
- Tamanho máximo: 10MB (configurável via `ATTACHMENT_MAX_SIZE_MB`)
- Máximo por registro: 20 (configurável via `ATTACHMENT_MAX_PER_ENTITY`)
- Extensões permitidas: `.jpg`, `.jpeg`, `.png`, `.gif`, `.webp`, `.svg`, `.pdf`, `.doc`, `.docx`, `.xls`, `.xlsx`, `.csv`, `.txt`, `.zip`, `.rar`, `.7z`
- Content-types correspondentes validados

Responde `201` com metadados do anexo (`id`, `filename`, `content_type`, `size_bytes`, `created_at`). Retorna `422` se o arquivo violar alguma regra.

#### `GET /attachments?entity_type=fiscal_request&entity_id=42`

Lista anexos de uma entidade. Responde `{items, total}`.

#### `GET /attachments/{id}/download`

Download do arquivo com `Content-Disposition: attachment`. Isolado por `company_id` — um tenant não pode baixar anexos de outro.

#### `DELETE /attachments/{id}`

Exclui o anexo do MinIO e do banco. Registra evento de auditoria `attachment_remove` com o nome do arquivo. Responde `204`. Retorna `404` se o anexo não existir ou pertencer a outro tenant.

### ACL (Controle de Acesso)

O sistema de permissões usa uma factory `require_permission(code)` em `app/core/permissions.py`. Cada endpoint protegido declara a permissão necessária via `Annotated[AuthenticatedUser, require_permission("modulo.acao")]`. O JWT já carrega `permissions: list[str]` populadas a partir do role do usuário. A permissão wildcard `*` (Administrador) bypassa todas as verificações.

#### Permissões disponíveis

| Módulo | Códigos |
| --- | --- |
| occurrence | `occurrence.view`, `.create`, `.edit`, `.delete` |
| fiscal_request | `fiscal_request.view`, `.create`, `.edit`, `.delete` |
| user | `user.view`, `.create`, `.edit`, `.delete` |
| registry | `registry.view`, `.create`, `.edit`, `.delete` |
| module | `module.view`, `.create`, `.edit`, `.delete` |
| procedure | `procedure.view`, `.create`, `.edit`, `.delete` |
| settings | `settings.view`, `.edit` |
| meeting | `meeting.view`, `.create`, `.edit`, `.delete` |
| shift_report | `shift_report.view`, `.create`, `.edit`, `.delete` |
| system | `*` (acesso total) |

Sem a permissão necessária, a API retorna `403 Forbidden` com `{"code": "forbidden", "required": "modulo.acao"}`.

#### Roles (`/roles`)

CRUD de cargos por tenant. Cada role tem um conjunto de permissões atribuídas via tabela junction `role_permissions`. A exclusão de um role só é permitida se nenhum usuário estiver atribuído a ele (retorna `409 role_has_users`).

### Ocorrências — funcionalidades adicionais

#### `GET /occurrences/{id}`

Detalhe de uma ocorrência com participantes. Retorna `OccurrenceDetail` com campos adicionais: `unit`, `participants: [{id, name}]` e `notify_user_ids`.

#### `POST /occurrences/{id}/clone`

Duplica uma ocorrência. Copia todos os campos, participantes e notificações. O título recebe prefixo "Cópia de ". O status volta para 1 (Em andamento). Timeline e anexos não são copiados. Retorna `201` com a nova ocorrência.

#### `GET /occurrences/{id}/pdf`

Exporta a ocorrência em PDF (reportlab). Inclui: header com nome da empresa, metadata (status, setor, local, responsável, prazo), descrição, participantes e histórico completo da timeline. Retorna `StreamingResponse` com `Content-Disposition: attachment`.

#### Participantes de ocorrências

A criação e atualização de ocorrências agora aceitam `participant_ids: list[int]` para vincular participantes. Os participantes são armazenados na tabela `occurrence_participants` (junction com chave composta `occurrence_id` + `user_id`).

### Reuniões (`/meetings`)

Reuniões foram promovidas de `module_records` para tabelas dedicadas: `meetings`, `meeting_participants` e `meeting_subjects`. Dados existentes foram migrados automaticamente.

#### Modelo

- `meetings`: title, description, scheduled_at (datetime), location, status, owner_user_id, notify_user_ids, deleted_at
- `meeting_participants`: meeting_id, user_id, role (organizer/attendee/optional)
- `meeting_subjects`: meeting_id, title, description, sort_order, resolved (boolean)

#### `POST /meetings`

Cria uma reunião com participantes e pautas em uma única chamada.

```json
{
  "title": "Alinhamento operacional semanal",
  "scheduled_at": "2026-06-25T09:00:00",
  "location": "Sala de reuniões",
  "status": "Agendada",
  "owner_user_id": 1,
  "participants": [
    {"user_id": 2, "role": "attendee"},
    {"user_id": 3, "role": "organizer"}
  ],
  "subjects": [
    {"title": "Revisão de indicadores", "sort_order": 0},
    {"title": "Planejamento da semana", "sort_order": 1}
  ],
  "notify_user_ids": [2, 3]
}
```

#### Pautas (`/meetings/{id}/subjects`)

- `POST /meetings/{id}/subjects` — adiciona pauta
- `PATCH /meetings/{id}/subjects/{sid}` — atualiza pauta (toggle resolved, reorder)
- `DELETE /meetings/{id}/subjects/{sid}` — remove pauta

#### `POST /meetings/{id}/clone`

Duplica a reunião com participantes e pautas. Título com prefixo "Cópia de ". Status volta para "Agendada".

#### Timeline e anexos

Reuniões usam os mesmos endpoints genéricos de timeline (`entity_type="meeting"`) e anexos. Toda mutação gera `AuditEvent`.

### Relatórios de turno (`/shift-reports`)

Relatórios de turno foram promovidos de `module_records` para a tabela `shift_reports`. Dados existentes foram migrados automaticamente.

#### Modelo

- `shift_reports`: title, description, shift_date (date), shift_type (morning/afternoon/night), status, started_at, ended_at, owner_user_id, notify_user_ids, deleted_at

#### `GET /shift-reports`

Lista relatórios com paginação, busca e filtro por data. Aceita query params opcionais `date_from` e `date_to` (formato `YYYY-MM-DD`).

#### `POST /shift-reports`

```json
{
  "title": "Turno manhã — Bloco A",
  "shift_date": "2026-06-20",
  "shift_type": "morning",
  "status": "Em andamento",
  "owner_user_id": 1,
  "description": "Passagem de turno com pendências do noturno."
}
```

#### Timeline e anexos

Relatórios usam `entity_type="shift_report"` para timeline e anexos.

### Infraestrutura: MinIO

O MinIO é o storage S3-compatible para anexos. Em dev, roda como container Docker (`localhost:9000` API, `localhost:9001` console). O bucket `registro-attachments` é criado automaticamente no startup da API. Em produção, pode ser substituído por AWS S3 ou qualquer storage S3-compatible via variáveis `S3_ENDPOINT_URL`, `S3_ACCESS_KEY`, `S3_SECRET_KEY` e `S3_BUCKET`.
