# Usuários e perfis de acesso

## Modelo de dados

### User (tabela `users`)

| Campo | Tipo | Descrição |
| --- | --- | --- |
| `id` | int PK | identificador |
| `company_id` | int FK | tenant (isolamento por empresa) |
| `role_id` | int FK nullable | perfil de acesso vinculado |
| `name` | varchar(160) | nome completo |
| `email` | varchar(255) | e-mail (único por empresa) |
| `phone` | varchar(20) nullable | telefone |
| `password` | varchar(255) | hash bcrypt |
| `job_title` | varchar(120) nullable | cargo/função (ex: Recepcionista, Gerente Geral) |
| `sector_id` | int FK nullable | setor vinculado (FK → `sectors.id`, SET NULL) |
| `avatar_url` | varchar(500) nullable | URL da foto no MinIO |
| `active` | bool | status ativo/inativo |
| `email_verified_at` | datetime nullable | preenchido ao definir senha via convite |
| `deleted_at` | datetime nullable | soft delete |

### Role (tabela `roles`)

| Campo | Tipo | Descrição |
| --- | --- | --- |
| `id` | int PK | identificador |
| `company_id` | int FK | tenant |
| `code` | varchar(80) | código único por empresa (ex: `gerente`) |
| `name` | varchar(120) | nome exibido (ex: Gerente) |
| `permissions` | M2M | via tabela junction `role_permissions` |

### Permission (tabela `permissions`)

Tabela global (não tenant-scoped). 33 permissões + wildcard `*`.

Formato do código: `{módulo}.{ação}` — ex: `occurrence.create`, `user.delete`.

Módulos: `occurrence`, `fiscal_request`, `user`, `registry`, `module`, `procedure`, `settings`, `meeting`, `shift_report`, `system`.

## Perfis pré-definidos (seed)

Criados automaticamente por empresa via migration `20260621_0039`:

| Código | Nome | Permissões |
| --- | --- | --- |
| `admin` | Administrador | `*` (acesso total) |
| `gerente` | Gerente | Todas exceto `settings.edit` e `user.delete` |
| `recepcao` | Recepção | `occurrence.*`, `fiscal_request.*`, `registry.view`, `meeting.view`, `shift_report.*` |
| `governanca` | Governança | `occurrence.*`, `registry.view`, `procedure.view`, `shift_report.view` |
| `manutencao` | Manutenção | `occurrence.view/create/edit`, `registry.view`, `procedure.view` |
| `financeiro` | Financeiro | `fiscal_request.*`, `settings.view`, `occurrence.view` |

## Fluxo de convite por e-mail

1. Admin chama `POST /users/invite` com nome, e-mail, perfil e setor
2. Sistema cria o usuário com senha aleatória (nunca exposta)
3. Gera um JWT tipo `invite` com expiração de 48h
4. Envia e-mail via Brevo com link: `{REGISTRO_WEB_URL}/definir-senha?token=...`
5. Usuário acessa a página pública `/definir-senha`, define sua senha
6. `POST /auth/set-password` valida o token, salva a senha e seta `email_verified_at`
7. Usuário é redirecionado ao login

### Segurança do convite

- Token JWT com `type: "invite"`, `sub: user_id`, `company_id` — não reutilizável entre tenants
- Rate limit: 10 convites/min (envio), 5 tentativas/min (set-password)
- Expiração de 48h hardcoded
- Senha validada com regras (mín 8 chars, letra + dígito)

## Upload de avatar

- Endpoint: `POST /users/{id}/avatar` (multipart/form-data)
- Tipos aceitos: JPEG, PNG, WebP
- Tamanho máximo: 2MB
- Armazenamento: MinIO (chave: `{company_id}/avatar/{user_id}/{uuid}.ext`)
- URL pública salva no campo `avatar_url` do User

## Endpoints

| Método | Rota | Permissão | Descrição |
| --- | --- | --- | --- |
| `PATCH` | `/users/me` | autenticado | edita perfil próprio |
| `GET` | `/users` | `user.view` | lista paginada com role_name e sector_name |
| `GET` | `/users/search?q=` | `user.view` | autocomplete (max 10) |
| `POST` | `/users` | `user.create` | cria com senha |
| `POST` | `/users/invite` | `user.create` | cria e envia convite |
| `POST` | `/users/{id}/avatar` | `user.edit` | upload de foto |
| `PATCH` | `/users/{id}` | `user.edit` | atualiza campos |
| `DELETE` | `/users/{id}` | `user.delete` | soft delete |
| `POST` | `/auth/set-password` | pública | define senha via token de convite |

## Frontend

### `/usuarios` — Cadastro de usuários

Formulário com campos: nome, e-mail, telefone, cargo, perfil de acesso (select), setor (select), status, senha. No modo criação, toggle "Convidar por e-mail" esconde o campo de senha e usa o fluxo de convite.

Dados de perfis e setores são carregados no server component e passados via `extraData` no `ModuleDefinition`.

### `/perfis` — Gerenciamento de perfis de acesso

Tela dedicada com:
- Lista de perfis com nome, código, contagem de usuários e quantidade de permissões
- Modal de criação/edição com nome, código e checkboxes de permissões agrupadas por módulo
- Toggle "selecionar todas" por grupo de módulo
- Exclusão de perfil (bloqueada se tem usuários atribuídos)

### `/definir-senha` — Página pública

Formulário com campos de senha e confirmação. Recebe o token via query param. Redireciona para `/login` após sucesso.
