.ONESHELL:
SHELL=/bin/bash

config:
	docker compose -f compose.yaml -f .devcontainer/compose.yaml config

cleanup:
	@docker rmi $(docker images -q -f "dangling=true" -f "label=autodelete=true") > /dev/null 2>&1 || true