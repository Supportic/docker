# Go Development

check config: `make config`  
clear dangling images: (if you rebuild the image, the old image will lose its Tag and get `<none>` instead, this command clears them)  
`docker rmi $(docker images -q -f "dangling=true" -f "label=autodelete=true")`

## Info

User may work in /go directory.
Use either develop or build stage. Don't forget to rebuild the image before use.

## Go

`docker compose run --rm go`

**or** start in background: `docker compose up --build -d go` and attach console `docker compose exec go bash`

## Tinygo

`docker compose run --rm tinygo`

**or** start in background: `docker compose up --build -d tinygo` and attach console `docker compose exec tinygo bash`

## VScode devcontainer

Switch in `.devcontainer.json` the service and runServices attribute between `go` and `tinygo`.
