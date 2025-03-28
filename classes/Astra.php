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
class Astra {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		/* Register REST API end point to update Astra options. */
		add_action( 'rest_api_init', [ $this, 'register_rest_api' ]);

	} /* plugables() */

	public function register_rest_api() {
		register_rest_route( 'custom/v1', '/autowp/theme/astra/update', array(
			'methods'  => 'POST',
			'callback' => [ $this, 'update' ],
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			}
		));

	} /* register_rest_api() */

	public function update( $request ) {
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

		$astra_settings['header-desktop-items']['primary']['primary_center'] = ['menu-1'];
		$astra_settings['header-desktop-items']['primary']['primary_right'] = ['widget-1'];

		$astra_settings['footer-copyright-editor'] = 'Copyright [copyright] [current_year] [site_title] | Powered by [site_title]';

		update_option( 'astra-settings', $astra_settings );

		return rest_ensure_response( [ 'message' => 'The theme has been updated successfully.' ] );

	} /* update() */

} /* RankMath() */
