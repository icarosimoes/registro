# Inventário da V1 Laravel

Fonte local: `docs/v1/`. Este diretório é ignorado pelo Git e não deve ser usado como dependência de build.

## Dimensão conhecida

- Laravel 7 e PHP 7.2+.
- 131 arquivos de migration.
- 194 views Blade.
- 119 declarações `Route::` em `web.php` e 4 em `api.php`.
- 60 tabelas criadas pelas migrations identificadas.

## Superfícies funcionais

| Prefixo/área | Funções observadas |
| --- | --- |
| autenticação | login, logout, redefinição, usuário inativo |
| `/admin` | usuários, perfis, permissões e configurações |
| `/register` | setores, locais, funções, procedimentos e arquivos |
| `/occurrence` | CRUD, participantes, anexos, clone e PDF |
| `/event/meeting` | reuniões, assuntos, participantes, anexos e ata |
| `/event/shiftreport` | relatórios de turno, validação e Excel |
| `/event/check_suite` | checklists e exportações |
| `/event/inspection_suite` | inspeções e exportações |
| `/event/apartment_inspection*` | vistorias V1/V2 e evidências |
| `/event/audit_report` | auditorias |
| `/event/work_diary` | diário de obra |

## Tabelas por domínio

- Identidade: `users`, `roles`, `acls`, `modules`, `role_acl`, `companies`.
- Geografia: `countries`, `states`, `cities`.
- Cadastros: `sectors`, `locals`, `funcs`, `procedures`, `procedure_files`.
- Ocorrências: `occurrences`, `occurrence_comments`, `occurrence_participants`, `type_occurrences`.
- Reuniões: `meetings`, `participants`, `meeting_subjects`, `meeting_subject_attaches`, `meeting_topics_covereds`, `meeting_new_subjects`, `meeting_invited_participants`, `meeting_registered_participants`.
- Turnos: `shift_reports`, `shift_report_comments`, `shift_report_customer_complaints`, `shift_report_extras`, `shift_report_frequencies`, `shift_report_maintenences`, `shift_report_uploads`.
- Inspeções/auditoria: `check_suites`, `check_suite_items`, `inspection_suites`, `inspection_suite_items`, `apartment_inspections`, `apartment_inspections_v2s`, itens, tipos e anexos, `audit_reports` e três grupos de itens.
- Obra: `work_diaries`, atividades, equipamentos, frequências, observações, turnos e equipes.
- Sistema: `configs`, `config_forms`, `notifications`, `routers`, `failed_jobs`, `password_resets`.

## Riscos a confirmar no banco real

- migrations com alterações repetidas ou reversões incorretas;
- nomes históricos com erros ortográficos preservados no schema;
- nullable, defaults e FKs diferentes entre ambientes;
- arquivos em storage sem referência ou referências sem arquivo;
- queries sem filtro de `company_id`;
- datas inválidas/zero, collations e encodings;
- V1 e V2 de vistoria coexistindo.

O inventário de código não substitui `information_schema`, contagens, checksums e amostras do banco real.
