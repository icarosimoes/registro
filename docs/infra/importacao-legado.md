# Importação do Laravel (V1)

O importador (`api/app/import_v1.py`) lê um dump MySQL da V1 (Chess Hotel / Aero) e carrega os dados no PostgreSQL do Registro como tenant `aero-hotel`.

## Scripts disponíveis

| Script | Ambiente | Quando usar |
| --- | --- | --- |
| `scripts/import-v1.sh` | Docker Compose (dev local) | Desenvolvimento e testes locais |
| `scripts/import-v1-swarm.sh` | Docker Swarm (VPS) | Staging e produção |

## Desenvolvimento local

```bash
bash scripts/import-v1.sh docs/aero-2026-06-19.sql
```

Usa o MySQL do Compose para staging, roda migrations e executa o importador.

## Swarm (staging ou produção)

```bash
bash scripts/import-v1-swarm.sh docs/aero-2026-06-19.sql
```

O script:

1. Copia o dump para a VPS via SCP
2. Sobe um MySQL temporário numa rede bridge isolada
3. Carrega o dump no MySQL
4. Conecta o PostgreSQL do stack à rede bridge
5. Roda `alembic upgrade head` num container efêmero da API
6. Executa `python -m app.import_v1` no mesmo container
7. Remove MySQL, rede bridge e dump remoto

Variáveis configuráveis (com defaults):

| Variável | Default | Descrição |
| --- | --- | --- |
| `VPS_HOST` | `95.111.250.4` | IP da VPS |
| `VPS_USER` | `root` | Usuário SSH |
| `STACK_NAME` | `registro` | Nome do stack no Swarm |
| `MYSQL_PASSWORD` | `import-v1-temp` | Senha do MySQL temporário |
| `LEGACY_DEMO_PASSWORD` | (vazio) | Se definido, cria `v1-demo@registro.local` |

O container efêmero usa a mesma imagem do `registro_api` no Swarm. A senha do PostgreSQL é lida de dentro do container do DB (secret montada em `/run/secrets/registro_postgres_password`). JWT e outras secrets recebem placeholders — não são usados pelo importador.

## O que é importado

| Conjunto | Tabela destino |
| --- | --- |
| Usuários | `users` (preserva hashes bcrypt Laravel) |
| Setores | `sectors` |
| Locais | `locations` |
| Funções | `functions` |
| Procedimentos | `procedures` |
| Ocorrências + comentários + participantes | `occurrences` |
| Reuniões + participantes + pautas | `meetings`, `meeting_participants`, `meeting_subjects` |
| Relatórios de turno + sub-tabelas | `shift_reports` (payload JSON) |
| Check suites + itens | `module_records` (module=inspecoes, payload JSON) |
| Auditorias + itens | `module_records` (module=manutencao, payload JSON) |
| Notificações | `notifications` |

## Permissões e roles

O importador cria dois roles para o tenant:

- **admin** — role principal com permissão wildcard `*`. Todos os usuários importados recebem este role e têm acesso total ao sistema.
- **legacy-admin** — preserva as ACLs originais do Laravel para referência. Não é usado para controle de acesso.

## Idempotência

- O ETL é idempotente por `(company_id, legacy_id)` — registros existentes são atualizados, novos são inseridos.
- O checksum SHA-256 do dump é registrado em `legacy_import_runs`. Se o mesmo checksum já foi processado, o importador pula a execução.
- Para reimportar com dump atualizado, basta rodar o script com o novo arquivo — o checksum diferente dispara uma nova execução.

## Regras

- O dump nunca é versionado nem incluído em imagem Docker.
- O MySQL temporário é efêmero — só existe durante a importação.
- Senhas Laravel bcrypt são preservadas. Login funciona com e-mail e senha da V1.
- Se o mesmo e-mail existe em mais de um tenant, o login solicita escolha da empresa.
- Arquivos/anexos físicos não são migrados pelo importador — requerem procedimento separado.

## Validação pós-importação

```bash
# Na VPS, via container do DB
DB=$(docker ps -q -f "name=registro_db" | head -1)

# Contagens por tenant
docker exec "$DB" psql -U registro -c "
  SELECT c.slug, 
    (SELECT count(*) FROM users WHERE company_id = c.id AND deleted_at IS NULL) as users,
    (SELECT count(*) FROM occurrences WHERE company_id = c.id AND deleted_at IS NULL) as occurrences,
    (SELECT count(*) FROM meetings WHERE company_id = c.id AND deleted_at IS NULL) as meetings,
    (SELECT count(*) FROM shift_reports WHERE company_id = c.id) as shift_reports
  FROM companies c ORDER BY c.id;
"

# Verificar roles e permissões do tenant
docker exec "$DB" psql -U registro -c "
  SELECT r.code, r.name, string_agg(p.code, ', ') as permissions
  FROM roles r
  JOIN role_permissions rp ON rp.role_id = r.id
  JOIN permissions p ON p.id = rp.permission_id
  WHERE r.company_id = (SELECT id FROM companies WHERE slug = 'aero-hotel')
  GROUP BY r.id ORDER BY r.code;
"

# Verificar import run
docker exec "$DB" psql -U registro -c "SELECT source, status, report FROM legacy_import_runs;"
```

## Plano de produção — Aero Hotel

O tenant `aero-hotel` é o cliente real. Procedimento para corte final:

1. Gerar dump fresco do MySQL de produção da V1.
2. Rodar `bash scripts/import-v1-swarm.sh <dump-fresco.sql>`.
3. Validar contagens e permissões (queries acima).
4. Testar login com usuário real da V1.
5. Migrar anexos/volumes físicos ao MinIO (procedimento separado).
6. Cortar acesso à V1 e apontar DNS para o Registro.
