<?php

// https://github.com/TimWolla/docker-adminer/blob/master/4/index.php
namespace docker {
    function adminer_object() {
        include_once './plugins/plugin.php';

        // autoloader
        foreach (glob("plugins/*.php") as $filename) {
            include_once "./$filename";
        }

        // https://www.adminer.org/en/extension/
        // https://github.com/adminerevo/adminerevo/blob/main/plugins/plugin.php
        class Adminer extends \AdminerPlugin {
            function _callParent($function, $args) {
                // before rendering the login form, insert default server in field
                if ($function === 'loginForm') {
                    ob_start();
                    $return = \Adminer::loginForm();
                    $form = ob_get_clean();

                    echo str_replace('name="auth[server]" value="" title="hostname[:port]"', 'name="auth[server]" value="'.(getenv('ADMINER_DEFAULT_SERVER') ?: 'db').'" title="hostname[:port]"', $form);

                    return $return;
                }

                return parent::_callParent($function, $args);
            }

            function name() {
                return 'Docker Adminer';
            }
        }

        // custom plugins
        $plugins = [];
        foreach (glob('plugins-enabled/*.php') as $plugin) {
            $plugins[] = require($plugin);
        }

        return new Adminer($plugins);
    }
}

namespace {
    // gets executed automatically
    function adminer_object() {
        return \docker\adminer_object();
    }

    include './adminer.php';
}