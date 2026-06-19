# Mapa do sistema

## Módulos legados identificados

| Domínio | Responsabilidade | Prioridade de migração |
| --- | --- | --- |
| Autenticação e ACL | usuários, perfis, permissões e empresas | 1 |
| Cadastros | setores, locais, funções e procedimentos | 2 |
| Ocorrências | registro, comentários, participantes e anexos | 3 |
| Reuniões | pautas, participantes, assuntos e anexos | 4 |
| Relatório de turno | frequências, manutenção e comentários | 5 |
| Inspeções | suítes, apartamentos e auditoria | 6 |
| Diário de obra | atividades, equipes, equipamentos e anexos | 7 |
| Relatórios | PDF e Excel | transversal |

## Serviços

| Serviço | Caminho | Estado |
| --- | --- | --- |
| Laravel 7 | `docs/v1/` | legado preservado |
| FastAPI | `api/` | fundação nova |
| Next.js | `web/` | fundação nova |
| MySQL | externo | banco atual, fonte de verdade |
| PostgreSQL | futuro | destino após estabilização da aplicação |

## Contratos críticos a preservar

- IDs existentes e relacionamentos do MySQL.
- Senhas Laravel existentes durante a convivência.
- `company_id`, `role_id`, ACL e regras de autorização.
- Semântica de `deleted_at` em entidades com soft delete.
- Caminhos e metadados dos anexos.
- Formatos de PDF/Excel necessários à operação.
