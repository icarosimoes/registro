# Migração MySQL para PostgreSQL

## Momento correto

O banco não será trocado durante a reescrita funcional. Primeiro a nova aplicação alcança equivalência usando o MySQL; depois ocorre uma migração de banco separada.

## Fases

1. Inventariar schema real, volumes, índices, FKs, collations, dados inválidos e anexos.
2. Sanear queries e garantir `company_id` em todos os agregados.
3. Congelar mudanças estruturais no MySQL.
4. Modelar PostgreSQL com Alembic, sem copiar vícios específicos do MySQL.
5. Fazer carga de ensaio e relatório de conversões.
6. Validar contagens, chaves, somatórios, checksums e amostras por empresa.
7. Ensaiar corte e rollback com tempo medido.
8. Fazer janela final, carga incremental controlada e troca única da conexão.
9. Ativar RLS somente depois de validar a integridade de `company_id`.

## Conversões de atenção

`unsigned`, enums, `tinyint`, datas zero, timezone, case sensitivity, collations, auto increment/sequences, JSON, blobs e nomes reservados.

## Proibições

- sem dual-write MySQL/PostgreSQL;
- sem migration destrutiva sem backup restaurável;
- sem renumerar IDs no primeiro corte;
- sem ativar RLS antes de corrigir registros órfãos ou sem empresa.

## Critérios de aceite

Contagens por tabela e empresa, FKs sem órfãos, hashes preservados, anexos acessíveis, principais consultas com plano aceitável, testes cross-tenant, ensaio de rollback e aprovação operacional.
