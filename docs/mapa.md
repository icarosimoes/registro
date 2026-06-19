# Mapa do sistema

## Estado em 19/06/2026

| Área | Estado | Fonte de dados |
| --- | --- | --- |
| Docker local | operacional | Compose |
| FastAPI | health, autenticação tenant e API da plataforma | MySQL 8.4 local |
| Next.js | dashboard responsivo demonstrativo | dados mockados |
| Painel admin | login isolado, métricas, tenants e planos | API da plataforma |
| SaaS | tenants, planos, assinaturas e faturas | dados fictícios |
| Asaas | contrato e regras preparados | integração desativada |
| Laravel V1 | preservado somente localmente | MySQL legado |
| Swarm | stack e runbook preparados | GHCR + secrets externos |
| PostgreSQL | planejado após equivalência | ainda não existe |

## Caminhos

| Área | Caminho |
| --- | --- |
| API | `api/app/` |
| testes API | `api/tests/` |
| Web | `web/app/`, `web/components/` |
| Admin SaaS | `admin/app/`, `admin/lib/` |
| Compose | `docker-compose.yml` |
| Swarm | `docker-stack.yml` |
| legado local | `docs/v1/` |
| documentação | `docs/` |

## Ordem de migração

| Prioridade | Domínio | Estado novo |
| --- | --- | --- |
| 1 | autenticação, usuários, perfis, ACL e empresas | autenticação inicial |
| 2 | setores, locais, funções e procedimentos | não iniciado |
| 3 | ocorrências | não iniciado |
| 4 | reuniões | não iniciado |
| 5 | relatórios de turno | não iniciado |
| 6 | inspeções e auditorias | não iniciado |
| 7 | diário de obra | não iniciado |
| transversal | anexos, PDF, Excel, notificações e auditoria | inventário pendente |

## Contratos críticos

IDs e relacionamentos existentes, hashes Laravel, status/soft delete, `company_id`, `role_id`, ACL, anexos e formatos operacionais de exportação devem ser preservados até um corte explicitamente validado.

## Bloqueios atuais

- falta inventário do schema e dos volumes reais;
- dashboard ainda não consome dados reais;
- falta receber e importar um dump sanitizado do legado;
- falta decidir credenciais, ambiente sandbox e política comercial do Asaas;
- falta CI no repositório.
