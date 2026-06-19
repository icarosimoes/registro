# Web — rotas e estados

## Estado atual

| Rota | Tipo | Estado | Dados |
| --- | --- | --- | --- |
| `/` | entrada | redireciona conforme cookie tenant | sessão server-side |
| `/login` | autenticação tenant | operacional | API `/auth/login` |
| `/dashboard` | dashboard autenticado | operacional | usuário real + indicadores demonstrativos |
| `/design-preview` | referência visual | protótipo livre | demonstração local |
| `/ocorrencias` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/reunioes` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/relatorios-turno` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/inspecoes` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/diarios-obra` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/manutencao` | lista e CRUD | operacional no navegador | dados fictícios por tenant |
| `/cadastros`, `/usuarios` | listas e CRUD | operacional no navegador | dados fictícios por tenant |
| `/mural` | cartões e CRUD | operacional no navegador | dados fictícios por tenant |
| `/configuracoes`, `/minha-conta` | formulários | operacional local | preferências do navegador |

## Painel administrativo (`admin/`)

| Rota | Estado | Dados |
| --- | --- | --- |
| `/login` | operacional | autenticação da plataforma |
| `/dashboard` | operacional | métricas, tenants e planos da API |

O admin é uma aplicação separada em `:3001`; a sessão usa cookie `httpOnly` e não compartilha o JWT do tenant.

O dashboard e os módulos validam o fluxo completo do redesign. CRUDs persistem no `localStorage` com chave por `company_id`, exclusivamente para teste. Eles não substituem autorização, validação ou persistência da futura API; números e atividades não devem orientar operação real.

## Integração planejada com a API

| Prioridade | Rota | Domínio |
| --- | --- | --- |
| P1 | `/usuarios`, `/cadastros` | acesso, ACL e cadastros reais |
| P2 | `/ocorrencias`, `/manutencao` | operação inicial |
| P3 | `/reunioes`, `/relatorios-turno` | atas e turno |
| P4 | `/inspecoes`, `/diarios-obra` | suites, auditoria e obra |

## Padrão obrigatório de tela

Toda lista tem título, contador, ação principal, filtros, tabela/cartões responsivos, paginação e estados de carregamento, vazio, erro e permissão. Exclusões exigem confirmação; ações exibem feedback. Ações sem permissão não aparecem e continuam bloqueadas na API.
