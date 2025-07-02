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
class SettingsPage {

	use Singleton;

	/**
	 * Register the plugable methods.
	 *
	 * @access private
	 */
	private function plugables() {

		/* Add the menu for settings page. */
		add_action( 'admin_menu', [ $this, 'add_menu' ] );

		/* Register the settings page options. */
		add_action( 'admin_init', [ $this, 'settings_init' ] );

	} /* plugables() */


	public function add_menu() {

		add_menu_page(
			'Settings',
			'AutoWP Swiss Knife',
			'manage_options',
			'ask-settings',
			[ $this, 'render_settings_page' ]
		);

	} /* render() */

	public function render_settings_page() {
		?>
    	<div class="wrap">
			<h1>AutoWP Swiss Knife</h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'ask_settings_group' );
					do_settings_sections( 'ask_settings_section' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	} /* render() */

	public function settings_init() {
		register_setting( 'ask_settings_group', 'ask_logo_font_family' );
		register_setting( 'ask_settings_group', 'ask_logo_font_size' );


		add_settings_section(
			'ask_settings_section_page',
			'Font Logo Settings',
			null,
			'ask_settings_section'
		);

		add_settings_field(
			'ask_logo_font_family',
			'Choose Logo Font Family',
			[ $this, 'ask_logo_font_family_dropdown_render' ],
			'ask_settings_section',
			'ask_settings_section_page'
		);

		add_settings_field(
			'ask_logo_font_size',
			'Choose Logo Font Size',
			[ $this, 'ask_logo_font_size_dropdown_render' ],
			'ask_settings_section',
			'ask_settings_section_page'
		);
	} /* settings_init() */

	public function ask_logo_font_family_dropdown_render() {
		$selected = get_option( 'ask_logo_font_family' );
		$font_families = ['Almond Script','Altariamiguel','Amro Sans','Andalan','Andasia','Arsenica Trial','Artine','Astratura','AwesomeStarthen','BackeyLonely','Bailies Script','Bakora Personal Use','BAR SADY Variable','Battiyan Script','Bauhaus','Bebas Neue','Belinda Script','Be Natural','Bogart Trial','Buvera','Buvera VF','Chillen','Dimyate','DIN','DIN Condensed','DIN Engschrift','DIN Mittelschrift','DIN Schablonierschrift','DIN Alternate','DIN Pro','Droid Sans','Emak','Fruitiy','Galink','Garet','Garetha','Garnet Capitals','Garnet Script','Giliant Demo','Gilmer','Gilmer Sans','Gravenora','Guthenberg','Guthen Bloots Personal Use','Guthen Jaqueline Demo','Hevernost','Honorveil','Kangge','Kithara','LIEUR PERSONAL USE ONLY','LuciaArditsy','LucindaScript','Magister Script One','Magister Script One Extrude','Magister Script Two','Magister Script Two Extrude','Magistral','Manilla Script','Merona','Millenial','Mokacino','Moodisst','Mooxy','Museo 700','Nordin Slab Outline','Nordin Slab','Nordin Slab Rough','Nordin Slab Stamp','PianoTeacher','Qualy Neue','Rafisqi','Rawclue','Roadster Script','Singolare','Singolare Stencil','Symphonys','TBJGoslap Display','TBJGoslap Text','Un Village','Varoste',];
		?>
		<select name="ask_logo_font_family">
			<option value="">Select Font Family</option>
			<?php foreach ($font_families as $font_family) : ?>
				<option value="<?php echo esc_attr($font_family); ?>" <?php selected($selected, $font_family); ?>>
					<?php echo esc_html($font_family); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function ask_logo_font_size_dropdown_render() {
		$selected = get_option( 'ask_logo_font_size' );
		$font_sizes = ['10px','11px','12px','13px','14px','15px','16px','17px','18px','19px','20px','21px','22px','23px','24px','25px','26px','27px','28px','29px','30px','31px','32px','33px','34px','35px','36px','37px','38px','39px','40px','41px','42px','43px','44px','45px','46px','47px','48px','49px','50px'];
		?>
		<select name="ask_logo_font_size">
			<option value="">Select Font Size</option>
			<?php foreach ($font_sizes as $font_size) : ?>
				<option value="<?php echo esc_attr($font_size); ?>" <?php selected($selected, $font_size); ?>>
					<?php echo esc_html($font_size); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

} /* SettingsPage() */
