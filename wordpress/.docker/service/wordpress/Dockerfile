ARG WPCLI_VER=cli
ARG WP_VER=latest

FROM wordpress:${WPCLI_VER} AS wpcli

FROM wordpress:${WP_VER} AS base
LABEL autodelete="true"

USER root

# enable mod_headers for htaccess (only when apache is in use)
RUN if [ -x "$(command -v a2enmod)" ]; then a2enmod headers; fi

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

COPY php.ini /usr/local/etc/php/conf.d/php.ini

# install Composer
# RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# install wpcli
COPY --from=wpcli /usr/local/bin/wp /usr/bin/wp

# setup User
ARG UID=1000
ARG GID=${UID}
ARG UNAME=docker
ARG GNAME=${UNAME}
ARG PASS

ENV USER=${UNAME}

RUN set -ex \
  # add group
  && groupadd --gid "${GID}" "${GNAME}" \
  # add user
  && useradd --uid "${UID}" --gid "${GID}" --groups www-data \
  --create-home --no-log-init --shell /bin/bash "${UNAME}" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd

# shell prompt
RUN printf "\nPS1=\"\u@\h:\w# \"\nalias ll=\"ls -la\"" >> "${HOME}/.bashrc"

FROM base AS develop

# RUN install-php-extensions xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN usermod -aG sudo ${UNAME}

# Remove sudo notice after shell startup
RUN mkdir -p /etc/sudoers.d &&\
  printf "Defaults lecture = never" > /etc/sudoers.d/privacy
