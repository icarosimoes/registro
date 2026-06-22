#!/usr/bin/env bash
# Importa base V1 (MySQL dump) para um tenant no ambiente Swarm.
#
# Uso:
#   bash scripts/import-v1-swarm.sh <dump.sql> [slug-do-tenant]
#
# Exemplos:
#   bash scripts/import-v1-swarm.sh docs/aero-2026-06-19.sql              # tenant: aero-hotel (default)
#   bash scripts/import-v1-swarm.sh dump-cliente.sql hotel-xyz             # tenant: hotel-xyz
#   LEGACY_TENANT_NAME="Hotel XYZ" bash scripts/import-v1-swarm.sh dump.sql hotel-xyz
#
# O script:
#   1. Copia o dump para a VPS
#   2. Sobe MySQL temporário numa rede bridge
#   3. Carrega o dump
#   4. Roda migrations + importador V1 num container efêmero da API
#   5. Limpa MySQL temporário e dump
set -euo pipefail

DUMP_PATH="${1:?Uso: $0 <dump.sql> [slug-do-tenant]}"
TENANT_SLUG="${2:-aero-hotel}"
VPS_HOST="${VPS_HOST:-95.111.250.4}"
VPS_USER="${VPS_USER:-root}"
STACK_NAME="${STACK_NAME:-registro}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-import-v1-temp}"
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-import-v1-root}"
LEGACY_TENANT_NAME="${LEGACY_TENANT_NAME:-}"
LEGACY_TENANT_EMAIL="${LEGACY_TENANT_EMAIL:-}"
LEGACY_DEMO_PASSWORD="${LEGACY_DEMO_PASSWORD:-}"

if [[ ! -f "$DUMP_PATH" ]]; then
  echo "Dump não encontrado: $DUMP_PATH" >&2
  exit 1
fi

CHECKSUM="$(sha256sum "$DUMP_PATH" | cut -d ' ' -f 1)"
REMOTE_DUMP="/tmp/registro-v1-import.sql"
MYSQL_CONTAINER="registro-v1-mysql-temp"
MYSQL_DB="registro_v1"
MYSQL_USER="registro"

echo "==> Dump:   $DUMP_PATH ($CHECKSUM)"
echo "==> Tenant: $TENANT_SLUG"
echo "==> VPS:    $VPS_USER@$VPS_HOST"

echo ""
echo "==> [1/6] Copiando dump para VPS..."
scp "$DUMP_PATH" "${VPS_USER}@${VPS_HOST}:${REMOTE_DUMP}"

BRIDGE_NET="registro-v1-import"

echo ""
echo "==> [2/6] Subindo MySQL temporário na VPS..."
ssh "${VPS_USER}@${VPS_HOST}" bash <<REMOTE
set -euo pipefail

docker rm -f $MYSQL_CONTAINER 2>/dev/null || true
docker network rm $BRIDGE_NET 2>/dev/null || true
docker network create $BRIDGE_NET

docker run -d \
  --name $MYSQL_CONTAINER \
  --network $BRIDGE_NET \
  -e MYSQL_DATABASE=$MYSQL_DB \
  -e MYSQL_USER=$MYSQL_USER \
  -e MYSQL_PASSWORD=$MYSQL_PASSWORD \
  -e MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD \
  mysql:8.4.8 \
  --character-set-server=utf8mb4 --collation-server=utf8mb4_0900_ai_ci

echo "Aguardando MySQL ficar pronto..."
for i in \$(seq 1 30); do
  if docker exec $MYSQL_CONTAINER mysqladmin ping -h 127.0.0.1 -uroot -p"$MYSQL_ROOT_PASSWORD" --silent 2>/dev/null; then
    echo "MySQL pronto."
    break
  fi
  sleep 2
done
REMOTE

echo ""
echo "==> [3/6] Carregando dump no MySQL..."
ssh "${VPS_USER}@${VPS_HOST}" bash <<REMOTE
set -euo pipefail
docker exec -i $MYSQL_CONTAINER mysql -uroot -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DB < $REMOTE_DUMP

docker exec $MYSQL_CONTAINER mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e \
  "GRANT SELECT ON \\\`$MYSQL_DB\\\`.* TO '$MYSQL_USER'@'%'; FLUSH PRIVILEGES;"

echo "Dump carregado."
REMOTE

echo ""
echo "==> [4/6] Rodando migrations e importação V1..."
ssh "${VPS_USER}@${VPS_HOST}" bash <<REMOTE
set -euo pipefail

API_IMAGE=\$(docker service inspect ${STACK_NAME}_api --format '{{.Spec.TaskTemplate.ContainerSpec.Image}}')
if [[ -z "\$API_IMAGE" ]]; then
  echo "Imagem da API não encontrada!" >&2
  exit 1
fi
echo "Imagem: \$API_IMAGE"

DB_CONTAINER=\$(docker ps -q -f "name=${STACK_NAME}_db" | head -1)
if [[ -z "\$DB_CONTAINER" ]]; then
  echo "Container do PostgreSQL não encontrado neste nó!" >&2
  exit 1
fi
docker network connect $BRIDGE_NET "\$DB_CONTAINER" 2>/dev/null || true

# Ler a senha do PG de dentro do container (ele tem a secret montada)
PG_PASS=\$(docker exec "\$DB_CONTAINER" cat /run/secrets/registro_postgres_password 2>/dev/null || true)
if [[ -z "\$PG_PASS" ]]; then
  PG_PASS=\$(docker exec "\$DB_CONTAINER" sh -c 'echo \$POSTGRES_PASSWORD' 2>/dev/null || true)
fi
if [[ -z "\$PG_PASS" ]]; then
  echo "Não conseguiu obter a senha do PostgreSQL!" >&2
  exit 1
fi
DB_URL="postgresql+asyncpg://registro:\${PG_PASS}@\${DB_CONTAINER}:5432/registro"

DUMMY_KEY="import-placeholder-not-used-000000000"
COMMON_ENV="-e DATABASE_URL=\$DB_URL -e JWT_SECRET=\$DUMMY_KEY -e CHESS_HOTEL_INTEGRATION_KEY=\$DUMMY_KEY -e WEB_ORIGINS=https://localhost -e ENVIRONMENT=production -e REDIS_URL=redis://localhost:6379/0 -e SEED_DEFAULT_PASSWORD=unused -e PLATFORM_ADMIN_PASSWORD=unused"

echo "--- Migrations ---"
docker run --rm \
  --network $BRIDGE_NET \
  \$COMMON_ENV \
  "\$API_IMAGE" alembic upgrade head

echo "--- Instalando asyncmy e rodando importação V1 ---"
docker run --rm \
  --network $BRIDGE_NET \
  --user root \
  \$COMMON_ENV \
  -e "LEGACY_TENANT_SLUG=$TENANT_SLUG" \
  -e "LEGACY_TENANT_NAME=$LEGACY_TENANT_NAME" \
  -e "LEGACY_TENANT_EMAIL=$LEGACY_TENANT_EMAIL" \
  -e "LEGACY_DATABASE_URL=mysql+asyncmy://$MYSQL_USER:$MYSQL_PASSWORD@$MYSQL_CONTAINER:3306/$MYSQL_DB?charset=utf8mb4" \
  -e "LEGACY_DUMP_SHA256=$CHECKSUM" \
  -e "LEGACY_DEMO_PASSWORD=$LEGACY_DEMO_PASSWORD" \
  "\$API_IMAGE" sh -c "pip install --quiet 'asyncmy>=0.2.10,<1' && python -m app.import_v1"

echo "Importação concluída."
REMOTE

echo ""
echo "==> [5/5] Limpando MySQL temporário e dump..."
ssh "${VPS_USER}@${VPS_HOST}" bash <<REMOTE
set -euo pipefail
DB_CONTAINER=\$(docker ps -q -f "name=${STACK_NAME}_db" | head -1)
docker network disconnect $BRIDGE_NET "\$DB_CONTAINER" 2>/dev/null || true
docker rm -f $MYSQL_CONTAINER
docker network rm $BRIDGE_NET 2>/dev/null || true
rm -f $REMOTE_DUMP
echo "Limpeza concluída."
REMOTE

echo ""
echo "==> Importação V1 finalizada com sucesso!"
echo "    Tenant: $TENANT_SLUG"
echo "    Checksum: $CHECKSUM"
