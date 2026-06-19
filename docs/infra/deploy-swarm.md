# Deploy Docker Swarm — Registro

## Decisão operacional

- Todo ambiente sobe em containers Docker.
- Desenvolvimento local usa `docker compose`.
- Produção usa Docker Swarm; não usar `docker compose up` na VPS.
- A VPS mantém a aplicação em `/opt/registro`.
- Imagens são publicadas no GHCR com tag imutável `sha-<GITHUB_SHA completo>`.
- Deploy usa `--with-registry-auth` e atualização gradual `start-first`.
- Nenhum segredo é versionado em `.env`, stack ou imagem.

## Desenvolvimento

O desenvolvimento usa o MySQL fictício do Compose:

```bash
docker compose up --build
```

Serviços:

- web: `http://localhost:3000`
- admin: `http://localhost:3001`
- API: `http://localhost:8000`
- OpenAPI local: `http://localhost:8000/docs`

## Preparação única do Swarm

```bash
docker network create --driver overlay --attachable traefik-public
printf '%s' 'mysql+asyncmy://...' | docker secret create registro_database_url -
openssl rand -base64 48 | docker secret create registro_jwt_secret -
mkdir -p /opt/registro
```

O secret do banco deve apontar para o MySQL atual durante a primeira fase. Quando a migração para PostgreSQL acontecer, ele será rotacionado em procedimento próprio e validado antes do corte. A chave JWT usa um secret independente.

## Variáveis da VPS

Arquivo local `/opt/registro/.env.prod`, nunca versionado:

```env
REGISTRO_WEB_HOST=registro.exemplo.com.br
REGISTRO_API_HOST=api.registro.exemplo.com.br
REGISTRO_ADMIN_HOST=admin.registro.exemplo.com.br
REGISTRO_WEB_ORIGIN=https://registro.exemplo.com.br
IMAGE_TAG=sha-<sha-completo>
GHCR_PAT=...
```

Os domínios acima são placeholders até a definição de DNS.

## Deploy

```bash
cd /opt/registro
set -a
. ./.env.prod
set +a
echo "$GHCR_PAT" | docker login ghcr.io -u icarosimoes --password-stdin
docker stack config -c docker-stack.yml >/dev/null
docker stack deploy -c docker-stack.yml --with-registry-auth registro
```

Nunca usar apenas `latest` em produção. O CI deve publicar e o deploy deve informar a tag completa baseada no SHA.

## Validação

```bash
docker service ls
docker service ps registro_api
docker service ps registro_web
docker service ps registro_admin
docker service logs --tail 100 registro_api
curl -fsS "https://${REGISTRO_API_HOST}/api/v1/health"
```

## Rollback

```bash
docker service rollback registro_api
docker service rollback registro_web
docker service rollback registro_admin
```

Antes de migrations, mudanças críticas ou reboot da VPS, gerar e validar backup. Migrations futuras devem rodar uma única vez, em tarefa controlada no manager, nunca simultaneamente em todas as réplicas.
