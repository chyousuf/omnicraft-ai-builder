<?php
/**
 * Google Gemini LLM Provider Adapter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/interface-provider.php';

class OmniCraft_AI_Provider_Gemini implements OmniCraft_AI_Provider_Interface {

	private $api_key;
	private $model;

	public function __construct( $api_key = '', $model = 'gemini-1.5-flash' ) {
		$this->api_key = trim( $api_key );
		$this->model   = ! empty( $model ) ? trim( $model ) : 'gemini-1.5-flash';
	}

	public function get_name() {
		return 'Google Gemini (' . $this->model . ')';
	}

	public function supports_vision() {
		return true; // Gemini 1.5/2.0 series all support multimodal vision
	}

	public function generate( $system_prompt, $user_prompt, $options = array() ) {
		if ( empty( $this->api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Google Gemini API key is missing. Please configure it in plugin settings.', 'omnicraft-ai-builder' ) );
		}

		$model = ! empty( $options['model'] ) ? $options['model'] : $this->model;
		$temperature = isset( $options['temperature'] ) ? (float) $options['temperature'] : 0.7;
		$max_tokens = isset( $options['max_tokens'] ) ? (int) $options['max_tokens'] : 4096;

		$endpoint = sprintf(
			'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
			rawurlencode( $model ),
			rawurlencode( $this->api_key )
		);

		$parts = array();

		// Add image part if provided
		if ( ! empty( $options['image_base64'] ) ) {
			$mime = ! empty( $options['image_mime'] ) ? $options['image_mime'] : 'image/png';
			$parts[] = array(
				'inline_data' => array(
					'mime_type' => $mime,
					'data'      => $options['image_base64'],
				),
			);
		}

		// Add text prompt part
		$parts[] = array(
			'text' => $user_prompt,
		);

		$payload = array(
			'contents' => array(
				array(
					'parts' => $parts,
				),
			),
			'generationConfig' => array(
				'temperature'       => $temperature,
				'maxOutputTokens'   => $max_tokens,
				'responseMimeType'  => 'application/json',
			),
		);

		if ( ! empty( $system_prompt ) ) {
			$payload['system_instruction'] = array(
				'parts' => array(
					array( 'text' => $system_prompt ),
				),
			);
		}

		$args = array(
			'headers'     => array(
				'Content-Type' => 'application/json',
			),
			'body'        => wp_json_encode( $payload ),
			'timeout'     => 90,
			'sslverify'   => true,
		);

		$response = wp_remote_post( $endpoint, $args );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'gemini_request_error', sprintf( __( 'Google Gemini HTTP request failed: %s', 'omnicraft-ai-builder' ), $response->get_error_message() ) );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( 200 !== $code ) {
			$error_msg = isset( $data['error']['message'] ) ? $data['error']['message'] : ( 'HTTP ' . $code . ' error' );
			return new WP_Error( 'gemini_api_error', sprintf( __( 'Google Gemini API Error (%d): %s', 'omnicraft-ai-builder' ), $code, $error_msg ) );
		}

		if ( empty( $data['candidates'][0]['content']['parts'] ) || ! is_array( $data['candidates'][0]['content']['parts'] ) ) {
			return new WP_Error( 'gemini_empty_content', __( 'Google Gemini returned an unexpected response structure.', 'omnicraft-ai-builder' ) );
		}

		$combined_text = '';
		foreach ( $data['candidates'][0]['content']['parts'] as $part ) {
			if ( ! empty( $part['thought'] ) ) {
				continue;
			}
			if ( isset( $part['text'] ) && '' !== $part['text'] ) {
				$combined_text .= $part['text'];
			}
		}

		if ( empty( $combined_text ) && isset( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
			$combined_text = $data['candidates'][0]['content']['parts'][0]['text'];
		}

		if ( empty( $combined_text ) ) {
			return new WP_Error( 'gemini_empty_content', __( 'Google Gemini returned an empty response.', 'omnicraft-ai-builder' ) );
		}

		return trim( $combined_text );
	}

	public function test_connection() {
		if ( empty( $this->api_key ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please enter a Google Gemini API Key.', 'omnicraft-ai-builder' ),
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
			'message' => sprintf( __( 'Connected successfully to Google Gemini (%s)!', 'omnicraft-ai-builder' ), $this->model ),
		);
	}
}
