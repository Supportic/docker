ARG NODE_VER=26.4.0-alpine
FROM node:${NODE_VER} AS base
LABEL autodelete="true"

USER root

# setup User
ARG UID=1000
ARG GID=1000
ARG UNAME=docker
ARG GNAME=docker
ARG PASS=password

# kick out node user
RUN  set -ex \
  && GLOBAL_NODE_MODULES="$(npm root -g)" \
  && if id -u node >/dev/null 2>&1; then deluser --remove-home node; fi \
  && if getent group node >/dev/null 2>&1; then delgroup node; fi \
  && addgroup --gid "${GID}" "${GNAME}" \
  && adduser --gecos "" --uid "${UID}" --ingroup "${GNAME}" -s /bin/sh -D "${UNAME}" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd \
  && chown -R "${UID}" "${GLOBAL_NODE_MODULES:-/usr/local/lib/node_modules}"

# exec npm as user
# https://github.com/nodejs/docker-node/blob/main/docs/BestPractices.md#global-npm-dependencies
ENV NPM_CONFIG_PREFIX="/home/${UNAME}/.npm-global"
ENV PATH="${PATH}:${NPM_CONFIG_PREFIX}/bin"

USER ${UID}
WORKDIR /app

RUN mkdir -p node_modules && npm install -g npm@latest yarn pnpm

COPY --chown="${UID}:${GID}" app/package*.json .
COPY --chown="${UID}:${GID}" app/yarn.lock* .
COPY --chown="${UID}:${GID}" app/pnpm-lock* .

FROM base AS develop

ENV NODE_ENV=development

WORKDIR /app

RUN npm install -g nodemon \
  # install modules inside image
  && if [ -f "package.json" ]; then npm install; fi

COPY --chown="${UID}:${GID}" app .

ENTRYPOINT [ "sh" ]

FROM base AS prod

ENV NODE_ENV=production

WORKDIR /app

COPY --chown="${UID}:${GID}" app .
# install but do not update package-lock
RUN if [ -f "package-lock.json" ]; then npm clean-install && npm cache clean --force; fi

# /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT [ "docker-entrypoint.sh" ]
CMD [ "node" ]
