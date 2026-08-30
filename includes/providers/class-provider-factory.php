<?php
/**
 * Factory class to instantiate LLM Providers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/interface-provider.php';
require_once dirname( __FILE__ ) . '/class-openai.php';
require_once dirname( __FILE__ ) . '/class-anthropic.php';
require_once dirname( __FILE__ ) . '/class-gemini.php';
require_once dirname( __FILE__ ) . '/class-openrouter.php';
require_once dirname( __FILE__ ) . '/class-custom-openai.php';

class OmniCraft_AI_Provider_Factory {

	/**
	 * Create and return a provider instance based on settings or explicit key.
	 *
	 * @param string|null $provider_slug Specific provider override (optional)
	 * @param array|null  $config_override Configuration array override (optional)
	 * @return OmniCraft_AI_Provider_Interface|WP_Error
	 */
	public static function create( $provider_slug = null, $config_override = null ) {
		$settings = get_option( 'omnicraft_ai_settings', array() );

		if ( null !== $config_override && is_array( $config_override ) ) {
			$settings = array_merge( $settings, $config_override );
		}

		$provider = ! empty( $provider_slug ) ? $provider_slug : ( ! empty( $settings['default_provider'] ) ? $settings['default_provider'] : 'openai' );

		switch ( $provider ) {
			case 'openai':
				$api_key = ! empty( $settings['openai_api_key'] ) ? $settings['openai_api_key'] : '';
				$model   = ! empty( $settings['openai_model'] ) ? $settings['openai_model'] : 'gpt-4o';
				return new OmniCraft_AI_Provider_OpenAI( $api_key, $model );

			case 'anthropic':
				$api_key = ! empty( $settings['anthropic_api_key'] ) ? $settings['anthropic_api_key'] : '';
				$model   = ! empty( $settings['anthropic_model'] ) ? $settings['anthropic_model'] : 'claude-3-5-sonnet-20241022';
				return new OmniCraft_AI_Provider_Anthropic( $api_key, $model );

			case 'gemini':
				$api_key = ! empty( $settings['gemini_api_key'] ) ? $settings['gemini_api_key'] : '';
				$model   = ! empty( $settings['gemini_model'] ) ? $settings['gemini_model'] : 'gemini-1.5-flash';
				return new OmniCraft_AI_Provider_Gemini( $api_key, $model );

			case 'openrouter':
				$api_key = ! empty( $settings['openrouter_api_key'] ) ? $settings['openrouter_api_key'] : '';
				$model   = ! empty( $settings['openrouter_model'] ) ? $settings['openrouter_model'] : 'anthropic/claude-3.5-sonnet';
				return new OmniCraft_AI_Provider_OpenRouter( $api_key, $model );

			case 'custom':
				$endpoint = ! empty( $settings['custom_endpoint'] ) ? $settings['custom_endpoint'] : '';
				$api_key  = ! empty( $settings['custom_api_key'] ) ? $settings['custom_api_key'] : '';
				$model    = ! empty( $settings['custom_model'] ) ? $settings['custom_model'] : '';
				return new OmniCraft_AI_Provider_Custom_OpenAI( $endpoint, $api_key, $model );

			default:
				return new WP_Error( 'unsupported_provider', sprintf( __( 'Unsupported provider: %s', 'omnicraft-ai-builder' ), esc_html( $provider ) ) );
		}
	}
}
