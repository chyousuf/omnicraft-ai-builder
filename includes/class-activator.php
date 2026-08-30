<?php
/**
 * Fired during plugin activation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_Activator {

	/**
	 * Run DB migrations and set default plugin options.
	 */
	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// History Table
		$history_table = $wpdb->prefix . 'omnicraft_ai_history';
		$sql_history = "CREATE TABLE $history_table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL DEFAULT 0,
			page_id bigint(20) NOT NULL DEFAULT 0,
			page_title varchar(255) NOT NULL DEFAULT '',
			builder_type varchar(50) NOT NULL DEFAULT 'elementor',
			input_type varchar(50) NOT NULL DEFAULT 'text',
			provider varchar(50) NOT NULL DEFAULT 'openai',
			model varchar(100) NOT NULL DEFAULT '',
			prompt_summary text,
			target_url varchar(500) DEFAULT '',
			screenshot_url varchar(500) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY page_id (page_id)
		) $charset_collate;";

		// Credits / Quota Table
		$credits_table = $wpdb->prefix . 'omnicraft_ai_credits';
		$sql_credits = "CREATE TABLE $credits_table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL DEFAULT 0,
			month_year varchar(7) NOT NULL DEFAULT '',
			generations_used int(11) NOT NULL DEFAULT 0,
			last_generation_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY user_month (user_id, month_year)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_history );
		dbDelta( $sql_credits );

		// Set default settings if not existing
		if ( ! get_option( 'omnicraft_ai_settings' ) ) {
			$defaults = array(
				// Provider settings
				'default_provider'      => 'openai',
				'openai_api_key'        => '',
				'openai_model'          => 'gpt-4o',
				'anthropic_api_key'     => '',
				'anthropic_model'       => 'claude-3-5-sonnet-20241022',
				'gemini_api_key'        => '',
				'gemini_model'          => 'gemini-1.5-flash',
				'openrouter_api_key'    => '',
				'openrouter_model'      => 'anthropic/claude-3.5-sonnet',
				'custom_endpoint'       => '',
				'custom_api_key'        => '',
				'custom_model'          => '',
				
				// Builder Preferences
				'default_builder'       => 'elementor',
				'elementor_template'    => 'elementor_header_footer', // or elementor_canvas
				'auto_publish'          => 'draft', // draft or publish

				// White-Label Settings
				'brand_name'            => 'AI Site Builder',
				'menu_title'            => 'AI Site Builder',
				'menu_icon'             => 'dashicons-superhero',
				'brand_logo_url'        => '',
				'brand_accent_color'    => '#6366f1', // Modern Indigo
				'hide_vendor_links'     => 1,
				'custom_footer_text'    => '',
				'support_url'           => '',

				// Credits / Limits
				'enable_limits'         => 0,
				'monthly_limit'         => 20,
				'allowed_roles'         => array( 'administrator', 'editor' ),
				'preserve_data_on_uninstall' => 1,
			);
			update_option( 'omnicraft_ai_settings', $defaults );
		}

		update_option( 'omnicraft_ai_db_version', OMNICRAFT_AI_VERSION );
	}
}
