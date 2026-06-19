# Integração Asaas

## Situação

Planejada e desativada. O schema possui campos para IDs externos em assinatura e fatura, mas nenhuma credencial ou chamada ao Asaas foi adicionada.

## Fluxo previsto

1. A plataforma cria ou vincula o cliente externo ao tenant.
2. Cria a cobrança da assinatura e salva somente IDs, estado e valores necessários.
3. O webhook autenticado persiste o evento recebido.
4. Um processador idempotente atualiza a fatura e a assinatura.
5. Uma rotina periódica reconcilia divergências com o provedor.

O evento pode ser entregue mais de uma vez e fora da ordem esperada. Por isso, recebimento e efeito de negócio são separados, identificadores externos são únicos e cada transição precisa ser repetível.

## Variáveis futuras

```text
ASAAS_ENVIRONMENT=sandbox
ASAAS_API_KEY=<docker-secret>
ASAAS_WEBHOOK_TOKEN=<docker-secret-diferente>
ASAAS_WEBHOOK_URL=https://api.exemplo.com/api/v1/webhooks/asaas
```

Essas variáveis não existem no Compose atual de propósito. A ativação depende de sandbox, HTTPS, política de cobrança e testes de replay/reconciliação.
