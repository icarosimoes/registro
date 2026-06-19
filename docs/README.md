# Documentação do Registro

Esta pasta é a fonte de verdade técnica, funcional e operacional do Registro. A aplicação está em migração gradual de Laravel 7 para FastAPI e Next.js, mantendo o MySQL legado até a equivalência funcional.

## Comece aqui

- [Estado atual e mapa do sistema](mapa.md)
- [Arquitetura](arquitetura.md)
- [Modelo de domínio](domain-model.md)
- [API atual](api-reference.md)
- [Rotas e estados da interface](web-rotas-ui.md)
- [Como desenvolver](desenvolvimento.md)
- [Backlog e critérios de corte](backlog.md)
- [Segurança](seguranca.md)

## Migração e legado

- [Inventário da V1 Laravel](legado/inventario-v1.md)
- [Estratégia FastAPI + Next.js](infra/migracao-fastapi-nextjs.md)
- [Plano MySQL → PostgreSQL](infra/migracao-banco.md)
- A V1 completa permanece somente no disco local em `docs/v1/` e é ignorada pelo Git.

## Operação

- [Docker Swarm e deploy](infra/deploy-swarm.md)
- [Runbook de produção](infra/runbook-producao.md)
- [Testes e critérios de aceite](infra/testes-integracao.md)

## Padrões e memória

- [Padrão de documentação](padroes/documentacao-projeto.md)
- [Agentes Jarvis aplicáveis](agentes/README.md)
- [Decisões técnicas](memoria-projeto.md)
- [Registro cronológico](registro-trabalho.md)

## Regra de atualização

Toda mudança em contrato de API, schema, autenticação, menu, CRUD, deploy, secrets ou operação deve atualizar o documento correspondente e o `registro-trabalho.md` no mesmo commit.
