ARG RUBY_VERSION='4.0'
ARG NODE_VERSION='26.3'
FROM ruby:${RUBY_VERSION}-slim-trixie AS base

USER root

##########################################################################
## setup linux packages
##########################################################################

RUN set -ex &&\
  export DEBIAN_FRONTEND="noninteractive" &&\
  apt-get update &&\
  apt-get -y --no-install-recommends install \
  sudo nano \
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

##########################################################################
## install Node && NPM && Yarn
##########################################################################

RUN set -ex \
  # create global npm packages directory
  && mkdir -p "/home/${UNAME}/.npm-global" \
  # create disposable npm cache directory
  && mkdir -p "/home/${UNAME}/.npm" \
  && chown -R "${UNAME}:${GNAME}" "/home/${UNAME}"

# This copies the entire node installation directory
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node --chmod=0755 /usr/local/bin/node /usr/local/bin/node

RUN ln -s /usr/local/bin/node /usr/local/bin/nodejs \
  && ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
  && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
  && ln -s /usr/local/lib/node_modules/corepack/dist/corepack.js /usr/local/bin/corepack

# tells node where to install global packages
ENV NPM_CONFIG_PREFIX="/home/${UNAME}/.npm-global"
# tells node where to find global executables
ENV PATH="/home/${UNAME}/.npm-global/bin:${PATH}"

USER ${UID}

## corepack removed from node v26+
# RUN corepack enable \
#   && corepack prepare yarn@stable --activate

RUN npm install -g @types/node yarn pnpm

USER root

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

RUN usermod -u "${UID}" --shell /bin/bash "${UNAME}" \
  && groupmod -g "${UID}" "${GNAME}" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd \
  && mkdir -p "/home/${UNAME}" \
  && chown -R "${UNAME}:${GNAME}" "/home/${UNAME}" \
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
