#!/usr/bin/env bash

set -Eeuo pipefail

npm install -g npm@latest @types/node

exec "$@"
