# Backlog da modernização

## P0 — consolidar SaaS e receber dados reais

- [x] Criar MySQL local, migrations e seed fictício com dois tenants.
- [x] Separar autenticação tenant e plataforma.
- [x] Criar painel administrativo inicial.
- [x] Testar bloqueio de token com `company_id` divergente.
- [ ] Receber dump sanitizado e executar inventário/importação em base temporária.
- [ ] Gerar inventário real de tabelas, volumes, índices, constraints, collations e anexos.
- [ ] Validar login com amostra de usuários ativos/inativos e hashes Laravel.
- [ ] Implementar página `/login` do produto tenant com cookie httpOnly.
- [ ] Ampliar testes cross-tenant para cada domínio novo.
- [ ] Criar CI com Ruff, mypy, pytest, typecheck, build e auditorias.

## P1 — comercial e cobrança

- [ ] CRUD auditado de tenants, planos e assinaturas.
- [ ] Definir trial, tolerância, suspensão e reativação.
- [ ] Configurar Asaas sandbox e segredos no Swarm.
- [ ] Implementar webhook autenticado, idempotente e com replay.
- [ ] Implementar reconciliação periódica de cobranças.

## P2 — identidade, ACL e cadastros

- [ ] Usuários, perfis e permissões com paridade de leitura.
- [ ] Setores, locais e funções.
- [ ] Procedimentos e anexos.
- [ ] Componentes reutilizáveis de lista, formulário, estado vazio e confirmação.

## P3 — operação

- [ ] Ocorrências, comentários, participantes, anexos, clone e PDF.
- [ ] Reuniões, participantes, assuntos, anexos, início e ata PDF.
- [ ] Relatórios de turno e Excel.

## P4 — inspeções e obra

- [ ] Check suites e inspection suites.
- [ ] Vistorias V2 e migração controlada da V1.
- [ ] Auditorias e relatórios.
- [ ] Diário de obra.

## P5 — corte e banco

- [ ] Retirar Laravel domínio a domínio.
- [ ] Congelar mudanças estruturais no MySQL.
- [ ] Ensaiar carga e validação no PostgreSQL.
- [ ] Executar corte sem dual-write e ativar isolamento PostgreSQL/RLS após saneamento.

## Definition of Done por módulo

Contrato, autorização, isolamento por empresa, estados de UI, CRUD necessário, anexos/exportações, testes, comparação de dados, observabilidade, documentação e rollback precisam estar aprovados antes do corte.
