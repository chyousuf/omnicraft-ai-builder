<?php
/**
 * Fired when the plugin is uninstalled.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Option to clean up database tables and settings if configured
$settings = get_option( 'omnicraft_ai_settings', array() );
$preserve_data = isset( $settings['preserve_data_on_uninstall'] ) ? (bool) $settings['preserve_data_on_uninstall'] : true;

if ( ! $preserve_data ) {
	global $wpdb;

	// Drop tables
	$history_table = $wpdb->prefix . 'omnicraft_ai_history';
	$credits_table = $wpdb->prefix . 'omnicraft_ai_credits';

	$wpdb->query( "DROP TABLE IF EXISTS {$history_table}" );
	$wpdb->query( "DROP TABLE IF EXISTS {$credits_table}" );

	// Delete options
	delete_option( 'omnicraft_ai_settings' );
	delete_option( 'omnicraft_ai_db_version' );
}
