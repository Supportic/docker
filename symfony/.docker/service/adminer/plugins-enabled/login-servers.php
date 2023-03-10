<?php
require_once('plugins/login-servers.php');

/** 
 * Set supported servers
 * @param array array(
 *   $description => array(
 *    "server" => $_ENV['ADMINER_DEFAULT_SERVER'],
 *    "driver" => "server|pgsql|sqlite|..."
 *   )
 * )
 */
return new AdminerLoginServers([
  "Docker MariaDB" => [
    "server" => $_ENV['ADMINER_DEFAULT_SERVER'],
    "driver" => "server"
  ],
]);
