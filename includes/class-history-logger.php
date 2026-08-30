<?php
/**
 * Handles recording and fetching generation history.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_History_Logger {

	/**
	 * Log a generated page into the database.
	 *
	 * @param array $data
	 * @return int|false
	 */
	public static function log_generation( $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'omnicraft_ai_history';

		$user_id = ! empty( $data['user_id'] ) ? (int) $data['user_id'] : get_current_user_id();

		$result = $wpdb->insert(
			$table,
			array(
				'user_id'        => $user_id,
				'page_id'        => ! empty( $data['page_id'] ) ? (int) $data['page_id'] : 0,
				'page_title'     => ! empty( $data['page_title'] ) ? sanitize_text_field( $data['page_title'] ) : 'Untitled Page',
				'builder_type'   => ! empty( $data['builder_type'] ) ? sanitize_text_field( $data['builder_type'] ) : 'elementor',
				'input_type'     => ! empty( $data['input_type'] ) ? sanitize_text_field( $data['input_type'] ) : 'text',
				'provider'       => ! empty( $data['provider'] ) ? sanitize_text_field( $data['provider'] ) : 'openai',
				'model'          => ! empty( $data['model'] ) ? sanitize_text_field( $data['model'] ) : '',
				'prompt_summary' => ! empty( $data['prompt_summary'] ) ? sanitize_textarea_field( $data['prompt_summary'] ) : '',
				'target_url'     => ! empty( $data['target_url'] ) ? esc_url_raw( $data['target_url'] ) : '',
				'screenshot_url' => ! empty( $data['screenshot_url'] ) ? esc_url_raw( $data['screenshot_url'] ) : '',
				'created_at'     => current_time( 'mysql' ),
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Get generation history logs with pagination.
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param int $user_id Filter by specific user if > 0
	 * @return array
	 */
	public static function get_history( $limit = 20, $offset = 0, $user_id = 0 ) {
		global $wpdb;
		$table = $wpdb->prefix . 'omnicraft_ai_history';

		$where = '1=1';
		$params = array();

		if ( $user_id > 0 ) {
			$where .= ' AND user_id = %d';
			$params[] = $user_id;
		}

		$params[] = (int) $limit;
		$params[] = (int) $offset;

		$sql = "SELECT * FROM $table WHERE $where ORDER BY created_at DESC LIMIT %d OFFSET %d";
		$results = $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );

		// Enrich each log item with page edit/preview URLs
		foreach ( $results as &$item ) {
			$page_id = (int) $item['page_id'];
			if ( $page_id && get_post_status( $page_id ) ) {
				$item['page_url'] = get_permalink( $page_id );
				$item['edit_url'] = get_edit_post_link( $page_id, 'raw' );
				$item['elementor_edit_url'] = admin_url( 'post.php?post=' . $page_id . '&action=elementor' );
				$item['page_status'] = get_post_status( $page_id );
			} else {
				$item['page_url'] = '';
				$item['edit_url'] = '';
				$item['elementor_edit_url'] = '';
				$item['page_status'] = 'deleted';
			}

			$user = get_userdata( $item['user_id'] );
			$item['user_display_name'] = $user ? $user->display_name : 'Unknown User';
		}

		return $results;
	}

	/**
	 * Get total count of history records.
	 *
	 * @param int $user_id
	 * @return int
	 */
	public static function get_history_count( $user_id = 0 ) {
		global $wpdb;
		$table = $wpdb->prefix . 'omnicraft_ai_history';

		if ( $user_id > 0 ) {
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE user_id = %d", $user_id ) );
		}

		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
	}

	/**
	 * Delete a single history record.
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function delete_history_item( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'omnicraft_ai_history';
		return (bool) $wpdb->delete( $table, array( 'id' => (int) $id ), array( '%d' ) );
	}

	/**
	 * Clear all history records.
	 *
	 * @return bool
	 */
	public static function clear_all() {
		global $wpdb;
		$table = $wpdb->prefix . 'omnicraft_ai_history';
		return (bool) $wpdb->query( "TRUNCATE TABLE $table" );
	}
}
