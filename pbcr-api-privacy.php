<?php
/**
 * Plugin Name: PBCR API Privacy
 * Description: Removes private owner data from the property REST API and, optionally, limits the property data block to users with an API key.
 * Version: 1.0.0
 * Author: ElPuas Digital Crafts
 * Author URI: https://elpuasdigitalcrafts.com
 * Text Domain: pbcr-api-privacy
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package PBCRApiPrivacy
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'PBCR_API_PRIVACY_VERSION', '1.0.0' );
define( 'PBCR_API_PRIVACY_PLUGIN_FILE', __FILE__ );
define( 'PBCR_API_PRIVACY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Autoloader for plugin classes.
 *
 * @param string $class_name The fully qualified class name.
 */
function pbcr_api_privacy_autoload( $class_name ) {
	if ( 0 !== strpos( $class_name, 'PBCRApiPrivacy\\' ) ) {
		return;
	}

	$relative  = str_replace( [ 'PBCRApiPrivacy\\', '\\' ], [ '', DIRECTORY_SEPARATOR ], $class_name );
	$file_path = PBCR_API_PRIVACY_PLUGIN_PATH . 'includes' . DIRECTORY_SEPARATOR . $relative . '.php';

	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}
spl_autoload_register( 'pbcr_api_privacy_autoload' );

/**
 * Activation callback: creates the read-only API role.
 */
function pbcr_api_privacy_activate() {
	\PBCRApiPrivacy\Roles::add();
}
register_activation_hook( __FILE__, 'pbcr_api_privacy_activate' );

/**
 * Initialize the plugin.
 */
function pbcr_api_privacy_init() {
	$plugin = new \PBCRApiPrivacy\Plugin();
	$plugin->register();
}
add_action( 'plugins_loaded', 'pbcr_api_privacy_init' );
