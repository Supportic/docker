<?php

// relative to adminer root
require_once 'plugins/login-servers.php';

return new AdminerLoginServers(array(
    "Docker MariaDB" => array(
        "server" => $_ENV['ADMINER_DEFAULT_SERVER'],
        "driver" => "server",
    ),
));
