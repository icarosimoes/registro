#!/usr/bin/env bash
set -euo pipefail

DUMP_PATH="${1:-docs/aero-2026-06-19.sql}"
STAGING_DATABASE="${LEGACY_DATABASE_NAME:-registro_v1}"
MYSQL_USER_VALUE="${MYSQL_USER:-registro}"
MYSQL_PASSWORD_VALUE="${MYSQL_PASSWORD:-registro}"
LEGACY_DEMO_PASSWORD_VALUE="${LEGACY_DEMO_PASSWORD:-Registro@123}"

if [[ ! -f "$DUMP_PATH" ]]; then
  echo "Dump não encontrado: $DUMP_PATH" >&2
  exit 1
fi

CHECKSUM="$(sha256sum "$DUMP_PATH" | cut -d ' ' -f 1)"

docker compose exec -T mysql sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD"' <<SQL
DROP DATABASE IF EXISTS \`${STAGING_DATABASE}\`;
CREATE DATABASE \`${STAGING_DATABASE}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL

docker compose exec -T -e "LEGACY_DB=${STAGING_DATABASE}" mysql \
  sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$LEGACY_DB"' < "$DUMP_PATH"

docker compose exec -T mysql sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD"' <<SQL
GRANT SELECT ON \`${STAGING_DATABASE}\`.* TO '${MYSQL_USER_VALUE}'@'%';
FLUSH PRIVILEGES;
SQL

docker compose exec -T api alembic upgrade head
docker compose exec -T \
  -e "LEGACY_DATABASE_URL=mysql+asyncmy://${MYSQL_USER_VALUE}:${MYSQL_PASSWORD_VALUE}@mysql:3306/${STAGING_DATABASE}?charset=utf8mb4" \
  -e "LEGACY_DUMP_SHA256=${CHECKSUM}" \
  -e "LEGACY_DEMO_PASSWORD=${LEGACY_DEMO_PASSWORD_VALUE}" \
  api python -m app.import_v1
