# Segurança

## Fronteiras

- O painel admin guarda seu token em cookie `httpOnly`; em produção também deve usar `Secure` e `SameSite` adequado.
- FastAPI valida payload, token, usuário ativo, empresa e permissão.
- O MySQL permanece externo e usa credencial com menor privilégio possível.
- Produção recebe URL do banco e chave JWT por Docker Secrets separados.

## Autenticação atual

- Compatibilidade com hashes bcrypt `$2y$` do Laravel.
- Senhas limitadas a 72 bytes pela fronteira bcrypt.
- JWT HS256 com algoritmo fixo e expiração padrão de 30 minutos.
- Chave de produção com no mínimo 32 caracteres; o default de desenvolvimento é recusado.
- `/auth/me` consulta novamente usuário e `company_id`, evitando confiar apenas no token.
- Tokens tenant e plataforma possuem tipos distintos e são rejeitados fora da própria fronteira.

## Multiempresa

Toda consulta de negócio deve receber a empresa da sessão, nunca do corpo enviado pelo cliente. Testes cross-tenant são obrigatórios — `test_cross_tenant.py` (tokens e endpoints) e `test_cross_tenant_crud.py` (CRUD isolado com banco real) garantem que tenant A não vê, edita ou exclui dados de tenant B. RLS só será adotado após o PostgreSQL e não substitui filtros de aplicação e autorização.

## Supply chain

- Instalação Python é definida em `pyproject.toml`; Node usa `npm ci` e lockfile.
- CI deverá executar `npm audit --audit-level=high` e auditoria Python sobre versões travadas.
- Não adicionar pacote recém-publicado sem necessidade e revisão.
- Imagens Docker usam runtime com patch explícito; tags de aplicação em produção usam SHA imutável.

## Dados e logs

Nunca registrar senha, JWT, URL completa de banco, dados pessoais desnecessários ou conteúdo de anexos. Erros públicos usam códigos estáveis e mensagens sem detalhes internos.

Chaves futuras do Asaas entram por Docker Secret, separadas do token do webhook. Webhooks falham fechado, deduplicam eventos e nunca confiam em `company_id` enviado pelo provedor ou cliente.

## Mudança crítica

Migration, restore, rotação de credencial, alteração de volume/rede ou corte de domínio exige backup novo, validação do artefato, plano de rollback e registro em `docs/registro-trabalho.md`.
