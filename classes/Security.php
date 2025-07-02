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
 * Provides basic security enhancements for WordPress
 */
class Security {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		/* Remove the WordPress version meta tag from the <head> section. */
		remove_action( 'wp_head', 'wp_generator' );

		/* Disable XML-RPC to prevent remote connections (which can be a security risk). */
		add_filter( 'xmlrpc_enabled', '__return_false' );

		/* Filter the generator output to remove version info from RSS feeds, etc. */
		add_filter( 'the_generator', [ $this, 'wp_remove_version' ] );

	} /* plugables() */

	/**
	 * Remove the WordPress version number from the generator tag.
	 *
	 * @return string Empty string to replace the default version output.
	 */
	public function wp_remove_version() {

		return '';

	} /* wp_remove_version() */

} /* Security() */
