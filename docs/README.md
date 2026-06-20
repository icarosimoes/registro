# Documentação do Registro

Esta pasta é a fonte de verdade técnica, funcional e operacional do Registro. A nova plataforma usa FastAPI, dois frontends Next.js e MySQL, nasce multitenant e preserva uma rota controlada para importar o legado e, depois, migrar para PostgreSQL.

## Comece aqui

- [Estado atual e mapa do sistema](mapa.md)
- [Arquitetura](arquitetura.md)
- [Modelo de domínio](domain-model.md)
- [API atual](api-reference.md)
- [Rotas e estados da interface](web-rotas-ui.md)
- [Como desenvolver](desenvolvimento.md)
- [Backlog e critérios de corte](backlog.md)
- [Segurança](seguranca.md)
- [Plataforma SaaS e painel administrativo](plataforma-saas.md)
- [Integração futura com Asaas](integracoes/asaas.md)

## Migração e legado

- [Inventário da V1 Laravel](legado/inventario-v1.md)
- [Estratégia FastAPI + Next.js](infra/migracao-fastapi-nextjs.md)
- [Plano MySQL → PostgreSQL](infra/migracao-banco.md)
- [Importação futura do dump Laravel](infra/importacao-legado.md)
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

Toda informação pertinente ao desenvolvimento ou ao sistema deve permanecer em `/docs`. Mudanças em contrato de API, schema, autenticação, menu, CRUD, deploy, secrets ou operação devem atualizar o documento correspondente e o `registro-trabalho.md` durante o mesmo trabalho e, quando houver commit, no mesmo commit. Decisões duráveis também atualizam `memoria-projeto.md`; riscos e pendências atualizam `backlog.md`.
