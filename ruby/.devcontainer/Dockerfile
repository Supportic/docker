ARG BASE_IMAGE="${BASE_IMAGE}"
FROM ${BASE_IMAGE}
LABEL autodelete="true"

USER root

##########################################################################
## Lifecycle commands
##########################################################################
COPY --chmod=0755 .devcontainer/lifecycle-command/on-create-command.sh /usr/local/bin/on-create-command
COPY --chmod=0755 .devcontainer/lifecycle-command/post-create-command.sh /usr/local/bin/post-create-command
COPY --chmod=0755 .devcontainer/lifecycle-command/post-start-command.sh /usr/local/bin/post-start-command
