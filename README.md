# php-validation

Teste Técnico

# Teste para à vaga de Desenvolvedor Full Stack

Teste de Rennan Alberto de Oliveira Nagel

## Requisitos

- Docker + Docker Compose
- (Opcional) make para executar os atalhos, se não tiver, use os one-liners abaixo

_A aplicação sobe em: http://localhost:8080_

## Setup

### Setup Inicial(apenas a primeira vez)

`make init`

- O que faz:
- Baixa e constrói as imagens (`docker compose pull/build`)
- Instala dependências PHP (`composer install`)
- Sobe os containers
- Gera a APP_KEY
- Roda migrações do banco e popula com dados fake + usuário de teste

_Login web:_

- Email: teste@exemplo.com
- Senha: teste123

### Subir/derrubar conteiners

`make up` _sobe p conteiner_
`make down` _derruba o conteiner_

### Resetar e semear banco de dados novamente

`make seed`

### Rodar os testes

`make test`

## Setup sem make

### Setup inicial

`docker compose pull`
`docker compose build`
`docker compose up -d`
`docker compose exec php composer install`
`docker compose exec php php artisan key:generate`
`docker compose exec php php artisan migrate:fresh --seed`
`docker compose run --rm -w /app/src node sh -lc 'npm ci && npm run build'`

### Subir/derrubar conteiners

`docker compose up -d`
`docker compose down`

### Resetar e semear o banco novamente

`docker compose exec php php artisan migrate:fresh --seed`

### Testes

`docker compose exec php php artisan test`

## Api - exemplos

### Ping

`curl -s http://localhost:8080/api/v1/ping`

### Login e obter token(bash)

```
TOKEN=$(curl -s -X POST http://localhost:8080/api/v1/auth/login \
  -H "Accept: application/json" \
  -d "email=teste@exemplo.com" \
  -d "password=teste123" | php -r 'echo json_decode(stream_get_contents(STDIN))->token ?? "";')
echo "$TOKEN"
```

### Login e obter token (powershell)

```
$login = Invoke-RestMethod -Method Post -Uri "http://localhost:8080/api/v1/auth/login" `
  -Headers @{ Accept = "application/json" } `
  -Body @{ email = "teste@exemplo.com"; password = "teste123" } `
  -ContentType "application/x-www-form-urlencoded"
$token = $login.token
$token

```

### Listar clientes (token obrigatório)

```
curl -s http://localhost:8080/api/v1/clients \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

```

### Ver um cliente

```
curl -s http://localhost:8080/api/v1/clients/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

```

### Criar um cliente

```
curl -s -X POST http://localhost:8080/api/v1/clients \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "name=Fulano API" \
  -d "email=fulano.api@example.com" \
  -d "phone=11999998888" \
  -d "password=12345678"

```

### Logout(revoga o token atual)

```
curl -s -X POST http://localhost:8080/api/v1/auth/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

```
