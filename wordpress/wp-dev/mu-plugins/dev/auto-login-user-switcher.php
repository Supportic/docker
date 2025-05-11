<?php

declare(strict_types=1);

/*
Plugin Name:  Auto Login User Switcher
Version:      1.0.0
Author:       Supportic
Text Domain:  wpdev-auto-login-user-switcher
License:      MIT License
*/

/**
 * dont use add_filter('login_form_middle', ... ) here
 * https://developer.wordpress.org/reference/functions/wp_login_form/#hooks
 * This filter is applied when using the wp_login_form() function to display a login form.
 *
 * However, it is not applied on the /wp-login.php form because it utelizes another function to render.
 * https://developer.wordpress.org/reference/hooks/login_form/
 */

/**
 * Check if the current environment is 'local'.
 *
 * @return bool True if the environment is local, false otherwise.
 */
function is_local_environment() {
	return defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE === 'local';
}

/**
 * Adjust form template and add auto-login-user-switcher on the login page.
 */
function wpdev_add_auto_login_user_switcher() {

    // already logged in, redirect to admin
    if ( is_user_logged_in() && is_login() ) {
        wp_redirect( admin_url() );
        return;
    }

    // don't show the auto-login-user-switcher if not in a local environment
	if ( !is_local_environment() || is_user_logged_in() || !is_login() ) {
		return;
	}

	/**
     * Get all users.
     * @var WP_USER[] $users
     */
	$users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );

	if ( empty( $users ) ) {
		return;
	}

    // first entry is empty, to enable username/paassword login
    $selectOptions = sprintf(
        '<option value="" disabled selected>%s</option>',
        esc_html__( '-- select a user --', 'wpdev' )
    );
    foreach ( $users as $user ) {
        $selectOptions .= sprintf(
            '<option value="%d">%s</option>',
            esc_attr( $user->ID ),
            esc_html( $user->display_name )
        );
    }

	?>
    <div class="auto-login-user-switcher-wrap" style="margin-bottom: 20px;">
        <label for="auto-login-user-switcher">
            <?php esc_html_e( 'Auto Login User:', 'wpdev' ); ?>
        </label>
        <select name="auto_login_user_switcher_user_id" id="auto-login-user-switcher" style="width: 100%;">
            <?php echo $selectOptions ?>
        </select>
        <?php wp_nonce_field( 'auto_login_user_switcher_login', 'auto_login_user_switcher_nonce' ); ?>
        <input type="hidden" name="auto_login_user_switcher_action" value="auto_login_user">
	</div>
	<?php
}
add_action( 'login_form', 'wpdev_add_auto_login_user_switcher' );

// auto submit the form when a user is selected
function wpdev_add_auto_login_user_switcher_script() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            const userSwitcher = document.getElementById('auto-login-user-switcher');
            if (!userSwitcher) { return; }

            userSwitcher.addEventListener('change', (evt) => {
                const $form = document.getElementById('loginform');
                if (evt.currentTarget.value && $form) {
                    $form.submit();
                }
            });
        }, false);
    </script>
    <?php
}
add_action( 'login_enqueue_scripts', 'wpdev_add_auto_login_user_switcher_script' );

/**
 * Handle the auto-login-user-switcher based on the dropdown selection.
 */
function wpdev_handle_auto_login_user_switcher() {
	// Only process if in a local environment, not logged in, and the form was submitted.
	if ( !is_local_environment() || is_user_logged_in() ) {
		return;
	}

    $isLoginAction = isset( $_POST['auto_login_user_switcher_action'] ) && $_POST['auto_login_user_switcher_action'] === 'auto_login_user';

    $isValidNonce = isset( $_POST['auto_login_user_switcher_nonce'] ) && wp_verify_nonce( $_POST['auto_login_user_switcher_nonce'], 'auto_login_user_switcher_login' );

    $isValidUserId = isset( $_POST['auto_login_user_switcher_user_id'] ) && !empty( $_POST['auto_login_user_switcher_user_id'] );

	// Check if our form was submitted and the nonce is valid.
	if ($isLoginAction && $isValidUserId && $isValidNonce) {
		$user_id = absint( $_POST['auto_login_user_switcher_user_id'] );
		$user = get_user_by( 'id', $user_id );

		if ( $user && !is_wp_error( $user ) ) {
			// Log the user in.
			wp_set_current_user( $user->ID, $user->user_login );
			wp_set_auth_cookie( $user->ID, true );

			// Redirect to the admin dashboard.
			wp_redirect( admin_url() );
		}
	}
}
add_action( 'login_init', 'wpdev_handle_auto_login_user_switcher' );
