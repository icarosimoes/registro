# Jarvis — Layout e CRUD

## Shell

Sidebar responsiva/recolhível, topbar, busca e drawers usam tokens existentes. Ícones vêm de `lucide-react`; foco visível e navegação por teclado são obrigatórios.

## CRUD

Toda lista inclui título, contador, ação principal, filtros, paginação e coluna de ações. Toda tela trata loading, vazio, erro, sucesso e falta de permissão.

Formulários têm label, ajuda/erro associado, validação server-side e botão com estado de envio. Exclusão, cancelamento e ação irreversível exigem confirmação. Feedback de sucesso ou erro nunca é silencioso.

## Next.js

- Server Component para leitura inicial.
- Client Component somente para estado e eventos.
- Server Action/Route Handler para sessão httpOnly e mutações server-side.
- `Link` para navegação interna; `<a>` apenas para externo/download.
- A API continua sendo a autoridade de permissão, mesmo quando o botão está oculto.
