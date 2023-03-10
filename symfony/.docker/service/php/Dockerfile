ARG PHP_VER=8.1-fpm-bullseye
FROM php:${PHP_VER} as base
LABEL autodelete="true"

USER root

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
  install-php-extensions zip intl opcache pcntl pdo pdo_mysql apcu

COPY php.ini /usr/local/etc/php/conf.d/php.ini

# install Composer
RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# setup User
ARG UID=1000
ARG GID=${UID}
ARG UNAME=docker
ARG GNAME=${UNAME}
ARG PASS=test

ENV USER=${UNAME}

RUN set -ex \
  # add group
  && groupadd --gid "${GID}" "${GNAME}" \
  # add user
  && useradd --uid "${UID}" --gid "${GID}" --groups www-data \
  --create-home --no-log-init --shell /bin/bash "${UNAME}" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd

FROM base as develop

RUN install-php-extensions xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

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

# RUN sed -i "s/user = www-data/user = ${UNAME}/g" /usr/local/etc/php-fpm.d/www.conf
# RUN sed -i "s/group = www-data/group = ${GNAME}/g" /usr/local/etc/php-fpm.d/www.conf

# install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
