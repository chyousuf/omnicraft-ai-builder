<?php
/**
 * Interface for all LLM Providers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface OmniCraft_AI_Provider_Interface {

	/**
	 * Send a prompt (text + optional image) to the LLM and return the response.
	 *
	 * @param string $system_prompt
	 * @param string $user_prompt
	 * @param array  $options Contains optional 'image_base64', 'image_mime', 'model', 'temperature', 'max_tokens'
	 * @return string|WP_Error Raw text/JSON response or error
	 */
	public function generate( $system_prompt, $user_prompt, $options = array() );

	/**
	 * Test whether the provided API credentials and endpoint are working.
	 *
	 * @return array ['success' => bool, 'message' => string]
	 */
	public function test_connection();

	/**
	 * Check whether this provider/model supports multimodal image/vision inputs.
	 *
	 * @return bool
	 */
	public function supports_vision();

	/**
	 * Get the provider display name.
	 *
	 * @return string
	 */
	public function get_name();
}
