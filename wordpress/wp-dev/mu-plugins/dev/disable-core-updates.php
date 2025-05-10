<?php

declare(strict_types=1);

// Exit if accessed directly outside wordpress context.
defined('ABSPATH') || exit;

/*
Plugin Name:  WP Core Updates Disabler
Version:      1.0.0
Author:       Supportic
Text Domain:  wpdev-disable-core-updates
License:      MIT License
*/

// remove cron events for core
function filter_cron_events ( $event ) {

	$ignore = array (
		'wp_version_check',
		'wp_maybe_auto_update',
	);

	if ( in_array ( $event->hook, $ignore ) ) {
		return false;
	}

	return $event;

}
add_action ( 'schedule_event', 'filter_cron_events' );

// hide all upgrade notices
function wpdev_hide_admin_notices () {
	remove_action ( 'admin_notices', 'update_nag', 3 );
}
add_action ( 'admin_menu', 'wpdev_hide_admin_notices' );

// remove the 'Updates' menu item from the admin interface
function wpdev_remove_menus () {
	global $submenu;
	remove_submenu_page ( 'index.php', 'update-core.php' );
}
add_action ( 'admin_menu', 'wpdev_remove_menus', 102 );

// disable core, theme and plugin updates
function wpdev_disable_updates () {
	remove_action ( 'load-update-core.php', 'wp_update_core' );
}
add_action ( 'init', 'wpdev_disable_updates', 1 );

// fake last checked time (using __return_null makes the dashboard slow)
function wpdev_last_checked () {
	global $wp_version;
	return ( object ) array (
		'last_checked' => time (),
		'version_checked' => $wp_version,
		'updates' => array ()
	);
}
add_filter ( 'pre_site_transient_update_core', 'wpdev_last_checked' );

// disable automatic updates
add_filter ( 'automatic_updater_disabled', '__return_true' );

// disable update health check
add_filter ( 'site_status_tests', function ( $tests ) {
	unset ( $tests['async']['background_updates'] );
	return $tests;
});
