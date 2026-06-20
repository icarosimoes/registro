# Modelo de domínio

Este modelo descreve o schema legado conhecido pelas migrations. Deve ser confirmado contra um dump sanitizado ou o MySQL antes de criar mappings definitivos.

```text
PlatformUser ──► PlatformAuditLog
Plan ──► Subscription ──► Company ──► User ──► Role ──► Permission
                    └────► Invoice

Company
  ├── Sector
  ├── Local
  ├── Func
  ├── Procedure ──► ProcedureFile
  ├── Occurrence
  │     ├── OccurrenceComment
  │     └── OccurrenceParticipant
  ├── Meeting
  │     ├── subjects / topics
  │     ├── invited / registered participants
  │     └── subject attachments
  ├── ShiftReport
  │     ├── frequencies / maintenance / complaints / extras
  │     └── comments / uploads
  ├── InspectionSuite ──► InspectionSuiteItem
  ├── CheckSuite ──► CheckSuiteItem
  ├── ApartmentInspection ──► items / attachments / types
  ├── AuditReport ──► item1 / item2 / item3
  ├── WorkDiary ──► activities / teams / equipment / observations
  ├── AuditEvent (imutável por empresa)
  ├── Notification (in-app por usuário)
  ├── FiscalRequest (persistente) ──► attachments / recipients (planejado)
  └── ModuleRecord (genérico: reuniões, inspeções, turnos, obra, manutenção, mural)
```

## Agregados principais

| Agregado | Tabelas centrais | Regras a preservar |
| --- | --- | --- |
| Identidade e acesso | `users`, `roles`, `acls`, `modules`, `role_acl`, `companies` | bcrypt Laravel, status, soft delete, empresa e ACL |
| Plataforma SaaS | `platform_users`, `plans`, `subscriptions`, `invoices`, `platform_audit_logs` | sessão isolada, centavos, estado explícito e auditoria |
| Núcleo V1 importado | `sectors`, `locations`, `functions`, `procedures`, `occurrences` | tenant `aero-hotel` (Aero Hotel), `legacy_id` (nullable — null em registros criados pelo Registro), soft delete e relações remapeadas |
| Cadastros | `sectors`, `locals`, `funcs`, `procedures`, `procedure_files` | empresa, anexos e exclusão lógica quando existente |
| Ocorrências | `occurrences`, comentários e participantes | CRUD com soft delete, `created_by_user_id`/`updated_by_user_id`, histórico, responsáveis, anexos e exportação |
| Reuniões | `meetings`, assuntos, pautas e participantes | início da reunião, ata, anexos e PDF |
| Turnos | `shift_reports` e tabelas filhas | aprovação/teste, anexos e Excel |
| Inspeções | suites, vistorias, auditorias e itens | versões V1/V2, evidências e exportações |
| Diário de obra | `work_diaries` e tabelas filhas | equipes, atividades, equipamentos e anexos |
| Solicitações fiscais | `fiscal_requests` | tenant, tipo, título, descrição, protocolo único, origin, status, `requester_user_id`, `responsible_user_id`, `sla_deadline`, `chess_user_id`, `reservation_number` e `payload` JSON |
| Módulos genéricos | `module_records` | tenant, `module` (slug), `title`, `description`, `category`, `status`, `owner_user_id`, `legacy_id`, `payload` JSON e soft delete. Compartilhado por reuniões, relatórios de turno, inspeções, diário de obra, manutenção e mural. Registros importados da V1 preservam dados ricos (subjects, participants, frequencies, items) no `payload` |
| Notificações | `notifications` | por tenant e usuário, `title`, `body`, `category`, `entity_type`/`entity_id` (link opcional ao registro), `read_at` para estado de leitura |
| Auditoria | `audit_events` | imutável por tenant, `user_id`, `entity_type`, `entity_id`, `event_type`, `diff` JSON com antes/depois por campo |

## Convenções de dados

- IDs legados são preservados enquanto o MySQL for a fonte de verdade.
- Como IDs novos podem colidir com dados fictícios, a identidade V1 é preservada por `company_id` + `legacy_id`. O campo `legacy_id` é nullable — registros criados pelo Registro ficam com valor null; registros importados da V1 mantêm o ID original.
- `company_id` deve participar de toda consulta de negócio.
- `deleted_at` significa exclusão lógica; registros apagados não autenticam nem aparecem por padrão.
- Anexos exigem inventário de caminho físico, metadados e política de acesso antes do corte.
- Dinheiro, se surgir em módulos futuros, usa centavos inteiros ou `Decimal`, nunca `float`.
- Usuário da plataforma nunca possui `company_id`; acesso cross-tenant é uma capacidade administrativa separada.
- IDs externos do Asaas são opcionais e únicos quando preenchidos; o Registro mantém suas próprias chaves.
- Toda mutação em ocorrências e solicitações fiscais gera automaticamente um `AuditEvent` com `user_id`, `company_id`, `entity_type`, `entity_id`, `event_type` e `diff` JSON. O diff registra apenas campos que mudaram, com valor anterior e novo. No front, a timeline local (`history[]`) será substituída pela leitura dos `audit_events` da API.
- Solicitações fiscais possuem modelo persistente (`fiscal_requests`) com `company_id`, `protocol`, `request_type`, `title`, `apartment`, `requester`, `requester_email`, `requester_user_id`, `responsible_user_id`, `chess_user_id`, `reservation_number`, `sla_deadline`, `description`, `origin`, `status` e `payload` JSON para campos específicos do tipo. O protocolo é gerado como `REG-{id:06d}`. O campo `origin` distingue registros criados pelo Registro (`registro`) dos criados pela integração Chess Hotel (`chess-hotel`). CPF/CNPJ e e-mail do tomador no `payload` são validados e normalizados na criação e atualização. Anexos permanecem planejados.
- Procedimentos possuem CRUD completo via `/procedures` com `name`, `link`, `file` e soft delete. Seguem o mesmo padrão de isolamento por `company_id` e auditoria dos demais módulos.
- Notificações in-app são persistidas na tabela `notifications` com `company_id`, `user_id`, `title`, `body`, `category` (default `info`), `entity_type`/`entity_id` opcionais para link ao registro de origem, e `read_at` para estado de leitura. A criação programática é feita via `create_notification()` em `app/domain/notifications/service.py`.
