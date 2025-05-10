<?php

declare(strict_types=1);

// Exit if accessed directly outside wordpress context.
defined('ABSPATH') || exit;

/*
Plugin Name:  Quality of Life Features
Description:  Improve local development.
Version:      1.0.0
Author:       Supportic
Text Domain:  wpdev-qof
Requires at least: 6.0
Requires PHP: 8.3
License:      MIT License
*/

// Exit if accessed directly outside wordpress context.
defined('ABSPATH') || exit;

if (!function_exists('wpdev_remove_admin_bar_nodes')) {
    function wpdev_remove_admin_bar_nodes() {
        // Hide WP Logo from the admin bar
        global $wp_admin_bar;
        $wp_admin_bar->remove_node( 'wp-logo' );
    }
    add_action( 'admin_bar_menu', 'wpdev_remove_admin_bar_nodes', PHP_INT_MAX );
}

// disable-xmlrpc
add_filter( 'xmlrpc_enabled', '__return_false' );

// disable-rss-links
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

// hide the meta tag generator from head
add_filter( 'the_generator', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );

// enable customizer
add_action( 'customize_register', '__return_true' );

// dequeue jQuery Migrate from frontend
function wpdev_dequeue_jquery_migrate( $scripts ) {
	if (
        !is_admin()
        && !empty( $scripts->registered['jquery'])
    ) {
		$jquery_dependencies = $scripts->registered['jquery']->deps;
		$scripts->registered['jquery']->deps = array_diff(
             $jquery_dependencies,
             ['jquery-migrate']
        );
	}
}
add_action( 'wp_default_scripts', 'wpdev_dequeue_jquery_migrate' );

if (!function_exists('action_plugins_loaded')){
    add_action('plugins_loaded', 'action_plugins_loaded' );

    /**
     * Fires once activated plugins have loaded.
     *
     */
    function action_plugins_loaded() : void {

    }
}
