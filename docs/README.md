# Registro — Documentação

O Registro está sendo migrado de Laravel 7 para FastAPI e Next.js. O Laravel legado foi preservado em `docs/v1/` e o MySQL atual continua sendo a fonte de verdade durante a transição.

## Atalhos

- [Mapa do sistema](./mapa.md)
- [Memória e decisões](./memoria-projeto.md)
- [Registro de trabalho](./registro-trabalho.md)
- [Plano de migração](./infra/migracao-fastapi-nextjs.md)
- [Deploy Docker Swarm](./infra/deploy-swarm.md)
- [Agentes Jarvis](./agentes/README.md)

## Estrutura durante a transição

```text
docs/v1/                  Laravel legado preservado
api/                       nova API FastAPI
web/                       nova interface Next.js
docs/                      memória e operação do projeto
```

Nenhum segredo deve ser versionado. Use os arquivos `.env.example` como contrato de configuração.
