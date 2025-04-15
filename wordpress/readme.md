# Wordpress Playground

Frontend: http://localhost  
Backend: http://localhost/wp-admin  
Adminer: http://localhost:8080  
Mailpit: http://localhost:8025

| **Command**    | **Description**                         |
| -------------- | --------------------------------------- |
| `make install` | Install the environment                 |
| `make start`   | Start the environment                   |
| `make stop`    | Pause the environment                   |
| `make down`    | Shutdown the environment                |
| `make restart` | Restart the environment                 |
| `make remove`  | Remove WordPress and volumes            |
| `make erase`   | Remove everything (incl. base images)   |
| `make shell`   | Enter the container                     |
| `make wpcli`   | Enter the WP-CLI container              |
| `make log`     | Print content of debug.log into console |

## Install

- Copy `.env.sample` -> `.env` and adjust variables.
- Place your theme, plugin, mu-plugins inside the wp-dev directory.
- execute `make install`
- go to backend w3-total-cache plugin settings and press skip

### Adding new themes and plugins

The wp-dev directory where you put your source code gets mounted into the home directory of the wordpress container.  
In order to get recognized by wordpress, the themes and plugins will be symlinked into the wordpress directory from inside the container.

Having files in the wp-dev/\* directories will automatically symlink them **when installing the environment for the first time**.  
They stay persistent as long as the wordpress directory exists.

**Adding new themes and plugins** coming from the user inside the wp-dev directory requires to run on the host: `make symlink-docker-create`.  
**Removing themes and plugins** coming from the user inside the wp-dev directory requires to run on the host: `make symlink-docker-remove-user`.  
**Removing leftover symlinks** inside the container requires to run on the host: `make symlink-docker-remove-broken`.  
If you want to make sure all symlinks are in sync (broken,new,current) you can run `make symlink-docker-recreate`.

## Info

WordPress options: https://codex.wordpress.org/Option_Reference

Might need to define PHP variables WP_SITEURL and WP_HOME in the future.

## Known issues

### w3-total-cache: db.php already exists

[https://querymonitor.com/help/db-php-symlink/#when-an-existing-db-php-file-is-already-in-place](https://querymonitor.com/help/db-php-symlink/#when-an-existing-db-php-file-is-already-in-place)

Query-Monitor creates its own db.php file to improve db queries.  
w3-total-cache also wants to create it's own db.php file but only one may exist in wp-content.

To prevent this on installation there is a mu-plugin which deletes the symlink when it comes from query-monitor.  
The `QM_DB_SYMLINK` variable only replaces the symlink with a real copy. It does not prevent the creation of the file.
