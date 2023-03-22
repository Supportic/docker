.SILENT:
.ONESHELL:
SHELL = /bin/bash
WP_CLI = docker compose run --rm wpcli wp

shell:
	docker compose exec wordpress bash
wpcli:
	docker compose run --rm wpcli bash

start:
	docker compose up -d wordpress db adminer
stop:
	docker compose stop
down:
	docker compose down
erase:
	docker compose down -v --rmi all

install: install-container install-deps

install-container: 
	docker compose up -d wordpress db adminer
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") 2> /dev/null || true

install-deps:
	$(WP_CLI) core install --url="http://localhost" --title=WPDemo --admin_user=admin --admin_password=admin --admin_email=admin@example.com --locale=en_US --skip-email --quiet
	$(WP_CLI) plugin uninstall akismet hello
	$(WP_CLI) plugin install debug-bar query-monitor wp-debugging --activate
	