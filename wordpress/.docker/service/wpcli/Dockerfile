# always alpine
FROM wordpress:cli-2-php8.3
LABEL autodelete="true"

USER root

RUN apk add --no-cache shadow bash

# setup User
ARG UID=1000
ARG GID=${UID}
ARG UNAME=docker
ARG GNAME=${UNAME}
ARG PASS=password

ENV USER=${UNAME}

RUN set -ex \
  # add group
  && addgroup --gid "${GID}" "${GNAME}" \
  # add user
  && adduser -D --gecos "" --ingroup "${GNAME}" --shell /bin/bash --uid "${UID}" "${UNAME}" \
  # add user to group
  # && addgroup "${UNAME}" www-data \
  # shell prompt
  && printf "\nPS1=\"\\\u@\\\h:\\\w# \"\nalias ll=\"ls -la\"" >> "/home/${UNAME}/.bashrc" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd

COPY install-plugins.txt /install-plugins.txt
COPY install-themes.txt /install-themes.txt
COPY --chown=${UID}:${GID} --chmod=0755 setup-wordpress.sh /usr/local/bin/setup-wordpress
