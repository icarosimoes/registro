# Runbook de produção

## Identidade operacional

| Item | Valor |
| --- | --- |
| diretório | `/opt/registro` |
| stack | `registro` |
| serviços | `registro_api`, `registro_web`, `registro_admin` |
| imagens | `ghcr.io/icarosimoes/registro/api`, `/web` e `/admin` |
| banco | MySQL externo |
| proxy | Traefik na rede `traefik-public` |

## Pré-deploy

1. Confirmar commit/tag imutável.
2. Validar CI, `docker stack config` e secrets existentes.
3. Se houver schema, storage ou infraestrutura: gerar backup novo, validar integridade e testar restore.
4. Registrar mudança e rollback esperado.

## Deploy e acompanhamento

Use o procedimento em `deploy-swarm.md`. Depois:

```bash
docker service ls
docker service ps registro_api --no-trunc
docker service ps registro_web --no-trunc
docker service ps registro_admin --no-trunc
docker service logs --since 10m registro_api
curl -fsS "https://${REGISTRO_API_HOST}/api/v1/health"
curl -fsS "https://${REGISTRO_API_HOST}/api/v1/health/ready"
```

Sucesso exige réplicas convergidas, health 200, readiness conectado, web e admin respondendo. `health` sozinho não comprova acesso ao banco.

## Incidente

1. Registrar horário, versão e sintoma.
2. Verificar réplicas, tasks rejeitadas e logs recentes.
3. Comparar `/health` e `/health/ready`.
4. Se começou após deploy, executar rollback antes de mudanças exploratórias.
5. Nunca imprimir Docker Secrets nem URLs de banco nos logs/tickets.

## Rollback

```bash
docker service rollback registro_api
docker service rollback registro_web
docker service rollback registro_admin
docker service ps registro_api --no-trunc
```

Rollback de aplicação não desfaz dados. Mudança de schema precisa de plano próprio e backup restaurável.

Alembic não roda automaticamente nas réplicas do Swarm. Cada migration de produção é uma tarefa única e controlada no manager, depois do backup e antes de liberar a versão que depende dela.

## Backup mínimo antes de mudança crítica

O comando exato depende de onde o MySQL é operado. O procedimento deve produzir artefato datado, tamanho, SHA-256, teste de integridade e teste de restore para mudanças de alto risco. Registrar tudo em `registro-trabalho.md`.
