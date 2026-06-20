# Integração Chess Hotel ↔ Registro

## Contexto

O Chess Hotel é o PMS (Property Management System) hoteleiro usado pela operação. Possui dois módulos:

- **Chess Hotel** (front) — recepção, reservas, check-in/out
- **Chess Gestão** (back) — backoffice financeiro e administrativo

O Registro não substitui nem duplica funcionalidades do Chess. A integração existe para **documentar e rastrear a comunicação entre recepção e financeiro** quando há problemas com emissão de notas fiscais, e para abrir chamados de manutenção.

## Arquitetura

A integração é unidirecional: o Chess abre chamados no Registro. O Registro não modifica dados no Chess.

```
Chess Hotel (Vue/Vuetify)
  └─ RegistroLauncher.vue (navbar)
       ├─ Solicitação Fiscal → POST /api/v1/fiscal-requests (futuro)
       └─ Chamado de Manutenção → POST /api/v1/occurrences (futuro)
```

## Componentes no Chess Hotel

Repositório: `/home/icarosimoes/dev/chess-hotel`

| Arquivo | Função |
| --- | --- |
| `spa/src/components/RegistroLauncher.vue` | Botão no navbar, dropdown de tipo, drawer com formulário |
| `spa/src/layouts/.../LayoutContentVerticalNav.vue` | Layout modificado para incluir o launcher |

## Launcher — comportamento

1. Botão laranja (ícone clipboard) no navbar, antes do menu do usuário.
2. Ao clicar, dropdown com duas opções: Solicitação Fiscal ou Chamado de Manutenção.
3. Ao selecionar, abre drawer lateral (480px) com formulário específico.
4. Auto-captura do contexto: nome do usuário (Vuex `login/getUser`), UH da rota (`$route.params.room_id`).
5. Envia POST para `VUE_APP_REGISTRO_API_URL`. Se falhar ou não estiver configurado, salva em `localStorage` sob `registro_pending_tickets`.
6. Exibe snackbar com protocolo (`REG-{timestamp}`) e fecha o drawer.

## Tipos de solicitação fiscal

| Tipo | Campos específicos |
| --- | --- |
| Dados do tomador incorretos | Nº reserva, CNPJ/CPF, nome, endereço, correção |
| Nota travada / erro no sistema | Nº da nota, descrição do erro, print (anexo obrigatório) |
| Nota solicitada após check-out | Nº reserva, data check-out, dados completos do tomador |
| Cancelamento de nota emitida | Nº da nota, motivo do cancelamento |

Campos comuns: UH/apartamento, observações, anexos.

## Pendências

- [ ] Criar endpoints no Registro para receber chamados do Chess (autenticação por token de serviço)
- [ ] Configurar `VUE_APP_REGISTRO_API_URL` no ambiente do Chess
- [ ] Definir autenticação cross-system (SSO, token compartilhado ou login embarcado)
- [ ] Implementar fila de sincronização para tickets salvos em localStorage quando offline
- [ ] Testar CORS entre domínios Chess e Registro em produção
