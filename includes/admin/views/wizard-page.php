<?php
/**
 * AI Website Builder Wizard View.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_name   = ! empty( $options['brand_name'] ) ? $options['brand_name'] : 'AI Site Builder';
$brand_logo   = ! empty( $options['brand_logo_url'] ) ? $options['brand_logo_url'] : '';
$default_bldr = ! empty( $options['default_builder'] ) ? $options['default_builder'] : ( $is_elementor_active ? 'elementor' : 'gutenberg' );
?>

<div class="wrap omnicraft-wrap">
	<!-- Top Bar -->
	<header class="omnicraft-header">
		<div class="omnicraft-brand">
			<?php if ( ! empty( $brand_logo ) ) : ?>
				<img src="<?php echo esc_url( $brand_logo ); ?>" alt="<?php echo esc_attr( $brand_name ); ?>" class="omnicraft-logo">
			<?php else : ?>
				<div class="omnicraft-brand-icon">
					<i class="fa-solid fa-wand-magic-sparkles"></i>
				</div>
			<?php endif; ?>
			<div class="omnicraft-brand-text">
				<h1><?php echo esc_html( $brand_name ); ?></h1>
				<p><?php esc_html_e( 'Multi-Modal AI Website Generator (Text, URL & Screenshot to WordPress)', 'omnicraft-ai-builder' ); ?></p>
			</div>
		</div>

		<div class="omnicraft-header-meta">
			<?php if ( ! empty( $options['enable_limits'] ) ) : ?>
				<div class="omnicraft-credit-badge <?php echo ( $credit_info['remaining'] <= 2 ) ? 'credit-low' : ''; ?>">
					<i class="fa-solid fa-coins"></i>
					<span><strong><?php echo esc_html( $credit_info['remaining'] ); ?></strong> / <?php echo esc_html( $credit_info['limit'] ); ?> <?php esc_html_e( 'Credits Left', 'omnicraft-ai-builder' ); ?></span>
				</div>
			<?php else : ?>
				<div class="omnicraft-credit-badge credit-unlimited">
					<i class="fa-solid fa-infinity"></i>
					<span><?php esc_html_e( 'Unlimited Generation', 'omnicraft-ai-builder' ); ?></span>
				</div>
			<?php endif; ?>

			<a href="<?php echo esc_url( admin_url( 'admin.php?page=omnicraft-ai-history' ) ); ?>" class="oc-btn oc-btn-outline">
				<i class="fa-solid fa-clock-rotate-left"></i> <?php esc_html_e( 'History', 'omnicraft-ai-builder' ); ?>
			</a>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=omnicraft-ai-settings' ) ); ?>" class="oc-btn oc-btn-outline">
					<i class="fa-solid fa-gear"></i> <?php esc_html_e( 'Settings', 'omnicraft-ai-builder' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</header>

	<!-- Main Generator Grid -->
	<div class="omnicraft-grid">
		<!-- Left / Main Form Column -->
		<div class="omnicraft-main-card">
			<form id="omnicraft-generator-form" onsubmit="return false;">
				
				<!-- Section 1: Business Identity -->
				<div class="oc-card-section">
					<div class="oc-section-title">
						<span class="oc-step-num">1</span>
						<div>
							<h3><?php esc_html_e( 'Business & Page Concept', 'omnicraft-ai-builder' ); ?></h3>
							<p><?php esc_html_e( 'Tell the AI about your brand, service, or target offering.', 'omnicraft-ai-builder' ); ?></p>
						</div>
					</div>

					<div class="oc-form-group">
						<label for="oc-page-title"><?php esc_html_e( 'Page Name / Business Title', 'omnicraft-ai-builder' ); ?></label>
						<input type="text" id="oc-page-title" class="oc-input" placeholder="<?php esc_attr_e( 'e.g., NovaPay — Next-Gen Payment Gateway', 'omnicraft-ai-builder' ); ?>" required>
					</div>

					<div class="oc-form-group">
						<label for="oc-prompt"><?php esc_html_e( 'Business Description & Key Features', 'omnicraft-ai-builder' ); ?></label>
						<textarea id="oc-prompt" rows="4" class="oc-textarea" placeholder="<?php esc_attr_e( 'Describe what your company does, target audience, core benefits, and key value propositions...', 'omnicraft-ai-builder' ); ?>"></textarea>
						
						<!-- Quick Prompt Templates -->
						<div class="oc-quick-prompts">
							<span><i class="fa-regular fa-lightbulb"></i> <?php esc_html_e( 'Quick Ideas:', 'omnicraft-ai-builder' ); ?></span>
							<button type="button" class="oc-tag-btn" data-title="Apex SaaS Cloud" data-prompt="A modern B2B SaaS platform for cloud analytics, automated deployments, real-time metrics, high uptime, and enterprise developer tools.">SaaS Platform</button>
							<button type="button" class="oc-tag-btn" data-title="Lumina Dental Clinic" data-prompt="A luxury modern dental clinic offering cosmetic dentistry, invisible aligners, teeth whitening, gentle patient care, and easy online appointment booking.">Dental Clinic</button>
							<button type="button" class="oc-tag-btn" data-title="Zenith Digital Studio" data-prompt="A boutique branding and web development agency creating memorable digital experiences for high-growth tech startups.">Creative Agency</button>
							<button type="button" class="oc-tag-btn" data-title="Pulse Fitness & Spa" data-prompt="An upscale health club, personal training studio, recovery sauna, and premium wellness community.">Fitness Club</button>
						</div>
					</div>
				</div>

				<!-- Section 2: Multi-Modal Inputs (URL & Screenshot) -->
				<div class="oc-card-section">
					<div class="oc-section-title">
						<span class="oc-step-num">2</span>
						<div>
							<h3><?php esc_html_e( 'Multi-Modal Reference Inputs (Optional)', 'omnicraft-ai-builder' ); ?></h3>
							<p><?php esc_html_e( 'Add a reference website URL or upload a design screenshot to clone the layout style.', 'omnicraft-ai-builder' ); ?></p>
						</div>
					</div>

					<div class="oc-input-tabs">
						<button type="button" class="oc-tab-btn active" data-tab="tab-url">
							<i class="fa-solid fa-globe"></i> <?php esc_html_e( 'Reference Website URL', 'omnicraft-ai-builder' ); ?>
						</button>
						<button type="button" class="oc-tab-btn" data-tab="tab-screenshot">
							<i class="fa-solid fa-image"></i> <?php esc_html_e( 'Upload Screenshot (Vision AI)', 'omnicraft-ai-builder' ); ?>
						</button>
					</div>

					<!-- Tab Content: URL -->
					<div id="tab-url" class="oc-tab-pane active">
						<div class="oc-form-group">
							<label for="oc-target-url"><?php esc_html_e( 'Target Reference URL to Analyze & Replicate', 'omnicraft-ai-builder' ); ?></label>
							<div class="oc-input-with-button">
								<input type="url" id="oc-target-url" class="oc-input" placeholder="https://stripe.com or https://linear.app">
								<button type="button" id="oc-btn-scrape-preview" class="oc-btn oc-btn-secondary">
									<i class="fa-solid fa-magnifying-glass"></i> <?php esc_html_e( 'Analyze URL', 'omnicraft-ai-builder' ); ?>
								</button>
							</div>
							<p class="oc-help-text"><?php esc_html_e( 'The AI will safely scrape structure, headings, color scheme, and content flow from this site.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- Scraped Info Preview Badge (Hidden initially) -->
						<div id="oc-scraped-info" class="oc-scraped-card" style="display:none;">
							<div class="oc-scraped-header">
								<i class="fa-solid fa-circle-check"></i>
								<strong id="oc-scraped-title">Site Title</strong>
							</div>
							<p id="oc-scraped-desc">Site description...</p>
							<div id="oc-scraped-tags" class="oc-scraped-tags"></div>
						</div>
					</div>

					<!-- Tab Content: Screenshot Vision -->
					<div id="tab-screenshot" class="oc-tab-pane">
						<div class="oc-form-group">
							<label><?php esc_html_e( 'Upload Website Design Screenshot', 'omnicraft-ai-builder' ); ?></label>
							<div id="oc-dropzone" class="oc-dropzone">
								<div class="oc-dropzone-inner">
									<i class="fa-solid fa-cloud-arrow-up oc-dropzone-icon"></i>
									<h4><?php esc_html_e( 'Drag & drop image here, or click to upload', 'omnicraft-ai-builder' ); ?></h4>
									<p><?php esc_html_e( 'Supports PNG, JPG, WEBP (Max 5MB)', 'omnicraft-ai-builder' ); ?></p>
									<button type="button" id="oc-btn-select-media" class="oc-btn oc-btn-sm oc-btn-outline">
										<i class="fa-solid fa-folder-open"></i> <?php esc_html_e( 'Choose File', 'omnicraft-ai-builder' ); ?>
									</button>
									<input type="file" id="oc-file-input" accept="image/png,image/jpeg,image/webp" style="display:none;">
								</div>
								
								<!-- Image Preview Container -->
								<div id="oc-image-preview-container" class="oc-image-preview" style="display:none;">
									<img id="oc-preview-img" src="" alt="Screenshot Preview">
									<button type="button" id="oc-remove-img-btn" class="oc-remove-btn" title="Remove image">
										<i class="fa-solid fa-xmark"></i>
									</button>
								</div>
							</div>
							<p class="oc-help-text"><?php esc_html_e( 'Vision AI analyzes layout composition, visual hierarchy, and card geometry.', 'omnicraft-ai-builder' ); ?></p>
						</div>
					</div>
				</div>

				<!-- Section 3: Design, Palette & Target Builder -->
				<div class="oc-card-section">
					<div class="oc-section-title">
						<span class="oc-step-num">3</span>
						<div>
							<h3><?php esc_html_e( 'Design & Target Builder', 'omnicraft-ai-builder' ); ?></h3>
							<p><?php esc_html_e( 'Customize tone of voice, color palette theme, and target WordPress builder.', 'omnicraft-ai-builder' ); ?></p>
						</div>
					</div>

					<div class="oc-form-row">
						<!-- Target Builder -->
						<div class="oc-form-group oc-col-6">
							<label for="oc-builder-type"><?php esc_html_e( 'Target Page Builder', 'omnicraft-ai-builder' ); ?></label>
							<select id="oc-builder-type" class="oc-select">
								<option value="elementor" <?php selected( $default_bldr, 'elementor' ); ?>>
									Elementor (Recommended) <?php echo $is_elementor_active ? '✓ Active' : '(Not installed)'; ?>
								</option>
								<option value="gutenberg" <?php selected( $default_bldr, 'gutenberg' ); ?>>
									Gutenberg (Native WordPress Blocks)
								</option>
							</select>
						</div>

						<!-- Tone of Voice -->
						<div class="oc-form-group oc-col-6">
							<label for="oc-tone"><?php esc_html_e( 'Tone of Copywriting', 'omnicraft-ai-builder' ); ?></label>
							<select id="oc-tone" class="oc-select">
								<option value="Modern & High-Converting" selected>Modern & High-Converting</option>
								<option value="Professional & Corporate">Professional & Corporate</option>
								<option value="Warm, Friendly & Welcoming">Warm, Friendly & Welcoming</option>
								<option value="Bold, Energetic & Disruptive">Bold, Energetic & Disruptive</option>
								<option value="Minimalist & Elegant">Minimalist & Elegant</option>
							</select>
						</div>
					</div>

					<!-- Color Palette Preset -->
					<div class="oc-form-group">
						<label><?php esc_html_e( 'Color Palette Theme', 'omnicraft-ai-builder' ); ?></label>
						<div class="oc-palette-grid">
							<label class="oc-palette-card selected">
								<input type="radio" name="oc_color_preset" value="indigo" checked>
								<div class="oc-palette-preview" style="background: linear-gradient(135deg, #6366f1 50%, #0f172a 50%);"></div>
								<span>Modern Indigo</span>
							</label>

							<label class="oc-palette-card">
								<input type="radio" name="oc_color_preset" value="emerald">
								<div class="oc-palette-preview" style="background: linear-gradient(135deg, #10b981 50%, #064e3b 50%);"></div>
								<span>Emerald Green</span>
							</label>

							<label class="oc-palette-card">
								<input type="radio" name="oc_color_preset" value="sunset">
								<div class="oc-palette-preview" style="background: linear-gradient(135deg, #f97316 50%, #1e1b4b 50%);"></div>
								<span>Sunset Coral</span>
							</label>

							<label class="oc-palette-card">
								<input type="radio" name="oc_color_preset" value="violet">
								<div class="oc-palette-preview" style="background: linear-gradient(135deg, #8b5cf6 50%, #1e1035 50%);"></div>
								<span>Royal Violet</span>
							</label>

							<label class="oc-palette-card">
								<input type="radio" name="oc_color_preset" value="dark_slate">
								<div class="oc-palette-preview" style="background: linear-gradient(135deg, #38bdf8 50%, #090d16 50%);"></div>
								<span>Dark Tech</span>
							</label>

							<label class="oc-palette-card" id="oc-card-custom-color">
								<input type="radio" name="oc_color_preset" value="custom">
								<div class="oc-palette-preview" id="oc-custom-swatch" style="background: linear-gradient(135deg, #ec4899 50%, #18181b 50%);"></div>
								<span>🎨 Custom Colors</span>
							</label>
						</div>

						<!-- Custom Color Picker Box (Shown when Custom is selected) -->
						<div id="oc-custom-color-pickers" style="display:none; margin-top:14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px 20px;">
							<div class="oc-form-row">
								<div class="oc-form-group oc-col-6" style="margin-bottom:0;">
									<label for="oc-custom-primary"><?php esc_html_e( 'Primary Brand / Accent Color', 'omnicraft-ai-builder' ); ?></label>
									<div style="display:flex; align-items:center; gap:10px;">
										<input type="color" id="oc-custom-primary" class="oc-color-picker" value="#ec4899">
										<input type="text" id="oc-custom-primary-text" class="oc-input" value="#ec4899" style="max-width:120px;">
									</div>
								</div>
								<div class="oc-form-group oc-col-6" style="margin-bottom:0;">
									<label for="oc-custom-secondary"><?php esc_html_e( 'Secondary / Heading Dark Color', 'omnicraft-ai-builder' ); ?></label>
									<div style="display:flex; align-items:center; gap:10px;">
										<input type="color" id="oc-custom-secondary" class="oc-color-picker" value="#18181b">
										<input type="text" id="oc-custom-secondary-text" class="oc-input" value="#18181b" style="max-width:120px;">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Section 4: Choose Website Sections & Architecture -->
				<div class="oc-card-section">
					<div class="oc-section-title" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
						<div style="display:flex; align-items:center; gap:12px;">
							<span class="oc-step-num">4</span>
							<div>
								<h3><?php esc_html_e( 'Choose Website Sections & Structure', 'omnicraft-ai-builder' ); ?></h3>
								<p><?php esc_html_e( 'Select, customize, edit or add custom sections to your site blueprint.', 'omnicraft-ai-builder' ); ?></p>
							</div>
						</div>
						<div class="oc-section-quick-actions" style="display:flex; align-items:center; gap:8px;">
							<span id="oc-section-count-badge" class="oc-count-pill">11 / 11 Selected</span>
							<button type="button" id="oc-btn-add-section" class="oc-btn oc-btn-sm oc-btn-outline" style="border-radius:999px; padding:4px 12px; font-size:12px;">
								<i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Add Section', 'omnicraft-ai-builder' ); ?>
							</button>
							<span style="color:#cbd5e1;">|</span>
							<button type="button" id="oc-btn-select-all-sections" class="oc-btn-link"><?php esc_html_e( 'Select All', 'omnicraft-ai-builder' ); ?></button>
							<span style="color:#cbd5e1;">|</span>
							<button type="button" id="oc-btn-deselect-all-sections" class="oc-btn-link"><?php esc_html_e( 'Deselect All', 'omnicraft-ai-builder' ); ?></button>
						</div>
					</div>

					<div class="oc-sections-grid" id="oc-sections-container">
						<!-- 1. Navbar -->
						<div class="oc-section-toggle-card selected" data-type="navbar" data-tag="[NAVBAR]" data-title="Navigation Header" data-desc="Sticky modern navbar with logo, smooth-scroll links, and CTA button.">
							<input type="checkbox" name="oc_sections" value="navbar" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[NAVBAR]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Navigation Header', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( 'Sticky modern navbar with logo, smooth-scroll links, and CTA button.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 2. Hero -->
						<div class="oc-section-toggle-card selected" data-type="hero" data-tag="[HERO]" data-title="Hero Banner" data-desc="Compelling H1 headline, animated badge, value prop, CTA, and floating graphic.">
							<input type="checkbox" name="oc_sections" value="hero" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[HERO]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Hero Banner', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( 'Compelling H1 headline, animated badge, value prop, CTA, and floating graphic.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 3. Stats -->
						<div class="oc-section-toggle-card selected" data-type="stats" data-tag="[STATS]" data-title="Stats & Metrics" data-desc="4 quantitative performance metrics, animated counters, and achievement badges.">
							<input type="checkbox" name="oc_sections" value="stats" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[STATS]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Stats & Metrics', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( '4 quantitative performance metrics, animated counters, and achievement badges.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 4. Features -->
						<div class="oc-section-toggle-card selected" data-type="features" data-tag="[FEATURES]" data-title="Core Features" data-desc="Core capabilities, advantages, and benefit cards with icons & hover lift.">
							<input type="checkbox" name="oc_sections" value="features" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[FEATURES]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Core Features', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( 'Core capabilities, advantages, and benefit cards with icons & hover lift.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 5. Slider / Showcase -->
						<div class="oc-section-toggle-card selected" data-type="slider" data-tag="[SLIDER / SHOWCASE]" data-title="Project Carousel Slider" data-desc="Interactive multi-slide image carousel with autoplay and navigation arrows.">
							<input type="checkbox" name="oc_sections" value="slider" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[SLIDER / SHOWCASE]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Project Carousel Slider', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( 'Interactive multi-slide image carousel with autoplay and navigation arrows.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 6. About -->
						<div class="oc-section-toggle-card selected" data-type="about" data-tag="[ABOUT / STORY]" data-title="Our Story & Mission" data-desc="2-column narrative with high-res workspace/team imagery, mission, and trust.">
							<input type="checkbox" name="oc_sections" value="about" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[ABOUT / STORY]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Our Story & Mission', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( '2-column narrative with high-res workspace/team imagery, mission, and trust.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 7. Testimonials -->
						<div class="oc-section-toggle-card selected" data-type="testimonials" data-tag="[TESTIMONIALS]" data-title="Client Reviews & Proof" data-desc="3 verified customer quotes with executive titles, companies, and 5-star ratings.">
							<input type="checkbox" name="oc_sections" value="testimonials" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[TESTIMONIALS]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Client Reviews & Proof', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( '3 verified customer quotes with executive titles, companies, and 5-star ratings.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 8. Pricing -->
						<div class="oc-section-toggle-card selected" data-type="pricing" data-tag="[PRICING]" data-title="Pricing & Packages" data-desc="3 transparent investment tiers with feature checklists, ribbons, and CTAs.">
							<input type="checkbox" name="oc_sections" value="pricing" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[PRICING]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Pricing & Packages', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( '3 transparent investment tiers with feature checklists, ribbons, and CTAs.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 9. FAQ -->
						<div class="oc-section-toggle-card selected" data-type="faq" data-tag="[FAQ ACCORDION]" data-title="FAQ Accordion" data-desc="4 to 6 interactive domain-specific question & answer toggles.">
							<input type="checkbox" name="oc_sections" value="faq" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[FAQ ACCORDION]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'FAQ Accordion', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( '4 to 6 interactive domain-specific question & answer toggles.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 10. CTA -->
						<div class="oc-section-toggle-card selected" data-type="cta" data-tag="[FINAL CTA]" data-title="Conversion Banner" data-desc="High-converting closing proposition banner with gradient styling and direct button.">
							<input type="checkbox" name="oc_sections" value="cta" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[FINAL CTA]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Conversion Banner', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( 'High-converting closing proposition banner with gradient styling and direct button.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- 11. Footer -->
						<div class="oc-section-toggle-card selected" data-type="footer" data-tag="[FOOTER]" data-title="Rich Modern Footer" data-desc="3-column dark slate footer with navigation links, brand mission, and legal copyright.">
							<input type="checkbox" name="oc_sections" value="footer" checked>
							<div class="oc-section-toggle-header">
								<span class="oc-sec-tag">[FOOTER]</span>
								<div class="oc-card-tools">
									<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="<?php esc_attr_e( 'Edit Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-pen"></i></button>
									<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="<?php esc_attr_e( 'Delete Section', 'omnicraft-ai-builder' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
									<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
								</div>
							</div>
							<h4 class="oc-sec-title"><?php esc_html_e( 'Rich Modern Footer', 'omnicraft-ai-builder' ); ?></h4>
							<p class="oc-sec-desc"><?php esc_html_e( '3-column dark slate footer with navigation links, brand mission, and legal copyright.', 'omnicraft-ai-builder' ); ?></p>
						</div>

						<!-- + Add Custom Section Card -->
						<div class="oc-add-section-card" id="oc-add-section-trigger">
							<div class="oc-add-icon"><i class="fa-solid fa-circle-plus"></i></div>
							<h4><?php esc_html_e( 'Add Custom Section', 'omnicraft-ai-builder' ); ?></h4>
							<p><?php esc_html_e( 'E.g. Integrations, Team, Roadmap, Comparison Table...', 'omnicraft-ai-builder' ); ?></p>
						</div>
					</div>
				</div>

				<!-- Action Submit Footer -->
				<div class="oc-form-footer">
					<button type="button" id="oc-btn-generate" class="oc-btn oc-btn-primary oc-btn-lg">
						<i class="fa-solid fa-wand-magic-sparkles"></i> <?php esc_html_e( 'Generate Complete Website', 'omnicraft-ai-builder' ); ?>
					</button>
				</div>
			</form>
		</div>

		<!-- Right Column: Generation Status & Live Output -->
		<div class="omnicraft-sidebar">
			<!-- Generation Live Stepper Card (Shown during generation) -->
			<div id="oc-progress-card" class="oc-side-card" style="display:none;">
				<div class="oc-side-card-header">
					<div class="oc-spinner"></div>
					<h4><?php esc_html_e( 'Generating Your Website...', 'omnicraft-ai-builder' ); ?></h4>
				</div>
				<div class="oc-stepper-list">
					<div class="oc-step-item" id="step-analyze">
						<span class="oc-step-dot"></span>
						<span class="oc-step-label"><?php esc_html_e( 'Analyzing Prompt & Multi-Modal Inputs', 'omnicraft-ai-builder' ); ?></span>
					</div>
					<div class="oc-step-item" id="step-scrape">
						<span class="oc-step-dot"></span>
						<span class="oc-step-label"><?php esc_html_e( 'Scraping & Vision Layout Synthesis', 'omnicraft-ai-builder' ); ?></span>
					</div>
					<div class="oc-step-item" id="step-copy">
						<span class="oc-step-dot"></span>
						<span class="oc-step-label"><?php esc_html_e( 'Drafting High-Converting Copy & Structure', 'omnicraft-ai-builder' ); ?></span>
					</div>
					<div class="oc-step-item" id="step-compile">
						<span class="oc-step-dot"></span>
						<span class="oc-step-label"><?php esc_html_e( 'Compiling Elementor / Gutenberg Elements', 'omnicraft-ai-builder' ); ?></span>
					</div>
					<div class="oc-step-item" id="step-publish">
						<span class="oc-step-dot"></span>
						<span class="oc-step-label"><?php esc_html_e( 'Creating & Publishing WordPress Page', 'omnicraft-ai-builder' ); ?></span>
					</div>
				</div>
			</div>

			<!-- Success Result Card (Shown after generation) -->
			<div id="oc-result-card" class="oc-side-card oc-result-success" style="display:none;">
				<div class="oc-result-header">
					<div class="oc-success-icon">
						<i class="fa-solid fa-circle-check"></i>
					</div>
					<h3><?php esc_html_e( 'Website Ready!', 'omnicraft-ai-builder' ); ?></h3>
					<p id="oc-result-page-title">Page Title</p>
				</div>

				<div class="oc-result-actions">
					<a id="oc-btn-edit-elementor" href="#" class="oc-btn oc-btn-primary oc-btn-block" target="_blank">
						<i class="fa-solid fa-pen-ruler"></i> <?php esc_html_e( 'Edit with Elementor', 'omnicraft-ai-builder' ); ?>
					</a>
					<a id="oc-btn-view-live" href="#" class="oc-btn oc-btn-secondary oc-btn-block" target="_blank">
						<i class="fa-solid fa-arrow-up-right-from-square"></i> <?php esc_html_e( 'View Live Page', 'omnicraft-ai-builder' ); ?>
					</a>
					<a id="oc-btn-edit-wp" href="#" class="oc-btn oc-btn-outline oc-btn-block" target="_blank">
						<i class="fa-brands fa-wordpress"></i> <?php esc_html_e( 'Standard WP Editor', 'omnicraft-ai-builder' ); ?>
					</a>
				</div>
			</div>

			<!-- Tips & Info Card -->
			<div class="oc-side-card oc-tips-card">
				<h4><i class="fa-solid fa-lightbulb"></i> <?php esc_html_e( 'Pro Generation Tips', 'omnicraft-ai-builder' ); ?></h4>
				<ul>
					<li><strong>Section Blueprint:</strong> Click the edit icon on any card to customize its prompt, or add brand new custom sections.</li>
					<li><strong>Multi-Modal Magic:</strong> Paste a competitor's reference URL along with your business description to adapt their layout to your branding.</li>
					<li><strong>100% Editable:</strong> All generated pages are native Elementor or Gutenberg blocks that you can tweak with drag-and-drop.</li>
				</ul>
			</div>
		</div>
	</div>

	<!-- Modal Dialog for Add / Edit Section -->
	<div id="oc-section-modal" class="oc-modal" style="display:none;">
		<div class="oc-modal-backdrop"></div>
		<div class="oc-modal-dialog">
			<div class="oc-modal-header">
				<h3 id="oc-modal-title"><i class="fa-solid fa-layer-group"></i> <?php esc_html_e( 'Add Custom Section', 'omnicraft-ai-builder' ); ?></h3>
				<button type="button" class="oc-modal-close" id="oc-modal-close-btn">&times;</button>
			</div>
			<div class="oc-modal-body">
				<input type="hidden" id="oc-modal-edit-id" value="">

				<div class="oc-form-group">
					<label for="oc-modal-sec-type"><?php esc_html_e( 'Section Type / Template Base', 'omnicraft-ai-builder' ); ?></label>
					<select id="oc-modal-sec-type" class="oc-select">
						<option value="custom"><?php esc_html_e( 'Custom Section (Adaptive Cards & Grid)', 'omnicraft-ai-builder' ); ?></option>
						<option value="features"><?php esc_html_e( 'Feature / Solution Cards Grid', 'omnicraft-ai-builder' ); ?></option>
						<option value="slider"><?php esc_html_e( 'Project / Showcase Carousel Slider', 'omnicraft-ai-builder' ); ?></option>
						<option value="stats"><?php esc_html_e( 'Performance Metrics / Counters', 'omnicraft-ai-builder' ); ?></option>
						<option value="about"><?php esc_html_e( 'Story / Narrative with Image', 'omnicraft-ai-builder' ); ?></option>
						<option value="testimonials"><?php esc_html_e( 'Reviews & Social Proof', 'omnicraft-ai-builder' ); ?></option>
						<option value="pricing"><?php esc_html_e( 'Pricing & Comparison Tiers', 'omnicraft-ai-builder' ); ?></option>
						<option value="faq"><?php esc_html_e( 'FAQ Accordion', 'omnicraft-ai-builder' ); ?></option>
						<option value="cta"><?php esc_html_e( 'Call to Action Banner', 'omnicraft-ai-builder' ); ?></option>
					</select>
				</div>

				<div class="oc-form-group">
					<label for="oc-modal-sec-tag"><?php esc_html_e( 'Section Tag / Badge (e.g. [INTEGRATIONS])', 'omnicraft-ai-builder' ); ?></label>
					<input type="text" id="oc-modal-sec-tag" class="oc-input" placeholder="e.g. [INTEGRATIONS] or [TEAM]" value="[CUSTOM]">
				</div>

				<div class="oc-form-group">
					<label for="oc-modal-sec-title"><?php esc_html_e( 'Section Display Title', 'omnicraft-ai-builder' ); ?></label>
					<input type="text" id="oc-modal-sec-title" class="oc-input" placeholder="e.g. App Integrations & Ecosystem">
				</div>

				<div class="oc-form-group">
					<label for="oc-modal-sec-desc"><?php esc_html_e( 'Section Description & AI Directives', 'omnicraft-ai-builder' ); ?></label>
					<textarea id="oc-modal-sec-desc" class="oc-textarea" rows="3" placeholder="<?php esc_attr_e( 'Describe what content and elements the AI should generate for this section (e.g. 6 tool integration cards with logos, descriptions, and docs links)...', 'omnicraft-ai-builder' ); ?>"></textarea>
				</div>
			</div>
			<div class="oc-modal-footer">
				<button type="button" class="oc-btn oc-btn-outline" id="oc-modal-cancel-btn"><?php esc_html_e( 'Cancel', 'omnicraft-ai-builder' ); ?></button>
				<button type="button" class="oc-btn oc-btn-primary" id="oc-modal-save-btn"><?php esc_html_e( 'Save Section', 'omnicraft-ai-builder' ); ?></button>
			</div>
		</div>
	</div>
</div>
