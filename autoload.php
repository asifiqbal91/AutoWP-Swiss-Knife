<?php

/* Exit, if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	echo 'Hi there! I\'m just a part of plugin, not much I can do when called directly.';
	exit();
}

if ( ! function_exists( 'autowp_swiss_knife_autoloader' ) ) {

	/**
	 * Define the AutoWPSWISSKnife plugin classes autoloader.
	 *
	 * @param string $class The name of called class.
	 */
	function autowp_swiss_knife_autoloader( $class ) {

		/* Define the class prefix. */
		$prefix = 'AutoWPSWISSKnife\\';

		/* Get the directory of AutoWPSWISSKnife plugin classes. */
		$class_dir = dirname( __FILE__ ) . '/classes/';

		/* Return, if the class prefix not exists. */
		$length = strlen( $prefix );
		if ( strncmp( $prefix, $class, $length ) !== 0 ) { return; }

		/* Get the relative class name. */
		$relative_class = substr( $class, $length );

		/* Get the path of class file. */
		$file_path = $class_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		/* Require, if the file exists. */
		if ( file_exists( $file_path ) ) { require_once $file_path; }

	}

	/* Call the AutoWPSWISSKnife plugin classes autoloader.. */
	spl_autoload_register( 'autowp_swiss_knife_autoloader' );

} /* autowp_swiss_knife_autoloader() */
