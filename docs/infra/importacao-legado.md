# Importação do Laravel

O dump `docs/aero-2026-06-19.sql` foi restaurado em `registro_v1` e seu núcleo foi importado para o schema novo sem sobrescrever os dados SaaS.

## Estado em 21/06/2026

| Conjunto | Quantidade | Destino |
| --- | ---: | --- |
| tabelas brutas | 66 | staging MySQL temporário (profile `mysql-import`) |
| usuários | 59 | `registro.users` (PostgreSQL) |
| setores | 17 | `registro.sectors` |
| locais | 69 | `registro.locations` |
| funções | 13 | `registro.functions` |
| procedimentos | 6 | `registro.procedures` |
| ocorrências | 375 | `registro.occurrences` |
| reuniões | 72 | `registro.meetings` + participantes + pautas |
| relatórios de turno | 1165 | `registro.shift_reports` |
| check suites | 4497 | `registro.check_suites` + items |
| auditorias | 104 | `registro.audit_reports` + items |
| notificações legadas | 3336 | `registro.notifications` |

A tabela de empresas da V1 está vazia e todos os usuários possuem `company_id` nulo. O importador associa esses dados ao tenant sintético `aero-hotel`, mantém os IDs antigos em `legacy_id` e preserva os hashes bcrypt Laravel. Usuários ativos entram com e-mail e senha da V1; não existe campo de slug na interface. Se o mesmo e-mail estiver ativo em mais de um tenant, a interface solicita a escolha da empresa.

Para navegação local existe um usuário adicional, fora dos 59 importados: `v1-demo@registro.local` / `Registro@123`. Ele não é criado se `LEGACY_DEMO_PASSWORD` não for informado ao importador.

```bash
bash scripts/import-v1.sh docs/aero-2026-06-19.sql
```

O comando recria somente a staging. No destino, o ETL é idempotente por `(company_id, legacy_id)` e registra o checksum em `legacy_import_runs`.

## Procedimento seguro

1. Receber dump criptografado ou sanitizado e registrar versão, origem e checksum. **Concluído localmente.**
2. Restaurar em banco temporário separado, sem acesso público. **Concluído em `registro_v1`.**
3. Inventariar tabelas, collation, IDs, FKs, soft deletes, hashes `$2y$`, anexos e volumes.
4. Produzir mapa explícito `legado → novo`, incluindo transformação de status e `company_id`.
5. Executar importador repetível em dry-run, preservando IDs quando o contrato exigir.
6. Comparar contagens, somatórios, órfãos, amostras e autorização por tenant.
7. Ensaiar tempo de corte e rollback antes de promover os dados.

## Regras

- O dump nunca é versionado nem incluído em imagem Docker.
- O banco temporário usa credencial e rede próprias.
- Não há dual-write implícito.
- Senhas Laravel bcrypt são preservadas; rehash só ocorre após login bem-sucedido e decisão explícita.
- Arquivos precisam de inventário e checksum separado do banco.
- O importador grava checkpoint e relatório, permitindo reinício sem duplicação.

## Plano de produção — Aero Hotel

O tenant `aero-hotel` é o cliente real. A base MySQL da V1 continua em operação no servidor de produção atual. Quando for hora de migrar:

1. Gerar dump fresco do MySQL de produção da V1 (com dados reais e atualizados).
2. Restaurar em staging temporária e reexecutar o importador com o novo checksum.
3. O ETL é idempotente por `(company_id, legacy_id)` — registros já importados são atualizados, novos são inseridos.
4. Validar contagens, hashes, permissões e ocorrências contra a V1 em produção.
5. Cortar o acesso da V1 e promover a nova stack como sistema principal.

O dump de desenvolvimento (`aero-2026-06-19.sql`) é um snapshot de referência. O dump de produção final será mais recente e potencialmente maior.

## Banco destino

O banco destino é **PostgreSQL 17** com RLS (Row-Level Security) em 24 tabelas. O MySQL só é utilizado como fonte temporária para leitura do dump V1. O procedimento completo de migração está documentado em [migracao-postgresql.md](../migracao-postgresql.md).

## Pendente para corte final

1. Puxar dump MySQL atualizado do servidor V1 em produção.
2. Executar o procedimento de importação com o dump fresco.
3. Validar contagens, hashes, permissões e integridade de FKs.
4. Inventariar anexos/volumes físicos fora do banco.
5. Cortar acesso à V1 e apontar DNS para o Registro.
