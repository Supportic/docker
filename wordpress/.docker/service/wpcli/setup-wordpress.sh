#!/usr/bin/env bash

set -Eeuo pipefail

# echo "Running as: $(id -un)"

if ! wp core is-installed; then
  echo >&2 "Installing WordPress"

  wp core install \
    --url=http://localhost \
    --title=WPDemo \
    --admin_user=admin \
    --admin_password=admin \
    --admin_email=admin@example.com \
    --locale=en_US \
    --skip-email \
    --quiet
  wp option update timezone_string "Europe/Berlin"
  wp option update time_format "H:i"
  wp option update date_format "d.m.Y"
  wp rewrite structure '/%postname%/'

  if ! wp language core is-installed de_DE; then
      wp language core install de_DE
  fi

  # delete existing pages and posts (and connected comments)
  wp post delete $(wp post list --post_type='page,post' --format=ids) --force

  wp theme delete --all
  wp plugin uninstall --all
  # wp plugin delete --all
fi

if wp core is-installed; then
  echo >&2 "Preparing Themes"

  # wp theme update --all

  for theme in $(cat /themes.txt); do
    if ! wp theme is-installed "$theme"; then
      wp theme install "$theme" --activate
    else
      wp theme update "$theme"
    fi
  done
fi

if wp core is-installed; then
  echo >&2 "Preparing Plugins"

  # wp plugin install --activate $(cat /plugins.txt)

  for plugin in $(cat /plugins.txt); do
    if ! wp plugin is-installed "$plugin"; then
      wp plugin install "$plugin" --activate
    else
      wp plugin update "$plugin"
    fi
  done
fi

# for apache2-foreground
exec "$@"
