.SILENT:
.ONESHELL:
SHELL = /bin/bash
.SHELLFLAGS := -eu -o pipefail -c
MAKEFLAGS += --no-print-directory
MAKEFLAGS += --warn-undefined-variables

ifneq (,$(wildcard ./.env))
  include .env
endif

BASH_EXEC = docker compose exec ruby bash
# BASH_RUN = docker compose exec ruby bash
NPM_EXEC := docker compose exec ruby npm

shell:
	$(BASH_EXEC)

###############################
# docker
###############################

start:
	docker compose up -d ruby db adminer
stop:
	docker compose stop
down:
	docker compose down
restart: down start
remove:
	docker compose down -v
erase:
	docker compose down -v --rmi all

install: install-precondition install-images start

install-precondition:
	@if [ ! -f .env ]; then\
		echo "Copy and adjust values .env.sample => .env";\
		exit 1;\
	fi

install-images:
	docker compose build --pull ruby
	docker compose pull db adminer
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") > /dev/null 2>&1 || true
