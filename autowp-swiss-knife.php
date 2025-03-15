<?php
/**
 * Plugin Name: AutoWP Swiss Knife
 * Plugin URI: https://adommo.com/swisknif
 * Description: A multipurpose WordPress toolkit with utilities, shortcodes, and admin enhancements.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL2
 */

// Prevent direct access to the file.
if (!defined('ABSPATH')) {
    exit;
}

/* Define plugin constants */
define('SWISKNIF_VERSION', '1.0.0');
define('SWISKNIF_PLUGIN_DIR', plugin_dir_path(__FILE__));

defined('SWISKNIF_PLUGIN_URL') || define('SWISKNIF_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Handles basic authentication for JSON API requests.
 *
 * @param mixed $user The current user or null.
 * @return mixed The authenticated user ID or null on failure.
 */
function json_basic_auth_handler($user) {
	global $wp_json_basic_auth_error;

	$wp_json_basic_auth_error = null;

	/* Prevent multiple authentication attempts. */
	if (!empty($user)) {
		return $user;
	}

	/* Check if authentication credentials are provided. */
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		return $user;
	}

	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];

	/**
	 * In a multisite setup, the `wp_authenticate_spam_check` filter is triggered during authentication.
	 * This filter calls `get_currentuserinfo`, which then calls the `determine_current_user` filter,
	 * causing infinite recursion and a stack overflow unless this function is temporarily removed.
	 */
	remove_filter('determine_current_user', 'json_basic_auth_handler', 20);

	$user = wp_authenticate($username, $password);

	add_filter('determine_current_user', 'json_basic_auth_handler', 20);

	/* If authentication fails, return an error. */
	if (is_wp_error($user)) {
		$wp_json_basic_auth_error = $user;
		return null;
	}

	$wp_json_basic_auth_error = true;

	return $user->ID;
}
add_filter('determine_current_user', 'json_basic_auth_handler', 20);

/**
 * Handles authentication errors for the JSON API.
 *
 * @param mixed $error Existing authentication error or null.
 * @return mixed The authentication error message or null if authentication is successful.
 */
function json_basic_auth_error($error) {
	/* Return existing errors if any. */
	if (!empty($error)) {
		return $error;
	}

	global $wp_json_basic_auth_error;

	return $wp_json_basic_auth_error;
}
add_filter('rest_authentication_errors', 'json_basic_auth_error');


function custom_get_wp_option( $request ) {
    $option_name = $request->get_param('option_name');

    if ( empty( $option_name ) ) {
        return new WP_Error( 'no_option', 'Option name is required', array( 'status' => 400 ) );
    }

    $option_value = get_option( $option_name );

    if ( $option_value === false ) {
        return new WP_Error( 'invalid_option', 'Option not found', array( 'status' => 404 ) );
    }

    return rest_ensure_response( array( 'option_name' => $option_name, 'option_value' => $option_value ) );
}

function custom_register_options_api() {
    register_rest_route( 'custom/v1', '/option/', array(
        'methods'  => 'GET',
        'callback' => 'custom_get_wp_option',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' ); // Restrict to admins
        }
    ));
}

add_action( 'rest_api_init', 'custom_register_options_api' );
