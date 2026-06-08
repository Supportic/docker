ARG RUBY_VERSION='4.0'
FROM ruby:${RUBY_VERSION}-slim-trixie AS base

USER root

##########################################################################
## setup linux packages
##########################################################################

RUN set -ex &&\
  export DEBIAN_FRONTEND="noninteractive" &&\
  apt-get update &&\
  apt-get -y --no-install-recommends install \
  # Rails relies on websocket-driver but it needs to be build. The docker slim image does not have development utilities like make and a C compiler. Those packages aren't pre-installed on the -slim image.
  build-essential \
  # allow the pg gem to communicate with your PostgreSQL database
  libpq-dev \
  sudo nano tzdata \
  && apt-get clean &&\
  rm -rf /var/lib/apt/lists/*

ENV EDITOR="/usr/bin/nano"

##########################################################################
## set the container timezone
##########################################################################

ARG TZ="Europe/Berlin"
ENV TZ=${TZ}
RUN ln -snf "/usr/share/zoneinfo/$TZ" /etc/localtime &&\
  dpkg-reconfigure -f noninteractive tzdata

FROM base AS ruby_deps

RUN gem install rails

FROM ruby_deps AS develop
LABEL autodelete="true"

##########################################################################
## setup user
##########################################################################

ARG UID="1000"
ARG GID="1000"
ARG UNAME="docker"
ARG GNAME="docker"
ARG PASS="password"
ENV HOME="/home/${UNAME}"

RUN set -ex \
  # add group
  && groupadd --gid "${GID}" "${GNAME}" \
  # add user
  && useradd --uid "${UID}" --gid "${GID}" --groups www-data \
  --create-home --no-log-init --shell /bin/bash "${UNAME}" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd \
  # shell prompt (no default .bashrc without includes)
  && printf "alias ll=\"ls -la\"\nPS1=\"\\\u@\\\h:\\\w# \"\n" > "/home/${UNAME}/.bashrc" \
  && chown "${UNAME}:${GNAME}" "/home/${UNAME}/.bashrc"

# installing sudo does not take care of existing users
RUN usermod -aG sudo "${UNAME}" &&\
  printf "${UNAME} ALL=(ALL:ALL) ALL" > "/etc/sudoers.d/${UNAME}" &&\
  chmod 0440 "/etc/sudoers.d/${UNAME}" &&\
  printf "Defaults lecture = never" > /etc/sudoers.d/privacy

USER ${UNAME}

WORKDIR /rails

FROM ruby_deps AS production
LABEL autodelete="true"
