# Plataforma SaaS

## Estado implementado

O Compose sobe uma base MySQL nova e isolada, executa Alembic e cria dados fictícios. Ela serve para desenvolver a nova aplicação e não representa o banco Laravel.

| Componente | URL local | Responsabilidade |
| --- | --- | --- |
| Web do tenant | `http://localhost:3000` | produto Registro |
| API | `http://localhost:8000` | regras tenant e plataforma |
| Painel admin | `http://localhost:3001/login` | operação comercial cross-tenant |
| MySQL | `localhost:3307` | banco de desenvolvimento |

Credenciais de demonstração:

| Contexto | E-mail | Senha | Tenant |
| --- | --- | --- | --- |
| tenant | `icaro@registro.local` | `Registro@123` | `empresa-demo` |
| tenant | `ana@registro.local` | `Registro@123` | `filial-teste` |
| plataforma | `admin@registro.local` | `RegistroAdmin@123` | não se aplica |

Essas credenciais são somente locais. Em produção o seed exige senhas fornecidas por secret/env e não exibe credenciais na interface.

## Isolamento

O login tenant recebe apenas e-mail e senha. Se o e-mail pertencer a um único tenant, entra direto. Se pertencer a mais de um, a API retorna `422 multi_tenant` com a lista de empresas para o front exibir um seletor; o segundo envio inclui `company_id`. O JWT carrega `company_id`, e a consulta revalida usuário, empresa, status e exclusão lógica. O token administrativo usa outro tipo, outro endpoint e outra tabela; não pode ser usado em `/auth/me`.

No MySQL, cada repository filtra `company_id` explicitamente. Na futura migração para PostgreSQL será adicionada RLS como terceira camada, sem remover o filtro da aplicação.

## Painel administrativo

O painel separado consulta métricas agregadas, tenants e planos. O token fica em cookie `httpOnly`, criado por Server Action. As primeiras rotas são somente leitura; futuras mutações deverão exigir permissão de plataforma e gravar `platform_audit_logs`.

## Próximas capacidades

- CRUD de tenants e planos com auditoria;
- convite e recuperação de acesso;
- política de trial, tolerância e suspensão;
- Asaas sandbox, webhooks idempotentes e reconciliação;
- dashboard do tenant consumindo a API;
- testes automatizados de isolamento em cada domínio.
