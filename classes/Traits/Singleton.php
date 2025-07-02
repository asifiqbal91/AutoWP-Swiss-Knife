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
 * Setup the singleton pattern.
 */
trait Singleton {

	use SingletonMagicMethods;

	/**
	 * @static
	 * @access private
	 * @var object $instance The object of singleton instance.
	 */
	private static $instance;

	/**
	 * Initialize the singleton instance.
	 *
	 * @static
	 * @access public
	 * @return object $instance The object of singleton instance.
	 */
	public static function init() {

		if ( ! isset( self::$instance ) ) {

			/* Initialize the class. */
			self::$instance = new self;

			/* Register the plugable methods. */
			self::$instance->plugables();

		}

		return self::$instance;

	} /* init() */

} /* Singleton() */
