# Adminer

- [https://hub.docker.com/\_/adminer](https://hub.docker.com/_/adminer)
- [https://github.com/TimWolla/docker-adminer](https://hub.docker.com/_/adminer/)
- [https://github.com/vrana/adminer](https://github.com/vrana/adminer)

Copy the adminer configuration of the compose.yml file into your project compose file.  
Copy the configuration files from the .docker directory into your project.

Adjust docker paths and port or adminer plugins and design if needed.

## Themes

- [https://github.com/vrana/adminer/tree/master/designs](https://github.com/vrana/adminer/tree/master/designs)

Select on of the themes by setting the `ADMINER_DESIGN` env variable. If you want to use your own theme, create a directory named after your theme and place a adminer.css file inside. Set your own theme as env variable.

## How Plugins work

Adminer has 2 plugin directories: `/var/www/html/plugins` and `/var/www/html/plugins-enabled`

Plugins in both directories must extend from `Adminer\Plugin`.

Adminer will only use plugins when they are in the `/var/www/html/plugins-enabled` directory.  
When the docker container starts, Adminer will copy all user defined plugins into: `/var/www/html/plugins-enabled`.  
You can define which plugins to use with the docker env `ADMINER_PLUGINS`.  
The file name corresponds with the names you define in env `ADMINER_PLUGINS`.

## Adminer main file

`var/www/html/index.php` is a custom docker adminer entrypoint. It is responsible to load the `var/www/html/adminer.php`. It also loads plugins from `var/www/html/plugins-enabled` directory instead of `var/www/html/plugins`.

If you want to override the file, make sure to require the original adminer.php at the end. (see https://www.adminer.org/en/extension/)

## Plugin Usage

You can see all loaded plugins in the database overview page.

### Simple plugins

If you want to use a plugin, you have to define the docker env variable and list all desired plugins: `ADMINER_PLUGINS: "tinymce tables-filter autologin"`.
These plugins do not require any configuration and are immediately active. (without constructor params)

If you want to use a custom simple plugin, copy it into `/var/www/html/plugins` and list it in env `ADMINER_PLUGINS`.

### Configured plugins

Plugins which require constructor parameters needs to be places inside the `/var/www/html/plugins-enabled` directory.
Instead of defining the plugin in env `ADMINER_PLUGINS` to load it, you include/require these plugins in a custom plugin file and instantiate it.

For instance: the AdminerLoginServers plugin requires constructor params. Include the plugin manually and return an configured instance.

```php
<?php

// relative to adminer root
require_once 'plugins/login-servers.php';

return new AdminerLoginServers(array(
    "Docker MariaDB" => array(
        "server" => $_ENV['ADMINER_DEFAULT_SERVER'],
        "driver" => "server",
    ),
));
```

### Custom Plugins

If you have a simple plugin:

- COPY file into `/var/www/html/plugins` (Dockerfile)
- mention plugins name (file name) in docker env `ADMINER_PLUGINS`

If you have a configured plugin:

- follow instructions of simple plugin above
- create a new file which requires your class from `/var/www/html/plugins`
- instantiate your class with parameters or configs (->withConfig()) and return it
- COPY file into `/var/www/html/plugins-enabled` (Dockerfile)

alternatively:

- COPY class file into `/var/www/html/plugins-enabled` (Dockerfile)
- instantiate your class with parameters or configs (->withConfig()) and return it at the end

## Autologin Plugin

Displays a selection field in the login form to quickly login into provided connections.  
Requires to instantiate the Autologin class because multiple classes defined in plugin file.

### Option 1: ENV Variable

Define the `ADMINER_SERVERS_JSON` env variable as JSON string. This is the easiest config since you can inject other env variables.
It contains objects with the following keys:

```json
[
  {
    "driver": "server|sqlite|pgsql|oracle|mssql",
    "server": "db:3306", // port here or as single key
    "username": "${DB_USER}",
    "password": "${DB_PASS}",
    "port": 3306, // optional if provided in server string
    "db": "${DB_NAME}", // optional
    "label": "Local Dev" // optional
  }
]
```

```yaml
adminer:
  image: adminer:latest
  environment:
    ADMINER_SERVERS_JSON: '[{"driver":"server","server":"db:3306","username":"${DB_USER}","password":"${DB_PASS}","db":"${DB_NAME}","label":"Local Dev (ENV)"}]'
  user: adminer
  volumes:
    - ./.docker/service/adminer/plugins/autologin.php:/var/www/html/plugins/autologin.php
    - ./.docker/service/adminer/plugins-enabled/autologin.php:/var/www/html/plugins-enabled/autologin.php
```

```php
// plugins-enabled/autologin.php
<?php

declare(strict_types=1);

require_once 'plugins/autologin.php';

return new Autologin();
```

### Option 2: connections.json

Place a connections.json file in the root directory of adminer. Use the same JSON structure as in option 1.  
Retrieving env variables is not possible here.

```yaml
adminer:
  image: adminer:latest
  user: adminer
  volumes:
    - ./.docker/service/adminer/plugins/autologin.php:/var/www/html/plugins/autologin.php
    - ./.docker/service/adminer/plugins-enabled/autologin.php:/var/www/html/plugins-enabled/autologin.php
    - ./.docker/service/adminer/connections.json:/var/www/html/connections.json
```

```php
// plugins-enabled/autologin.php
<?php

declare(strict_types=1);

require_once 'plugins/autologin.php';

return new Autologin();
```

### Option 3: AutologinServer constructor parameter

Pass AutologinServer classes as array parameter into the constructor.

```yaml
adminer:
  image: adminer:latest
  user: adminer
  volumes:
    - ./.docker/service/adminer/plugins/autologin.php:/var/www/html/plugins/autologin.php
    - ./.docker/service/adminer/plugins-enabled/autologin.php:/var/www/html/plugins-enabled/autologin.php
```

```php
// plugins-enabled/autologin.php
<?php

declare(strict_types=1);

require_once 'plugins/autologin.php';

return new Autologin([
    new AutologinServer('db:3306', 'username', 'password', /*port, label, driver, db*/ )
]);
```

## DEV

### Translations

Translate strings in your plugin via the `$translations` class property and `$this->lang()` function.

```php
class Autologin extends Adminer\Plugin
{
    protected $translations = [
        'en' => ['warning' => 'WARNING'],
        'de' => ['warning' => 'WARNUNG'],
    ];

    public function editInput($table, $field, $attrs, $value){
      return $this->lang('warning');
    }
}
```

### Plugin description

Displays a description of your plugin in the loaded plugins list (database overview page)

Option 1: Define empty key in your `$translations` property

```php
class Autologin extends Adminer\Plugin
{
    protected $translations = [
        'en' => ['' => 'Enable logins without passwords.'],
        'cs' => ['' => 'Povolí přihlášení bez hesla'],
        'de' => ['' => 'Ermöglicht die Anmeldung ohne Passwort'],
        'pl' => ['' => 'Włącz logowanie bez hasła'],
        'ro' => ['' => 'Activați autentificarea fără parolă'],
        'ja' => ['' => 'パスワードなしのログインを許可'],
    ];
}

```

Option 2: Putting a PHP comment description above your class

### use Adminer functions

Option1: class reference: `$this->lang()`

Option2: prefix namespace: `Adminer\lang()`

Option3: import namespace:

```php
use function Adminer\lang;
use function Adminer\adminer;

lang()
```
