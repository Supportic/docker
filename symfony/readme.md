# Symfony App

**Build images and start containers**  
`make start`

**attach shell to php container**  
`make shell`

**remove containers**  
`docker compose down`

**remove containers + DB**  
`docker compose down -v`

## Vscode devcontainer

requires to build php image first!: `make install` or `make start`

Install 'Dev Containers' extension. Click on symbol in bottom left corner and choose: 'Reopen in container'.

We extend the php image by installing node inside in order to use IDE intellisense while developing in devcontainer.

## Info

Frontend: http://localhost  
Database UI: http://localhost:8080/

## Notes

- create project with symfony 5.4 `symfony new <PROJECT_NAME> --dir=. --version=lts [--webapp]`
- --webapp installs all below by default
  - add doctrine db migration commands to symfony console: `composer require orm`
  - add make commands to symfony console: `composer require --dev symfony/maker-bundle`
- copy .env file to .env.local and adjust DATABASE_URL

### Commands

| Command                                             | Description              |
| --------------------------------------------------- | ------------------------ |
| `symfony console debug:router`                      | see all available routes |
| `symfony console make:controller <controller_name>` | create controller        |
