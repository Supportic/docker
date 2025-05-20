<?php

declare(strict_types=1);

/*
Plugin Name:  Redirect logged in user to dashboard
Version:      1.0.0
Author:       Supportic
Text Domain:  wpdev-redirect-logged-in
License:      MIT License
*/

// /wp-login.php
// /wp-login.php?action=register
function wpdev_redirect_logged_in() {

    // global $pagenow;
    // $login_pages = [
        // 'wp-login.php',
        // 'wp-register.php',
        // 'wp-signup.php'
    // ];
    // $isLoginPage =  in_array($pagenow, $login_pages, true);

    $isLoginPage =  is_login();

    // Check if we're on a login page
    // Don't redirect if not logged in
    if (!$isLoginPage || !is_user_logged_in()) {
        return;
    }

    // Don't redirect for specific login actions
    $skip_actions = [
        'register',              // Registration form
        'logout',                // Logout
        'lostpassword',          // Lost password form
        'retrievepassword',      // Password retrieval
        'rp',                    // Reset password
        'resetpass',             // Password reset
        'entered_recovery_mode', // Recovery mode
        'exit_recovery_mode'     // Exit Recovery mode
    ];

    $current_action = $_GET['action'] ?? '';
    if (in_array($current_action, $skip_actions, true)) {
        return;
    }

    // Redirect to admin dashboard
    wp_redirect(admin_url());
    exit;
}
add_action( 'init', 'wpdev_redirect_logged_in' );
