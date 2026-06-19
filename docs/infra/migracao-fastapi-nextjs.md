# Migração FastAPI + Next.js

## Estratégia

Adotar o padrão strangler: a nova aplicação assume módulos inteiros gradualmente, enquanto o Laravel continua atendendo os demais.

## Fases

1. Fundação, observabilidade, documentação e leitura segura do MySQL.
2. Autenticação compatível, usuários, papéis e ACL.
3. Cadastros básicos e componentes CRUD reutilizáveis.
4. Ocorrências e anexos.
5. Reuniões, turnos, inspeções e diário de obra.
6. Relatórios, exportações e tarefas assíncronas.
7. Desativação controlada do Laravel.
8. Migração MySQL para PostgreSQL.

## Regra de corte por módulo

Antes do corte, validar:

- contratos de API e autorização;
- contagem e amostra de registros;
- criação, edição, exclusão lógica e anexos;
- exportações e ações em massa;
- trilha de auditoria;
- rollback documentado.

Durante o corte, apenas uma aplicação pode gravar no módulo. Leituras paralelas são permitidas para comparação.

## Migração futura para PostgreSQL

1. Congelar mudanças estruturais no MySQL.
2. Criar schema PostgreSQL com Alembic, sem reproduzir vícios específicos do MySQL.
3. Fazer carga de ensaio e mapear tipos, collations, datas zero, unsigned, enums e sequences.
4. Validar contagens, chaves, somatórios, checksums e anexos.
5. Executar ensaio de corte e rollback.
6. Fazer janela final, carga incremental e troca de conexão.
7. Ativar RLS somente após validar `company_id` em todas as entidades de negócio.

Nunca manter dual-write da aplicação entre os dois bancos.
