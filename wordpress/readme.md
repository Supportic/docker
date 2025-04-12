# Wordpress Playground

Copy `.env.sample` -> `.env` and adjust variables.

Frontend: http://localhost  
Backend: http://localhost/wp-admin  
Adminer: http://localhost:8080  
Mailpit: http://localhost:8025

**install**  
`make install`

**start**
`make start`

**shutdown**
`make down`

**enter container**  
`make shell`

**enter wpcli container**
`make wpcli`

**remove wordpress and volumes**  
`make remove`

**remove everything**  
`make erase`

## Info

WordPress options: https://codex.wordpress.org/Option_Reference

### Adding new themes and plugins

The wp-dev directory where you place your source code gets mounted into the home directory of the wordpress container.  
In order to get recognized by wordpress, the themes and plugins will be symlinked into the wordpress directory from inside the container.

Having files in the wp-dev/\* directories will automatically symlink them when installing the environment for the first time.  
They stay persistent as long as the wordpress directory exists.

Adding new themes and plugins requires to run on the host: `make symlink-docker-create`.

Removing themes and plugins from the wp-dev directory requires to run on the host: `make symlink-docker-remove-broken`

If you want to make sure all symlinks are in sync (broken,new,current) you can run `make symlink-docker-recreate`
