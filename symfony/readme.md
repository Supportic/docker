# Symfony App

**Build images and start containers**  
`make start`

**attach shell to php container**  
`make shell`

**remove containers**  
`make down`

**remove containers + DB**  
`docker compose down -v`

## Vscode devcontainer

requires to build php image first!: `make install` or `make start`

Install 'Dev Containers' extension. Click on symbol in bottom left corner and choose: 'Reopen in container'.

We extend the php image by installing node inside in order to use IDE intellisense while developing in devcontainer.

## Notes

- create project with symfony 6.4 `symfony local:new --dir=. --version=lts --php="8.2" --no-git`
- --webapp installs all below by default
  - add doctrine db migration commands to symfony console: `composer require orm`
  - add make commands to symfony console: `composer require --dev symfony/maker-bundle`
- copy .env file to .env.local and adjust DATABASE_URL

### Commands

| Command                                             | Description              |
| --------------------------------------------------- | ------------------------ |
| `symfony console debug:router`                      | see all available routes |
| `symfony console make:controller <controller_name>` | create controller        |
