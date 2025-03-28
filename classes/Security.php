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
class Security {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		remove_action('wp_head', 'wp_generator');

		add_filter( 'xmlrpc_enabled', '__return_false' );

		add_filter('the_generator', [ $this, 'wp_remove_version' ]);

	} /* plugables() */


	public function wp_remove_version() {
		return '';
	} /* wp_remove_version() */

} /* Security() */
