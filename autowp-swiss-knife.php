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


function update_theme_astra( $request ) {
	$parameters = $request->get_params();

	if ( empty( $parameters['colorPalette'] ) ) {
        return new WP_Error( 'no_color_palette', 'A color palette is required.', array( 'status' => 400 ) );
    }

	if ( empty( $parameters['fonts'] ) ) {
        return new WP_Error( 'no_font_family', 'A font family is required.', array( 'status' => 400 ) );
    }

	$color_palettes = get_option( 'astra-color-palettes' );
	$color_palettes['currentPalette'] = 'palette_1';
	$color_palettes['palettes']['palette_1'] = $parameters['colorPalette'];

	update_option( 'astra-color-palettes', $color_palettes );

	$astra_settings = get_option( 'astra-settings' );
	$astra_settings['global-color-palette']['palette'] = $parameters['colorPalette'];
	$astra_settings['body-font-family'] = $parameters['fonts']['body'];
	$astra_settings['headings-font-family'] = $parameters['fonts']['heading'];
	$astra_settings['footer-copyright-editor'] = 'Copyright [copyright] [current_year] [site_title] | Powered by [site_title]';
	update_option( 'astra-settings', $astra_settings );

	return rest_ensure_response( [ 'message' => 'The theme has been updated successfully.' ] );
}

function update_plugin_rank_math( $request ) {
	$parameters = $request->get_params();

	update_option( 'rank-math-options-general', $parameters['general'], 'on' );
	update_option( 'ranrank-math-options-titles', $parameters['titles'], 'on' );
	update_option( 'rank-math-options-sitemap', $parameters['sitemap'], 'auto' );
	update_option( 'rank-math-options-instant-indexing', $parameters['instantIndexing'], 'auto' );

	update_option( 'rank_math_registration_skip', '1', 'auto' );
	update_option( 'rank_math_review_posts_converted', '1', 'auto' );
	update_option( 'rank_math_wizard_completed', '1', 'off' );
	update_option( 'rank_math_is_configured', '1', 'off' );

	return rest_ensure_response( [ 'message' => 'Rank Math plugin has been updated successfully.' ] );
}

function custom_register_options_api() {
    register_rest_route( 'custom/v1', '/autowp/theme/astra/update', array(
        'methods'  => 'POST',
        'callback' => 'update_theme_astra',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        }
    ));

    register_rest_route( 'custom/v1', '/autowp/plugin/rank-math/update', array(
        'methods'  => 'POST',
        'callback' => 'update_plugin_rank_math',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        }
    ));
}

add_action( 'rest_api_init', 'custom_register_options_api' );

function expose_rank_math_meta_to_rest_api() {
    $rank_math_meta_keys = [
        'rank_math_title',
        'rank_math_description',
        'rank_math_focus_keyword',
        'rank_math_secondary_keywords',
        'rank_math_canonical_url',
        'rank_math_robots',
    ];

    foreach ($rank_math_meta_keys as $meta_key) {
        register_meta('post', $meta_key, [
            'type'         => 'string',
            'description'  => 'Rank Math SEO meta data',
            'single'       => true,
            'show_in_rest' => true,
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
    }
}
add_action('init', 'expose_rank_math_meta_to_rest_api');
