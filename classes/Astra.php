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
 *Handles REST API endpoints to update Astra theme settings.
 */
class Astra {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		/* Register the custom REST API endpoint. */
		add_action( 'rest_api_init', [ $this, 'register_rest_api' ] );

	} /* plugables() */

	/**
	 * Register the custom REST API endpoint.
	 *
	 * @return void
	 */
	public function register_rest_api() {

		register_rest_route( 'custom/v1', '/autowp/theme/astra/update', array(
			'methods'  => 'POST',
			'callback' => [ $this, 'update' ],
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			}
		));

	} /* register_rest_api() */

	/**
	 * Updates Astra theme settings based on the provided parameters.
	 *
	 * @param WP_REST_Request $request The incoming request object.
	 * @return WP_REST_Response|WP_Error REST response or error object.
	 */
	public function update( $request ) {

		/* Get all request parameters. */
		$parameters = $request->get_params();

		/* Validate required parameters. */
		if ( empty( $parameters['colorPalette'] ) ) {
			return new \WP_Error( 'no_color_palette', 'A color palette is required.', array( 'status' => 400 ) );
		}

		if ( empty( $parameters['fonts'] ) ) {
			return new \WP_Error( 'no_font_family', 'A font family is required.', array( 'status' => 400 ) );
		}

		if ( empty( $parameters['phone'] ) ) {
			return new \WP_Error( 'no_phone', 'A phone number is required.', array( 'status' => 400 ) );
		}

		/* Update the color palettes. */
		$color_palettes = get_option( 'astra-color-palettes' );
		$color_palettes['currentPalette'] = 'palette_1';
		$color_palettes['palettes']['palette_1'] = $parameters['colorPalette'];
		update_option( 'astra-color-palettes', $color_palettes );

		/* Update the Astra theme settings. */
		$astra_settings = get_option( 'astra-settings' );

		/* Set global color palette. */
		$astra_settings['global-color-palette']['palette'] = $parameters['colorPalette'];

		/* Set body and heading font families. */
		$astra_settings['body-font-family'] = $parameters['fonts']['body'];
		$astra_settings['headings-font-family'] = $parameters['fonts']['heading'];

		/* Configure header layout for desktop view. */
		$astra_settings['header-desktop-items'] = array(
			'popup' => array(
				'popup_content' => array( 0 => 'mobile-menu' ),
			),
			'above' => array(
				'above_left' => array(),
				'above_left_center' => array(),
				'above_center' => array(),
				'above_right_center' => array(),
				'above_right' => array(),
			),
			'primary' => array(
				'primary_left' => array( 0 => 'logo' ),
				'primary_left_center' => array(),
				'primary_center' => array( 0 => 'menu-1' ),
				'primary_right_center' => array(),
				'primary_right' => array( 0 => 'html-1' ),
			),
			'below' => array(
				'below_left' => array(),
				'below_left_center' => array(),
				'below_center' => array(),
				'below_right_center' => array(),
				'below_right' => array(),
			),
			'flag' => true,
		);

		/* Set clickable phone number in the header. */
		$astra_settings['header-html-1'] = '<h5><a href="tel:' . esc_attr( $parameters['phone'] ) . '">' . esc_html( $parameters['phone'] ) . '</a></h5>';

		/* Set default footer copyright. */
		$astra_settings['footer-copyright-editor'] = 'Copyright [copyright] [current_year] [site_title] | Powered by [site_title]';

		/* Save updated settings. */
		update_option( 'astra-settings', $astra_settings );

		/* Return a successful response. */
		return rest_ensure_response( [ 'message' => 'The theme has been updated successfully.' ] );

	} /* update() */

} /* Astra() */
