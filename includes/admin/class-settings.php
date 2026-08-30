<?php
/**
 * Handles plugin settings registration, defaults, and sanitization.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_Settings {

	public static $option_name = 'omnicraft_ai_settings';

	/**
	 * Register settings in WordPress.
	 */
	public function register_settings() {
		register_setting(
			'omnicraft_ai_settings_group',
			self::$option_name,
			array( $this, 'sanitize_settings' )
		);
	}

	/**
	 * Sanitize input settings.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		// Providers
		$sanitized['default_provider']   = sanitize_text_field( $input['default_provider'] ?? 'openai' );
		$sanitized['openai_api_key']     = sanitize_text_field( $input['openai_api_key'] ?? '' );
		$sanitized['openai_model']       = sanitize_text_field( $input['openai_model'] ?? 'gpt-4o' );
		$sanitized['anthropic_api_key']  = sanitize_text_field( $input['anthropic_api_key'] ?? '' );
		$sanitized['anthropic_model']    = sanitize_text_field( $input['anthropic_model'] ?? 'claude-3-5-sonnet-20241022' );
		$sanitized['gemini_api_key']     = sanitize_text_field( $input['gemini_api_key'] ?? '' );
		$sanitized['gemini_model']       = sanitize_text_field( $input['gemini_model'] ?? 'gemini-1.5-flash' );
		$sanitized['openrouter_api_key'] = sanitize_text_field( $input['openrouter_api_key'] ?? '' );
		$sanitized['openrouter_model']   = sanitize_text_field( $input['openrouter_model'] ?? 'anthropic/claude-3.5-sonnet' );
		$sanitized['custom_endpoint']    = esc_url_raw( $input['custom_endpoint'] ?? '' );
		$sanitized['custom_api_key']     = sanitize_text_field( $input['custom_api_key'] ?? '' );
		$sanitized['custom_model']       = sanitize_text_field( $input['custom_model'] ?? '' );

		// Builder defaults
		$sanitized['default_builder']    = in_array( $input['default_builder'] ?? '', array( 'elementor', 'gutenberg' ), true ) ? $input['default_builder'] : 'elementor';
		$sanitized['elementor_template'] = in_array( $input['elementor_template'] ?? '', array( 'elementor_header_footer', 'elementor_canvas', 'default' ), true ) ? $input['elementor_template'] : 'elementor_canvas';
		$sanitized['clean_canvas_mode']  = ! empty( $input['clean_canvas_mode'] ) ? 1 : 0;
		$sanitized['auto_publish']       = in_array( $input['auto_publish'] ?? '', array( 'draft', 'publish' ), true ) ? $input['auto_publish'] : 'draft';

		// White-Label Branding
		$sanitized['brand_name']         = sanitize_text_field( $input['brand_name'] ?? 'AI Site Builder' );
		$sanitized['menu_title']         = sanitize_text_field( $input['menu_title'] ?? 'AI Site Builder' );
		$sanitized['menu_icon']          = sanitize_text_field( $input['menu_icon'] ?? 'dashicons-superhero' );
		$sanitized['brand_logo_url']     = esc_url_raw( $input['brand_logo_url'] ?? '' );
		$sanitized['brand_accent_color'] = sanitize_hex_color( $input['brand_accent_color'] ?? '#6366f1' ) ?: '#6366f1';
		$sanitized['hide_vendor_links']  = ! empty( $input['hide_vendor_links'] ) ? 1 : 0;
		$sanitized['custom_footer_text'] = sanitize_text_field( $input['custom_footer_text'] ?? '' );
		$sanitized['support_url']        = esc_url_raw( $input['support_url'] ?? '' );

		// Credits & Access Limits
		$sanitized['enable_limits']      = ! empty( $input['enable_limits'] ) ? 1 : 0;
		$sanitized['monthly_limit']      = max( 1, (int) ( $input['monthly_limit'] ?? 20 ) );
		$sanitized['allowed_roles']      = ! empty( $input['allowed_roles'] ) && is_array( $input['allowed_roles'] ) ? array_map( 'sanitize_text_field', $input['allowed_roles'] ) : array( 'administrator', 'editor' );
		$sanitized['preserve_data_on_uninstall'] = ! empty( $input['preserve_data_on_uninstall'] ) ? 1 : 0;

		return $sanitized;
	}

	/**
	 * Get merged options with fallback defaults.
	 */
	public static function get_options() {
		$defaults = array(
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
			'default_builder'       => 'elementor',
			'elementor_template'    => 'elementor_canvas',
			'clean_canvas_mode'     => 1,
			'auto_publish'          => 'draft',
			'brand_name'            => 'AI Site Builder',
			'menu_title'            => 'AI Site Builder',
			'menu_icon'             => 'dashicons-superhero',
			'brand_logo_url'        => '',
			'brand_accent_color'    => '#6366f1',
			'hide_vendor_links'     => 1,
			'custom_footer_text'    => '',
			'support_url'           => '',
			'enable_limits'         => 0,
			'monthly_limit'         => 20,
			'allowed_roles'         => array( 'administrator', 'editor' ),
			'preserve_data_on_uninstall' => 1,
		);

		$saved = get_option( self::$option_name, array() );
		return wp_parse_args( $saved, $defaults );
	}
}
