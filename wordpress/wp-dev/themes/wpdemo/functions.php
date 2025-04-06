<?php

declare(strict_types=1);

// Exit if accessed directly outside wordpress context.
defined('ABSPATH') || exit;

// if(!is_admin()){
//   var_export("wow");
// }
do_action('qm/debug', 'Plugin loaded: ' . __FILE__);
