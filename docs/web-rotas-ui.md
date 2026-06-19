# Web — rotas e estados

## Estado atual

| Rota | Tipo | Estado | Dados |
| --- | --- | --- | --- |
| `/` | dashboard | protótipo responsivo | demonstração local |

O dashboard atual valida o redesign: sidebar recolhível, topbar, busca, indicadores, tabela, mural e drawers. Os números e atividades ainda são demonstrativos e não devem orientar operação.

## Rotas planejadas

| Prioridade | Rota | Domínio |
| --- | --- | --- |
| P0 | `/login` | autenticação |
| P0 | `/` | dashboard autenticado |
| P1 | `/usuarios`, `/perfis` | acesso e ACL |
| P1 | `/cadastros/setores`, `/cadastros/locais`, `/cadastros/funcoes` | cadastros |
| P1 | `/cadastros/procedimentos` | procedimentos e anexos |
| P2 | `/ocorrencias` | ocorrências |
| P3 | `/reunioes` | reuniões e atas |
| P3 | `/relatorios-turno` | turno |
| P4 | `/inspecoes` | suites, auditoria e vistorias |
| P4 | `/diarios-obra` | diário de obra |

## Padrão obrigatório de tela

Toda lista tem título, contador, ação principal, filtros, tabela/cartões responsivos, paginação e estados de carregamento, vazio, erro e permissão. Exclusões exigem confirmação; ações exibem feedback. Ações sem permissão não aparecem e continuam bloqueadas na API.
