<?php
/**
 * OpenAI LLM Provider Adapter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/interface-provider.php';

class OmniCraft_AI_Provider_OpenAI implements OmniCraft_AI_Provider_Interface {

	private $api_key;
	private $model;
	private $api_url = 'https://api.openai.com/v1/chat/completions';

	public function __construct( $api_key = '', $model = 'gpt-4o' ) {
		$this->api_key = trim( $api_key );
		$this->model   = ! empty( $model ) ? trim( $model ) : 'gpt-4o';
	}

	public function get_name() {
		return 'OpenAI (' . $this->model . ')';
	}

	public function supports_vision() {
		$vision_models = array( 'gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo', 'gpt-4-vision-preview' );
		foreach ( $vision_models as $vm ) {
			if ( false !== stripos( $this->model, $vm ) ) {
				return true;
			}
		}
		return true;
	}

	public function generate( $system_prompt, $user_prompt, $options = array() ) {
		if ( empty( $this->api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'OpenAI API key is missing. Please configure it in plugin settings.', 'omnicraft-ai-builder' ) );
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

		// Check if vision/image is provided
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
						'url'    => $image_data_uri,
						'detail' => 'high',
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
			'model'       => $model,
			'messages'    => $messages,
			'temperature' => $temperature,
			'max_tokens'  => $max_tokens,
		);

		// Request JSON object response format for models that support it
		if ( false !== stripos( $model, 'gpt-4' ) || false !== stripos( $model, 'gpt-3.5-turbo' ) ) {
			$payload['response_format'] = array( 'type' => 'json_object' );
		}

		$args = array(
			'headers'     => array(
				'Authorization' => 'Bearer ' . $this->api_key,
				'Content-Type'  => 'application/json',
			),
			'body'        => wp_json_encode( $payload ),
			'timeout'     => 90,
			'sslverify'   => true,
		);

		$response = wp_remote_post( $this->api_url, $args );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'openai_request_error', sprintf( __( 'OpenAI HTTP request failed: %s', 'omnicraft-ai-builder' ), $response->get_error_message() ) );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( 200 !== $code ) {
			$error_msg = isset( $data['error']['message'] ) ? $data['error']['message'] : ( 'HTTP ' . $code . ' error' );
			return new WP_Error( 'openai_api_error', sprintf( __( 'OpenAI API Error (%d): %s', 'omnicraft-ai-builder' ), $code, $error_msg ) );
		}

		if ( ! isset( $data['choices'][0]['message']['content'] ) ) {
			return new WP_Error( 'openai_empty_content', __( 'OpenAI returned an unexpected response structure.', 'omnicraft-ai-builder' ) );
		}

		return trim( $data['choices'][0]['message']['content'] );
	}

	public function test_connection() {
		if ( empty( $this->api_key ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please enter an OpenAI API Key.', 'omnicraft-ai-builder' ),
			);
		}

		$test_res = $this->generate( 'You are a test helper.', 'Respond with {"status": "ok"} in JSON format.', array( 'max_tokens' => 20 ) );

		if ( is_wp_error( $test_res ) ) {
			return array(
				'success' => false,
				'message' => $test_res->get_error_message(),
			);
		}

		return array(
			'success' => true,
			'message' => sprintf( __( 'Connected successfully to OpenAI (%s)!', 'omnicraft-ai-builder' ), $this->model ),
		);
	}
}
