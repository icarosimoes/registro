# Registro

Sistema de gestão operacional em modernização gradual de Laravel 7 para FastAPI + Next.js.

O Laravel e o MySQL atuais continuam sendo a fonte de verdade durante a transição. A nova aplicação vive na raiz e a versão anterior foi preservada em `docs/v1/`:

- `api/`: FastAPI + SQLAlchemy assíncrono;
- `web/`: Next.js App Router + TypeScript;
- `docs/v1/`: aplicação Laravel legada completa;
- `docs/`: arquitetura, mapa, decisões e registro de trabalho.

## Desenvolvimento da nova aplicação

```bash
# API
cd api
python3 -m venv .venv
.venv/bin/pip install -e '.[dev]'
.venv/bin/uvicorn app.main:app --reload --port 8000

# Web
cd web
npm install
npm run dev
```

Copie `api/.env.example` e `web/.env.example` para os respectivos `.env` locais. Nunca versione credenciais.

Leia a [documentação do projeto](./docs/README.md) antes de alterar arquitetura, banco ou contratos do legado.

## Docker

```bash
docker compose up --build
```

Produção usa exclusivamente Docker Swarm. Consulte o [runbook de deploy](./docs/infra/deploy-swarm.md).
