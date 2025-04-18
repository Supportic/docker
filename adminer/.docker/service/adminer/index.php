<?php

if (basename($_SERVER['DOCUMENT_URI'] ?? $_SERVER['REQUEST_URI']) === 'adminer.css' && is_readable('adminer.css')) {
    header('Content-Type: text/css');
    readfile('adminer.css');
    exit;
}

// https://www.adminer.org/plugins/#use
function adminer_object() {
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
        new AdminerTinymce("https://cdn.tiny.cloud/1/no-api-key/tinymce/6.3.1-12/tinymce.min.js")
    ];

    // Adminer\Adminer and Adminer\Plugins are basically the same class
    // https://www.adminer.org/en/extension/
    // https://github.com/vrana/adminer/blob/master/adminer/include/plugins.inc.php
    class AdminerCustomization extends Adminer\Plugins {
        function name() {
            return 'Docker Adminer';
        }

        public function loginFormField(...$args) {
            $field = Adminer\Plugins::loginFormField(...$args);

            // modify the login form field
            // \str_replace(
            //     'name="auth[server]" value="" title="hostname[:port]"',
            //     \sprintf('name="auth[server]" value="%s" title="hostname[:port]"', ($_ENV['ADMINER_DEFAULT_SERVER'] ?: 'db')),
            //     $field,
            // )

            return $field;
        }
    }

    $adminer = new AdminerCustomization($plugins);

    return $adminer;
}

// include original Adminer or Adminer Editor
include "./adminer.php";
