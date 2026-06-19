# Jarvis — Engenharia

## Arquitetura

- FastAPI por domínio: router → service → repository; schemas nas fronteiras.
- Router não contém regra de negócio; service não conhece HTTP.
- MySQL permanece fonte de verdade até corte formal; nunca dual-write.
- Um módulo tem somente um escritor durante a transição.
- Mudança de schema exige backup, migration controlada e rollback.

## Qualidade

- Ruff, mypy, pytest, typecheck e build devem passar.
- Bug corrigido ganha teste de regressão.
- Query multiempresa ganha teste cross-tenant.
- Erros públicos são estruturados; logs preservam contexto sem segredo.
- Código, documentação e configuração Docker mudam juntos.

## Revisão

Verifique: regra no lugar certo, autorização, isolamento, N+1, paginação, estados da UI, teste negativo, documentação e possibilidade de rollback.
