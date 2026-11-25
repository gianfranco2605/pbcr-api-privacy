<?php
/**
 * Main Plugin Class
 *
 * @package PBCRApiPrivacy
 */

namespace PBCRApiPrivacy;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers all plugin components.
 */
class Plugin {

	/**
	 * Register all plugin functionality.
	 */
	public function register() {
		add_action( 'init', [ $this, 'load_textdomain' ] );

		( new Rest\PropertyPrivacy() )->register();

		if ( is_admin() ) {
			( new Admin\Settings() )->register();
			add_action( 'admin_init', [ Roles::class, 'maybe_add' ] );
		}
	}

	/**
	 * Load plugin text domain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'pbcr-api-privacy', false, dirname( plugin_basename( PBCR_API_PRIVACY_PLUGIN_FILE ) ) . '/languages' );
	}
}
