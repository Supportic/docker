ARG GO_VER=1.19.5-bullseye
FROM golang:${GO_VER} as base

USER root

# setup User
ARG UID=1000
ARG GID=${UID}
ARG UNAME=docker
ARG GNAME=${UNAME}
ARG PASS=test

ENV USER=${UNAME}

RUN set -ex &&\
  # add group
  groupadd --gid ${GID} ${GNAME} &&\
  # add user
  useradd --uid ${UID} --gid ${GID} \
  --create-home --no-log-init --shell /bin/bash ${UNAME} &&\
  # set user PW
  echo "${UNAME}:${PASS}" | chpasswd

# ENV GOROOT="/usr/local/go"
ENV GOPATH="/go"
ENV GOBIN="${GOPATH}/bin"
# make our binaries visible to system
ENV PATH="${PATH}:${GOBIN}"

# create dir and give it to user
RUN mkdir -p "${GOPATH}" && chown -R "${UID}:${GID}" "${GOPATH}"

FROM base as develop

RUN export DEBIAN_FRONTEND=noninteractive \
  && apt-get update \
  && apt-get -y --no-install-recommends install \
  sudo make ssh-client

RUN usermod -aG sudo ${UNAME}

# Remove sudo notice after shell startup
RUN mkdir -p /etc/sudoers.d &&\
  printf "Defaults lecture = never" > /etc/sudoers.d/privacy

RUN rm -rf /var/lib/apt/lists/*

# [Optional SUDO] Let user execute sudo without password prompt.
# RUN echo ${UNAME} ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/${UNAME} &&\
#   chmod 0440 /etc/sudoers.d/${UNAME}

ENTRYPOINT [ "bash" ]

FROM base as builder

USER ${UID}
WORKDIR ${GOPATH}/src/app

COPY ./app .

RUN go mod download && CGO_ENABLED=0 GOOS=linux go build -o app .

FROM scratch as build

WORKDIR /bin

COPY --from=builder /go/src/app/app .

CMD [ "app" ]