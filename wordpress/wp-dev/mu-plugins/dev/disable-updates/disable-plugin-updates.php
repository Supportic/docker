<?php

declare(strict_types=1);

/*
Plugin Name:  WP Plugin Updates Disabler
Version:      1.0.0
Author:       Supportic
Text Domain:  wpdev-disable-plugin-updates
License:      MIT License
*/

// Exit if accessed directly outside wordpress context.
defined('ABSPATH') || exit;

function wpdev_disable_plugin_update_notices() {
    // Disable plugin version checks
    remove_action( 'wp_update_plugins', 'wp_update_plugins' );
    remove_action( 'admin_init', '_maybe_update_plugins' );
    wp_clear_scheduled_hook( 'wp_update_plugins' );

    remove_action( 'load-plugins.php', 'wp_update_plugins' );
    remove_action( 'load-update.php', 'wp_update_plugins' );
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
}
add_action( 'admin_init', 'wpdev_disable_plugin_update_notices' );

// Disable plugin updates
add_filter( 'pre_transient_update_plugins', 'wpdev_override_version_check_info' );
add_filter( 'pre_site_transient_update_plugins', 'wpdev_override_version_check_info' );
add_action( 'pre_set_site_transient_update_plugins', 'wpdev_override_version_check_info', 20 );
add_filter( 'auto_update_plugin', '__return_false' );
