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

Não existem endpoints de anexos ou notificações. Essas funcionalidades permanecem planejadas.
