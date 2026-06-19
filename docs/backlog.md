# Backlog da modernização

## P0 — colocar dados reais com segurança

- [ ] Configurar acesso de desenvolvimento somente ao MySQL necessário.
- [ ] Gerar inventário real de tabelas, volumes, índices, constraints, collations e anexos.
- [ ] Validar login com amostra de usuários ativos/inativos e hashes Laravel.
- [ ] Implementar página `/login` com cookie httpOnly via camada server-side do Next.js.
- [ ] Testar isolamento por `company_id` e ACL com pelo menos duas empresas.
- [ ] Criar CI com Ruff, mypy, pytest, typecheck, build e auditorias.

## P1 — identidade, ACL e cadastros

- [ ] Usuários, perfis e permissões com paridade de leitura.
- [ ] Setores, locais e funções.
- [ ] Procedimentos e anexos.
- [ ] Componentes reutilizáveis de lista, formulário, estado vazio e confirmação.

## P2 — operação

- [ ] Ocorrências, comentários, participantes, anexos, clone e PDF.
- [ ] Reuniões, participantes, assuntos, anexos, início e ata PDF.
- [ ] Relatórios de turno e Excel.

## P3 — inspeções e obra

- [ ] Check suites e inspection suites.
- [ ] Vistorias V2 e migração controlada da V1.
- [ ] Auditorias e relatórios.
- [ ] Diário de obra.

## P4 — corte e banco

- [ ] Retirar Laravel domínio a domínio.
- [ ] Congelar mudanças estruturais no MySQL.
- [ ] Ensaiar carga e validação no PostgreSQL.
- [ ] Executar corte sem dual-write e ativar isolamento PostgreSQL/RLS após saneamento.

## Definition of Done por módulo

Contrato, autorização, isolamento por empresa, estados de UI, CRUD necessário, anexos/exportações, testes, comparação de dados, observabilidade, documentação e rollback precisam estar aprovados antes do corte.
