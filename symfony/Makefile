.SILENT:
.ONESHELL:
SHELL = /bin/bash
TERMINAL = docker compose exec php bash
SYMFONY = docker compose run --rm --entrypoint symfony php
COMPOSER = docker compose exec php composer
NPM = docker compose run --rm --service-ports --entrypoint npm node
TERMINAL_NODE = docker compose run --rm --service-ports node

config:
	docker compose -f compose.yaml -f .devcontainer/compose.yaml config

shell:
	$(TERMINAL)
shell-node:
	$(TERMINAL_NODE)

watch:
	$(NPM) run watch
assets:
	$(NPM) run build

start:
	docker compose up --build -d nginx php db adminer
stop:
	docker compose stop
destroy:
	docker compose down
erase:
	docker compose down -v --rmi all

install: install-container install-deps

install-container:
	docker compose up --build -d nginx php db adminer || exit 1
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") 2> /dev/null || true

install-deps:
	$(NPM) clean-install
	$(COMPOSER) install
	docker compose stop

update:
	$(NPM) update
	$(COMPOSER) update
