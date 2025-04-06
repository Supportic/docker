.SILENT:
.ONESHELL:
SHELL = /bin/bash
MAKEFLAGS += --no-print-directory

-include .env

WORDPRESS_BASH_EXEC = docker compose exec wordpress bash
WORDPRESS_BASH_RUN = docker compose run --rm wordpress bash
WPCLI_BASH_RUN = docker compose run --rm wpcli bash
WP_CLI_RUN = docker compose run --rm wpcli wp

shell:
	$(WORDPRESS_BASH_EXEC)
wpcli:
	$(WPCLI_BASH_RUN)

###############################
# docker
###############################

start:
	docker compose up -d wordpress db adminer mailpit
stop:
	docker compose stop
down:
	docker compose down
restart: down start
remove:
	docker compose down -v
	$(MAKE) delete-wordpress
erase:
	docker compose down -v --rmi all
	$(MAKE) delete-wordpress

install: install-precondition install-image install-wordpress
rebuild: remove install

install-precondition:
	@if [ ! -f .env ]; then\
		echo "Copy and adjust values .env.sample => .env";\
	fi

install-image:
	docker compose build wordpress wpcli
	$(MAKE) start
	@docker rmi $$(docker images -q -f "dangling=true" -f "label=autodelete=true") 2> /dev/null || true

install-wordpress:
# make sure the entrypoint-script reruns which copys files into /var/www/html
	@if [ ! -f "wordpress/index.php" ]; then \
			docker compose restart wordpress; \
	fi;
	@echo "Installing WordPress..."
	$(WPCLI_BASH_RUN) -c "setup-wordpress.sh"
	$(MAKE) symlink-docker-create
	@echo "WordPress installation complete."
delete-wordpress:
	$(MAKE) symlink-docker-remove
	@echo "Deleting WordPress files..."
	@find wordpress -maxdepth 1 -mindepth 1 ! -name '.gitkeep' -exec rm -rf {} +
	@echo "WordPress files deleted."

log:
	$(WORDPRESS_BASH_RUN) -c "tail -f -n 30 wp-content/debug.log"

symlink-docker-create:
	docker compose run --rm -w /home/$(UNAME) wordpress bash -c "make symlink-docker-create"
symlink-docker-remove:
	docker compose run --rm -w /home/$(UNAME) wordpress bash -c "make symlink-docker-remove"
symlink-docker-remove-broken:
	docker compose run --rm -w /home/$(UNAME) wordpress bash -c "make symlink-docker-remove-broken"
symlink-docker-recreate:
	docker compose run --rm -w /home/$(UNAME) wordpress bash -c "make symlink-docker-recreate"
