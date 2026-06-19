# Importação futura do Laravel

O MySQL local atual é novo e contém dados fictícios. Quando o dump estiver disponível, ele não será restaurado diretamente sobre essa base.

## Procedimento seguro

1. Receber dump criptografado ou sanitizado e registrar versão, origem e checksum.
2. Restaurar em banco temporário separado, sem acesso público.
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

Depois da equivalência no MySQL, a migração MySQL → PostgreSQL segue o plano em [migracao-banco.md](migracao-banco.md).
