<?php

namespace AutoWPSWISSKnife\Traits;

/* Exit, if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	echo 'Hi there! I\'m just a part of plugin, not much I can do when called directly.';
	exit();
}

/**
 * Setup the magic methods for singleton pattern.
 */
trait SingletonMagicMethods {

	use MagicMethods;

	/**
	 * Define the dummy constructor to prevent from being loaded more than once.
	 *
	 * @access private
	 */
	private function __construct() {  /* do nothing here. */  }

} /* SingletonMagicMethods() */
