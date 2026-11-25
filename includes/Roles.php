<?php
/**
 * Roles Class
 *
 * Manages the read-only role used by external integrations (e.g. CIMA).
 *
 * @package PBCRApiPrivacy
 */

namespace PBCRApiPrivacy;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read-only API role and capability.
 */
class Roles {

	/**
	 * Role slug for API readers.
	 */
	const ROLE = 'pbcr_api_reader';

	/**
	 * Capability that allows reading the full property data block.
	 */
	const CAPABILITY = 'pbcr_read_property_api';

	/**
	 * Create the role and give the capability to administrators.
	 */
	public static function add() {
		if ( ! get_role( self::ROLE ) ) {
			add_role(
				self::ROLE,
				__( 'API reader (read only)', 'pbcr-api-privacy' ),
				[
					'read'           => true,
					self::CAPABILITY => true,
				]
			);
		}

		$admin = get_role( 'administrator' );
		if ( $admin && ! $admin->has_cap( self::CAPABILITY ) ) {
			$admin->add_cap( self::CAPABILITY );
		}
	}

	/**
	 * Recreate the role if it is missing (e.g. plugin uploaded without activation hook running).
	 */
	public static function maybe_add() {
		if ( ! get_role( self::ROLE ) ) {
			self::add();
		}
	}

	/**
	 * Remove the role and the capability. Used on uninstall.
	 */
	public static function remove() {
		remove_role( self::ROLE );

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->remove_cap( self::CAPABILITY );
		}
	}
}
