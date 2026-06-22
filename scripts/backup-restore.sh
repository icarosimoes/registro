#!/usr/bin/env bash
set -euo pipefail

usage() {
  echo "Uso: $0 <backup|restore> [dump-file]"
  echo ""
  echo "  backup   Gera backup manual do PostgreSQL e MinIO"
  echo "  restore  Restaura PostgreSQL a partir de dump e sincroniza MinIO"
  echo ""
  echo "Exemplos:"
  echo "  $0 backup"
  echo "  $0 restore /backups/registro_20260622_120000.dump"
  exit 1
}

[ "${1:-}" = "" ] && usage

DB_CONTAINER=$(docker ps -q -f "name=registro_db" | head -1)
if [ -z "$DB_CONTAINER" ]; then
  echo "ERRO: container do banco nao encontrado" >&2
  exit 1
fi

PG_PASS=$(docker exec "$DB_CONTAINER" cat /run/secrets/registro_postgres_password)

backup() {
  local ts
  ts=$(date -u +%Y%m%d_%H%M%S)
  local target="registro_${ts}.dump"

  echo "==> Gerando backup PostgreSQL: $target"
  docker exec -e PGPASSWORD="$PG_PASS" "$DB_CONTAINER" \
    pg_dump -U registro -Fc -f "/tmp/$target" registro

  docker cp "$DB_CONTAINER:/tmp/$target" "./$target"
  sha256sum "./$target" | tee "./${target}.sha256"

  echo "==> Validando integridade..."
  pg_restore --list "./$target" > /dev/null 2>&1 && echo "OK" || echo "ERRO: dump corrompido"

  echo "==> Backup MinIO (mc mirror)..."
  if command -v mc &> /dev/null; then
    mc mirror --overwrite registro/registro-attachments "./minio-backup-${ts}/"
    echo "MinIO backup: minio-backup-${ts}/"
  else
    echo "AVISO: mc nao instalado, backup MinIO ignorado"
  fi

  echo "==> Concluido: $target"
}

restore() {
  local dump_file="${1:-}"
  if [ -z "$dump_file" ] || [ ! -f "$dump_file" ]; then
    echo "ERRO: arquivo de dump nao encontrado: $dump_file" >&2
    exit 1
  fi

  echo "==> Validando integridade do dump..."
  pg_restore --list "$dump_file" > /dev/null 2>&1 || {
    echo "ERRO: dump corrompido ou invalido" >&2
    exit 1
  }

  echo "==> Copiando dump para container..."
  docker cp "$dump_file" "$DB_CONTAINER:/tmp/restore.dump"

  echo "==> Parando API para evitar escritas..."
  docker service scale registro_api=0

  echo "==> Restaurando banco..."
  docker exec -e PGPASSWORD="$PG_PASS" "$DB_CONTAINER" \
    pg_restore -U registro -d registro --clean --if-exists /tmp/restore.dump || true

  echo "==> Reiniciando API..."
  docker service scale registro_api=2

  echo "==> Aguardando API subir..."
  sleep 10

  echo "==> Restaurando MinIO (se mc disponivel)..."
  if command -v mc &> /dev/null; then
    local minio_backup_dir
    minio_backup_dir=$(dirname "$dump_file")/minio
    if [ -d "$minio_backup_dir/registro-attachments" ]; then
      mc mirror --overwrite "$minio_backup_dir/registro-attachments" registro/registro-attachments
      echo "MinIO restaurado"
    else
      echo "AVISO: diretorio MinIO nao encontrado em $minio_backup_dir"
    fi
  else
    echo "AVISO: mc nao instalado, restore MinIO ignorado"
  fi

  echo "==> Restore concluido. Valide com:"
  echo "    curl -fsS https://\$REGISTRO_API_HOST/api/v1/health/ready"
}

case "${1}" in
  backup)  backup ;;
  restore) restore "${2:-}" ;;
  *)       usage ;;
esac
