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
  └── FiscalRequest (persistente) ──► attachments / timeline / recipients (planejado)
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
| Solicitações fiscais | `fiscal_requests` | tenant, tipo, título, descrição, reserva/nota/tomador em `payload` JSON, protocolo único, origin e status |

## Convenções de dados

- IDs legados são preservados enquanto o MySQL for a fonte de verdade.
- Como IDs novos podem colidir com dados fictícios, a identidade V1 é preservada por `company_id` + `legacy_id`. O campo `legacy_id` é nullable — registros criados pelo Registro ficam com valor null; registros importados da V1 mantêm o ID original.
- `company_id` deve participar de toda consulta de negócio.
- `deleted_at` significa exclusão lógica; registros apagados não autenticam nem aparecem por padrão.
- Anexos exigem inventário de caminho físico, metadados e política de acesso antes do corte.
- Dinheiro, se surgir em módulos futuros, usa centavos inteiros ou `Decimal`, nunca `float`.
- Usuário da plataforma nunca possui `company_id`; acesso cross-tenant é uma capacidade administrativa separada.
- IDs externos do Asaas são opcionais e únicos quando preenchidos; o Registro mantém suas próprias chaves.
- Toda mutação em registros operacionais deve gerar uma entrada de histórico com usuário, data/hora e campos alterados. No front, o histórico fica em `history[]` dentro do registro; na API, será persistido em tabela de auditoria isolada por empresa.
- Solicitações fiscais possuem modelo persistente (`fiscal_requests`) com `company_id`, `protocol`, `request_type`, `title`, `apartment`, `requester`, `description`, `origin`, `status` e `payload` JSON para campos específicos do tipo. O protocolo é gerado como `REG-{id:06d}`. O campo `origin` distingue registros criados pelo Registro (`registro`) dos criados pela integração Chess Hotel (`chess-hotel`). SLA, anexos, destinatários e auditoria permanecem planejados e devem usar referências por ID, cálculo no servidor e armazenamento fora do payload principal.
