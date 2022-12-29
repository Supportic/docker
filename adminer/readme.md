# Adminer

- [https://hub.docker.com/\_/adminer/](https://hub.docker.com/_/adminer/)
- [https://github.com/TimWolla/docker-adminer](https://hub.docker.com/_/adminer/)
- [https://github.com/vrana/adminer](https://github.com/vrana/adminer)

Copy the adminer configuration of the compose.yml file into your project compose file.  
Copy the configuration files from the .docker directory into your project.

Adjust docker paths and port or adminer plugins and design if needed.

## Extending

The image includes a [index.php](https://github.com/TimWolla/docker-adminer/blob/master/4/index.php) file inside `/var/html` which exposes its own Adminer class in the docker namespace. This class extends the [AdminerPlugin](https://github.com/vrana/adminer/blob/master/plugins/plugin.php) class which again extends the [Adminer](https://github.com/vrana/adminer/blob/master/adminer/include/adminer.inc.php) class. You can overwrite these functions by modifying the index.php file: see [https://www.adminer.org/en/extension/](https://www.adminer.org/en/extension/)

## Plugins

- [https://www.adminer.org/plugins/](https://www.adminer.org/plugins/)
- [https://github.com/vrana/adminer/tree/master/plugins](https://github.com/vrana/adminer/tree/master/plugins)

Simple plugins without configuration will be automatically created via the `ADMINER_PLUGINS` env variable, seperated by space.
This will create .php files in the plugins-enabled directory with ownership of the adminer user.

Plugins which require configuration through arguments need to be created manually. Create a .php file inside the plugins-enabled directory and instantiate the plugin class with arguments.

## Themes

- [https://github.com/vrana/adminer/tree/master/designs](https://github.com/vrana/adminer/tree/master/designs)

Select on of the themes by setting the `ADMINER_DESIGN` env variable. If you want to use your own theme, create a directory named after your theme and place a adminer.css file inside. Set your own theme as env variable.
