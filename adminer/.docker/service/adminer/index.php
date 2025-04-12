<?php

if (basename($_SERVER['DOCUMENT_URI'] ?? $_SERVER['REQUEST_URI']) === 'adminer.css' && is_readable('adminer.css')) {
    header('Content-Type: text/css');
    readfile('adminer.css');
    exit;
}


// class to work on the adminer object
final class DefaultServerPlugin
{
    public function __construct(
        private \AdminerPlugin $adminer
    ) {}

    public function loginFormField(...$args)
    {
        return (function (...$args) {
            $field = Adminer\Adminer::loginFormField(...$args);

            // modify the login form field

            return $field;
        })->call($this->adminer, ...$args);
    }
}

// https://www.adminer.org/plugins/#use
function adminer_object()
{
    // required to run any plugin
    include_once "./plugins/plugin.php";

    // enable extra drivers just by including them
    //~ include "./plugins/drivers/simpledb.php";

    // autoloader
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }

    $plugins = [
        /**
         * Set supported servers
         * @param array array(
         *   $description => array(
         *    "server" => $_ENV['ADMINER_DEFAULT_SERVER'],
         *    "driver" => "server|pgsql|sqlite|..."
         *   )
         * )
         */
        new AdminerLoginServers([
            "Docker MariaDB" => [
                "server" => $_ENV['ADMINER_DEFAULT_SERVER'],
                "driver" => "server",
            ],
        ]),

        new AdminerTablesFilter(),

        // https://www.tiny.cloud/docs/tinymce/6/cloud-quick-start/
        new AdminerTinymce("https://cdn.tiny.cloud/1/no-api-key/tinymce/6.3.1-12/tinymce.min.js"),
    ];

    // Load the DefaultServerPlugin last to give other plugins a chance to
    // override loginFormField() if they wish to.
    $plugins[] = &$loginFormPlugin;

    // https://www.adminer.org/en/extension/
    // https://github.com/vrana/adminer/blob/master/plugins/plugin.php
    class AdminerCustomization extends \AdminerPlugin
    {
        function name()
        {
            return 'Docker Adminer';
        }
    }

    $adminer = new AdminerCustomization($plugins);

    $loginFormPlugin = new DefaultServerPlugin($adminer);

    return $adminer;
}

// include original Adminer or Adminer Editor
include "./adminer.php";
