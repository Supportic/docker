<?php

declare(strict_types=1);

// Exit if accessed directly outside wordpress context.
defined('ABSPATH') || exit;

/**
 * Override version check info stored in transients named update_core, update_plugins, update_themes.
 * Fake last checked time (using __return_null makes the dashboard slow)
 */
function wpdev_override_version_check_info() {
    include( ABSPATH . WPINC . '/version.php' ); // get $wp_version from here
	global $wp_version;

    return ( object ) array (
		'updates' => array (),
		'response' => array (),
		'version_checked' => $wp_version,
		'last_checked' => time(),
	);
}

include_once __DIR__.'/disable-updates/disable-core-updates.php';
// include_once __DIR__.'/disable-updates/disable-theme-updates.php';
// include_once __DIR__.'/disable-updates/disable-plugin-updates.php';
