<?php
/**
 * Fired during plugin deactivation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_Deactivator {

	/**
	 * Clean up transients or temporary resources during deactivation.
	 */
	public static function deactivate() {
		// Clean up any scheduled cron jobs or transients if used
		delete_transient( 'omnicraft_ai_test_connection_result' );
	}
}
