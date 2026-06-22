# Backup e Restore

## Visão geral

| Componente | Frequência | Retenção | Método |
|---|---|---|---|
| PostgreSQL | Diário (24h) | 14 dias | `pg_dump -Fc` + SHA-256 + validação |
| MinIO (anexos) | Diário (24h) | Espelho contínuo | `mc mirror` para volume local |
| Redis | Sem backup | Cache invalidável | Regenera no startup |

## RTO / RPO

| Métrica | Target | Justificativa |
|---|---|---|
| **RPO** (perda máxima) | 24 horas | Backup diário; dados de um dia podem ser reinseridos manualmente |
| **RTO** (tempo de restore) | 1 hora | Restore local + restart dos services |

## PostgreSQL

### Backup automático

O service `backup` no `docker-stack.yml` roda 1x/dia:

1. `pg_dump -Fc` → `/backups/registro_YYYYMMDD_HHMMSS.dump`
2. SHA-256 → `*.dump.sha256`
3. Validação com `pg_restore --list` (verifica integridade)
4. Limpeza de backups com mais de 14 dias

### Backup manual (antes de mudança crítica)

```bash
DB_CONTAINER=$(docker ps -q -f "name=registro_db" | head -1)
PG_PASS=$(docker exec "$DB_CONTAINER" cat /run/secrets/registro_postgres_password)

docker exec "$DB_CONTAINER" pg_dump \
  -U registro -Fc -f /tmp/registro_pre_change.dump registro

docker cp "$DB_CONTAINER":/tmp/registro_pre_change.dump ./
sha256sum registro_pre_change.dump
```

### Restore do PostgreSQL

```bash
DB_CONTAINER=$(docker ps -q -f "name=registro_db" | head -1)

# 1. Copiar dump para o container
docker cp registro_YYYYMMDD_HHMMSS.dump "$DB_CONTAINER":/tmp/restore.dump

# 2. Verificar integridade
docker exec "$DB_CONTAINER" pg_restore --list /tmp/restore.dump > /dev/null

# 3. Parar a API para evitar escritas
docker service scale registro_api=0

# 4. Restaurar (drop + recreate)
docker exec "$DB_CONTAINER" pg_restore \
  -U registro -d registro --clean --if-exists /tmp/restore.dump

# 5. Restartar API
docker service scale registro_api=2

# 6. Validar
curl -fsS "https://$REGISTRO_API_HOST/api/v1/health/ready"
```

## MinIO (anexos)

### Backup automático

O service `backup-minio` no `docker-stack.yml` roda 1x/dia:

- `mc mirror --overwrite` do bucket `registro-attachments` para `/backups/minio/`
- Usa o mesmo volume `registro-backups` do PostgreSQL

### Restore do MinIO

```bash
# Configurar mc apontando para o MinIO do Swarm
mc alias set registro http://localhost:9000 ACCESS_KEY SECRET_KEY

# Restaurar do backup local
mc mirror /backups/minio/registro-attachments registro/registro-attachments
```

### Restore do volume MinIO (se o volume foi perdido)

```bash
# Se os dados do volume minio foram perdidos mas o backup existe:
docker service scale registro_minio=0

# Copiar backup para volume ou path montado
# (depende de onde o volume está mapeado)

docker service scale registro_minio=1
mc mirror /backups/minio/registro-attachments registro/registro-attachments
```

## Script de restore completo

Para restore completo (PostgreSQL + MinIO) de uma vez:

```bash
bash scripts/backup-restore.sh restore <path-do-dump.dump>
```

## Checklist pós-restore

- [ ] `curl /api/v1/health` retorna 200
- [ ] `curl /api/v1/health/ready` retorna 200 (banco + redis conectados)
- [ ] Login funciona no frontend
- [ ] Verificar contagem de registros (ocorrências, OS, etc.)
- [ ] Testar download de um anexo para validar MinIO
- [ ] Verificar logs da API por erros

## Monitoramento de backups

O service de backup emite logs com `echo`:
- Sucesso: `backup ok: /backups/registro_YYYYMMDD_HHMMSS.dump`
- Falha: `ERRO: backup corrompido`

Monitorar via:
```bash
docker service logs registro_backup --since 24h | grep -E "ok|ERRO"
docker service logs registro_backup-minio --since 24h | grep -E "ok|ERRO"
```

## Off-site backup

O volume `registro-backups` está no node manager. Para backup off-site:

```bash
# rsync diário para servidor externo (cron no host)
rsync -avz /var/lib/docker/volumes/registro_registro-backups/_data/ \
  backup-user@backup-server:/backups/registro/
```

Configurar no crontab do host manager:
```
0 4 * * * rsync -avz /var/lib/docker/volumes/registro_registro-backups/_data/ backup-user@backup-server:/backups/registro/ >> /var/log/registro-offsite-backup.log 2>&1
```
