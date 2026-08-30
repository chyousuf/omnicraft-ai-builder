<?php
/**
 * The core plugin singleton class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-i18n.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-credits-manager.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-history-logger.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-url-scraper.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/admin/class-admin.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/api/class-rest-controller.php';

class OmniCraft_AI_Plugin {

	private static $instance = null;
	private $i18n;
	private $admin;
	private $rest_controller;

	/**
	 * Get Singleton Instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->i18n            = new OmniCraft_AI_i18n();
		$this->admin           = new OmniCraft_AI_Admin();
		$this->rest_controller = new OmniCraft_AI_REST_Controller();
	}

	/**
	 * Run the plugin logic.
	 */
	public function run() {
		// Internationalization
		add_action( 'plugins_loaded', array( $this->i18n, 'load_plugin_textdomain' ) );

		// Admin functionality
		if ( is_admin() ) {
			$this->admin->init();
		}

		// REST API Routes
		add_action( 'rest_api_init', array( $this->rest_controller, 'register_routes' ) );

		// Frontend Clean Canvas & Animation Assets
		add_action( 'wp_head', array( $this, 'render_frontend_clean_canvas_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_animation_assets' ) );
	}

	/**
	 * Enqueue dedicated modern animations and interactive motion scripts on frontend.
	 */
	public function enqueue_frontend_animation_assets() {
		if ( ! is_singular( 'page' ) ) {
			return;
		}

		$post_id    = get_the_ID();
		$is_ai_page = get_post_meta( $post_id, '_omnicraft_ai_generated', true );
		$template   = get_post_meta( $post_id, '_wp_page_template', true );

		if ( $is_ai_page || 'omnicraft_canvas' === $template || 'elementor_canvas' === $template ) {
			// Enqueue Elementor native animations stylesheet if Elementor is active
			if ( wp_style_is( 'e-animations', 'registered' ) ) {
				wp_enqueue_style( 'e-animations' );
			}

			// Enqueue OmniCraft AI dynamic GPU animation suite
			wp_enqueue_style(
				'omnicraft-ai-animations',
				OMNICRAFT_AI_PLUGIN_URL . 'assets/css/frontend-animations.css',
				array(),
				OMNICRAFT_AI_VERSION
			);

			// Enqueue Scroll-Reveal and Interactive Motion controller
			wp_enqueue_script(
				'omnicraft-ai-animations-js',
				OMNICRAFT_AI_PLUGIN_URL . 'assets/js/frontend-animations.js',
				array(),
				OMNICRAFT_AI_VERSION,
				true
			);
		}
	}

	/**
	 * Output clean full-width styling on AI generated pages to hide theme header/title/footer.
	 */
	public function render_frontend_clean_canvas_styles() {
		if ( ! is_singular( 'page' ) ) {
			return;
		}

		$post_id = get_the_ID();
		$is_ai_page = get_post_meta( $post_id, '_omnicraft_ai_generated', true );
		$template   = get_post_meta( $post_id, '_wp_page_template', true );

		$settings = get_option( 'omnicraft_ai_settings', array() );
		$clean_mode = isset( $settings['clean_canvas_mode'] ) ? (bool) $settings['clean_canvas_mode'] : true;

		if ( $is_ai_page || 'omnicraft_canvas' === $template || 'elementor_canvas' === $template || $clean_mode ) {
			if ( $is_ai_page || 'omnicraft_canvas' === $template ) {
				echo '<style id="omnicraft-clean-canvas-css">
					/* OmniCraft AI Clean Canvas: Hide theme default header, entry title and footer */
					#site-header, .site-header, header#masthead, header.site-header,
					#site-footer, .site-footer, footer#colophon, footer.site-footer,
					.entry-header, .entry-title, .page-header, .page-title,
					.post-header, .post-title, .site-branding,
					.wp-site-blocks > header, .wp-site-blocks > footer,
					.ast-header-break-point, .site-below-footer-wrap {
						display: none !important;
					}
					body.page {
						margin: 0 !important;
						padding: 0 !important;
					}
					.site-content, .entry-content, #primary, #main {
						margin: 0 !important;
						padding: 0 !important;
						max-width: 100% !important;
						width: 100% !important;
					}
				</style>' . "\n";
			}
		}
	}
}
