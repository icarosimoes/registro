# Mapa do sistema

## Estado em 19/06/2026

| Área | Estado | Fonte de dados |
| --- | --- | --- |
| Docker local | operacional | Compose |
| FastAPI | health, readiness e autenticação implementados | MySQL quando configurado |
| Next.js | dashboard responsivo demonstrativo | dados mockados |
| Laravel V1 | preservado somente localmente | MySQL legado |
| Swarm | stack e runbook preparados | GHCR + secrets externos |
| PostgreSQL | planejado após equivalência | ainda não existe |

## Caminhos

| Área | Caminho |
| --- | --- |
| API | `api/app/` |
| testes API | `api/tests/` |
| Web | `web/app/`, `web/components/` |
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

- falta conexão segura com uma base de desenvolvimento;
- falta inventário do schema e dos volumes reais;
- falta tela de login/cookie httpOnly;
- dashboard ainda não consome dados reais;
- falta CI no repositório.
