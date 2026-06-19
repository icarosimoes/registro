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
  └── WorkDiary ──► activities / teams / equipment / observations
```

## Agregados principais

| Agregado | Tabelas centrais | Regras a preservar |
| --- | --- | --- |
| Identidade e acesso | `users`, `roles`, `acls`, `modules`, `role_acl`, `companies` | bcrypt Laravel, status, soft delete, empresa e ACL |
| Plataforma SaaS | `platform_users`, `plans`, `subscriptions`, `invoices`, `platform_audit_logs` | sessão isolada, centavos, estado explícito e auditoria |
| Cadastros | `sectors`, `locals`, `funcs`, `procedures`, `procedure_files` | empresa, anexos e exclusão lógica quando existente |
| Ocorrências | `occurrences`, comentários e participantes | histórico, responsáveis, anexos e exportação |
| Reuniões | `meetings`, assuntos, pautas e participantes | início da reunião, ata, anexos e PDF |
| Turnos | `shift_reports` e tabelas filhas | aprovação/teste, anexos e Excel |
| Inspeções | suites, vistorias, auditorias e itens | versões V1/V2, evidências e exportações |
| Diário de obra | `work_diaries` e tabelas filhas | equipes, atividades, equipamentos e anexos |

## Convenções de dados

- IDs legados são preservados enquanto o MySQL for a fonte de verdade.
- `company_id` deve participar de toda consulta de negócio.
- `deleted_at` significa exclusão lógica; registros apagados não autenticam nem aparecem por padrão.
- Anexos exigem inventário de caminho físico, metadados e política de acesso antes do corte.
- Dinheiro, se surgir em módulos futuros, usa centavos inteiros ou `Decimal`, nunca `float`.
- Usuário da plataforma nunca possui `company_id`; acesso cross-tenant é uma capacidade administrativa separada.
- IDs externos do Asaas são opcionais e únicos quando preenchidos; o Registro mantém suas próprias chaves.
