.SILENT:
.ONESHELL:
SHELL = /bin/bash
WP_CLI_BASH = docker compose run --rm wordpress bash
WP_CLI = docker compose run --rm wordpress wp

shell:
	docker compose exec wordpress bash
wpcli:
	$(WP_CLI_BASH)

###############################
# docker
###############################

start:
	docker compose up -d db adminer mailpit wordpress
stop:
	docker compose stop
down:
	docker compose down
restart: down start

destroy:
	docker compose down -v
erase:
	docker compose down -v --rmi all

install: install-image install-plugins
rebuild: down install

install-image:
	[ -f .env ] || cp .env.sample .env
	[ -f .db.env ] || cp .db.env.sample .db.env
	docker compose build wordpress
	docker compose up -d db adminer mailpit wordpress
	$(WP_CLI_BASH) -c "wp core install --url="http://localhost" --title=WPDemo --admin_user=admin --admin_password=admin --admin_email=admin@example.com  --locale=en_US --skip-email --quiet && wp option update timezone_string "Europe/Berlin" && wp option update time_format "H:i" && wp rewrite structure '/%postname%/' && wp language core install de_DE"
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") 2> /dev/null || true

install-plugins:
	$(WP_CLI) plugin uninstall akismet hello
	$(WP_CLI) plugin install query-monitor wp-dummy-content-generator --activate
