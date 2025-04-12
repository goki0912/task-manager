DCO := docker compose

init:
	@make build
	@make up
	@make migrate

build:
	$(DCO) build --no-cache

up:
	$(DCO) up -d

down:
	$(DCO) down

migrate:
	$(DCO) run --rm app php artisan migrate

tinker:
	$(DCO) run --rm app php artisan tinker

schedule:
	docker exec app php artisan schedule:work

queue:
	docker exec app php artisan queue:work

workers:
	@make schedule
	@make queue

test:
	$(DCO) -f docker-compose.test.yml run --rm app_test php artisan test --env=testing
