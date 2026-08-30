<?php
/**
 * Settings & White-Label Administration View.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_name = ! empty( $options['brand_name'] ) ? $options['brand_name'] : 'AI Site Builder';
$brand_logo = ! empty( $options['brand_logo_url'] ) ? $options['brand_logo_url'] : '';
?>

<div class="wrap omnicraft-wrap">
	<!-- Top Bar -->
	<header class="omnicraft-header">
		<div class="omnicraft-brand">
			<?php if ( ! empty( $brand_logo ) ) : ?>
				<img src="<?php echo esc_url( $brand_logo ); ?>" alt="<?php echo esc_attr( $brand_name ); ?>" class="omnicraft-logo">
			<?php else : ?>
				<div class="omnicraft-brand-icon">
					<i class="fa-solid fa-sliders"></i>
				</div>
			<?php endif; ?>
			<div class="omnicraft-brand-text">
				<h1><?php esc_html_e( 'Plugin Settings & White-Label', 'omnicraft-ai-builder' ); ?></h1>
				<p><?php esc_html_e( 'Configure LLM API keys, target page builders, white-label branding, and monthly user quotas.', 'omnicraft-ai-builder' ); ?></p>
			</div>
		</div>

		<div class="omnicraft-header-meta">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=omnicraft-ai-builder' ) ); ?>" class="oc-btn oc-btn-outline">
				<i class="fa-solid fa-wand-magic-sparkles"></i> <?php esc_html_e( 'Open AI Builder', 'omnicraft-ai-builder' ); ?>
			</a>
		</div>
	</header>

	<!-- Settings Form Container -->
	<form method="post" action="options.php" class="omnicraft-settings-form">
		<?php
		settings_fields( 'omnicraft_ai_settings_group' );
		?>

		<!-- Settings Tabs Navigation -->
		<div class="oc-settings-tabs">
			<button type="button" class="oc-settings-tab-btn active" data-tab="tab-providers">
				<i class="fa-solid fa-brain"></i> <?php esc_html_e( 'AI Models & API Keys', 'omnicraft-ai-builder' ); ?>
			</button>
			<button type="button" class="oc-settings-tab-btn" data-tab="tab-builder">
				<i class="fa-solid fa-cubes"></i> <?php esc_html_e( 'Page Builder Preferences', 'omnicraft-ai-builder' ); ?>
			</button>
			<button type="button" class="oc-settings-tab-btn" data-tab="tab-whitelabel">
				<i class="fa-solid fa-palette"></i> <?php esc_html_e( '100% White-Label Branding', 'omnicraft-ai-builder' ); ?>
			</button>
			<button type="button" class="oc-settings-tab-btn" data-tab="tab-limits">
				<i class="fa-solid fa-gauge-high"></i> <?php esc_html_e( 'Credits & Monthly Quotas', 'omnicraft-ai-builder' ); ?>
			</button>
		</div>

		<!-- TAB 1: AI Models & API Keys -->
		<div id="tab-providers" class="oc-settings-tab-content active">
			<div class="oc-settings-card">
				<h3><?php esc_html_e( 'Active LLM Provider Engine', 'omnicraft-ai-builder' ); ?></h3>
				<p class="oc-card-subtitle"><?php esc_html_e( 'Select the primary AI provider used to analyze prompts, scrape reference URLs, and process design screenshots.', 'omnicraft-ai-builder' ); ?></p>

				<div class="oc-form-group">
					<label for="default_provider"><?php esc_html_e( 'Default AI Provider', 'omnicraft-ai-builder' ); ?></label>
					<select name="omnicraft_ai_settings[default_provider]" id="default_provider" class="oc-select">
						<option value="openai" <?php selected( $options['default_provider'], 'openai' ); ?>>OpenAI (GPT-4o / GPT-4o-mini)</option>
						<option value="anthropic" <?php selected( $options['default_provider'], 'anthropic' ); ?>>Anthropic Claude (Claude 3.5 Sonnet / Haiku)</option>
						<option value="gemini" <?php selected( $options['default_provider'], 'gemini' ); ?>>Google Gemini (1.5 Flash / 2.0 Flash / Pro)</option>
						<option value="openrouter" <?php selected( $options['default_provider'], 'openrouter' ); ?>>OpenRouter (Multi-model / Open Source)</option>
						<option value="custom" <?php selected( $options['default_provider'], 'custom' ); ?>>Custom / Local OpenAI-Compatible (Ollama, DeepSeek, vLLM)</option>
					</select>
				</div>
			</div>

			<!-- OpenAI Configuration -->
			<div class="oc-settings-card">
				<div class="oc-provider-card-header">
					<div class="oc-provider-title">
						<i class="fa-solid fa-microchip"></i>
						<h4>OpenAI Configuration</h4>
					</div>
					<button type="button" class="oc-btn oc-btn-sm oc-btn-outline oc-btn-test-provider" data-provider="openai">
						<i class="fa-solid fa-bolt"></i> <?php esc_html_e( 'Test OpenAI Connection', 'omnicraft-ai-builder' ); ?>
					</button>
				</div>
				<div class="oc-form-row">
					<div class="oc-form-group oc-col-6">
						<label for="openai_api_key"><?php esc_html_e( 'OpenAI API Key', 'omnicraft-ai-builder' ); ?></label>
						<input type="password" name="omnicraft_ai_settings[openai_api_key]" id="openai_api_key" class="oc-input" value="<?php echo esc_attr( $options['openai_api_key'] ); ?>" placeholder="sk-...">
					</div>
					<div class="oc-form-group oc-col-6">
						<label for="openai_model"><?php esc_html_e( 'Model Selection', 'omnicraft-ai-builder' ); ?></label>
						<select name="omnicraft_ai_settings[openai_model]" id="openai_model" class="oc-select">
							<option value="gpt-4o" <?php selected( $options['openai_model'], 'gpt-4o' ); ?>>gpt-4o (High Intelligence & Vision)</option>
							<option value="gpt-4o-mini" <?php selected( $options['openai_model'], 'gpt-4o-mini' ); ?>>gpt-4o-mini (Fast & Cost Efficient)</option>
							<option value="gpt-4-turbo" <?php selected( $options['openai_model'], 'gpt-4-turbo' ); ?>>gpt-4-turbo</option>
						</select>
					</div>
				</div>
			</div>

			<!-- Anthropic Claude Configuration -->
			<div class="oc-settings-card">
				<div class="oc-provider-card-header">
					<div class="oc-provider-title">
						<i class="fa-solid fa-sparkles"></i>
						<h4>Anthropic Claude Configuration</h4>
					</div>
					<button type="button" class="oc-btn oc-btn-sm oc-btn-outline oc-btn-test-provider" data-provider="anthropic">
						<i class="fa-solid fa-bolt"></i> <?php esc_html_e( 'Test Claude Connection', 'omnicraft-ai-builder' ); ?>
					</button>
				</div>
				<div class="oc-form-row">
					<div class="oc-form-group oc-col-6">
						<label for="anthropic_api_key"><?php esc_html_e( 'Anthropic API Key', 'omnicraft-ai-builder' ); ?></label>
						<input type="password" name="omnicraft_ai_settings[anthropic_api_key]" id="anthropic_api_key" class="oc-input" value="<?php echo esc_attr( $options['anthropic_api_key'] ); ?>" placeholder="sk-ant-...">
					</div>
					<div class="oc-form-group oc-col-6">
						<label for="anthropic_model"><?php esc_html_e( 'Model Selection', 'omnicraft-ai-builder' ); ?></label>
						<select name="omnicraft_ai_settings[anthropic_model]" id="anthropic_model" class="oc-select">
							<option value="claude-3-5-sonnet-20241022" <?php selected( $options['anthropic_model'], 'claude-3-5-sonnet-20241022' ); ?>>Claude 3.5 Sonnet (Recommended)</option>
							<option value="claude-3-5-haiku-20241022" <?php selected( $options['anthropic_model'], 'claude-3-5-haiku-20241022' ); ?>>Claude 3.5 Haiku (Ultra Fast)</option>
						</select>
					</div>
				</div>
			</div>

			<!-- Google Gemini Configuration -->
			<div class="oc-settings-card">
				<div class="oc-provider-card-header">
					<div class="oc-provider-title">
						<i class="fa-brands fa-google"></i>
						<h4>Google Gemini Configuration</h4>
					</div>
					<button type="button" class="oc-btn oc-btn-sm oc-btn-outline oc-btn-test-provider" data-provider="gemini">
						<i class="fa-solid fa-bolt"></i> <?php esc_html_e( 'Test Gemini Connection', 'omnicraft-ai-builder' ); ?>
					</button>
				</div>
				<div class="oc-form-row">
					<div class="oc-form-group oc-col-6">
						<label for="gemini_api_key"><?php esc_html_e( 'Google Gemini API Key', 'omnicraft-ai-builder' ); ?></label>
						<input type="password" name="omnicraft_ai_settings[gemini_api_key]" id="gemini_api_key" class="oc-input" value="<?php echo esc_attr( $options['gemini_api_key'] ); ?>" placeholder="AIzaSy...">
					</div>
					<div class="oc-form-group oc-col-6">
						<label for="gemini_model"><?php esc_html_e( 'Model Selection', 'omnicraft-ai-builder' ); ?></label>
						<select name="omnicraft_ai_settings[gemini_model]" id="gemini_model" class="oc-select">
							<option value="gemini-3.5-flash-lite" <?php selected( $options['gemini_model'], 'gemini-3.5-flash-lite' ); ?>>Gemini 3.5 Flash Lite (Fastest & Stable)</option>
							<option value="gemini-3.1-flash-lite" <?php selected( $options['gemini_model'], 'gemini-3.1-flash-lite' ); ?>>Gemini 3.1 Flash Lite</option>
							<option value="gemini-3.5-flash" <?php selected( $options['gemini_model'], 'gemini-3.5-flash' ); ?>>Gemini 3.5 Flash</option>
							<option value="gemini-3.7-flash" <?php selected( $options['gemini_model'], 'gemini-3.7-flash' ); ?>>Gemini 3.7 Flash</option>
						</select>
					</div>
				</div>
			</div>

			<!-- OpenRouter Configuration -->
			<div class="oc-settings-card">
				<div class="oc-provider-card-header">
					<div class="oc-provider-title">
						<i class="fa-solid fa-network-wired"></i>
						<h4>OpenRouter Configuration</h4>
					</div>
					<button type="button" class="oc-btn oc-btn-sm oc-btn-outline oc-btn-test-provider" data-provider="openrouter">
						<i class="fa-solid fa-bolt"></i> <?php esc_html_e( 'Test OpenRouter Connection', 'omnicraft-ai-builder' ); ?>
					</button>
				</div>
				<div class="oc-form-row">
					<div class="oc-form-group oc-col-6">
						<label for="openrouter_api_key"><?php esc_html_e( 'OpenRouter API Key', 'omnicraft-ai-builder' ); ?></label>
						<input type="password" name="omnicraft_ai_settings[openrouter_api_key]" id="openrouter_api_key" class="oc-input" value="<?php echo esc_attr( $options['openrouter_api_key'] ); ?>" placeholder="sk-or-v1-...">
					</div>
					<div class="oc-form-group oc-col-6">
						<label for="openrouter_model"><?php esc_html_e( 'Model String', 'omnicraft-ai-builder' ); ?></label>
						<input type="text" name="omnicraft_ai_settings[openrouter_model]" id="openrouter_model" class="oc-input" value="<?php echo esc_attr( $options['openrouter_model'] ); ?>" placeholder="anthropic/claude-3.5-sonnet">
					</div>
				</div>
			</div>

			<!-- Custom / Local Endpoint Configuration -->
			<div class="oc-settings-card">
				<div class="oc-provider-card-header">
					<div class="oc-provider-title">
						<i class="fa-solid fa-server"></i>
						<h4>Custom / Local OpenAI-Compatible Endpoint</h4>
					</div>
					<button type="button" class="oc-btn oc-btn-sm oc-btn-outline oc-btn-test-provider" data-provider="custom">
						<i class="fa-solid fa-bolt"></i> <?php esc_html_e( 'Test Custom Endpoint', 'omnicraft-ai-builder' ); ?>
					</button>
				</div>
				<div class="oc-form-group">
					<label for="custom_endpoint"><?php esc_html_e( 'Endpoint Base URL', 'omnicraft-ai-builder' ); ?></label>
					<input type="url" name="omnicraft_ai_settings[custom_endpoint]" id="custom_endpoint" class="oc-input" value="<?php echo esc_attr( $options['custom_endpoint'] ); ?>" placeholder="http://localhost:11434/v1">
				</div>
				<div class="oc-form-row">
					<div class="oc-form-group oc-col-6">
						<label for="custom_api_key"><?php esc_html_e( 'API Key (Optional for local)', 'omnicraft-ai-builder' ); ?></label>
						<input type="password" name="omnicraft_ai_settings[custom_api_key]" id="custom_api_key" class="oc-input" value="<?php echo esc_attr( $options['custom_api_key'] ); ?>" placeholder="Optional">
					</div>
					<div class="oc-form-group oc-col-6">
						<label for="custom_model"><?php esc_html_e( 'Custom Model Name', 'omnicraft-ai-builder' ); ?></label>
						<input type="text" name="omnicraft_ai_settings[custom_model]" id="custom_model" class="oc-input" value="<?php echo esc_attr( $options['custom_model'] ); ?>" placeholder="llama3:latest or deepseek-chat">
					</div>
				</div>
			</div>
		</div>

		<!-- TAB 2: Page Builder Preferences -->
		<div id="tab-builder" class="oc-settings-tab-content">
			<div class="oc-settings-card">
				<h3><?php esc_html_e( 'Default Builder Target', 'omnicraft-ai-builder' ); ?></h3>
				
				<div class="oc-form-group">
					<label for="default_builder"><?php esc_html_e( 'Preferred Builder', 'omnicraft-ai-builder' ); ?></label>
					<select name="omnicraft_ai_settings[default_builder]" id="default_builder" class="oc-select">
						<option value="elementor" <?php selected( $options['default_builder'], 'elementor' ); ?>>Elementor (Default)</option>
						<option value="gutenberg" <?php selected( $options['default_builder'], 'gutenberg' ); ?>>Gutenberg Block Editor</option>
					</select>
				</div>

				<div class="oc-form-group">
					<label for="elementor_template"><?php esc_html_e( 'Elementor Page Layout Template', 'omnicraft-ai-builder' ); ?></label>
					<select name="omnicraft_ai_settings[elementor_template]" id="elementor_template" class="oc-select">
						<option value="elementor_canvas" <?php selected( $options['elementor_template'] ?? 'elementor_canvas', 'elementor_canvas' ); ?>>Elementor Canvas (Clean Blank Landing Page — Recommended)</option>
						<option value="elementor_header_footer" <?php selected( $options['elementor_template'] ?? '', 'elementor_header_footer' ); ?>>Elementor Full Width (With Theme Header & Footer)</option>
						<option value="default" <?php selected( $options['elementor_template'] ?? '', 'default' ); ?>>Default Theme Template</option>
					</select>
				</div>

				<div class="oc-form-group">
					<label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer;">
						<input type="checkbox" name="omnicraft_ai_settings[clean_canvas_mode]" value="1" <?php checked( ! empty( $options['clean_canvas_mode'] ) || ! isset( $options['clean_canvas_mode'] ) ); ?> style="margin-top:3px;">
						<span>
							<strong><?php esc_html_e( 'Auto-Hide Theme Header, Page Title & Theme Footer (Clean Canvas Mode)', 'omnicraft-ai-builder' ); ?></strong><br>
							<small style="color:#64748b;"><?php esc_html_e( 'Ensures generated pages across Gutenberg & Elementor never display theme duplicate headers (e.g. "Aielememtor") or theme H1 page titles on any WordPress theme.', 'omnicraft-ai-builder' ); ?></small>
						</span>
					</label>
				</div>

				<div class="oc-form-group">
					<label for="auto_publish"><?php esc_html_e( 'Default Post Status on Creation', 'omnicraft-ai-builder' ); ?></label>
					<select name="omnicraft_ai_settings[auto_publish]" id="auto_publish" class="oc-select">
						<option value="draft" <?php selected( $options['auto_publish'], 'draft' ); ?>>Draft (Recommended for review)</option>
						<option value="publish" <?php selected( $options['auto_publish'], 'publish' ); ?>>Publish Instantly</option>
					</select>
				</div>
			</div>
		</div>

		<!-- TAB 3: White-Label Branding -->
		<div id="tab-whitelabel" class="oc-settings-tab-content">
			<div class="oc-settings-card">
				<h3><?php esc_html_e( '100% White-Label Branding Settings', 'omnicraft-ai-builder' ); ?></h3>
				<p class="oc-card-subtitle"><?php esc_html_e( 'Completely rebrand this plugin for your SaaS hosting customers. No vendor mentions or external links will be shown.', 'omnicraft-ai-builder' ); ?></p>

				<div class="oc-form-row">
					<div class="oc-form-group oc-col-6">
						<label for="brand_name"><?php esc_html_e( 'Custom Brand / Plugin Title', 'omnicraft-ai-builder' ); ?></label>
						<input type="text" name="omnicraft_ai_settings[brand_name]" id="brand_name" class="oc-input" value="<?php echo esc_attr( $options['brand_name'] ); ?>">
					</div>
					<div class="oc-form-group oc-col-6">
						<label for="menu_title"><?php esc_html_e( 'WP Admin Sidebar Menu Label', 'omnicraft-ai-builder' ); ?></label>
						<input type="text" name="omnicraft_ai_settings[menu_title]" id="menu_title" class="oc-input" value="<?php echo esc_attr( $options['menu_title'] ); ?>">
					</div>
				</div>

				<div class="oc-form-row">
					<div class="oc-form-group oc-col-6">
						<label for="brand_logo_url"><?php esc_html_e( 'Custom Brand Logo URL', 'omnicraft-ai-builder' ); ?></label>
						<div class="oc-input-with-button">
							<input type="url" name="omnicraft_ai_settings[brand_logo_url]" id="brand_logo_url" class="oc-input" value="<?php echo esc_attr( $options['brand_logo_url'] ); ?>" placeholder="https://yoursite.com/logo.png">
							<button type="button" id="oc-upload-logo-btn" class="oc-btn oc-btn-secondary"><?php esc_html_e( 'Upload', 'omnicraft-ai-builder' ); ?></button>
						</div>
					</div>
					<div class="oc-form-group oc-col-6">
						<label for="brand_accent_color"><?php esc_html_e( 'Brand Primary Accent Color', 'omnicraft-ai-builder' ); ?></label>
						<input type="color" name="omnicraft_ai_settings[brand_accent_color]" id="brand_accent_color" class="oc-color-picker" value="<?php echo esc_attr( $options['brand_accent_color'] ); ?>">
					</div>
				</div>

				<div class="oc-form-row">
					<div class="oc-form-group oc-col-6">
						<label for="menu_icon"><?php esc_html_e( 'Admin Menu Dashicon', 'omnicraft-ai-builder' ); ?></label>
						<input type="text" name="omnicraft_ai_settings[menu_icon]" id="menu_icon" class="oc-input" value="<?php echo esc_attr( $options['menu_icon'] ); ?>" placeholder="dashicons-superhero">
					</div>
					<div class="oc-form-group oc-col-6">
						<label for="support_url"><?php esc_html_e( 'Custom Help / Support URL', 'omnicraft-ai-builder' ); ?></label>
						<input type="url" name="omnicraft_ai_settings[support_url]" id="support_url" class="oc-input" value="<?php echo esc_attr( $options['support_url'] ); ?>" placeholder="https://yourhosting.com/support">
					</div>
				</div>

				<div class="oc-form-group">
					<label class="oc-checkbox-label">
						<input type="checkbox" name="omnicraft_ai_settings[hide_vendor_links]" value="1" <?php checked( $options['hide_vendor_links'], 1 ); ?>>
						<span><?php esc_html_e( 'Hide all vendor links, author attributions, and external notices', 'omnicraft-ai-builder' ); ?></span>
					</label>
				</div>
			</div>
		</div>

		<!-- TAB 4: Usage Limits & Credits -->
		<div id="tab-limits" class="oc-settings-tab-content">
			<div class="oc-settings-card">
				<h3><?php esc_html_e( 'Monthly User Credits & Rate Limits', 'omnicraft-ai-builder' ); ?></h3>
				<p class="oc-card-subtitle"><?php esc_html_e( 'Limit how many times an end-user or client can generate websites per month to control API consumption.', 'omnicraft-ai-builder' ); ?></p>

				<div class="oc-form-group">
					<label class="oc-checkbox-label">
						<input type="checkbox" name="omnicraft_ai_settings[enable_limits]" id="enable_limits" value="1" <?php checked( $options['enable_limits'], 1 ); ?>>
						<span><strong><?php esc_html_e( 'Enable Monthly Generation Limits per User', 'omnicraft-ai-builder' ); ?></strong></span>
					</label>
				</div>

				<div class="oc-form-group">
					<label for="monthly_limit"><?php esc_html_e( 'Maximum Generations Allowed Per Month', 'omnicraft-ai-builder' ); ?></label>
					<input type="number" name="omnicraft_ai_settings[monthly_limit]" id="monthly_limit" class="oc-input" value="<?php echo esc_attr( $options['monthly_limit'] ); ?>" min="1" max="1000" style="max-width:200px;">
					<p class="oc-help-text"><?php esc_html_e( 'Credits automatically reset to full capacity on the 1st of every month.', 'omnicraft-ai-builder' ); ?></p>
				</div>

				<div class="oc-form-group">
					<label><?php esc_html_e( 'User Roles with Generation Access', 'omnicraft-ai-builder' ); ?></label>
					<?php
					$all_roles = wp_roles()->get_names();
					$selected_roles = (array) $options['allowed_roles'];
					foreach ( $all_roles as $role_key => $role_name ) :
						?>
						<label class="oc-checkbox-label" style="margin-right:20px; display:inline-block;">
							<input type="checkbox" name="omnicraft_ai_settings[allowed_roles][]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $selected_roles, true ) ); ?>>
							<span><?php echo esc_html( $role_name ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>

				<div class="oc-form-group" style="margin-top:30px; border-top:1px solid #e2e8f0; padding-top:20px;">
					<label class="oc-checkbox-label">
						<input type="checkbox" name="omnicraft_ai_settings[preserve_data_on_uninstall]" value="1" <?php checked( $options['preserve_data_on_uninstall'], 1 ); ?>>
						<span><?php esc_html_e( 'Preserve generation history and credit tables when plugin is uninstalled', 'omnicraft-ai-builder' ); ?></span>
					</label>
				</div>
			</div>
		</div>

		<!-- Submit Button -->
		<div class="oc-settings-footer">
			<button type="submit" class="oc-btn oc-btn-primary oc-btn-lg">
				<i class="fa-solid fa-floppy-disk"></i> <?php esc_html_e( 'Save All Settings', 'omnicraft-ai-builder' ); ?>
			</button>
		</div>
	</form>
</div>
