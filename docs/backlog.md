# Backlog da modernização

## P0 — consolidar SaaS e receber dados reais

- [x] Criar MySQL local, migrations e seed fictício com dois tenants.
- [x] Separar autenticação tenant e plataforma.
- [x] Criar painel administrativo inicial.
- [x] Testar bloqueio de token com `company_id` divergente.
- [x] Receber dump de desenvolvimento e restaurar/importar em staging temporária.
- [x] Inventariar as 66 tabelas do dump V1 e importar o núcleo normalizado.
- [x] Validar login compatível com hashes bcrypt Laravel.
- [x] Implementar página `/login` do produto tenant com cookie `httpOnly`.
- [x] Corrigir o login multitenant para validar a senha antes de revelar a lista de empresas.
- [x] Adicionar testes de login para e-mail único, senha inválida, e-mail em múltiplos tenants e seleção explícita de tenant.
- [x] Validar `company_id` positivo no contrato de login e documentar integralmente a resposta `422 multi_tenant`.
- [ ] Concluir inventário de índices, constraints, collations, órfãos, soft deletes, anexos e volumes fora do banco.
- [ ] Criar migration de upgrade que renomeie com segurança um eventual tenant antigo `aero-v1` para `aero-hotel`, sem criar tenant duplicado.
- [ ] Ampliar testes cross-tenant para cada domínio novo.
- [ ] Criar CI com Ruff, mypy, pytest, typecheck, build e auditorias.
- [ ] Estabilizar a execução do mypy na imagem de desenvolvimento; a versão 1.20.2 encerrou com erro interno em 20/06/2026.

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
- [x] Timeline de alterações nos registros operacionais (front local, todas as telas).
- [ ] Componentes reutilizáveis de lista, formulário, estado vazio e confirmação.
- [ ] Tabela de auditoria na API para persistir o histórico de alterações com `user_id` e `company_id`.
- [ ] Persistir comentários e alterações da tratativa na API; remover o histórico operacional do `localStorage`.
- [ ] Tornar a auditoria imutável, com ator, tenant, data UTC, tipo de evento e diferenças estruturadas.
- [ ] Registrar na timeline todos os campos específicos do domínio, inclusive os campos fiscais e anexos.
- [ ] Impedir a criação de eventos de alteração vazios quando nenhum campo tiver mudado.

## P3 — operação

- [x] Remover o corte nos primeiros 100 registros, carregando todas as páginas disponíveis da API de ocorrências.
- [ ] Evoluir ocorrências para paginação e busca server-side sob demanda antes de volumes altos, evitando carregar todo o conjunto no servidor Next.js.
- [x] Remover a precedência de dados antigos do `localStorage` sobre ocorrências retornadas pela API.
- [x] Manter ocorrências reais explicitamente em modo leitura enquanto os endpoints de mutação não existirem.
- [ ] Implementar mutações de ocorrências na API com autorização, auditoria e testes cross-tenant.
- [ ] Ocorrências: comentários, participantes, anexos, clone e PDF.
- [ ] Reuniões, participantes, assuntos, anexos, início e ata PDF.
- [ ] Relatórios de turno e Excel.

## P3B — solicitações fiscais

- [x] Criar protótipo funcional no frontend com tipos de solicitação, campos condicionais, SLA, anexos e tratativa.
- [ ] Criar modelo, migration, schemas, service, autorização e endpoints FastAPI para solicitações fiscais.
- [ ] Persistir solicitações, responsáveis e notificações por IDs, sempre isolados por `company_id`.
- [ ] Definir SLA no servidor, incluindo timezone, calendário útil, pausa, conclusão e política de vencimento.
- [ ] Substituir anexos Base64 no `localStorage` por armazenamento próprio, com metadados no banco.
- [ ] Validar tamanho, quantidade, extensão, MIME real, nome e autorização de download dos anexos.
- [ ] Restringir previews e downloads para impedir conteúdo ativo ou arquivo malicioso.
- [ ] Validar e normalizar CPF/CNPJ, e-mail, reserva, nota fiscal e datas conforme o tipo da solicitação.
- [ ] Implementar notificações reais, preferências, destinatários e registro de entrega.
- [ ] Cobrir CRUD, SLA, anexos, auditoria e isolamento cross-tenant com testes.

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

## P6 — documentação e governança

- [x] Definir `/docs` como fonte de verdade técnica, funcional, operacional e histórica do Registro.
- [x] Registrar que toda informação pertinente ao desenvolvimento e ao sistema deve ser mantida em `/docs`.
- [ ] Atualizar `mapa.md`, contratos, runbooks, memória, backlog e registro de trabalho continuamente, junto da mudança correspondente.
- [x] Corrigir referências atuais que instruíam login por slug; o histórico cronológico preserva menções ao contrato antigo.
- [ ] Documentar o módulo de solicitações fiscais em `web-rotas-ui.md`, `domain-model.md` e `api-reference.md` conforme o backend for implementado.
- [ ] Criar ADR quando uma decisão alterar stack, isolamento, persistência, segurança, deploy, cobrança ou estratégia de migração.
- [ ] Manter documentação de estado atual separada de funcionalidades apenas planejadas.

## Correções de repositório

- [x] Restaurar `.idea/` e `.vscode/` no `.gitignore`.
- [ ] Manter `docs/v1/`, dumps SQL, credenciais, secrets e arquivos locais fora do Git e das imagens Docker.

## Definition of Done por módulo

Contrato, autorização, isolamento por empresa, estados de UI, CRUD necessário, anexos/exportações, testes, comparação de dados, observabilidade, documentação e rollback precisam estar aprovados antes do corte. Uma entrega não está concluída se a documentação pertinente em `/docs` estiver ausente ou desatualizada.
