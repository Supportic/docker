.SILENT:
.ONESHELL:
SHELL = /bin/bash
TERMINAL = docker compose exec php bash
SYMFONY = docker compose run --rm --entrypoint symfony php
COMPOSER = docker compose exec php composer

config:
	docker compose -f compose.yaml -f .devcontainer/compose.yaml config

shell:
	$(TERMINAL)

start:
	docker compose up --build -d nginx php db adminer mailhog
stop:
	docker compose stop
down:
	docker compose down
erase:
	docker compose down -v --rmi all

install: install-container install-deps

install-container:
	docker compose up --build -d nginx php db adminer mailhog || exit 1
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") 2> /dev/null || true

install-deps:
	$(COMPOSER) install
	docker compose stop

update:
	$(COMPOSER) update
