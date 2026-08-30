<?php
/**
 * Admin Area Management, Menus, Asset Enqueues, and View Rendering.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/admin/class-settings.php';

class OmniCraft_AI_Admin {

	private $settings_handler;

	public function __construct() {
		$this->settings_handler = new OmniCraft_AI_Settings();
	}

	/**
	 * Register admin hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_admin_menus' ) );
		add_action( 'admin_init', array( $this->settings_handler, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_head', array( $this, 'inject_dynamic_brand_styles' ) );
	}

	/**
	 * Register top-level and submenu pages dynamically with White-Label settings.
	 */
	public function register_admin_menus() {
		$options = OmniCraft_AI_Settings::get_options();
		$menu_title = ! empty( $options['menu_title'] ) ? $options['menu_title'] : 'AI Site Builder';
		$menu_icon  = ! empty( $options['menu_icon'] ) ? $options['menu_icon'] : 'dashicons-superhero';

		// Main Menu Page (Wizard)
		add_menu_page(
			$menu_title,
			$menu_title,
			'read',
			'omnicraft-ai-builder',
			array( $this, 'render_wizard_page' ),
			$menu_icon,
			30
		);

		// Submenu: Create New (Wizard)
		add_submenu_page(
			'omnicraft-ai-builder',
			__( 'Create New Page', 'omnicraft-ai-builder' ),
			__( '✨ Create New Page', 'omnicraft-ai-builder' ),
			'read',
			'omnicraft-ai-builder',
			array( $this, 'render_wizard_page' )
		);

		// Submenu: Generation History
		add_submenu_page(
			'omnicraft-ai-builder',
			__( 'Generation History', 'omnicraft-ai-builder' ),
			__( '📜 History & Logs', 'omnicraft-ai-builder' ),
			'read',
			'omnicraft-ai-history',
			array( $this, 'render_history_page' )
		);

		// Submenu: Settings (Admin Only)
		add_submenu_page(
			'omnicraft-ai-builder',
			__( 'Settings & White-Label', 'omnicraft-ai-builder' ),
			__( '⚙️ Settings', 'omnicraft-ai-builder' ),
			'manage_options',
			'omnicraft-ai-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Enqueue styles and scripts only on our plugin pages.
	 */
	public function enqueue_assets( $hook ) {
		if ( false === strpos( $hook, 'omnicraft-ai' ) ) {
			return;
		}

		// WordPress Media Uploader support
		wp_enqueue_media();

		// Enqueue FontAwesome for icons in builder preview
		wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1' );

		// Plugin CSS
		wp_enqueue_style(
			'omnicraft-ai-admin-wizard',
			OMNICRAFT_AI_PLUGIN_URL . 'assets/css/admin-wizard.css',
			array(),
			OMNICRAFT_AI_VERSION
		);

		wp_enqueue_style(
			'omnicraft-ai-admin-settings',
			OMNICRAFT_AI_PLUGIN_URL . 'assets/css/admin-settings.css',
			array(),
			OMNICRAFT_AI_VERSION
		);

		// Plugin JS
		wp_enqueue_script(
			'omnicraft-ai-admin-wizard',
			OMNICRAFT_AI_PLUGIN_URL . 'assets/js/admin-wizard.js',
			array( 'jquery' ),
			OMNICRAFT_AI_VERSION,
			true
		);

		wp_enqueue_script(
			'omnicraft-ai-admin-settings',
			OMNICRAFT_AI_PLUGIN_URL . 'assets/js/admin-settings.js',
			array( 'jquery' ),
			OMNICRAFT_AI_VERSION,
			true
		);

		// Check if Elementor plugin is active
		$is_elementor_active = did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );

		$options = OmniCraft_AI_Settings::get_options();
		$credit_info = OmniCraft_AI_Credits_Manager::check_user_limit();

		wp_localize_script(
			'omnicraft-ai-admin-wizard',
			'omniCraftData',
			array(
				'restUrl'          => esc_url_raw( rest_url( 'omnicraft-ai/v1/' ) ),
				'nonce'            => wp_create_nonce( 'wp_rest' ),
				'isElementor'      => $is_elementor_active,
				'credits'          => $credit_info,
				'options'          => $options,
				'currentUserId'    => get_current_user_id(),
				'strings'          => array(
					'generating'       => __( 'Analyzing inputs & generating website...', 'omnicraft-ai-builder' ),
					'scrapingUrl'      => __( 'Fetching reference website content...', 'omnicraft-ai-builder' ),
					'analyzingVision'  => __( 'Scanning visual hierarchy & screenshot design...', 'omnicraft-ai-builder' ),
					'assemblingLayout' => __( 'Compiling layout blocks and styling...', 'omnicraft-ai-builder' ),
					'creatingPage'     => __( 'Publishing WordPress page...', 'omnicraft-ai-builder' ),
					'success'          => __( 'Website generated successfully!', 'omnicraft-ai-builder' ),
					'error'            => __( 'An error occurred during generation.', 'omnicraft-ai-builder' ),
				),
			)
		);
	}

	/**
	 * Inject dynamic white-label brand accent color in admin head.
	 */
	public function inject_dynamic_brand_styles() {
		$options = OmniCraft_AI_Settings::get_options();
		$accent  = ! empty( $options['brand_accent_color'] ) ? $options['brand_accent_color'] : '#6366f1';
		?>
		<style id="omnicraft-dynamic-brand-css">
			:root {
				--oc-primary: <?php echo esc_attr( $accent ); ?>;
				--oc-primary-hover: <?php echo esc_attr( $accent ); ?>cc;
			}
		</style>
		<?php
	}

	/**
	 * Render the Generator Wizard View.
	 */
	public function render_wizard_page() {
		$options = OmniCraft_AI_Settings::get_options();
		$credit_info = OmniCraft_AI_Credits_Manager::check_user_limit();
		$is_elementor_active = did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
		require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/admin/views/wizard-page.php';
	}

	/**
	 * Render the Generation History View.
	 */
	public function render_history_page() {
		$options = OmniCraft_AI_Settings::get_options();
		require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/admin/views/history-page.php';
	}

	/**
	 * Render the Settings View.
	 */
	public function render_settings_page() {
		$options = OmniCraft_AI_Settings::get_options();
		require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/admin/views/settings-page.php';
	}
}
