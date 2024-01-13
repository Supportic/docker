ARG PHP_VER=8.2-fpm-bookworm
FROM php:${PHP_VER} as base
LABEL autodelete="true"

USER root

ARG UID=1000
ARG GID=${UID}
ARG UNAME=docker
ARG GNAME=${UNAME}
ARG PASS=password

ENV USER=${UNAME}

# setup User
RUN set -ex \
  # add group
  && groupadd --gid "${GID}" "${GNAME}" \
  # add user
  && useradd --uid "${UID}" --gid "${GID}" --groups www-data \
  --create-home --no-log-init --shell /bin/bash "${UNAME}" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd

COPY php.ini $PHP_INI_DIR/conf.d/php.ini

# install Composer
RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
  install-php-extensions zip intl opcache pcntl pdo pdo_mysql apcu

FROM base as develop

# php -r 'phpinfo();' | grep -i conf
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# order important - creates docker-php-ext-xdebug.ini
RUN install-php-extensions xdebug && rm "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"
COPY xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

RUN export DEBIAN_FRONTEND=noninteractive \
  && apt-get update \
  && apt-get -y --no-install-recommends install \
  sudo \
  && rm -rf /var/lib/apt/lists/*

RUN usermod -aG sudo ${UNAME}

# Remove sudo notice after shell startup
RUN mkdir -p /etc/sudoers.d &&\
  printf "Defaults lecture = never" > /etc/sudoers.d/privacy

# [Optional SUDO] Let user execute sudo without password prompt.
# RUN echo ${UNAME} ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/${UNAME} &&\
#   chmod 0440 /etc/sudoers.d/${UNAME}

# RUN sed -i "s/user = www-data/user = ${UNAME}/g" $PHP_INI_DIR/php-fpm.d/www.conf
# RUN sed -i "s/group = www-data/group = ${GNAME}/g" $PHP_INI_DIR/php-fpm.d/www.conf

# install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

FROM base as production

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
