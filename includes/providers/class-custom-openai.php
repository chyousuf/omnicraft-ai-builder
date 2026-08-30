<?php
/**
 * Custom / OpenAI-Compatible Endpoint Provider Adapter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/interface-provider.php';

class OmniCraft_AI_Provider_Custom_OpenAI implements OmniCraft_AI_Provider_Interface {

	private $endpoint;
	private $api_key;
	private $model;

	public function __construct( $endpoint = '', $api_key = '', $model = '' ) {
		$this->endpoint = trim( $endpoint );
		$this->api_key  = trim( $api_key );
		$this->model    = trim( $model );

		// Normalize endpoint URL
		if ( ! empty( $this->endpoint ) && false === strpos( $this->endpoint, '/chat/completions' ) ) {
			$this->endpoint = rtrim( $this->endpoint, '/' ) . '/chat/completions';
		}
	}

	public function get_name() {
		return 'Custom / Compatible (' . ( ! empty( $this->model ) ? $this->model : 'Default Model' ) . ')';
	}

	public function supports_vision() {
		return true;
	}

	public function generate( $system_prompt, $user_prompt, $options = array() ) {
		if ( empty( $this->endpoint ) ) {
			return new WP_Error( 'missing_endpoint', __( 'Custom endpoint URL is missing. Please configure it in plugin settings.', 'omnicraft-ai-builder' ) );
		}

		$model = ! empty( $options['model'] ) ? $options['model'] : $this->model;
		$temperature = isset( $options['temperature'] ) ? (float) $options['temperature'] : 0.7;
		$max_tokens = isset( $options['max_tokens'] ) ? (int) $options['max_tokens'] : 4096;

		$messages = array();
		if ( ! empty( $system_prompt ) ) {
			$messages[] = array(
				'role'    => 'system',
				'content' => $system_prompt,
			);
		}

		// Handle Vision / Image
		if ( ! empty( $options['image_base64'] ) ) {
			$mime = ! empty( $options['image_mime'] ) ? $options['image_mime'] : 'image/png';
			$image_data_uri = 'data:' . $mime . ';base64,' . $options['image_base64'];

			$user_content = array(
				array(
					'type' => 'text',
					'text' => $user_prompt,
				),
				array(
					'type'      => 'image_url',
					'image_url' => array(
						'url' => $image_data_uri,
					),
				),
			);

			$messages[] = array(
				'role'    => 'user',
				'content' => $user_content,
			);
		} else {
			$messages[] = array(
				'role'    => 'user',
				'content' => $user_prompt,
			);
		}

		$payload = array(
			'messages'    => $messages,
			'temperature' => $temperature,
			'max_tokens'  => $max_tokens,
		);

		if ( ! empty( $model ) ) {
			$payload['model'] = $model;
		}

		$headers = array(
			'Content-Type' => 'application/json',
		);

		if ( ! empty( $this->api_key ) ) {
			$headers['Authorization'] = 'Bearer ' . $this->api_key;
		}

		$args = array(
			'headers'   => $headers,
			'body'      => wp_json_encode( $payload ),
			'timeout'   => 90,
			'sslverify' => false,
		);

		$response = wp_remote_post( $this->endpoint, $args );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'custom_request_error', sprintf( __( 'Custom API request failed: %s', 'omnicraft-ai-builder' ), $response->get_error_message() ) );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( $code < 200 || $code >= 300 ) {
			$error_msg = isset( $data['error']['message'] ) ? $data['error']['message'] : ( 'HTTP ' . $code . ' error' );
			return new WP_Error( 'custom_api_error', sprintf( __( 'Custom API Error (%d): %s', 'omnicraft-ai-builder' ), $code, $error_msg ) );
		}

		if ( ! isset( $data['choices'][0]['message']['content'] ) ) {
			return new WP_Error( 'custom_empty_content', __( 'Custom endpoint returned an unexpected response structure.', 'omnicraft-ai-builder' ) );
		}

		return trim( $data['choices'][0]['message']['content'] );
	}

	public function test_connection() {
		if ( empty( $this->endpoint ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please enter a custom API Endpoint URL.', 'omnicraft-ai-builder' ),
			);
		}

		$test_res = $this->generate( 'You are a test helper.', 'Respond with {"status": "ok"} in JSON format.', array( 'max_tokens' => 30 ) );

		if ( is_wp_error( $test_res ) ) {
			return array(
				'success' => false,
				'message' => $test_res->get_error_message(),
			);
		}

		return array(
			'success' => true,
			'message' => sprintf( __( 'Connected successfully to custom endpoint (%s)!', 'omnicraft-ai-builder' ), $this->endpoint ),
		);
	}
}
