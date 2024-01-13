ARG BASE_IMAGE
FROM ${BASE_IMAGE}

# Avoid warnings by switching to noninteractive for these commands
RUN export DEBIAN_FRONTEND=noninteractive \
  && apt-get update \
  && apt-get -y --no-install-recommends install \
  git openssh-client \
  apt-transport-https ca-certificates curl gnupg2 lsb-release \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# [Optional SUDO] Let user execute sudo without password prompt.
# RUN echo ${UNAME} ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/${UNAME} &&\
#   chmod 0440 /etc/sudoers.d/${UNAME}

### BEGIN DOCKER INSTALL
# https://github.com/microsoft/vscode-dev-containers/tree/main/containers/docker-from-docker-compose#enabling-root-user-access-to-docker-in-the-container

# Install Docker CE CLI
RUN curl -fsSL https://download.docker.com/linux/$(lsb_release -is | tr '[:upper:]' '[:lower:]')/gpg | apt-key add - 2>/dev/null \
  && echo "deb [arch=$(dpkg --print-architecture)] https://download.docker.com/linux/$(lsb_release -is | tr '[:upper:]' '[:lower:]') $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list \
  && apt-get update \
  && apt-get install -y docker-ce-cli

ARG UNAME=docker

# check GID of mounted docker socket
RUN echo -e "#!/bin/sh\n\
  sudoIf() { if [ \"\$(id -u)\" -ne 0 ]; then sudo \"\$@\"; else \"\$@\"; fi }\n\
  SOCKET_GID=\$(stat -c '%g' /var/run/docker.sock) \n\
  if [ \"${SOCKET_GID}\" != '0' ]; then\n\
  if [ \"\$(cat /etc/group | grep :\${SOCKET_GID}:)\" = '' ]; then sudoIf groupadd --gid \${SOCKET_GID} docker-host; fi \n\
  if [ \"\$(id ${UNAME} | grep -E \"groups=.*(=|,)\${SOCKET_GID}\(\")\" = '' ]; then sudoIf usermod -aG \${SOCKET_GID} ${UNAME}; fi\n\
  fi\n\
  exec \"\$@\"" > /usr/local/share/docker-init.sh \
  && chmod u+x /usr/local/share/docker-init.sh

### FINISH DOCKER INSTALL

# install nvm (setup .bashrc), Node.js
ARG NVM_VERSION=0.39.7
ARG NODE_VERSION=20.11.0
ENV NVM_DIR=/home/${UNAME}/.nvm

# export home only for the current RUN layer for root because of install script
RUN set -ex && mkdir -p "${NVM_DIR}" && chown "${UNAME}" "${NVM_DIR}" \
  && export HOME="/home/${UNAME}" \
  && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v${NVM_VERSION}/install.sh | bash - \
  && . ~/.nvm/nvm.sh \
  && nvm install "${NODE_VERSION}" \
  && nvm alias default "${NODE_VERSION}" \
  && nvm use default \
  && npm install -g npm@latest

ENV NODE_PATH=${NVM_DIR}/v${NODE_VERSION}/lib/node_modules
ENV PATH=${NVM_DIR}/versions/node/v${NODE_VERSION}/bin:$PATH

# Setting the ENTRYPOINT to docker-init.sh will configure non-root access
# to the Docker socket. The script will also execute CMD as needed.
ENTRYPOINT [ "/usr/local/share/docker-init.sh" ]
CMD [ "sleep", "infinity" ]
