<?php
/**
 * Creates and saves WordPress pages with Elementor or Gutenberg metadata.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_Page_Creator {

	/**
	 * Create a WordPress Page from compiled builder data.
	 *
	 * @param string $page_title
	 * @param string $builder_type 'elementor' or 'gutenberg'
	 * @param mixed  $compiled_content Array for Elementor, string for Gutenberg
	 * @param array  $options Optional overrides (status, template, etc.)
	 * @return int|WP_Error Page ID on success or WP_Error
	 */
	public static function create_page( $page_title, $builder_type, $compiled_content, $options = array() ) {
		$settings = get_option( 'omnicraft_ai_settings', array() );
		$post_status = ! empty( $options['status'] ) ? $options['status'] : ( ! empty( $settings['auto_publish'] ) ? $settings['auto_publish'] : 'draft' );
		$elementor_tpl = ! empty( $settings['elementor_template'] ) ? $settings['elementor_template'] : 'elementor_header_footer';

		$page_data = array(
			'post_title'   => sanitize_text_field( $page_title ),
			'post_status'  => in_array( $post_status, array( 'publish', 'draft', 'pending' ), true ) ? $post_status : 'draft',
			'post_type'    => 'page',
			'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
			'post_content' => ( 'gutenberg' === $builder_type && is_string( $compiled_content ) ) ? $compiled_content : '',
		);

		$page_id = wp_insert_post( $page_data, true );

		if ( is_wp_error( $page_id ) ) {
			return $page_id;
		}

		// Mark as OmniCraft AI Generated page
		update_post_meta( $page_id, '_omnicraft_ai_generated', '1' );

		if ( 'elementor' === $builder_type ) {
			// Ensure content is formatted as JSON string for Elementor
			$json_data = is_array( $compiled_content ) ? wp_json_encode( $compiled_content ) : $compiled_content;

			// Elementor requires raw JSON stored in _elementor_data (with proper wp_slash)
			update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
			update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
			update_post_meta( $page_id, '_elementor_version', '3.20.0' );
			update_post_meta( $page_id, '_elementor_data', wp_slash( $json_data ) );
			update_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );

			// Set Elementor page settings (hide theme page title)
			update_post_meta( $page_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
		} else {
			update_post_meta( $page_id, '_wp_page_template', 'omnicraft_canvas' );
		}

		return $page_id;
	}
}
