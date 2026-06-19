# Arquitetura do Registro

## Objetivo

Substituir gradualmente o monólito Laravel por uma API FastAPI, produto Next.js e painel administrativo separado, com uma base nova de desenvolvimento e importação posterior do legado.

```text
Navegador
   │
   ├── Produto Next.js :3000 ────────► FastAPI :8000
   ├── Admin Next.js :3001 ──────────► API /platform
   │                                      │
   │                                      └── SQLAlchemy/asyncmy
   │                                              │
   └── Laravel V1 (referência/importação) ────────┤
                                                  ▼
                                            MySQL legado
```

Em produção, Next.js e FastAPI possuem duas réplicas no Swarm, passam pelo Traefik e recebem secrets por Docker Secrets. O MySQL permanece externo à stack.

## Componentes

| Componente | Tecnologia | Responsabilidade | Estado |
| --- | --- | --- | --- |
| `web/` | Next.js 16, React 19, TypeScript | Produto do tenant | protótipo funcional |
| `admin/` | Next.js 16, React 19, TypeScript | Operação da plataforma | login + dashboard |
| `api/` | FastAPI, SQLAlchemy async, Pydantic | contratos tenant e plataforma | fundação SaaS |
| `docs/v1/` | Laravel 7/PHP | sistema operacional anterior | local, somente referência |
| MySQL | 8.4 local / externo em produção | base nova e futura importação | operacional com seed |
| PostgreSQL | futuro | destino após equivalência | não iniciado |

## Organização da API

```text
api/app/
  core/                 configuração, conexão e segurança
  domain/<domínio>/     router, schemas, service e repository
  models/               identidade tenant e plataforma
  seed.py               dados fictícios idempotentes
  main.py               composição do FastAPI e middlewares

api/alembic/            evolução versionada do schema
```

- Router valida HTTP e delega.
- Service contém regra de negócio e não depende de HTTP.
- Repository concentra SQL e isolamento por empresa.
- Schema Pydantic define toda fronteira de entrada e saída.
- Nenhum módulo novo consulta outra empresa sem operação administrativa explícita.

## Organização web e admin

```text
web/app/                rotas App Router e Server Components
web/components/         componentes interativos e reutilizáveis
admin/app/              login e dashboard da plataforma
admin/lib/              acesso server-side à API
```

Server Components são o padrão. Client Components ficam restritos à interação. Rotas internas devem usar `Link`; buscas independentes devem usar `Promise.all`.

## Estratégia strangler

1. Implementar um domínio completo na nova stack.
2. Comparar leitura com a V1 e validar autorização.
3. Definir um único escritor para o domínio.
4. Cortar o menu/rota para a nova versão.
5. Monitorar e manter rollback documentado.

Não existe dual-write. O dump futuro entra primeiro em uma base temporária e passa por mapeamento e validação. O MySQL só será substituído depois que todos os domínios críticos estiverem equivalentes.
