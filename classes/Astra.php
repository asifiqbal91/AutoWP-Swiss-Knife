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

		// $astra_settings['header-desktop-items']['primary']['primary_center'] = ['menu-1'];
		// $astra_settings['header-desktop-items']['primary']['primary_right'] = ['widget-1'];

		// $astra_settings['footer-desktop-items']['popup']['popup_content'] = [];
		// $astra_settings['footer-desktop-items']['layouts']['primary']['column'] = 4;
		// $astra_settings['footer-desktop-items']['layouts']['primary']['layout'] = [
		// 	'mobile' => 'full',
		// 	'tablet' => '4-equal',
		// 	'desktop' => '4-equal'
		// ];
		// $astra_settings['hb-footer-column'] = '4';
		// $astra_settings['hb-footer-layout'] = [
		// 	'desktop' => '4-equal',
		// 	'tablet' => '4-equal',
		// 	'mobile' => 'full',
		// ];

		// $astra_settings['footer-desktop-items']['primary']['primary_1'] = ['html-1', 'social-icons-1'];
		// $astra_settings['footer-html-1'] = '<h5>About Us</h5>\nQuam quam lacus, amet lorem eu nunc, eget dui libero et faucibus facilisis sed odio suspendisse';
		// $astra_settings['footer-html-1-alignment'] = [
		// 	'desktop' => 'left',
		// 	'tablet' => 'center',
		// 	'mobile' => 'center',
		// ];
		// $astra_settings['footer-social-1']['enable'] = false;
		// $astra_settings['footer-social-1-alignment'] = [
		// 	'desktop' => 'left',
		// 	'tablet' => 'center',
		// 	'mobile' => 'center',
		// ];

		// $astra_settings['footer-desktop-items']['primary']['primary_2'] = ['widget-1'];
		// $astra_settings['footer-desktop-items']['primary']['primary_3'] = ['widget-2'];
		// $astra_settings['footer-desktop-items']['primary']['primary_4'] = ['widget-3'];

		$astra_settings['footer-copyright-editor'] = 'Copyright [copyright] [current_year] [site_title] | Powered by [site_title]';

		update_option( 'astra-settings', $astra_settings );

		return rest_ensure_response( [ $astra_settings ] );
		// return rest_ensure_response( [ 'message' => 'The theme has been updated successfully.' ] );

	} /* update() */

} /* RankMath() */
