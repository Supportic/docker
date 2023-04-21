ARG WP_VER=latest
FROM wordpress:${WP_VER} as base
LABEL autodelete="true"

USER root

# enable mod_headers for htaccess (only when apache is in use)
RUN if [ -x "$(command -v a2enmod)" ]; then a2enmod headers; fi

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

COPY php.ini /usr/local/etc/php/conf.d/php.ini

# install Composer
RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

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

RUN chown -R "${UID}:${GID}" /var/www/html/wp-content

FROM base as develop

# RUN install-php-extensions xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN usermod -aG sudo ${UNAME}

# Remove sudo notice after shell startup
RUN mkdir -p /etc/sudoers.d &&\
  printf "Defaults lecture = never" > /etc/sudoers.d/privacy