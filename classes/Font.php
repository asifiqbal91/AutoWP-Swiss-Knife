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
class Font {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		add_action( 'wp_enqueue_scripts', [ $this, 'fonts' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'inline_styles' ] );

	} /* plugables() */


	public function fonts() {

		wp_enqueue_style(
			'autowp-swiss-knife-fonts',
			plugin_dir_url( __FILE__ ) . '../assets/css/fonts.css'
		);

	} /* fonts() */


	public function inline_styles() {
		$get_font_family = get_option( 'ask_logo_font_family' );
		$get_font_size = get_option( 'ask_logo_font_size' );

		$custom_css = "
			.site-title a {
				font-family: '{$get_font_family}';
				font-size: {$get_font_size};
			}
		";

		wp_register_style( 'autowp-swiss-knife-inline-css', false );
		wp_add_inline_style( 'autowp-swiss-knife-inline-css', $custom_css );
		wp_enqueue_style( 'autowp-swiss-knife-inline-css' );

	} /* inline_styles() */

} /* Font() */
