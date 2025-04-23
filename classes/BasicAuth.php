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
 * Provides Basic Authentication functionality for WordPress REST API requests.
 */
class BasicAuth {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		/* Handle the basic authentication for API requests. */
		add_filter( 'determine_current_user', [ $this, 'handler' ], 20 );

		/* Handle the errors that occur during basic authentication. */
		add_filter( 'rest_authentication_errors', [ $this, 'error_handler' ] );

	} /* plugables() */

	/**
	 * Handle the basic authentication for API requests.
	 *
	 * @param mixed $user The current user object or ID (can be null).
	 * @return int|null|\WP_Error
	 */
	public function handler( $user ) {

		global $wp_json_basic_auth_error;

		/* Reset the global error before processing. */
		$wp_json_basic_auth_error = null;

		/* If user is already authenticated, no need to re-authenticate. */
		if ( !empty( $user ) ) {
			return $user;
		}

		/* If no authentication credentials are provided, return as is (unauthenticated). */
		if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
			return $user;
		}

		/* Get username and password from server variables. */
		$username = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];

		/**
		 * Multisite Specific:
		 *
		 * Avoid infinite recursion in multisite installations by temporarily
		 * removing the 'determine_current_user' filter while authenticating.
		 */
		remove_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );

		/* Attempt to authenticate the user. */
		$user = wp_authenticate( $username, $password );

		/* Re-add the filter after authentication attempt. */
		add_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );

		/* If authentication fails, capture the error and return null. */
		if ( is_wp_error( $user ) ) {
			$wp_json_basic_auth_error = $user;
			return null;
		}

		/* Set global variable to indicate successful authentication. */
		$wp_json_basic_auth_error = true;

		/* Return authenticated user ID. */
		return $user->ID;

	} /* handler() */

	/**
	 * Handle the errors that occur during basic authentication.
	 *
	 * @param mixed $error Existing authentication errors.
	 * @return mixed
	 */
	public function error_handler( $error ) {

		/* If there are already authentication errors, return them. */
		if ( !empty( $error ) ) {
			return $error;
		}

		/* Otherwise, return the global error captured during authentication. */
		global $wp_json_basic_auth_error;

		return $wp_json_basic_auth_error;

	} /* error_handler() */

} /* BasicAuth(). */
