APP_CONTAINER_ID = `docker compose ps -q app`

init_local:
	@cp .env.example .env
	@cp docker-compose-local.yml compose.yml

init_prod:
	@cp .env.example .env
	@cp docker-compose-prod.yml compose.yml

init_mutagen:
	@curl -L https://github.com/mutagen-io/mutagen/releases/download/v0.16.2/mutagen_linux_amd64_v0.16.2.tar.gz | sudo tar -zxf - -C /usr/local/bin
	@curl -L https://github.com/mutagen-io/mutagen-compose/releases/download/v0.16.2/mutagen-compose_linux_amd64_v0.16.2.tar.gz | sudo tar -zxf - -C /usr/local/bin

docker_build:
	@docker compose build --no-cache

composer_install:
	@docker compose exec app composer install --ignore-platform-reqs

cache_clear:
	@docker compose exec app php artisan cache:clear
	@docker compose exec app php artisan view:clear
	@docker compose exec app php artisan route:clear
	@docker compose exec app php artisan config:clear

permission:
	@docker compose exec app chmod -R 777 /code/storage bootstrap/cache

migrate:
	@docker compose exec app php artisan migrate

migrate_seed:
	@docker compose exec app php artisan migrate:fresh --seed --drop-views

test_seed:
	@docker compose exec app php artisan migrate:fresh --seed --drop-views --env=testing

test:
	@docker compose exec app bash -c "vendor/bin/phpunit --testdox --colors=always"

bash:
	@docker compose exec app bash

node-ssh:
	@docker compose exec js bash

ide_helper:
	@docker compose exec app php artisan ide-helper:generate

ide_helper_models:
	@docker compose exec app php artisan ide-helper:models --dir="infra/EloquentModels"

up:
	@docker compose up -d

up-mutagen:
	@mutagen-compose -f compose.yml -f compose.mutagen.yml up -d

down:
	@docker compose down

down-mutagen:
	@mutagen-compose -f compose.yml -f compose.mutagen.yml down

stop:
	@mutagen-compose stop

setup:
	@make composer_install
	@docker compose exec app php artisan key:generate
	@make ide_helper
	@make cache_clear
	@make migrate_seed
	@make test_seed
	@make permission
	@docker compose exec app php artisan storage:link

start_discord_bot:
	@docker compose exec app php artisan discord-bot:run

