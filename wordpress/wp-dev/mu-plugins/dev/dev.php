<?php

declare(strict_types=1);

// Exit if accessed directly outside WordPress context.
defined('ABSPATH') || exit;

include_once __DIR__.'/disable-core-updates.php';
include_once __DIR__.'/redirect-logged-in.php';
include_once __DIR__.'/auto-login-user-switcher.php';
include_once __DIR__.'/mailpit.php';
include_once __DIR__.'/qof.php';
include_once __DIR__.'/qm.php';
