# Arquitetura do Registro

## Objetivo

Substituir gradualmente o monólito Laravel por uma API FastAPI e uma interface Next.js, sem interromper a operação e sem trocar o banco durante a reescrita.

```text
Navegador
   │
   ├── Next.js :3000 ──HTTP interno──► FastAPI :8000
   │                                      │
   │                                      └── SQLAlchemy/asyncmy
   │                                              │
   └── Laravel V1 (durante transição) ────────────┤
                                                  ▼
                                            MySQL legado
```

Em produção, Next.js e FastAPI possuem duas réplicas no Swarm, passam pelo Traefik e recebem secrets por Docker Secrets. O MySQL permanece externo à stack.

## Componentes

| Componente | Tecnologia | Responsabilidade | Estado |
| --- | --- | --- | --- |
| `web/` | Next.js 16, React 19, TypeScript | Shell e experiência web | protótipo funcional |
| `api/` | FastAPI, SQLAlchemy async, Pydantic | contratos e regras novas | fundação + autenticação |
| `docs/v1/` | Laravel 7/PHP | sistema operacional anterior | local, somente referência |
| MySQL | banco externo | fonte de verdade atual | obrigatório para dados reais |
| PostgreSQL | futuro | destino após equivalência | não iniciado |

## Organização da API

```text
api/app/
  core/                 configuração, conexão e segurança
  domain/<domínio>/     router, schemas, service e repository
  main.py               composição do FastAPI e middlewares
```

- Router valida HTTP e delega.
- Service contém regra de negócio e não depende de HTTP.
- Repository concentra SQL e isolamento por empresa.
- Schema Pydantic define toda fronteira de entrada e saída.
- Nenhum módulo novo consulta outra empresa sem operação administrativa explícita.

## Organização web

```text
web/app/                rotas App Router e Server Components
web/components/         componentes interativos e reutilizáveis
```

Server Components são o padrão. Client Components ficam restritos à interação. Rotas internas devem usar `Link`; buscas independentes devem usar `Promise.all`.

## Estratégia strangler

1. Implementar um domínio completo na nova stack.
2. Comparar leitura com a V1 e validar autorização.
3. Definir um único escritor para o domínio.
4. Cortar o menu/rota para a nova versão.
5. Monitorar e manter rollback documentado.

Não existe dual-write. O MySQL só será substituído depois que todos os domínios críticos estiverem equivalentes.
