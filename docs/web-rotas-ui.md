# Web — rotas e estados

## Estado atual

| Rota | Tipo | Estado | Dados |
| --- | --- | --- | --- |
| `/` | entrada | redireciona conforme cookie tenant | sessão server-side |
| `/login` | autenticação tenant | operacional | API `/auth/login` |
| `/dashboard` | dashboard autenticado | operacional | usuário real + indicadores demonstrativos |
| `/design-preview` | referência visual | protótipo livre | demonstração local |
| `/ocorrencias` | lista e CRUD | CRUD via API + mutações server-side | API `occurrences` isolada por tenant |
| `/reunioes` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/relatorios-turno` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/inspecoes` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/diarios-obra` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/manutencao` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/solicitacoes-fiscais` | lista, formulário condicional, SLA, anexos e tratativa | CRUD via API + mutações server-side | API `fiscal_requests` isolada por tenant |
| `/cadastros`, `/usuarios` | listas e CRUD | operacional no navegador | dados fictícios por tenant |
| `/mural` | cartões e CRUD | operacional no navegador | dados fictícios por tenant |
| `/configuracoes`, `/minha-conta` | formulários | operacional local | preferências do navegador |

## Painel administrativo (`admin/`)

| Rota | Estado | Dados |
| --- | --- | --- |
| `/login` | operacional | autenticação da plataforma |
| `/dashboard` | operacional | métricas, tenants e planos da API |

O admin é uma aplicação separada em `:3001`; a sessão usa cookie `httpOnly` e não compartilha o JWT do tenant.

O dashboard e os módulos validam o fluxo completo do redesign. Ocorrências e solicitações fiscais possuem CRUD completo via API com mutações server-side e auditoria automática. Ocorrências usam paginação server-side (20 por página) com busca via query params na URL. Os demais módulos permanecem fictícios e persistem localmente por `company_id`.

## Integração planejada com a API

| Prioridade | Rota | Domínio |
| --- | --- | --- |
| P1 | `/usuarios`, `/cadastros` | acesso, ACL e cadastros reais |
| P2 | `/manutencao` | manutenção corretiva e preventiva |
| P3 | `/reunioes`, `/relatorios-turno` | atas e turno |
| P4 | `/inspecoes`, `/diarios-obra` | suites, auditoria e obra |

## Padrão obrigatório de tela

Toda lista tem título, contador, ação principal, filtros, tabela/cartões responsivos, paginação e estados de carregamento, vazio, erro e permissão. Exclusões exigem confirmação; ações exibem feedback. Ações sem permissão não aparecem e continuam bloqueadas na API.

## Tratativa (timeline de conversa)

Todo registro operacional possui uma timeline de tratativa (`history`) no estilo de conversa de ticket. A thread aparece em dois lugares:

- **Drawer de detalhes**: exibida abaixo dos dados do registro, com campo de comentário para adicionar mensagens.
- **Modal de edição**: exibida abaixo do formulário (somente leitura, sem campo de comentário), para que o usuário veja o histórico completo enquanto edita.

Cada entrada possui:

| Campo | Conteúdo |
| --- | --- |
| `type` | `comment` (mensagem livre), `change` (edição de campos) ou `create` (criação do registro) |
| `user` | Nome do usuário que realizou |
| `date` | Data e hora no formato `dd/mm/aaaa hh:mm` |
| `message` | Texto do comentário (em `comment` e `create`) |
| `changes` | Diferenças campo a campo, separadas por `;` (só em `change`) |

Visual por tipo:

| Tipo | Avatar | Conteúdo |
| --- | --- | --- |
| `comment` | azul (iniciais) | balão de mensagem com texto livre |
| `change` | roxo (iniciais) | chips listando cada campo alterado com valor anterior e novo |
| `create` | verde (iniciais) | mensagem em itálico indicando a criação |

A timeline é comum a todas as telas que usam o `OperationalModule`. A API já grava `AuditEvent` para cada mutação em ocorrências e solicitações fiscais com diff JSON. A próxima etapa é alimentar a timeline do frontend com esses eventos, removendo a dependência do `localStorage`.

## Solicitações fiscais

O protótipo atual atende solicitações da recepção para o financeiro: dados incorretos do tomador, nota travada, nota solicitada depois do check-out e cancelamento. O formulário apresenta campos condicionais de reserva, nota, CPF/CNPJ, tomador, correção, cancelamento, check-out, responsável e pessoas a notificar. A lista exibe UH, status e contagem regressiva de SLA.

### Persistência

Solicitações fiscais possuem CRUD completo via API (`POST`, `GET`, `PATCH`, `DELETE /fiscal-requests`). A criação e edição passam por server actions (`createFiscalRequestAction`, `updateFiscalRequestAction`, `deleteFiscalRequestAction`) que chamam a API com o token do cookie `tenant_token`. Após cada mutação, a página revalida via `router.refresh()`.

Campos específicos do tipo de solicitação (tomador, reserva, nota, CPF/CNPJ, correção, cancelamento, etc.) são enviados no campo `payload` como JSON.

A integração Chess Hotel cria solicitações via `POST /integrations/chess-hotel/tickets` autenticado por header `X-Registro-Key`.

### Limitações remanescentes

- o SLA da integração Chess Hotel calcula 24h corridas; o servidor ainda não aplica calendário útil ou timezone;
- anexos são Data URLs/Base64 sem limite de tamanho, quantidade ou validação real de MIME — não são persistidos na API;
- nomes informados em “Notificar” não correspondem a IDs e não disparam notificações;
- tratativas (comentários e alterações) ainda ficam no `localStorage` — a API já grava `audit_events`, mas o frontend ainda não os consome;
- alterações específicas do formulário fiscal ainda não aparecem integralmente na timeline.
