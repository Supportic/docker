ARG NODE_VER=18.13-alpine
FROM node:${NODE_VER} as base
LABEL autodelete="true"

# setup User
ARG UID=1000
ARG GID=$UID
ARG UNAME=docker
ARG GNAME=${UNAME}

# kick out node user
RUN deluser --remove-home node \
  && addgroup -g ${GID} ${GNAME} \
  && adduser -u ${UID} -G ${GNAME} -s /bin/sh -D ${UNAME} \
  && chown -R ${UID} /usr/local/lib/node_modules

# exec npm as user
# https://github.com/nodejs/docker-node/blob/main/docs/BestPractices.md#global-npm-dependencies
ENV NPM_CONFIG_PREFIX="/home/${UNAME}/.npm-global"
ENV PATH="${PATH}:${NPM_CONFIG_PREFIX}/bin"

# yarn
RUN corepack enable \
  && corepack prepare yarn@stable --activate

USER ${UID}
WORKDIR /app

RUN mkdir -p node_modules && npm install -g npm@latest 

COPY --chown=${UID}:${GID} app/package*.json .
COPY --chown=${UID}:${GID} app/yarn.lock* .
COPY --chown=${UID}:${GID} app/pnpm-lock* .


FROM base as develop

ENV NODE_ENV=development

RUN npm install -g nodemon \
  # install modules inside image
  && if [ -f "package.json" ]; then npm install; fi

COPY --chown=${UID}:${GID} app .

ENTRYPOINT [ "sh" ]


FROM base as prod

ENV NODE_ENV=production

# install but do not update package-lock
RUN npm clean-install && npm cache clean --force

COPY --chown=${UID}:${GID} app .

# /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT docker-entrypoint.sh
CMD [ "node" ]
