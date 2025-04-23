<?php
/**
 * Plugin Name: AutoWP Swiss Knife
 * Description: A multipurpose WordPress toolkit with utilities, shortcodes, and admin enhancements.
 * Version: 1.0.0
 * Plugin URI: https://adommo.com/swissknife
 * Author:		adommo Team
 * Author URI:	https://adommo.com
 * License:		GNU General Public License
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 */

use AutoWPSWISSKnife\BasicAuth;
use AutoWPSWISSKnife\Astra;
use AutoWPSWISSKnife\RankMath;
use AutoWPSWISSKnife\Security;
use AutoWPSWISSKnife\Font;
use AutoWPSWISSKnife\SettingsPage;

 /* Exit, if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	echo 'Hi there! I\'m just a part of plugin, not much I can do when called directly.';
	exit();
}

/* Require, the plugin autoload. */
require_once 'autoload.php';

BasicAuth::init();
Astra::init();
RankMath::init();
Security::init();
Font::init();
SettingsPage::init();
