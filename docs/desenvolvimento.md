# Desenvolvimento local

## Pré-requisitos

- Docker Engine com Compose v2.
- Portas 3000, 3001, 8000, 3307, 9000 e 9001 livres.

## Configuração

Copie o exemplo versionado:

```bash
cp .env.example .env
```

O Compose cria `registro_dev`, executa a migration Alembic e aplica seed fictício na primeira subida.

## Comandos

```bash
docker compose up --build -d
docker compose ps
docker compose logs -f mysql minio api web admin
docker compose down
```

## Qualidade

```bash
docker compose exec -T -e RUFF_CACHE_DIR=/tmp/ruff api ruff check app tests
docker compose exec -T -e MYPY_CACHE_DIR=/tmp/mypy api mypy app
docker compose exec -T api pytest -q -p no:cacheprovider
docker compose exec -T web npm run typecheck
docker compose exec -T web npm run build
docker compose exec -T admin npm run typecheck
docker compose exec -T admin npm run build
```

Para recriar somente os dados fictícios, derrube o ambiente removendo o volume local e suba novamente. Isso apaga o MySQL de desenvolvimento e nunca deve ser usado contra um ambiente com dados úteis.

## Regras

- Nunca copiar `.env` da V1 para o repositório.
- Não executar migrations no MySQL legado sem inventário, backup e plano de rollback.
- Não editar `docs/v1/`; ela é referência local.
- Toda feature inclui testes proporcionais, documentação e validação Docker.
