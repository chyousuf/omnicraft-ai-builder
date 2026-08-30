<?php
/**
 * Handles user credits, monthly quotas, and generation limits.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_Credits_Manager {

	/**
	 * Check if the user is allowed to generate a site.
	 *
	 * @param int $user_id
	 * @return array ['allowed' => bool, 'message' => string, 'remaining' => int, 'limit' => int, 'used' => int]
	 */
	public static function check_user_limit( $user_id = 0 ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$settings = get_option( 'omnicraft_ai_settings', array() );
		$enable_limits = ! empty( $settings['enable_limits'] );
		$monthly_limit = isset( $settings['monthly_limit'] ) ? (int) $settings['monthly_limit'] : 20;

		// If limits are disabled, user has unlimited generations
		if ( ! $enable_limits || $monthly_limit <= 0 ) {
			return array(
				'allowed'   => true,
				'unlimited' => true,
				'remaining' => 999999,
				'limit'     => 0,
				'used'      => self::get_user_monthly_usage( $user_id ),
				'message'   => __( 'Unlimited generations available.', 'omnicraft-ai-builder' ),
			);
		}

		// Admins can bypass limits if desired, or adhere to them. By default, check usage.
		$used = self::get_user_monthly_usage( $user_id );
		$remaining = max( 0, $monthly_limit - $used );

		if ( $used >= $monthly_limit ) {
			return array(
				'allowed'   => false,
				'unlimited' => false,
				'remaining' => 0,
				'limit'     => $monthly_limit,
				'used'      => $used,
				'message'   => sprintf(
					__( 'You have reached your monthly generation limit of %d websites. Quota resets on the 1st of next month.', 'omnicraft-ai-builder' ),
					$monthly_limit
				),
			);
		}

		return array(
			'allowed'   => true,
			'unlimited' => false,
			'remaining' => $remaining,
			'limit'     => $monthly_limit,
			'used'      => $used,
			'message'   => sprintf(
				__( 'You have %d of %d generations remaining this month.', 'omnicraft-ai-builder' ),
				$remaining,
				$monthly_limit
			),
		);
	}

	/**
	 * Get the number of generations used by a user in the current month.
	 *
	 * @param int $user_id
	 * @return int
	 */
	public static function get_user_monthly_usage( $user_id = 0 ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		global $wpdb;
		$table = $wpdb->prefix . 'omnicraft_ai_credits';
		$month_year = gmdate( 'Y-m' );

		$used = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT generations_used FROM $table WHERE user_id = %d AND month_year = %s",
				$user_id,
				$month_year
			)
		);

		return $used ? (int) $used : 0;
	}

	/**
	 * Increment credit usage after a successful generation.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public static function increment_usage( $user_id = 0 ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		global $wpdb;
		$table = $wpdb->prefix . 'omnicraft_ai_credits';
		$month_year = gmdate( 'Y-m' );
		$now = current_time( 'mysql' );

		$existing = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, generations_used FROM $table WHERE user_id = %d AND month_year = %s",
				$user_id,
				$month_year
			)
		);

		if ( $existing ) {
			$updated = $wpdb->update(
				$table,
				array(
					'generations_used'    => (int) $existing->generations_used + 1,
					'last_generation_at' => $now,
				),
				array( 'id' => $existing->id ),
				array( '%d', '%s' ),
				array( '%d' )
			);
			return false !== $updated;
		} else {
			$inserted = $wpdb->insert(
				$table,
				array(
					'user_id'            => $user_id,
					'month_year'         => $month_year,
					'generations_used'    => 1,
					'last_generation_at' => $now,
				),
				array( '%d', '%s', '%d', '%s' )
			);
			return false !== $inserted;
		}
	}

	/**
	 * Reset a user's credit usage for the current month.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public static function reset_user_credits( $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'omnicraft_ai_credits';
		$month_year = gmdate( 'Y-m' );

		$result = $wpdb->delete(
			$table,
			array(
				'user_id'    => $user_id,
				'month_year' => $month_year,
			),
			array( '%d', '%s' )
		);

		return false !== $result;
	}
}
