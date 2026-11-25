<?php
/**
 * Settings Class
 *
 * Adds a checkbox to "Settings > Reading" to restrict the property data block
 * to users with an API key. Uses the Settings API, which handles the nonce and
 * the `manage_options` capability check.
 *
 * @package PBCRApiPrivacy
 */

namespace PBCRApiPrivacy\Admin;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin settings on the Reading screen.
 */
class Settings {

	/**
	 * Option name.
	 */
	const OPTION = 'pbcr_api_privacy_restrict_meta';

	/**
	 * Settings page where the field is shown.
	 */
	const PAGE = 'reading';

	/**
	 * Register hooks.
	 */
	public function register() {
		add_action( 'admin_init', [ $this, 'register_setting' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( PBCR_API_PRIVACY_PLUGIN_FILE ), [ $this, 'add_settings_link' ] );
	}

	/**
	 * Whether the property data block is restricted to API readers.
	 *
	 * @return bool
	 */
	public static function is_restricted() {
		return (bool) get_option( self::OPTION, false );
	}

	/**
	 * Register the option and its field.
	 */
	public function register_setting() {
		register_setting(
			self::PAGE,
			self::OPTION,
			[
				'type'              => 'boolean',
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => false,
			]
		);

		add_settings_field(
			self::OPTION,
			__( 'Property API', 'pbcr-api-privacy' ),
			[ $this, 'render_field' ],
			self::PAGE,
			'default',
			[ 'label_for' => self::OPTION ]
		);
	}

	/**
	 * Render the checkbox.
	 */
	public function render_field() {
		?>
		<label for="<?php echo esc_attr( self::OPTION ); ?>">
			<input type="checkbox" id="<?php echo esc_attr( self::OPTION ); ?>" name="<?php echo esc_attr( self::OPTION ); ?>" value="1" <?php checked( self::is_restricted() ); ?> />
			<?php esc_html_e( 'Only users with an API key can read the property data (price, code, gallery…)', 'pbcr-api-privacy' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'Owner name, contact, address and private notes are always hidden. Turn this on only after the integrations use their key.', 'pbcr-api-privacy' ); ?>
		</p>
		<?php
	}

	/**
	 * Add a "Settings" link on the Plugins screen.
	 *
	 * @param string[] $links Existing action links.
	 * @return string[]
	 */
	public function add_settings_link( $links ) {
		$url = admin_url( 'options-reading.php#' . self::OPTION );

		array_unshift(
			$links,
			sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'Settings', 'pbcr-api-privacy' ) )
		);

		return $links;
	}
}
