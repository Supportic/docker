<?php

declare(strict_types=1);

/*
Plugin Name:  Auto Login User Switcher
Version:      1.0.0
Author:       Supportic
Text Domain:  wpdev-auto-login-user-switcher
License:      MIT
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
 * Test: when user loses session (wp-login.php?interim-login=1)
 * remove all cookies in devtools and call wp.heartbeat.connectNow() function in devtools console
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

	if ( !is_local_environment() || !is_login() || is_user_logged_in() ) {
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
	if ( !is_local_environment() || !is_login() || is_user_logged_in() ) {
		return;
	}

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
    // Check if the auto-login form was submitted with the correct action and nonce.
    $is_auto_login_action = isset( $_POST['auto_login_user_switcher_action'] ) && $_POST['auto_login_user_switcher_action'] === 'auto_login_user';

    $has_user_id_in_request = !empty( $_POST['auto_login_user_switcher_user_id'] );

    if (! $has_user_id_in_request || !$is_auto_login_action ) {
        return;
    }

    $is_verified_nonce = isset( $_POST['auto_login_user_switcher_nonce'] ) && wp_verify_nonce( $_POST['auto_login_user_switcher_nonce'], 'auto_login_user_switcher_login' );

    if(!$is_verified_nonce){
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'wpdev' ), esc_html__( 'Permission Denied', 'wpdev' ), [ 'response' => 403 ] );
    }

    $user_id = absint( $_POST['auto_login_user_switcher_user_id'] );
    /** @var WP_User|false $user */
    $user = get_user_by( 'id', $user_id );

    if ( ! $user instanceof WP_User ) {
		wp_die( esc_html__( 'Invalid user ID.', 'wpdev' ), esc_html__( 'Error', 'wpdev' ), [ 'response' => 400, 'back_link' => true ] );
	}

    // Get the current user ID *before* clearing auth cookies.
    $current_user_id = get_current_user_id();

    // Log the new user in by clearing old cookies and setting new ones.
    wp_clear_auth_cookie();
    wp_set_current_user( $user->ID, $user->user_login );
    wp_set_auth_cookie( $user->ID, true, is_ssl() );
    do_action( 'wp_login', $user->user_login, $user );

    // default: redirect the user to the admin dashboard.
    $redirect_to = admin_url();

    if ( ! empty( $_REQUEST['redirect_to'] ) ) {
        $redirect_to = wp_sanitize_redirect( $_REQUEST['redirect_to'] );
    }

    $is_interim_login = isset( $_REQUEST['interim-login'] ) && '1' === $_REQUEST['interim-login'];

    // if it's not a session timeout login, do a regular redirect
    if(!$is_interim_login){
        wp_safe_redirect( $redirect_to );
        exit;
    }

    // Handle interim login (overlay/popup after session timeout)
    $message = '<p class="message">' . esc_html__( 'You have logged in successfully.', 'wpdev' ) . '</p>';
    login_header( '', $message );

    ?>
    <script type="text/javascript">
    // Use a self-invoking function to avoid global variable pollution.
    (function() {
        const parent = window.parent;

        if (!parent) {
            return;
        }

        const $ = parent.jQuery, windowParent = parent.window;
        const { adminpage, wp } = parent;

        setTimeout(function(){
            // Remove the beforeunload event handler first
            $(windowParent).off('beforeunload.wp-auth-check');

            // When on the Edit Post screen, speed up heartbeat
            // after the user logs in to quickly refresh nonces.
            if ( ( adminpage === 'post-php' || adminpage === 'post-new-php' ) && wp && wp.heartbeat ) {
                wp.heartbeat.connectNow();
            }

            /**
             * Improve this when the previous user is the same as the new logged in user just close the modal and remove the iframe instead of a reload.
             *
            */
            // $('#wp-auth-check-wrap').fadeOut(200, function() {
            //     $('#wp-auth-check-wrap').addClass('hidden').css('display', '');
            //     $('#wp-auth-check-frame').remove();
            //     $('body', parent.document).removeClass('modal-open');
            // });

            if ( parent.document ) {
                parent.location.reload();
            } else {
                window.opener.location.reload();
                window.close();
            }
        }, 300);
    })();
    </script>
    <?php

    // important to prevent the normal login to continue
    exit;
}
// not possible to retrieve current user in login_init
add_action( 'login_init', 'wpdev_handle_auto_login_user_switcher' );

/**
 * Prevent default authentication errors when using auto-login user switcher.
 */
function wpdev_bypass_authenticate_for_auto_login_user_switcher( $user, $username, $password ) {
    $isLoginAction = isset( $_POST['auto_login_user_switcher_action'] ) && $_POST['auto_login_user_switcher_action'] === 'auto_login_user';

    $isValidUserId = isset( $_POST['auto_login_user_switcher_user_id'] ) && !empty( $_POST['auto_login_user_switcher_user_id'] );

    if (
        is_local_environment()
        && is_login()
        && $isLoginAction
        && $isValidUserId
    ) {
        $user_id = absint( $_POST['auto_login_user_switcher_user_id'] );
        $user_obj = get_user_by( 'id', $user_id );
        if ( $user_obj && !is_wp_error( $user_obj ) ) {
            return $user_obj;
        }
    }
    return $user;
}
add_filter( 'authenticate', 'wpdev_bypass_authenticate_for_auto_login_user_switcher', 1, 3 );
