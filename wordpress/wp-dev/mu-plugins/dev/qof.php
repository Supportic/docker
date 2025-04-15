<?php

declare(strict_types=1);

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

if (!function_exists('qof_remove_admin_bar_nodes')) {
    function qof_remove_admin_bar_nodes() {
        // Hide WP Logo from the admin bar
        global $wp_admin_bar;
        $wp_admin_bar->remove_node( 'wp-logo' );
    }
    add_action( 'admin_bar_menu', 'qof_remove_admin_bar_nodes', PHP_INT_MAX );
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


if (!function_exists('action_plugins_loaded')){
    add_action('plugins_loaded', 'action_plugins_loaded' );

    /**
     * Fires once activated plugins have loaded.
     *
     */
    function action_plugins_loaded() : void {

    }
}
