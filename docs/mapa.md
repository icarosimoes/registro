# Mapa do sistema

## Estado em 20/06/2026

| Área | Estado | Fonte de dados |
| --- | --- | --- |
| Docker local | operacional | Compose |
| FastAPI | health, auth, dashboard, CRUD de ocorrências, fiscais, usuários, cadastros e módulos genéricos | MySQL 8.4 local |
| Next.js | portal autenticado, todos os módulos operacionais e dashboard com dados reais | todos os módulos via API |
| Painel admin | login isolado, métricas, tenants e planos | API da plataforma |
| SaaS | tenants, planos, assinaturas e faturas | dados fictícios |
| Asaas | contrato e regras preparados | integração desativada |
| Laravel V1 | 66 tabelas restauradas em staging | dump local |
| Swarm | stack e runbook preparados | GHCR + secrets externos |
| PostgreSQL | planejado após equivalência | ainda não existe |
| Solicitações fiscais | CRUD via API + integração Chess Hotel | `fiscal_requests` isolada por tenant |
| Integração Chess Hotel | launcher no navbar do Chess enviando para API do Registro | `POST /integrations/chess-hotel/tickets` |

## Caminhos

| Área | Caminho |
| --- | --- |
| API | `api/app/` |
| testes API | `api/tests/` |
| Web | `web/app/`, `web/components/` (`app-layout.tsx` é o shell unificado; `dashboard-shell.tsx` e `operational-module.tsx` renderizam apenas conteúdo) |
| Admin SaaS | `admin/app/`, `admin/lib/` |
| Compose | `docker-compose.yml` |
| Swarm | `docker-stack.yml` |
| legado local | `docs/v1/` |
| CI | `.github/workflows/ci.yml` |
| documentação | `docs/` |

## Ordem de migração

| Prioridade | Domínio | Estado novo |
| --- | --- | --- |
| 1 | autenticação, usuários, perfis, ACL e empresas | núcleo V1 importado |
| 2 | setores, locais, funções e procedimentos | importado |
| 3 | ocorrências | CRUD API completo (soft delete) |
| 4 | reuniões | CRUD API via módulo genérico |
| 5 | relatórios de turno | CRUD API via módulo genérico |
| 6 | inspeções e auditorias | CRUD API via módulo genérico |
| 7 | diário de obra | CRUD API via módulo genérico |
| transversal | anexos, PDF, Excel e notificações | inventário pendente |
| transversal | auditoria (`audit_events`) | operacional para ocorrências e fiscais |

## Contratos críticos

IDs e relacionamentos existentes, hashes Laravel, status/soft delete, `company_id`, `role_id`, ACL, anexos e formatos operacionais de exportação devem ser preservados até um corte explicitamente validado.

## Bloqueios atuais

- falta inventário dos anexos/volumes fora do banco;
- tratativas (comentários) no frontend ainda ficam no `localStorage` — a API grava `audit_events` mas o frontend não os consome;
- anexos fiscais ainda usam Base64 no navegador, sem política de tamanho, tipo ou armazenamento;
- módulos genéricos (reuniões, inspeções, etc.) usam tabela compartilhada `module_records` — quando precisarem de campos específicos, serão promovidos a tabelas próprias;
- falta normalizar os demais domínios preservados na staging;
- falta decidir credenciais, ambiente sandbox e política comercial do Asaas.
