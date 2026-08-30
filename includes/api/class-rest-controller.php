<?php
/**
 * REST API Controller for OmniCraft AI Builder.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/generators/class-generator-engine.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-credits-manager.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-history-logger.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-url-scraper.php';

class OmniCraft_AI_REST_Controller {

	private $namespace = 'omnicraft-ai/v1';

	/**
	 * Register REST routes.
	 */
	public function register_routes() {
		// Generate Website Endpoint
		register_rest_route(
			$this->namespace,
			'/generate',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_generate' ),
				'permission_callback' => array( $this, 'check_user_permission' ),
			)
		);

		// Scrape URL Endpoint
		register_rest_route(
			$this->namespace,
			'/scrape-url',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_scrape_url' ),
				'permission_callback' => array( $this, 'check_user_permission' ),
			)
		);

		// Test Provider Connection Endpoint
		register_rest_route(
			$this->namespace,
			'/test-connection',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_test_connection' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			)
		);

		// Get Credits & Limits Endpoint
		register_rest_route(
			$this->namespace,
			'/credits',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'handle_get_credits' ),
				'permission_callback' => array( $this, 'check_user_permission' ),
			)
		);

		// Get Generation History Endpoint
		register_rest_route(
			$this->namespace,
			'/history',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'handle_get_history' ),
				'permission_callback' => array( $this, 'check_user_permission' ),
			)
		);

		// Delete Single History Endpoint
		register_rest_route(
			$this->namespace,
			'/history/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'handle_delete_history' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			)
		);

		// Clear All History Endpoint
		register_rest_route(
			$this->namespace,
			'/history/clear',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'handle_clear_history' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			)
		);
	}

	/**
	 * Permission check for users allowed to generate pages.
	 */
	public function check_user_permission( $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden', __( 'Authentication required.', 'omnicraft-ai-builder' ), array( 'status' => 401 ) );
		}

		$settings = get_option( 'omnicraft_ai_settings', array() );
		$allowed_roles = ! empty( $settings['allowed_roles'] ) ? (array) $settings['allowed_roles'] : array( 'administrator', 'editor' );

		$user = wp_get_current_user();
		$user_roles = (array) $user->roles;

		foreach ( $allowed_roles as $role ) {
			if ( in_array( $role, $user_roles, true ) || current_user_can( 'manage_options' ) ) {
				return true;
			}
		}

		return new WP_Error( 'rest_forbidden_role', __( 'You do not have permission to generate AI websites.', 'omnicraft-ai-builder' ), array( 'status' => 403 ) );
	}

	/**
	 * Permission check for admin-only endpoints.
	 */
	public function check_admin_permission( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Administrator capability required.', 'omnicraft-ai-builder' ), array( 'status' => 403 ) );
		}
		return true;
	}

	/**
	 * Handle generation request.
	 */
	public function handle_generate( $request ) {
		$params = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_params();
		}

		$result = OmniCraft_AI_Generator_Engine::generate( $params );

		if ( is_wp_error( $result ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $result->get_error_message(),
					'code'    => $result->get_error_code(),
				),
				400
			);
		}

		return new WP_REST_Response( $result, 200 );
	}

	/**
	 * Handle reference URL scraping.
	 */
	public function handle_scrape_url( $request ) {
		$url = $request->get_param( 'url' );
		if ( empty( $url ) ) {
			return new WP_REST_Response( array( 'success' => false, 'message' => __( 'URL is required.', 'omnicraft-ai-builder' ) ), 400 );
		}

		$scraped = OmniCraft_AI_URL_Scraper::scrape( $url );
		if ( is_wp_error( $scraped ) ) {
			return new WP_REST_Response( array( 'success' => false, 'message' => $scraped->get_error_message() ), 400 );
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $scraped ), 200 );
	}

	/**
	 * Handle provider API key / connection test.
	 */
	public function handle_test_connection( $request ) {
		$provider_slug = $request->get_param( 'provider' );
		$api_key       = $request->get_param( 'api_key' );
		$model         = $request->get_param( 'model' );
		$endpoint      = $request->get_param( 'endpoint' );

		$override = array();
		if ( ! empty( $api_key ) ) {
			$override[ $provider_slug . '_api_key' ] = $api_key;
		}
		if ( ! empty( $model ) ) {
			$override[ $provider_slug . '_model' ] = $model;
		}
		if ( ! empty( $endpoint ) ) {
			$override['custom_endpoint'] = $endpoint;
		}

		$provider_instance = OmniCraft_AI_Provider_Factory::create( $provider_slug, $override );
		if ( is_wp_error( $provider_instance ) ) {
			return new WP_REST_Response( array( 'success' => false, 'message' => $provider_instance->get_error_message() ), 400 );
		}

		$test_result = $provider_instance->test_connection();
		return new WP_REST_Response( $test_result, $test_result['success'] ? 200 : 400 );
	}

	/**
	 * Handle get credits.
	 */
	public function handle_get_credits( $request ) {
		$credits_data = OmniCraft_AI_Credits_Manager::check_user_limit();
		return new WP_REST_Response( $credits_data, 200 );
	}

	/**
	 * Handle get history.
	 */
	public function handle_get_history( $request ) {
		$limit  = ! empty( $request->get_param( 'limit' ) ) ? (int) $request->get_param( 'limit' ) : 20;
		$offset = ! empty( $request->get_param( 'offset' ) ) ? (int) $request->get_param( 'offset' ) : 0;
		
		$user_filter = 0;
		if ( ! current_user_can( 'manage_options' ) ) {
			$user_filter = get_current_user_id();
		}

		$history = OmniCraft_AI_History_Logger::get_history( $limit, $offset, $user_filter );
		$total   = OmniCraft_AI_History_Logger::get_history_count( $user_filter );

		return new WP_REST_Response(
			array(
				'success' => true,
				'history' => $history,
				'total'   => $total,
			),
			200
		);
	}

	/**
	 * Handle delete single history item.
	 */
	public function handle_delete_history( $request ) {
		$id = (int) $request->get_param( 'id' );
		$deleted = OmniCraft_AI_History_Logger::delete_history_item( $id );
		return new WP_REST_Response( array( 'success' => $deleted ), $deleted ? 200 : 400 );
	}

	/**
	 * Handle clear all history.
	 */
	public function handle_clear_history( $request ) {
		$cleared = OmniCraft_AI_History_Logger::clear_all();
		return new WP_REST_Response( array( 'success' => $cleared ), $cleared ? 200 : 400 );
	}
}
