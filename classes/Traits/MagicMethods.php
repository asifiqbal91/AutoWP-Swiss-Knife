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
 * Setup the common magic methods for the class.
 */
trait MagicMethods {

	/**
	 * Define the magic method to set properties from outside of the class context.
	 *
	 * @param string $name The name of a property.
	 * @param mixed $value The value to assign to the property.
	 * @return void.
	 */
	public function __set( $name, $value ) {

		$this->{$name} = $value;

	} /* __set() */


	/**
	 * Define magic method to get the value of a property outside of the class context.
	 *
	 * @param string $name The name of a property.
	 * @return mixed The property value.
	 */
	public function __get( $name ) {

		return $this->{$name};

	} /* __get() */

} /* MagicMethods() */
