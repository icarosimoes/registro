# Jarvis — Performance

## Web

- Fetches independentes usam `Promise.all`.
- Aba inativa não dispara fetch.
- Navegação interna usa `Link` e preserva o shell.
- Evitar transformar páginas inteiras em Client Components.
- Tabelas grandes são paginadas no servidor; busca tem debounce quando remota.

## API e banco

- Listagens retornam apenas colunas necessárias e contrato paginado.
- Evitar N+1; agregações pertencem ao SQL.
- Índice deve acompanhar filtros reais (`company_id`, status, datas e FKs), após validar o banco.
- Não usar `SELECT *` em contratos sensíveis ao schema legado.
- Medir antes de cachear; não introduzir Redis sem necessidade comprovada.

## Deploy

Health comprova processo; readiness comprova dependência. Rolling update usa imagem imutável e rollback automático.
