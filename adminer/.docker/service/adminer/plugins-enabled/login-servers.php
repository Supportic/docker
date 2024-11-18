<?php

/** 
 * Set supported servers
 * @param array array(
 *   $description => array(
 *    "server" => getenv('ADMINER_DEFAULT_SERVER'),
 *    "driver" => "server|pgsql|sqlite|..."
 *   )
 * )
 */

return new AdminerLoginServers([
  "MariaDB" => [
    "server" => getenv('ADMINER_DEFAULT_SERVER'),
    "driver" => "server"
  ],
]);
