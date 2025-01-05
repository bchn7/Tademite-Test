.PHONY: up down build install migrate create-migration test logs bash

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

install:
	docker compose exec php composer install

migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

create-migration:
	docker compose exec php php bin/console doctrine:migrations:diff

test:
	docker compose exec php php bin/console doctrine:schema:validate
	docker compose exec php php bin/phpunit

logs:
	docker compose logs -f

bash:
	docker compose exec php bash