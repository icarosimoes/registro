# Memória do projeto

## 2026-06-19 — Arquitetura de modernização

Decisão: migrar gradualmente para FastAPI + Next.js App Router, seguindo os agentes Jarvis em `/home/icarosimoes/dev/aloji/docs/agentes`.

Motivos:

- alinhar o Registro à stack moderna já usada no ecossistema Aloji;
- separar regras de negócio da camada de apresentação;
- manter o sistema operacional durante a reescrita;
- permitir que SQLAlchemy acesse o MySQL atual e, posteriormente, PostgreSQL;
- reduzir o risco de uma substituição integral em uma única entrega.

### Restrições

- O Laravel permanece funcionando e não será removido nesta fase.
- O MySQL atual permanece como fonte de verdade.
- Um módulo terá somente um escritor por vez.
- Não haverá dual-write entre MySQL e PostgreSQL.
- A migração de banco acontecerá depois da equivalência funcional da aplicação.

### Padrões Jarvis adotados

- Backend organizado por domínio: router, service, models e schemas.
- Router sem regra de negócio e service sem dependência de HTTP.
- Next.js com Server Components por padrão e Client Components somente para interação.
- Navegação interna com `Link`; buscas independentes executadas em paralelo.
- Validação nas fronteiras e respostas de erro estruturadas.
- Valores monetários em centavos/Decimal, nunca `float` como fonte contábil.
- Estados de carregamento, vazio, erro, sucesso e permissão em todos os fluxos.
- Documentação e registro atualizados junto das mudanças.

### Multiempresa

O schema legado já possui `company_id`, mas ainda não foi comprovado que todas as tabelas e queries estejam isoladas corretamente. O filtro por empresa será preservado na fase MySQL. RLS em três camadas será ativado apenas no PostgreSQL, depois de inventário e saneamento dos dados.

## 2026-06-19 — Nome e arquivamento da V1

- O nome da aplicação passou de Aero para **Registro**.
- A aplicação Laravel completa foi movida para `docs/v1/` e removida do índice do Git; permanece somente no ambiente local.
- A organização é apenas física: o schema e o nome do banco MySQL não foram alterados.
- A V1 deve permanecer imutável, salvo correção crítica necessária durante a transição.

## 2026-06-19 — Docker e produção

- Docker é obrigatório em todos os ambientes da nova aplicação.
- Desenvolvimento usa `docker compose`; produção usa Docker Swarm.
- Diretório padrão na VPS: `/opt/registro`.
- Imagens de produção ficam no GHCR e usam tags imutáveis baseadas no SHA completo.
- Deploy Swarm sempre usa `--with-registry-auth`, healthchecks, rolling update e rollback automático.
- Secrets do banco são fornecidos por Docker Secrets; nunca entram no repositório ou na imagem.
- O banco permanece externo à stack enquanto o Registro utilizar o MySQL legado.
