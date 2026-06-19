# Registro V1 — Laravel legado

Esta pasta preserva a aplicação anterior completa, construída com Laravel 7, PHP 7.2+, Blade/AdminLTE e Laravel Mix.

## Conteúdo

- `app/`, `routes/`, `resources/`: aplicação e interface legadas;
- `database/`: 131 migrations e seeds;
- `public/`, `storage/`: assets e estrutura de arquivos;
- `tests/`: testes PHPUnit existentes;
- `composer.json`, `package.json`: dependências originais;
- `.env example`: contrato de ambiente da V1;
- `.github/`, `server_deploy.sh`: automação e deploy anteriores.

## Regra de preservação

A V1 é referência funcional e histórica durante a migração. Não criar funcionalidades novas aqui. Correções críticas devem ser documentadas em `../registro-trabalho.md` e comparadas com a implementação equivalente na nova stack.

## Execução excepcional da V1

Os comandos originais devem ser executados dentro desta pasta:

```bash
cd docs/v1
php artisan serve
```

O arquivo `.env example` não contém credenciais operacionais e deve ser copiado para `.env` apenas em ambiente local controlado.
