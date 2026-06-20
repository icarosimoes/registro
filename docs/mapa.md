# Mapa do sistema

## Estado em 20/06/2026

| Área | Estado | Fonte de dados |
| --- | --- | --- |
| Docker local | operacional | Compose |
| FastAPI | health, autenticação tenant/plataforma e leitura de ocorrências | MySQL 8.4 local |
| Next.js | portal autenticado, módulos operacionais e dashboard | ocorrências via API; demais módulos locais/mockados |
| Painel admin | login isolado, métricas, tenants e planos | API da plataforma |
| SaaS | tenants, planos, assinaturas e faturas | dados fictícios |
| Asaas | contrato e regras preparados | integração desativada |
| Laravel V1 | 66 tabelas restauradas em staging | dump local |
| Swarm | stack e runbook preparados | GHCR + secrets externos |
| PostgreSQL | planejado após equivalência | ainda não existe |
| Solicitações fiscais | protótipo funcional no frontend | `localStorage`; backend planejado |

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
| 1 | autenticação, usuários, perfis, ACL e empresas | núcleo V1 importado |
| 2 | setores, locais, funções e procedimentos | importado |
| 3 | ocorrências | leitura API implementada |
| 4 | reuniões | não iniciado |
| 5 | relatórios de turno | não iniciado |
| 6 | inspeções e auditorias | não iniciado |
| 7 | diário de obra | não iniciado |
| transversal | anexos, PDF, Excel, notificações e auditoria | inventário pendente |

## Contratos críticos

IDs e relacionamentos existentes, hashes Laravel, status/soft delete, `company_id`, `role_id`, ACL, anexos e formatos operacionais de exportação devem ser preservados até um corte explicitamente validado.

## Bloqueios atuais

- falta inventário dos anexos/volumes fora do banco;
- dashboard ainda não consome indicadores reais;
- ocorrências ainda precisam de paginação/busca server-side sob demanda para volumes altos;
- tratativas, mutações e solicitações fiscais ainda não são persistidas na API;
- anexos fiscais ainda usam Base64 no navegador, sem política de tamanho, tipo ou armazenamento;
- falta normalizar os demais domínios preservados na staging;
- falta decidir credenciais, ambiente sandbox e política comercial do Asaas;
- falta CI no repositório.
