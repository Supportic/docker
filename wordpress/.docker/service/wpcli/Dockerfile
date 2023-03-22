ARG WPCLI_VER=cli
FROM wordpress:${WPCLI_VER}
LABEL autodelete="true"

USER root

RUN apk add --no-cache shadow bash

# setup User
ARG UID=1000
ARG GID=${UID}
ARG UNAME=docker
ARG GNAME=${UNAME}
ARG PASS=test

ENV USER=${UNAME}

RUN set -ex \
  # add group
  && addgroup --gid "${GID}" "${GNAME}" \
  # add user
  && adduser -D --gecos "" --ingroup "${GNAME}" --shell /bin/bash --uid "${UID}" "${UNAME}" \
  # add user to group
  && addgroup "${UNAME}" www-data \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd
  