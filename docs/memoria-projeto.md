# Memória do projeto

## 2026-06-19 — Arquitetura de modernização

Decisão: migrar gradualmente para FastAPI + Next.js App Router. As regras Jarvis aplicáveis foram adaptadas e versionadas em `docs/agentes/`; o Aloji permanece como referência de origem.

Motivos:

- alinhar o Registro à stack moderna já usada no ecossistema Aloji;
- separar regras de negócio da camada de apresentação;
- manter o sistema operacional durante a reescrita;
- permitir que SQLAlchemy acesse o MySQL atual e, posteriormente, PostgreSQL;
- reduzir o risco de uma substituição integral em uma única entrega.

### Restrições (atualizadas em 21/06/2026)

- O Laravel V1 permanece em operação até o corte final; não foi removido.
- O PostgreSQL 17 com RLS é o banco principal desde 20/06/2026. MySQL é usado apenas para leitura do dump V1 via profile `mysql-import`.
- Um módulo terá somente um escritor por vez.
- Não haverá dual-write.

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

RLS (Row-Level Security) ativo em 24 tabelas com `company_id`. O GUC `app.current_company_id` é setado na dependency `current_user` e resetado no `finally` da session. Tabelas filhas herdam isolamento via FK CASCADE. Rotas platform (admin) não setam o GUC — o owner tem `BYPASSRLS`.

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

## 2026-06-19 — Autenticação compatível

- A nova API autentica por leitura do `users` legado e valida hashes bcrypt do Laravel.
- Tokens HS256 carregam usuário, empresa, papel e permissões, com algoritmo fixo na validação.
- A chave JWT de produção vem do Docker Secret `registro_jwt_secret`.
- Esta etapa não cria tabelas, não altera senhas e não escreve no MySQL.

## 2026-06-19 — Documentação autossuficiente

- O padrão documental do Aloji foi adaptado ao Registro.
- Arquitetura, domínios, API, UI, desenvolvimento, segurança, backlog, legado, testes, produção e migração de banco possuem fontes de verdade próprias.
- Regras Jarvis de engenharia, layout/CRUD, performance, segurança e multiempresa foram trazidas para o repositório.
- Agentes e documentos de reservas, Channex, Asaas, CRM e financeiro não foram copiados por não pertencerem ao escopo atual.

## 2026-06-19 — Fundação SaaS comercial

- A decisão de comercializar o Registro tornou SaaS e Asaas aderentes ao produto; as regras Jarvis correspondentes passaram a integrar a documentação.
- Foi criada uma base MySQL 8.4 nova para desenvolvimento, com Alembic e seed fictício. Ela não substitui nem representa o dump Laravel.
- Tenant, operador da plataforma e seus tokens são identidades separadas.
- O painel administrativo é outra aplicação Next.js, publicada em domínio próprio no Swarm.
- Planos, assinaturas, faturas e auditoria formam o núcleo comercial; o Asaas permanece desativado até sandbox, credenciais e política comercial.
- O dump legado será restaurado em base temporária e importado por processo repetível, nunca diretamente sobre o banco novo.

## 2026-06-20 — Governança documental obrigatória

Decisão: o diretório `/docs` é a memória oficial e a fonte de verdade técnica, funcional, operacional e histórica do Registro.

- Toda informação pertinente ao desenvolvimento ou ao funcionamento do sistema deve ser registrada em `/docs`.
- Mudanças de código, banco, contrato, interface, segurança, tenant, deploy, integração, migração ou operação devem atualizar a documentação correspondente durante o mesmo trabalho.
- `backlog.md` registra trabalho pendente, prioridade, riscos encontrados e critérios de conclusão.
- `memoria-projeto.md` registra decisões duráveis, contexto e restrições que não podem depender apenas do histórico do chat ou do conhecimento de uma pessoa.
- `registro-trabalho.md` registra cronologicamente o que foi executado, validado, alterado ou identificado.
- Documentos de arquitetura, domínio, API, UI e infraestrutura devem refletir o estado implementado; funcionalidades futuras precisam ser marcadas explicitamente como planejadas.
- Correções e descobertas relevantes devem ser documentadas mesmo quando não forem implementadas imediatamente.
- Nenhuma credencial, secret, dump, dado pessoal desnecessário ou informação sensível deve ser copiada para a documentação versionada.

Essa regra passa a integrar a Definition of Done: código sem a atualização documental pertinente não é considerado concluído.

## 2026-06-20 — Revisão técnica do estado atual

- O ambiente Docker local foi validado com API, web, admin e MySQL ativos.
- O frontend passou em `typecheck` e build de produção; a API passou em 7 testes dentro do container.
- O tenant `Aero Hotel` (`aero-hotel`) possui 60 usuários e 375 ocorrências importadas no banco local.
- Foi identificado risco no login multitenant: a lista de empresas é produzida antes da validação da senha.
- A interface de ocorrências busca no máximo 100 registros e dados antigos do `localStorage` podem prevalecer sobre a API.
- Tratativas, mutações operacionais e solicitações fiscais ainda são persistidas somente no navegador.
- Anexos fiscais ainda usam Data URL/Base64 sem limites ou validação adequada e precisam migrar para armazenamento controlado pela API.
- O backlog foi atualizado com as correções de autenticação, paginação, persistência, auditoria, anexos, SLA, testes, documentação e higiene do repositório.

## 2026-06-20 — Correção do primeiro bloco crítico

- O login multitenant passou a validar a senha antes de retornar qualquer empresa.
- Quando diferentes tenants possuem o mesmo e-mail, somente usuários cuja senha confere participam da seleção; senha inválida não revela tenants.
- `company_id` opcional passou a aceitar somente inteiros positivos.
- Foram adicionados testes para tenant único, senha inválida, múltiplos tenants, senhas diferentes e seleção explícita; a suíte passou a 12 testes.
- Ocorrências passaram a consumir todas as páginas disponíveis da API, eliminando o corte nos primeiros 100 registros.
- Dados reais de ocorrências não são mais substituídos por cópias antigas do `localStorage`.
- Como mutações ainda não existem na API, ocorrências reais ficam em modo leitura e a interface comunica essa limitação.
- Para crescimento de volume, permanece planejada paginação e busca server-side sob demanda, sem hidratar todo o conjunto no Next.js.
