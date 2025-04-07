ARG ALPINE_VER=3.21

FROM alpine:${ALPINE_VER}
LABEL autodelete="true"

RUN apk add --no-cache curl sudo nss

ARG UID=1000
ARG GID=1000
ARG UNAME=docker
ARG GNAME=docker

RUN addgroup -g "${GID}" --system "${GNAME}" &&\
  adduser -G "${GNAME}" --system -D -s /bin/sh -u "${UID}" "${UNAME}"
RUN echo "${UNAME}" ALL=\(root\) NOPASSWD:ALL > "/etc/sudoers.d/${UNAME}" &&\
  chmod 0440 "/etc/sudoers.d/${UNAME}"

USER ${UNAME}
WORKDIR "/home/${UNAME}"

RUN mkdir -p ./certs ./bin

ADD --chown="${UID}:${GID}" ./docker-entrypoint.sh .
RUN sudo chmod +x ./docker-entrypoint.sh

ARG MKCERT_VER=1.4.4
ENV MKCERT_VER=${MKCERT_VER}
# install to be able to copy root ca.pem ("mkcert -uninstall" to uninstall)
RUN curl -fsSL --retry 3 "https://github.com/FiloSottile/mkcert/releases/download/v${MKCERT_VER}/mkcert-v${MKCERT_VER}-linux-amd64" -o mkcert && chmod +x ./mkcert

CMD ["./docker-entrypoint.sh"]
