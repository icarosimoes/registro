# Jarvis — Asaas

O Asaas será usado para cobrar a assinatura do Registro, não para movimentar o financeiro operacional dos tenants nesta etapa. A integração está desenhada, mas desativada: não há chave configurada nem chamada externa no código atual.

## Fronteiras

- Credencial da plataforma e eventual credencial de tenant são segredos diferentes.
- A chave nunca chega ao navegador, log, banco em texto aberto ou imagem Docker.
- `subscriptions` guarda o vínculo comercial; `invoices` espelha cobranças e eventos externos.
- IDs externos são referências, não chaves primárias do Registro.
- Valores usam centavos inteiros e datas usam UTC.

## Webhooks futuros

- Validar `asaas-access-token` em comparação constante e falhar fechado.
- Persistir o identificador do evento antes de executar efeitos.
- Processar de forma idempotente: entrega repetida não duplica fatura, baixa ou auditoria.
- Responder rapidamente e mover trabalho pesado para processamento assíncrono.
- Aceitar eventos fora de ordem por meio de reconciliação com a API do provedor.
- Registrar metadados úteis sem armazenar segredos ou payloads sensíveis sem necessidade.

## Operação

Antes da ativação são obrigatórios sandbox, rotação de chave, URL HTTPS dedicada, alerta de falhas, replay seguro, reconciliação periódica e runbook de indisponibilidade. Nenhum webhook pode habilitar acesso cross-tenant.
