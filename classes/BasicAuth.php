<?php

namespace AutoWPSWISSKnife;

use AutoWPSWISSKnife\Traits\Singleton;

/* Exit, if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	echo 'Hi there! I\'m just a part of plugin, not much I can do when called directly.';
	exit();
}

/**
  *
 */
class BasicAuth {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		/* Handles basic authentication for API requests. */
		add_filter('determine_current_user', [ $this, 'handler' ], 20);

		/* Handles basic authentication for API requests errors. */
		add_filter('rest_authentication_errors', [ $this, 'error_handler' ]);

	} /* plugables() */

	public function handler ( $user ) {
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

	} /* handler() */

	public function error_handler( $error ) {
		/* Return existing errors if any. */
		if (!empty($error)) {
			return $error;
		}

		global $wp_json_basic_auth_error;

		return $wp_json_basic_auth_error;
	} /* error_handler() */

} /* BasicAuth() */
