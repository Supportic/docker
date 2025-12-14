<?php

declare(strict_types=1);

require_once 'plugins/autologin.php';

return new Autologin([
    new AutologinServer('db:3306', 'admin', 'admin')
]);
