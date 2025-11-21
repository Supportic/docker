<?php

declare(strict_types=1);

// Exit if accessed directly outside WordPress context.
defined('ABSPATH') || exit;

include_once __DIR__.'/tools/disable-core-updates.php';
include_once __DIR__.'/tools/redirect-logged-in.php';
include_once __DIR__.'/tools/auto-login-user-switcher.php';
include_once __DIR__.'/tools/mailpit.php';
include_once __DIR__.'/tools/qof.php';
include_once __DIR__.'/tools/qm.php';
