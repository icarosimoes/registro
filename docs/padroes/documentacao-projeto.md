# Padrão de documentação do Registro

Adaptado do padrão Aloji para manter memória e operação junto do código.

| Documento | Responsabilidade |
| --- | --- |
| `README.md` | índice e ponto de entrada |
| `mapa.md` | estado, componentes e domínios |
| `arquitetura.md` | limites e decisões estruturais |
| `domain-model.md` | entidades e invariantes |
| `api-reference.md` | contratos HTTP implementados |
| `web-rotas-ui.md` | rotas, estados e padrões de UI |
| `backlog.md` | prioridade e Definition of Done |
| `memoria-projeto.md` | decisões duráveis e contexto |
| `registro-trabalho.md` | execução cronológica e incidentes |
| `infra/` | deploy, operação, testes e migração |
| `agentes/` | regras Jarvis aplicadas localmente |

Documente contrato atual como atual; futuro deve ser marcado como planejado. Nunca registrar secrets, dados pessoais, IPs privados desnecessários ou credenciais. Mudança técnica e atualização documental pertencem ao mesmo commit.
