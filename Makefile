.PHONY: up down build restart test migrate seed fresh shell-app shell-mysql logs setup

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build --no-cache

restart: down up

# Setup: build images, start containers, run migrations, seed
setup: build up
	@echo "Waiting for MySQL to be ready..."
	@sleep 15
	$(MAKE) migrate
	$(MAKE) seed
	@echo ""
	@echo "==========================================="
	@echo "  CRM Hackathon is ready!"
	@echo "  API:      http://localhost:8000/api"
	@echo "  Frontend: http://localhost:3000"
	@echo "==========================================="

# --- Backend ---
test:
	docker-compose exec app php artisan test --colors=always

test-unit:
	docker-compose exec app php artisan test --testsuite=Unit --colors=always

test-feature:
	docker-compose exec app php artisan test --testsuite=Feature --colors=always

migrate:
	docker-compose exec app php artisan migrate --force

migrate-fresh:
	docker-compose exec app php artisan migrate:fresh --force

seed:
	docker-compose exec app php artisan db:seed --force

fresh: migrate-fresh seed

shell-app:
	docker-compose exec app bash

artisan:
	docker-compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

# --- Database ---
shell-mysql:
	docker-compose exec mysql mysql -u hackathon -phackathon_secret hackathon_crm

# --- Logs ---
logs:
	docker-compose logs -f

logs-app:
	docker-compose logs -f app

logs-nginx:
	docker-compose logs -f nginx

# --- Frontend ---
shell-frontend:
	docker-compose exec frontend sh

npm:
	docker-compose exec frontend npm $(filter-out $@,$(MAKECMDGOALS))

%:
	@:
