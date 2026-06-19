# Referência da API

Base local: `http://localhost:8000/api/v1`. OpenAPI: `http://localhost:8000/docs` fora de produção.

## Endpoints implementados

| Método | Rota | Autenticação | Resultado |
| --- | --- | --- | --- |
| `GET` | `/health` | pública | processo FastAPI está vivo |
| `GET` | `/health/ready` | pública | conexão do banco pronta ou não configurada |
| `POST` | `/auth/login` | pública | JWT de acesso e perfil legado |
| `GET` | `/auth/me` | Bearer | perfil revalidado no MySQL |

### Login

```json
{
  "email": "usuario@empresa.com.br",
  "password": "senha"
}
```

O token expõe `sub`, `company_id`, `role_id`, `permissions`, `type`, `iat` e `exp`. O algoritmo aceito é exclusivamente HS256. A senha nunca é devolvida ou registrada.

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

## Contrato de listas futuro

Listas deverão responder `{items, total, page, page_size}` e aceitar filtros explícitos. CRUDs não serão publicados antes de autorização, paginação, estado vazio/erro e teste de isolamento por empresa.
