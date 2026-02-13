<?php
/**
 * Plugin Name: RTL Localization & Fonts
 * Description: Better support for Right To Left languages like Persian, Arabic and Hebrew and Fonts
 * Plugin URI: https://parsmizban.com
 * Author: DediData
 * Author URI: https://parsmizban.com
 * Version: 2.5.5
 * Requires at least: 6.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * License: GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: persian-font
 *
 * @package Persian_Font
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( '\DediData\Plugin_Autoloader' ) ) {
	require 'includes/DediData/class-plugin-autoloader.php';
}
// Set name spaces we use in this plugin
new \DediData\Plugin_Autoloader( array( 'DediData', 'PersianFont' ) );
/**
 * The function PERSIAN_FONT returns an instance of the Persian_Font class.
 *
 * @return object an instance of the \PersianFont\Persian_Font class.
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function PERSIAN_FONT() { // phpcs:ignore Squiz.Functions.GlobalFunction.Found, WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return \PersianFont\Persian_Font::get_instance( __FILE__ );
}
PERSIAN_FONT();
