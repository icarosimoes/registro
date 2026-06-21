# Mapa do sistema

## Estado em 21/06/2026

| Área | Estado | Fonte de dados |
| --- | --- | --- |
| Docker local | operacional | Compose (PostgreSQL + MinIO + API + Web + Admin) |
| PostgreSQL 17 | ativo com RLS em 24 tabelas, asyncpg | banco principal |
| FastAPI | health, auth, dashboard, CRUD completo de todos os domínios operacionais | PostgreSQL via SQLAlchemy async |
| Next.js | portal autenticado, todos os módulos operacionais e dashboard com dados reais | todos os módulos via API |
| Painel admin | sidebar Jarvis, dashboard com stat cards, CRUD de empresas, planos, auditoria | Tailwind 4 + Lucide + API plataforma |
| SaaS/Billing | tenants, planos, assinaturas, faturas, lifecycle trial→suspended | Asaas sandbox + webhook idempotente |
| Asaas | AsaasClient async, webhook autenticado, reconciliação periódica | sandbox configurado |
| Laravel V1 | 66 tabelas restauradas em staging | dump local (MySQL via profile `mysql-import`) |
| Swarm | stack e runbook preparados | GHCR + secrets externos |
| ACL | 35 permissões, roles por empresa, wildcard `*` | seed + CRUD via `/roles` |
| Solicitações fiscais | CRUD via API + integração Chess Hotel + SLA + anexos MinIO | `fiscal_requests` isolada por tenant |
| Integração Chess Hotel | launcher no navbar do Chess enviando para API do Registro | `POST /integrations/chess-hotel/tickets` |
| Inspeções/Obra | check suites, inspection suites, vistorias V2, auditorias, diário de obra | tabelas dedicadas com RLS |
| Reuniões | tabela dedicada com participantes, pautas e ata PDF | `meetings` + filhas |
| Relatórios de turno | tabela dedicada com filtro por data e turno | `shift_reports` |

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
| ADRs | `docs/adr/` |
| documentação | `docs/` |

## Ordem de migração

| Prioridade | Domínio | Estado novo |
| --- | --- | --- |
| 1 | autenticação, usuários, perfis, ACL e empresas | núcleo V1 importado, ACL com 35 permissões |
| 2 | setores, locais, funções e procedimentos | importado, CRUD completo |
| 3 | ocorrências | CRUD completo, participantes, clone, PDF, soft delete |
| 4 | reuniões | tabela dedicada, participantes, pautas, clone, ata PDF |
| 5 | relatórios de turno | tabela dedicada, filtro por data/turno |
| 6 | inspeções e auditorias | check suites, inspection suites, vistorias V2, audit reports — tabelas dedicadas |
| 7 | diário de obra | tabela dedicada com 4 filhas (activities, teams, equipment, observations) |
| transversal | anexos | MinIO (S3-compatible), validação de tamanho/tipo/quantidade |
| transversal | PDF e Excel | reportlab (PDF), openpyxl (Excel) |
| transversal | notificações | in-app com preferências por usuário/módulo, Brevo para email |
| transversal | auditoria (`audit_events`) | operacional em todos os domínios, diff JSON campo a campo |

## Contratos críticos

IDs e relacionamentos existentes, hashes Laravel, status/soft delete, `company_id`, `role_id`, ACL, anexos e formatos operacionais de exportação devem ser preservados até um corte explicitamente validado.

## Acesso de desenvolvimento

Login: `demo@aerohotel.local` / `Registro@123` (tenant Aero Hotel, admin com wildcard `*`).

## Funcionalidades implementadas vs planejadas

### Implementado e operacional

- Auth JWT multitenant com refresh, ACL e 35 permissões
- Todos os domínios operacionais com CRUD completo (ocorrências, reuniões, turnos, inspeções, obra, fiscais)
- Reuniões e turnos em tabelas dedicadas (`meetings`, `shift_reports`)
- `import_v1.py` grava diretamente em tabelas dedicadas (reuniões, turnos)
- Integração Evolution (WhatsApp) — configuração + envio real + status de conexão
- Notificações multicanal: in-app + e-mail (Brevo) + WhatsApp (Evolution)
- Anexos via MinIO com validação completa
- Auditoria imutável com diff JSON
- 70 testes automatizados (SLA, CRUD, cross-tenant, anexos, auditoria)
- RLS em 24 tabelas PostgreSQL

### Planejado / pendente de produção

- Corte do Laravel — depende de dump atualizado + inventário de anexos físicos
- Inventário de anexos/volumes fora do banco na V1
- Dump MySQL atualizado do servidor V1

### Limitações conhecidas

- Inspeções (4497) e manutenção (104) permanecem em `module_records` — frontend usa endpoints genéricos `/modules/{slug}`
- Mural e diário de obra sem dados V1 (tabelas vazias)
- Solicitações fiscais sem dados V1 (apenas criáveis manualmente ou via integração Chess)
