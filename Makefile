.PHONY: build create-network up dev stop down sh install phpstan phpcs

APP_SERVICE="php"

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Generaremos una build de los contenedores
	docker-compose build --no-cache

create-network: ## Crea el network:
	docker network create siroko-network

up: ## Levanta contenedores. Detached mode (Run containers in the background)
	docker-compose up -d

dev: ## Levanta contenedores
	docker-compose up

stop: ## Para contenedores
	docker-compose stop

down: ## Para y eliminar contenedores
	docker-compose down

sh: ## Conectarse a la terminal del contenedor de la aplicación
	docker-compose exec ${APP_SERVICE} /bin/bash

install: ## Install dependencies
	docker-compose exec ${APP_SERVICE} /bin/sh -c 'composer install'

phpstan:
	docker-compose exec ${APP_SERVICE} /bin/sh -c 'vendor/bin/phpstan analyze -c phpstan.neon src'

phpcs:
	docker-compose exec ${APP_SERVICE} /bin/sh -c './vendor/bin/php-cs-fixer fix src'