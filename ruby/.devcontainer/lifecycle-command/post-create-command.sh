#!/usr/bin/env bash

set -Eeuo pipefail

# Install dependencies

if [ -f package.json ]; then
  if [ -f package-lock.json ]; then
    npm ci
  else
    npm install
  fi
  # npm run build
fi

if [ -f composer.json ]; then
  set +e
  composer dump-autoload -q > /dev/null 2>&1
  valid_classmap=$?
  composer validate -q > /dev/null 2>&1
  valid_composer_config=$?
  set -e

  if [ "$valid_composer_config" -ne 0 ]; then
    echo "composer.json file is invalid. Run \"composer validate\"."
  fi

  if [ "$valid_classmap" -ne 0 ]; then
    echo "Classmap generation failed. Please check your autoload configuration in composer.json."
  fi

  if [ "$valid_composer_config" -eq 0 ] && [ "$valid_classmap" -eq 0 ]; then
    composer install
  fi
fi

# make translate

exec "$@"
