# Referência da API

Base local: `http://localhost:8000/api/v1`. OpenAPI: `http://localhost:8000/docs` fora de produção.

## Endpoints implementados

| Método | Rota | Autenticação | Resultado |
| --- | --- | --- | --- |
| `GET` | `/health` | pública | processo FastAPI está vivo |
| `GET` | `/health/ready` | pública | conexão do banco pronta ou não configurada |
| `POST` | `/auth/login` | pública | JWT tenant e perfil |
| `GET` | `/auth/me` | Bearer | perfil revalidado no MySQL |
| `GET` | `/occurrences` | Tenant Bearer | ocorrências paginadas e isoladas por empresa |
| `POST` | `/occurrences` | Tenant Bearer | cria ocorrência |
| `PATCH` | `/occurrences/{id}` | Tenant Bearer | atualiza ocorrência |
| `DELETE` | `/occurrences/{id}` | Tenant Bearer | soft delete de ocorrência |
| `POST` | `/integrations/chess-hotel/users/resolve` | `X-Registro-Key` | resolve usuário Chess no Registro por e-mail |
| `POST` | `/integrations/chess-hotel/tickets` | `X-Registro-Key` | cria solicitação fiscal via integração Chess Hotel |
| `GET` | `/integrations/chess-hotel/tickets` | `X-Registro-Key` | lista solicitações do usuário Chess com tracking |
| `GET` | `/fiscal-requests` | Tenant Bearer | solicitações fiscais do tenant |
| `POST` | `/fiscal-requests` | Tenant Bearer | cria solicitação fiscal |
| `PATCH` | `/fiscal-requests/{id}` | Tenant Bearer | atualiza solicitação fiscal |
| `DELETE` | `/fiscal-requests/{id}` | Tenant Bearer | exclui solicitação fiscal |
| `GET` | `/dashboard/metrics` | Tenant Bearer | métricas agregadas do dashboard |
| `GET` | `/users` | Tenant Bearer | usuários paginados do tenant |
| `POST` | `/users` | Tenant Bearer | cria usuário |
| `PATCH` | `/users/{id}` | Tenant Bearer | atualiza usuário |
| `DELETE` | `/users/{id}` | Tenant Bearer | soft delete de usuário |
| `GET` | `/registries` | Tenant Bearer | cadastros (setores, locais e funções) |
| `POST` | `/registries` | Tenant Bearer | cria cadastro |
| `PATCH` | `/registries/{id}?category=` | Tenant Bearer | atualiza cadastro |
| `DELETE` | `/registries/{id}?category=` | Tenant Bearer | soft delete de cadastro |
| `GET` | `/modules/{slug}` | Tenant Bearer | registros genéricos paginados |
| `POST` | `/modules/{slug}` | Tenant Bearer | cria registro genérico |
| `PATCH` | `/modules/{slug}/{id}` | Tenant Bearer | atualiza registro genérico |
| `DELETE` | `/modules/{slug}/{id}` | Tenant Bearer | soft delete de registro genérico |
| `GET` | `/users/search?q=` | Tenant Bearer | autocomplete de usuários ativos (max 10) |
| `GET` | `/settings/evolution` | Tenant Bearer | configuração da Evolution API |
| `POST` | `/settings/evolution` | Tenant Bearer | salva configuração da Evolution API |
| `GET` | `/settings/brevo` | Tenant Bearer | configuração do Brevo (e-mail) |
| `POST` | `/settings/brevo` | Tenant Bearer | salva configuração do Brevo |
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

`company_id` é opcional. Se o e-mail pertencer a um único tenant, o login resolve automaticamente. Se pertencer a mais de um, a API retorna `422` com `code: "multi_tenant"` e a lista de empresas disponíveis; o front exibe um seletor e reenvia com `company_id`. O token expõe `sub`, `company_id`, `role_id`, `permissions`, `type`, `iat` e `exp`. O algoritmo aceito é exclusivamente HS256.

No fluxo multitenant, a senha é validada antes da resposta de seleção. A API retorna somente os tenants cujos usuários possuem credencial compatível; senha inválida responde `401` sem revelar empresas. `company_id`, quando informado, deve ser um inteiro positivo.

O token da plataforma contém `type=platform_access` e não é aceito nas rotas tenant. O painel admin o mantém em cookie `httpOnly`; a API continua recebendo Bearer pela conexão server-side.

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

`GET /occurrences` responde `{items, total, page, page_size}` e aceita `page`, `page_size` e `search`. Demais listas seguirão o mesmo contrato.

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

#### `POST /integrations/chess-hotel/users/resolve`

Verifica se um e-mail do Chess corresponde a um usuário ativo no tenant do Registro. Retorna `{ "exists": true, "id": 1, "name": "...", "email": "..." }` ou `404` se não encontrar.

#### `GET /integrations/chess-hotel/tickets?email=...`

Lista as últimas 50 solicitações do usuário (por `requester_user_id`) com histórico de auditoria e status de tracking. Retorna o perfil do usuário e array de `FiscalRequestTracking` com protocolo, status, responsável, SLA, flag de conclusão, URL e histórico de eventos.

### Auditoria

Toda mutação em ocorrências e solicitações fiscais gera um `AuditEvent` com `company_id`, `user_id`, `entity_type`, `entity_id`, `event_type` e `diff` JSON. O diff registra campo a campo o valor anterior e o novo, apenas quando há mudança. Eventos de create e delete não possuem diff.

| `entity_type` | `event_type` | Quando |
| --- | --- | --- |
| `occurrence` | `create` | POST /occurrences |
| `occurrence` | `update` | PATCH /occurrences/{id}, apenas se houve diff |
| `occurrence` | `delete` | DELETE /occurrences/{id} |
| `fiscal_request` | `create` | POST /fiscal-requests |
| `fiscal_request` | `create_from_chess` | POST /integrations/chess-hotel/tickets |
| `fiscal_request` | `update` | PATCH /fiscal-requests/{id}, apenas se houve diff |
| `fiscal_request` | `delete` | DELETE /fiscal-requests/{id} |

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

Slugs válidos: `reunioes`, `relatorios-turno`, `inspecoes`, `diarios-obra`, `manutencao`, `mural`.

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

Não existem endpoints de anexos ou notificações. Essas funcionalidades permanecem planejadas.
