.SILENT:
.ONESHELL:
SHELL = /bin/bash
WP_CLI_BASH = docker compose run --rm wpcli bash

shell:
	docker compose exec wordpress bash
wpcli:
	$(WP_CLI_BASH)

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
	$(WP_CLI_BASH) -c "wp core install --url="http://localhost" --title=WPDemo --admin_user=admin --admin_password=admin --admin_email=admin@example.com --locale=en_US --skip-email --quiet && wp option update timezone_string "Europe/Berlin" && wp option update time_format "H:i" && wp rewrite structure '/%postname%/' && wp language core install de_DE"
	$(WP_CLI) plugin uninstall akismet hello
	$(WP_CLI) plugin install query-monitor --activate
	