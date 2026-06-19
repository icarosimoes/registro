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
| `POST` | `/platform/auth/login` | pública | JWT administrativo isolado |
| `GET` | `/platform/metrics` | Platform Bearer | métricas SaaS agregadas |
| `GET` | `/platform/tenants` | Platform Bearer | empresas e assinatura |
| `GET` | `/platform/plans` | Platform Bearer | catálogo de planos |

### Login

```json
{
  "email": "usuario@empresa.com.br",
  "password": "senha",
  "company_slug": "empresa-demo"
}
```

`company_slug` é opcional quando o e-mail identifica uma única empresa. Se houver o mesmo e-mail em mais de um tenant, sua ausência recusa o login. O token expõe `sub`, `company_id`, `role_id`, `permissions`, `type`, `iat` e `exp`. O algoritmo aceito é exclusivamente HS256.

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

`GET /occurrences` responde `{items, total, page, page_size}` e aceita `page`, `page_size` e `search`. Demais listas seguirão o mesmo contrato. Mutações não serão publicadas antes de autorização, validação e teste de isolamento por empresa.
