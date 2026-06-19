# Testes e critérios de aceite

## Pipeline mínimo

| Camada | Comando/validação |
| --- | --- |
| API lint | Ruff |
| API tipos | mypy |
| API testes | pytest |
| Web tipos | `npm run typecheck` |
| Web build | `npm run build` |
| Dependências | auditorias Python e npm |
| Containers | build de targets de produção |
| Swarm | `docker stack config -c docker-stack.yml` |

## Smoke local

```bash
docker compose up --build -d
docker compose ps
curl -fsS http://localhost:8000/api/v1/health
curl -fsS http://localhost:8000/api/v1/health/ready
curl -fsS http://localhost:3000/ >/dev/null
```

Sem banco configurado, readiness `not_configured` é esperado no ambiente de protótipo. Com dados reais, readiness deve ser `ready`.

## Autenticação

- hash Laravel `$2y$` válido e senha incorreta;
- usuário ativo, inativo e soft-deleted;
- token válido, expirado, assinatura errada e tipo errado;
- usuário movido/removido depois da emissão do token;
- mesma ID/consulta tentando acessar outra `company_id`.

## Critério por CRUD

Criar, listar, detalhar, editar, exclusão lógica, paginação, filtros, permissão, cross-tenant, concorrência relevante, estado vazio/erro, anexos e exportações. Comparar contagens e amostras com a V1 antes do corte.
