<?php
/**
 * Anthropic Claude LLM Provider Adapter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/interface-provider.php';

class OmniCraft_AI_Provider_Anthropic implements OmniCraft_AI_Provider_Interface {

	private $api_key;
	private $model;
	private $api_url = 'https://api.anthropic.com/v1/messages';

	public function __construct( $api_key = '', $model = 'claude-3-5-sonnet-20241022' ) {
		$this->api_key = trim( $api_key );
		$this->model   = ! empty( $model ) ? trim( $model ) : 'claude-3-5-sonnet-20241022';
	}

	public function get_name() {
		return 'Anthropic Claude (' . $this->model . ')';
	}

	public function supports_vision() {
		return true; // Claude 3.5 Sonnet, 3 Opus, 3 Haiku all support vision
	}

	public function generate( $system_prompt, $user_prompt, $options = array() ) {
		if ( empty( $this->api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Anthropic API key is missing. Please configure it in plugin settings.', 'omnicraft-ai-builder' ) );
		}

		$model = ! empty( $options['model'] ) ? $options['model'] : $this->model;
		$temperature = isset( $options['temperature'] ) ? (float) $options['temperature'] : 0.7;
		$max_tokens = isset( $options['max_tokens'] ) ? (int) $options['max_tokens'] : 4096;

		$content_blocks = array();

		// Add image block if present
		if ( ! empty( $options['image_base64'] ) ) {
			$mime = ! empty( $options['image_mime'] ) ? $options['image_mime'] : 'image/png';
			$content_blocks[] = array(
				'type'   => 'image',
				'source' => array(
					'type'       => 'base64',
					'media_type' => $mime,
					'data'       => $options['image_base64'],
				),
			);
		}

		// Add user prompt text block
		$content_blocks[] = array(
			'type' => 'text',
			'text' => $user_prompt,
		);

		$payload = array(
			'model'       => $model,
			'max_tokens'  => $max_tokens,
			'temperature' => $temperature,
			'messages'    => array(
				array(
					'role'    => 'user',
					'content' => $content_blocks,
				),
			),
		);

		if ( ! empty( $system_prompt ) ) {
			$payload['system'] = $system_prompt;
		}

		$args = array(
			'headers'     => array(
				'x-api-key'         => $this->api_key,
				'anthropic-version' => '2023-06-01',
				'content-type'      => 'application/json',
			),
			'body'        => wp_json_encode( $payload ),
			'timeout'     => 90,
			'sslverify'   => true,
		);

		$response = wp_remote_post( $this->api_url, $args );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'anthropic_request_error', sprintf( __( 'Anthropic HTTP request failed: %s', 'omnicraft-ai-builder' ), $response->get_error_message() ) );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( 200 !== $code ) {
			$error_msg = isset( $data['error']['message'] ) ? $data['error']['message'] : ( 'HTTP ' . $code . ' error' );
			return new WP_Error( 'anthropic_api_error', sprintf( __( 'Anthropic API Error (%d): %s', 'omnicraft-ai-builder' ), $code, $error_msg ) );
		}

		if ( ! isset( $data['content'][0]['text'] ) ) {
			return new WP_Error( 'anthropic_empty_content', __( 'Anthropic returned an unexpected response structure.', 'omnicraft-ai-builder' ) );
		}

		return trim( $data['content'][0]['text'] );
	}

	public function test_connection() {
		if ( empty( $this->api_key ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please enter an Anthropic API Key.', 'omnicraft-ai-builder' ),
			);
		}

		$test_res = $this->generate( 'You are a test helper.', 'Respond with {"status": "ok"} in valid JSON format.', array( 'max_tokens' => 30 ) );

		if ( is_wp_error( $test_res ) ) {
			return array(
				'success' => false,
				'message' => $test_res->get_error_message(),
			);
		}

		return array(
			'success' => true,
			'message' => sprintf( __( 'Connected successfully to Anthropic (%s)!', 'omnicraft-ai-builder' ), $this->model ),
		);
	}
}
