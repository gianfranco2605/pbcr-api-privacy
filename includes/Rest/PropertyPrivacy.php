<?php
/**
 * Property REST Privacy Class
 *
 * Filters the `property_meta` field that the theme adds to /wp-json/wp/v2/propiedad.
 *
 * Step 1 (always on): private owner fields are removed for everyone.
 * Step 2 (setting "Settings > Reading"): the whole `property_meta` block is
 * only returned to users with the API reader capability.
 *
 * Developer filters:
 * - 'pbcr_api_privacy_private_fields'      Array of meta keys removed from the response.
 * - 'pbcr_api_privacy_reader_capability'   Capability needed to read property_meta in step 2.
 *
 * @package PBCRApiPrivacy
 */

namespace PBCRApiPrivacy\Rest;

use PBCRApiPrivacy\Admin\Settings;
use PBCRApiPrivacy\Roles;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Removes private data from property REST responses.
 */
class PropertyPrivacy {

	/**
	 * Post type whose REST responses are filtered.
	 */
	const POST_TYPE = 'property';

	/**
	 * Name of the REST field added by the theme.
	 */
	const META_FIELD = 'property_meta';

	/**
	 * Meta keys that must never leave the site.
	 */
	const PRIVATE_FIELDS = [
		'inspiry_property_owner_name',
		'inspiry_property_owner_contact',
		'inspiry_property_owner_address',
		'REAL_HOMES_property_private_note',
		'inspiry_message_to_reviewer',
		'rvr_property_owner',
	];

	/**
	 * Register hooks.
	 */
	public function register() {
		// Late priority so the theme has already added its field.
		add_filter( 'rest_prepare_' . self::POST_TYPE, [ $this, 'filter_response' ], 99, 3 );
	}

	/**
	 * Filter a single property REST response.
	 *
	 * @param \WP_REST_Response $response The response object.
	 * @param \WP_Post          $post     The property post.
	 * @param \WP_REST_Request  $request  The request.
	 * @return \WP_REST_Response
	 */
	public function filter_response( $response, $post, $request ) {
		if ( ! $response instanceof \WP_REST_Response ) {
			return $response;
		}

		// Leave the block editor untouched for people who can edit this property.
		if ( 'edit' === $request->get_param( 'context' ) && current_user_can( 'edit_post', $post->ID ) ) {
			return $response;
		}

		$data = $response->get_data();

		if ( ! isset( $data[ self::META_FIELD ] ) || ! is_array( $data[ self::META_FIELD ] ) ) {
			return $response;
		}

		if ( Settings::is_restricted() && ! $this->current_user_can_read() ) {
			unset( $data[ self::META_FIELD ] );
		} else {
			foreach ( $this->get_private_fields() as $field ) {
				unset( $data[ self::META_FIELD ][ $field ] );
			}
		}

		$response->set_data( $data );

		return $response;
	}

	/**
	 * Whether the current user may read the property data block.
	 *
	 * @return bool
	 */
	private function current_user_can_read() {
		$capability = apply_filters( 'pbcr_api_privacy_reader_capability', Roles::CAPABILITY );

		return is_string( $capability ) && '' !== $capability && current_user_can( $capability );
	}

	/**
	 * Get the list of private meta keys.
	 *
	 * Developers may add keys; the default ones are always kept.
	 *
	 * @return string[]
	 */
	private function get_private_fields() {
		$fields = apply_filters( 'pbcr_api_privacy_private_fields', self::PRIVATE_FIELDS );

		if ( ! is_array( $fields ) ) {
			$fields = [];
		}

		return array_unique( array_merge( self::PRIVATE_FIELDS, array_filter( $fields, 'is_string' ) ) );
	}
}
