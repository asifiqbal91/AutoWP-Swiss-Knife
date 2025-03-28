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
class RankMath {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		/* Register Rank Math meta to REST API. */
		add_action('init', [ $this, 'add_meta' ]);

		/* Register REST API end point to update Rank Math options. */
		add_action( 'rest_api_init', [ $this, 'register_rest_api' ]);

	} /* plugables() */

	public function add_meta() {
		$rank_math_meta_keys = [
			'rank_math_title',
			'rank_math_description',
			'rank_math_focus_keyword',
			'rank_math_secondary_keywords',
			'rank_math_canonical_url',
			'rank_math_robots',
		];

		foreach ($rank_math_meta_keys as $meta_key) {
			register_meta('post', $meta_key, [
				'type'         => 'string',
				'description'  => 'Rank Math SEO meta data',
				'single'       => true,
				'show_in_rest' => true,
				'auth_callback' => function () {
					return current_user_can('edit_posts');
				}
			]);
		}
	} /* add_meta() */


	public function register_rest_api() {
		register_rest_route( 'custom/v1', '/autowp/plugin/rank-math/update', array(
			'methods'  => 'POST',
			'callback' => [ $this, 'update' ],
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			}
		));
	} /* register_rest_api() */

	public function update( $request ) {
		$parameters = $request->get_params();

		update_option( 'rank-math-options-general', $parameters['general'], 'on' );
		update_option( 'ranrank-math-options-titles', $parameters['titles'], 'on' );
		update_option( 'rank-math-options-sitemap', $parameters['sitemap'], 'auto' );
		update_option( 'rank-math-options-instant-indexing', $parameters['instantIndexing'], 'auto' );

		update_option( 'rank_math_registration_skip', '1', 'auto' );
		update_option( 'rank_math_review_posts_converted', '1', 'auto' );
		update_option( 'rank_math_wizard_completed', '1', 'off' );
		update_option( 'rank_math_is_configured', '1', 'off' );

		return rest_ensure_response( [ 'message' => 'Rank Math plugin has been updated successfully.' ] );

	} /* update() */

} /* RankMath() */
