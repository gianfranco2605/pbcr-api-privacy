<?php
/**
 * Uninstall routine: removes the option and the API reader role.
 *
 * @package PBCRApiPrivacy
 */

// Only run when WordPress uninstalls the plugin.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'pbcr_api_privacy_restrict_meta' );

remove_role( 'pbcr_api_reader' );

$pbcr_api_privacy_admin = get_role( 'administrator' );
if ( $pbcr_api_privacy_admin ) {
	$pbcr_api_privacy_admin->remove_cap( 'pbcr_read_property_api' );
}
