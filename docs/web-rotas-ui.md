# Web — rotas e estados

## Estado atual

| Rota | Tipo | Estado | Dados |
| --- | --- | --- | --- |
| `/` | entrada | redireciona conforme cookie tenant | sessão server-side |
| `/login` | autenticação tenant | operacional | API `/auth/login` |
| `/dashboard` | dashboard autenticado | operacional | usuário real + indicadores demonstrativos |
| `/design-preview` | referência visual | protótipo livre | demonstração local |
| `/ocorrencias` | lista e CRUD demonstrativo | leitura API + mutações locais | V1 em `aero-hotel` |
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

O dashboard e os módulos validam o fluxo completo do redesign. Ocorrências carregam a leitura real da API quando o tenant possui dados importados; enquanto os endpoints de mutação não existem, alterações da interface persistem no `localStorage` por `company_id`. Os demais módulos permanecem fictícios.

## Integração planejada com a API

| Prioridade | Rota | Domínio |
| --- | --- | --- |
| P1 | `/usuarios`, `/cadastros` | acesso, ACL e cadastros reais |
| P2 | `/ocorrencias`, `/manutencao` | operação inicial |
| P3 | `/reunioes`, `/relatorios-turno` | atas e turno |
| P4 | `/inspecoes`, `/diarios-obra` | suites, auditoria e obra |

## Padrão obrigatório de tela

Toda lista tem título, contador, ação principal, filtros, tabela/cartões responsivos, paginação e estados de carregamento, vazio, erro e permissão. Exclusões exigem confirmação; ações exibem feedback. Ações sem permissão não aparecem e continuam bloqueadas na API.

## Timeline de alterações

Todo registro operacional possui um histórico de alterações (`history`) exibido no drawer de detalhes. Cada entrada registra:

| Campo | Conteúdo |
| --- | --- |
| `action` | Tipo da ação (Criou, Editou) |
| `user` | Nome do usuário que realizou |
| `date` | Data e hora no formato `dd/mm/aaaa hh:mm` |
| `changes` | Diferenças campo a campo (só em edições) |

A timeline é comum a todas as telas que usam o `OperationalModule`: ocorrências, reuniões, relatórios de turno, inspeções, diário de obra, manutenção, cadastros, usuários e mural. Quando a API assumir as mutações, o histórico será gravado em tabela de auditoria com `user_id`, `company_id` e payload imutável.
