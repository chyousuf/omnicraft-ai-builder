<?php
/**
 * Plugin Name:       OmniCraft AI — Multi-Modal Website Builder
 * Plugin URI:        https://example.com/omnicraft-ai-builder
 * Description:       Generate complete, high-converting WordPress pages in seconds using Text Prompts, Screenshots (Vision AI), or Reference URLs. Native Elementor & Gutenberg support with 100% White-Label capability.
 * Version:           1.2.0
 * Author:            Chaudhry Yousuf
 * Author URI:        https://github.com/chyousuf
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       omnicraft-ai-builder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define Plugin Constants
define( 'OMNICRAFT_AI_VERSION', '1.2.0' );
define( 'OMNICRAFT_AI_PLUGIN_FILE', __FILE__ );
define( 'OMNICRAFT_AI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OMNICRAFT_AI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'OMNICRAFT_AI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_omnicraft_ai_builder() {
	require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-activator.php';
	OmniCraft_AI_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_omnicraft_ai_builder() {
	require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-deactivator.php';
	OmniCraft_AI_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_omnicraft_ai_builder' );
register_deactivation_hook( __FILE__, 'deactivate_omnicraft_ai_builder' );

/**
 * Include the core plugin class.
 */
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-plugin.php';

/**
 * Begins execution of the plugin.
 */
function run_omnicraft_ai_builder() {
	$plugin = OmniCraft_AI_Plugin::get_instance();
	$plugin->run();
}
run_omnicraft_ai_builder();
