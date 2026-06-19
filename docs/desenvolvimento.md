# Desenvolvimento local

## Pré-requisitos

- Docker Engine com Compose v2.
- Acesso ao MySQL apenas quando forem necessários dados reais.
- Portas 3000 e 8000 livres.

## Configuração

Crie `.env` na raiz; ele é ignorado pelo Git:

```env
DATABASE_URL=mysql+asyncmy://usuario:senha@host.docker.internal:3306/banco?charset=utf8mb4
JWT_SECRET=gere-uma-chave-com-pelo-menos-32-caracteres
```

Sem `DATABASE_URL`, interface e healthcheck rodam, mas login responde 503 e `/health/ready` informa `not_configured`.

## Comandos

```bash
docker compose up --build -d
docker compose ps
docker compose logs -f api web
docker compose down
```

## Qualidade

```bash
docker compose exec -T -e RUFF_CACHE_DIR=/tmp/ruff api ruff check app tests
docker compose exec -T -e MYPY_CACHE_DIR=/tmp/mypy api mypy app
docker compose exec -T api pytest -q -p no:cacheprovider
docker compose exec -T web npm run typecheck
docker compose exec -T web npm run build
```

## Regras

- Nunca copiar `.env` da V1 para o repositório.
- Não executar migrations no MySQL legado sem inventário, backup e plano de rollback.
- Não editar `docs/v1/`; ela é referência local.
- Toda feature inclui testes proporcionais, documentação e validação Docker.
