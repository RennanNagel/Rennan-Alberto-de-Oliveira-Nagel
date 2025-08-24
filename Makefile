SHELL := /bin/bash

.PHONY: init up down seed test logs ps assets

init:
	docker compose pull
	docker compose build
	docker compose up -d
	docker compose exec php composer install
	docker compose exec php php artisan key:generate
	docker compose exec php php artisan migrate:fresh --seed
	$(MAKE) assets 

up:
	docker compose up -d

down:
	docker compose down

seed:
	docker compose exec php php artisan migrate:fresh --seed

test:
	docker compose exec php php artisan test

logs:
	docker compose logs -f

ps:
	docker compose ps

assets:
	docker compose run --rm -w /app/src node sh -lc 'npm ci && npm run build'