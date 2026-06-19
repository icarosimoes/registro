# Jarvis — Segurança

## Autenticação

- bcrypt Laravel é apenas verificado; nunca rehash silencioso sem plano.
- JWT usa PyJWT, algoritmo explícito e chave de produção via Docker Secret.
- Tokens não ficam em `localStorage`; a UI final usa cookie httpOnly.
- Usuário, status e empresa são revalidados para operações sensíveis.

## Autorização e tenant

`company_id` vem da sessão. IDs enviados pelo cliente nunca definem empresa. Permissão é validada na API e testada com empresa diferente.

## Supply chain

Usar lockfile, `npm ci`, auditorias no CI e imagens com patch explícito. Revisar pacote novo, mantenedor e publicação recente antes de adicionar.

## Operação

Nunca versionar `.env`, dumps, chaves ou tokens. Antes de migration, restore, reboot planejado ou mudança de volume/rede: backup novo, integridade, restore-test quando aplicável e registro do resultado.
