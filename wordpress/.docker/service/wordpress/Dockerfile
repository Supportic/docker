FROM wordpress:6.7.2-php8.3-apache AS cleanup

USER root

# Set the container timezone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
  && echo $TZ > /etc/timezone  \
  && dpkg-reconfigure -f noninteractive tzdata

# enable Apache modules
# mod_headers for htaccess
ARG APACHE_MODULES="headers"
RUN a2enmod $APACHE_MODULES

## Remove Initial Themes & Plugins
## changes in /usr/src/wordpress directory sometimes does not track changes on file save when reloading the browser
# RUN find /usr/src/wordpress/wp-content/plugins -maxdepth 1 -mindepth 1 ! -name 'index.php' -exec rm -rf {} + \
#   && find /usr/src/wordpress/wp-content/themes -maxdepth 1 -mindepth 1 ! -name 'index.php' -exec rm -rf {} +

## /usr/local/etc/php/conf.d/php.ini
COPY configs/php.ini "$PHP_INI_DIR/conf.d/php.ini"
COPY configs/uploads.ini "$PHP_INI_DIR/conf.d/uploads.ini"

## enable opcache when needed, only the existance of the file will enable opcache, even if the config says enable=0
# COPY configs/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# COPY custom.ini $PHP_INI_DIR/conf.d/
# Use the default production configuration
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY apache/vhost/000-default.conf "$APACHE_CONFDIR/sites-available/000-default.conf"
COPY apache/config/security.conf "$APACHE_CONFDIR/conf-available/security.conf"

## changes in /usr/src/wordpress directory sometimes does not track changes on file save when reloading the browser
# COPY --chown=www-data:www-data .htaccess /usr/src/wordpress/.htaccess

FROM cleanup AS base

# setup User
ARG UID=1000
ARG GID=${UID}
ARG UNAME=docker
ARG GNAME=${UNAME}
ARG PASS=password

RUN set -ex \
  # add group
  && groupadd --gid "${GID}" "${GNAME}" \
  # add user
  && useradd --uid "${UID}" --gid "${GID}" --groups www-data \
  --create-home --no-log-init --shell /bin/bash "${UNAME}" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd \
  # shell prompt
  && printf "\nPS1=\"\\\u@\\\h:\\\w# \"\nalias ll=\"ls -la\"" >> "/home/${UNAME}/.bashrc" \
  && chown "${UNAME}:${GNAME}" "/home/${UNAME}/.bashrc"

COPY --chown=${UID}:${GID} --chmod=0755 symlink.sh /usr/local/bin/symlink

# install Composer
# RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# install PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# https://make.wordpress.org/hosting/handbook/server-environment/#php-extensions
RUN install-php-extensions opcache igbinary timezonedb

FROM base AS develop
LABEL autodelete="true"

## allow root command without sudo
# RUN usermod -aG sudo ${UNAME}

# first install xdebug which sets zend_extension=xdebug.so automatically for you
RUN install-php-extensions xdebug-3.4.2 memcached

COPY configs/xdebug.ini "$PHP_INI_DIR/conf.d/xdebug.ini"

HEALTHCHECK --interval=10s --timeout=5s --start-period=10s --retries=3 \
  CMD curl --fail http://localhost || exit 1
