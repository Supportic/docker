# Node Development

Clear dangling images: (if you rebuild the image, the old image will lose its Tag and get `<none>` instead, this command clears them)  
`docker rmi $(docker images -q -f "dangling=true" -f "label=autodelete=true")`

## Info

User may work in /app directory.
Run: `docker compose run --rm node`

## Example

Typescript bundler: https://tsup.egoist.dev/  
Transpile TS: `npm run build` or `npm run watch`  
Transpile TS when using node packages: `npm run build:node` or `npm run watch:node`
