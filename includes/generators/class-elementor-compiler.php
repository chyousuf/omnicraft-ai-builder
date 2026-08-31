<?php
/**
 * Compiles AI JSON Layout into native Elementor _elementor_data schema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_Elementor_Compiler {

	/**
	 * Generate a unique 7-char alphanumeric ID for Elementor elements.
	 *
	 * @return string
	 */
	public static function generate_id() {
		return substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 7 );
	}

	/**
	 * Compile structured site JSON to Elementor sections array.
	 *
	 * @param array $site_data Structured AI response
	 * @param array $selected_sections Optional filter of user-chosen section types
	 * @return array
	 */
	public static function compile( $site_data, $selected_sections = array() ) {
		$elements = array();
		$primary_color   = ! empty( $site_data['color_palette']['primary'] ) ? $site_data['color_palette']['primary'] : '#6366f1';
		$secondary_color = ! empty( $site_data['color_palette']['secondary'] ) ? $site_data['color_palette']['secondary'] : '#0f172a';
		$accent_color    = ! empty( $site_data['color_palette']['accent'] ) ? $site_data['color_palette']['accent'] : '#38bdf8';
		$bg_light        = ! empty( $site_data['color_palette']['bg_light'] ) ? $site_data['color_palette']['bg_light'] : '#f8fafc';
		$text_dark       = ! empty( $site_data['color_palette']['text_dark'] ) ? $site_data['color_palette']['text_dark'] : '#1e293b';

		$sections = ! empty( $site_data['sections'] ) && is_array( $site_data['sections'] ) ? $site_data['sections'] : array();

		$selected_types = array();
		if ( ! empty( $selected_sections ) && is_array( $selected_sections ) ) {
			foreach ( $selected_sections as $s ) {
				$selected_types[] = is_array( $s ) ? ( isset( $s['type'] ) ? $s['type'] : 'custom' ) : $s;
			}
		}

		// 1. Prepend Modern Navigation Header Section if enabled
		$include_navbar = empty( $selected_types ) || in_array( 'navbar', $selected_types, true );
		if ( $include_navbar ) {
			$elements[] = self::build_header_section( $site_data, $primary_color, $secondary_color );
		}

		foreach ( $sections as $index => $section ) {
			$type = ! empty( $section['type'] ) ? strtolower( $section['type'] ) : 'custom';

			switch ( $type ) {
				case 'hero':
					$elements[] = self::build_hero_section( $section, $primary_color, $secondary_color, $accent_color, $text_dark );
					break;

				case 'features':
				case 'services':
					$elements[] = self::build_features_section( $section, $primary_color, $secondary_color, $bg_light, $text_dark );
					break;

				case 'about':
					$elements[] = self::build_about_section( $section, $primary_color, $secondary_color, $text_dark );
					break;

				case 'stats':
				case 'counter':
					$elements[] = self::build_stats_section( $section, $primary_color, $secondary_color, $bg_light );
					break;

				case 'testimonials':
				case 'reviews':
					$elements[] = self::build_testimonials_section( $section, $primary_color, $secondary_color, $bg_light, $text_dark );
					break;

				case 'pricing':
					$elements[] = self::build_pricing_section( $section, $primary_color, $secondary_color, $accent_color, $bg_light, $text_dark );
					break;

				case 'slider':
				case 'showcase':
				case 'carousel':
					$elements[] = self::build_slider_section( $section, $primary_color, $secondary_color, $accent_color, $bg_light );
					break;

				case 'gallery':
				case 'portfolio':
					$elements[] = self::build_gallery_section( $section, $primary_color, $secondary_color, $bg_light, $text_dark );
					break;

				case 'map':
				case 'location':
					$elements[] = self::build_map_section( $section, $primary_color, $secondary_color, $bg_light, $text_dark );
					break;

				case 'form':
				case 'multi_step_form':
				case 'multistep':
				case 'lead_form':
					$elements[] = self::build_multi_step_form_section( $section, $primary_color, $secondary_color, $bg_light, $text_dark );
					break;

				case 'team':
				case 'leadership':
				case 'members':
					$elements[] = self::build_team_section( $section, $primary_color, $secondary_color, $bg_light, $text_dark );
					break;

				case 'integrations':
				case 'tools':
				case 'ecosystem':
					$elements[] = self::build_integrations_section( $section, $primary_color, $secondary_color, $bg_light, $text_dark );
					break;

				case 'timeline':
				case 'roadmap':
					$elements[] = self::build_timeline_section( $section, $primary_color, $secondary_color, $bg_light, $text_dark );
					break;

				case 'faq':
				case 'accordion':
					$elements[] = self::build_faq_section( $section, $primary_color, $secondary_color, $text_dark );
					break;

				case 'cta':
				case 'contact':
					$elements[] = self::build_cta_section( $section, $primary_color, $secondary_color, $accent_color );
					break;

				default:
					$elements[] = self::build_generic_section( $section, $primary_color, $secondary_color, $text_dark );
					break;
			}
		}

		// 2. Append Modern Footer Section if enabled
		$include_footer = empty( $selected_types ) || in_array( 'footer', $selected_types, true );
		if ( $include_footer ) {
			$elements[] = self::build_footer_section( $site_data, $primary_color, $secondary_color, $text_dark );
		}

		return $elements;
	}

	/**
	 * Build Hero Section
	 */
	/**
	 * Build Hero Section
	 */
	private static function build_hero_section( $sec, $primary, $secondary, $accent, $text_dark ) {
		$title       = ! empty( $sec['title'] ) ? $sec['title'] : 'Empower Decisions. Elevate Outcomes.';
		$subtitle    = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Award-winning and AI-powered enterprise software that unifies your teams and accelerates business performance.';
		$cta_text    = ! empty( $sec['cta_text'] ) ? $sec['cta_text'] : 'Request an Executive Demo';
		$cta_url     = ! empty( $sec['cta_url'] ) ? $sec['cta_url'] : '#contact';
		$image_url   = ! empty( $sec['image_url'] ) ? $sec['image_url'] : 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&auto=format&fit=crop&q=80';
		$badge       = ! empty( $sec['badge'] ) ? $sec['badge'] : '✦ Enterprise AI & Workflow Platform';

		$left_content_html = '
		<div style="display:flex; flex-direction:column; gap:20px;">
			<div>
				<span style="display:inline-flex; align-items:center; gap:8px; padding:6px 16px; background:rgba(99,102,241,0.08); color:' . esc_attr( $primary ) . '; border:1px solid ' . esc_attr( $primary ) . '30; border-radius:999px; font-size:12.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
					<span style="width:7px; height:7px; border-radius:50%; background:' . esc_attr( $primary ) . '; display:inline-block; box-shadow:0 0 8px ' . esc_attr( $primary ) . ';"></span>
					' . esc_html( $badge ) . '
				</span>
			</div>
			<h1 style="font-size:46px; font-weight:850; line-height:1.15; letter-spacing:-0.03em; color:' . esc_attr( $secondary ) . '; margin:0;">
				' . esc_html( $title ) . '
			</h1>
			<p style="font-size:18px; line-height:1.65; color:#475569; margin:0;">
				' . esc_html( $subtitle ) . '
			</p>
			<div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-top:8px;">
				<a href="' . esc_url( $cta_url ) . '" style="background:' . esc_attr( $primary ) . '; color:#ffffff; padding:15px 32px; border-radius:10px; font-weight:700; font-size:15px; text-decoration:none; box-shadow:0 10px 25px rgba(0,0,0,0.12); display:inline-flex; align-items:center; gap:8px; transition:transform 0.2s;">
					' . esc_html( $cta_text ) . ' <i class="fa-solid fa-arrow-right"></i>
				</a>
				<a href="#solutions" style="background:#ffffff; color:#0f172a; border:1px solid #cbd5e1; padding:14px 26px; border-radius:10px; font-weight:600; font-size:15px; text-decoration:none; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(0,0,0,0.03);">
					<i class="fa-solid fa-circle-play" style="color:' . esc_attr( $primary ) . ';"></i> Explore Solutions
				</a>
			</div>
			<div style="display:flex; align-items:center; gap:20px; margin-top:16px; font-size:13px; color:#64748b; font-weight:600; flex-wrap:wrap;">
				<span><i class="fa-solid fa-circle-check" style="color:#10b981; margin-right:5px;"></i> Enterprise Security</span>
				<span><i class="fa-solid fa-circle-check" style="color:#10b981; margin-right:5px;"></i> 99.99% Uptime SLA</span>
				<span><i class="fa-solid fa-circle-check" style="color:#10b981; margin-right:5px;"></i> 10,000+ Global Clients</span>
			</div>
		</div>';

		$right_visual_html = '
		<div style="position:relative; border-radius:20px; padding:10px; background:linear-gradient(135deg, rgba(255,255,255,0.9), rgba(241,245,249,0.7)); border:1px solid #e2e8f0; box-shadow:0 25px 50px -12px rgba(0,0,0,0.15);">
			<div style="height:32px; background:#f8fafc; border-radius:12px 12px 0 0; display:flex; align-items:center; gap:6px; padding:0 12px; border-bottom:1px solid #e2e8f0;">
				<span style="width:10px; height:10px; border-radius:50%; background:#ef4444;"></span>
				<span style="width:10px; height:10px; border-radius:50%; background:#f59e0b;"></span>
				<span style="width:10px; height:10px; border-radius:50%; background:#10b981;"></span>
				<span style="margin-left:auto; font-size:11px; color:#94a3b8; font-family:monospace;">app.enterprise.io/dashboard</span>
			</div>
			<div style="overflow:hidden; border-radius:0 0 12px 12px; height:360px; position:relative;">
				<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" style="width:100%; height:100%; object-fit:cover; display:block;">
				<div style="position:absolute; bottom:16px; left:16px; background:rgba(15,23,42,0.88); backdrop-filter:blur(8px); padding:12px 18px; border-radius:12px; color:#fff; border:1px solid rgba(255,255,255,0.15); display:flex; align-items:center; gap:12px;">
					<div style="width:36px; height:36px; border-radius:8px; background:' . esc_attr( $primary ) . '; display:flex; align-items:center; justify-content:center; font-size:16px;"><i class="fa-solid fa-bolt"></i></div>
					<div>
						<strong style="display:block; font-size:13px;">AI Orchestration Engine</strong>
						<span style="font-size:11px; color:#94a3b8;">99.9% Real-Time Compliance Sync</span>
					</div>
				</div>
			</div>
		</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 90, 'right' => 20, 'bottom' => 90, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'_css_id'               => 'hero',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size'   => 52,
						'vertical_align' => 'middle',
						'padding'        => array( 'unit' => 'px', 'top' => 10, 'right' => 25, 'bottom' => 10, 'left' => 0, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array( 'editor' => $left_content_html ),
						),
					),
				),
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size'   => 48,
						'vertical_align' => 'middle',
						'padding'        => array( 'unit' => 'px', 'top' => 10, 'right' => 0, 'bottom' => 10, 'left' => 15, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array( 'editor' => $right_visual_html ),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Features / Solutions Bento Grid Section
	 */
	private static function build_features_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Enterprise Solutions & Capabilities';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Unified software architecture designed to eliminate operational friction and accelerate growth.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		if ( empty( $items ) ) {
			$items = array(
				array( 'title' => 'Legal Operations & Governance', 'description' => 'Unify matter management, e-Billing, and spend analytics with automated compliance tracking.' ),
				array( 'title' => 'Risk & Compliance Automation', 'description' => 'Continuous audit readiness, policy lifecycle management, and third-party risk mitigation.' ),
				array( 'title' => 'Human Resources & Talent Compliance', 'description' => 'Automated background screening, I-9 verification, and global workforce onboarding.' ),
				array( 'title' => 'Workflow & AI Orchestration', 'description' => 'Connect 50+ enterprise systems with zero-code automation triggers and real-time alerts.' ),
			);
		}

		$icons = array( 'fa-scale-balanced', 'fa-shield-halved', 'fa-users-gear', 'fa-diagram-project', 'fa-brain', 'fa-cubes' );

		// Build Bento Grid HTML
		$bento_html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:24px; margin-top:40px;">';
		foreach ( $items as $i => $item ) {
			$f_title = ! empty( $item['title'] ) ? $item['title'] : 'Core Capability';
			$f_desc  = ! empty( $item['description'] ) ? $item['description'] : 'Comprehensive enterprise capability designed for mission-critical operations.';
			$icon    = $icons[ $i % count( $icons ) ];

			$bento_html .= '
			<div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:18px; padding:32px; box-shadow:0 10px 25px rgba(0,0,0,0.04); display:flex; flex-direction:column; justify-content:space-between; transition:all 0.3s ease;">
				<div>
					<div style="width:48px; height:48px; border-radius:12px; background:' . esc_attr( $primary ) . '15; color:' . esc_attr( $primary ) . '; display:flex; align-items:center; justify-content:center; font-size:20px; margin-bottom:20px;">
						<i class="fa-solid ' . esc_attr( $icon ) . '"></i>
					</div>
					<h3 style="font-size:20px; font-weight:750; color:' . esc_attr( $secondary ) . '; margin:0 0 10px 0; line-height:1.3;">
						' . esc_html( $f_title ) . '
					</h3>
					<p style="font-size:14.5px; color:#64748b; line-height:1.6; margin:0 0 20px 0;">
						' . esc_html( $f_desc ) . '
					</p>
				</div>
				<div>
					<span style="color:' . esc_attr( $primary ) . '; font-size:13.5px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
						Explore Solution <i class="fa-solid fa-arrow-right" style="font-size:12px;"></i>
					</span>
				</div>
			</div>';
		}
		$bento_html .= '</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#f8fafc',
				'_css_id'               => 'features',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:680px; margin:12px auto 0 auto;">' . esc_html( $subtitle ) . '</p>' . $bento_html,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Alternating Deep-Dive Service Rows Section (About / Solutions Deep Dive)
	 */
	private static function build_about_section( $sec, $primary, $secondary, $text_dark ) {
		$title     = ! empty( $sec['title'] ) ? $sec['title'] : 'At Mitratech, We Turn Innovation Into Tangible Results';
		$content   = ! empty( $sec['content'] ) ? $sec['content'] : 'Our award-winning compliance and legal operations software unites your enterprise teams, eliminates operational blind spots, and delivers measurable ROI.';
		$image_url = ! empty( $sec['image_url'] ) ? $sec['image_url'] : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1000&auto=format&fit=crop&q=80';

		$deep_dive_html = '
		<div style="display:flex; flex-direction:column; gap:20px;">
			<span style="display:inline-block; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:' . esc_attr( $primary ) . ';">THE ARCHITECTURAL ADVANTAGE</span>
			<h2 style="font-size:36px; font-weight:800; line-height:1.2; color:' . esc_attr( $secondary ) . '; margin:0;">' . esc_html( $title ) . '</h2>
			<p style="font-size:16.5px; line-height:1.7; color:#475569; margin:0;">' . esc_html( $content ) . '</p>
			
			<div style="display:flex; flex-direction:column; gap:12px; margin-top:10px;">
				<div style="display:flex; align-items:center; gap:10px;">
					<i class="fa-solid fa-circle-check" style="color:' . esc_attr( $primary ) . '; font-size:18px;"></i>
					<span style="color:#1e293b; font-weight:600; font-size:15px;">Unified GRC, Legal & HR Compliance Data Lake</span>
				</div>
				<div style="display:flex; align-items:center; gap:10px;">
					<i class="fa-solid fa-circle-check" style="color:' . esc_attr( $primary ) . '; font-size:18px;"></i>
					<span style="color:#1e293b; font-weight:600; font-size:15px;">Automated Risk Scoring & Continuous Audit Trails</span>
				</div>
				<div style="display:flex; align-items:center; gap:10px;">
					<i class="fa-solid fa-circle-check" style="color:' . esc_attr( $primary ) . '; font-size:18px;"></i>
					<span style="color:#1e293b; font-weight:600; font-size:15px;">SOC2 Type II, ISO27001 & GDPR Compliant Infrastructure</span>
				</div>
			</div>

			<div style="margin-top:16px;">
				<a href="#contact" style="background:' . esc_attr( $primary ) . '; color:#fff; padding:14px 28px; border-radius:8px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
					Discover The Platform <i class="fa-solid fa-arrow-right"></i>
				</a>
			</div>
		</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'_css_id'               => 'about',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size'   => 50,
						'vertical_align' => 'middle',
						'padding'        => array( 'unit' => 'px', 'top' => 10, 'right' => 30, 'bottom' => 10, 'left' => 10, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'image',
							'settings'   => array(
								'image'         => array( 'url' => esc_url_raw( $image_url ) ),
								'image_size'    => 'full',
								'border_radius' => array( 'unit' => 'px', 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ),
								'box_shadow_box_shadow_type' => 'yes',
								'box_shadow_box_shadow' => array( 'horizontal' => 0, 'vertical' => 15, 'blur' => 35, 'spread' => -5, 'color' => 'rgba(0,0,0,0.1)' ),
							),
						),
					),
				),
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size'   => 50,
						'vertical_align' => 'middle',
						'padding'        => array( 'unit' => 'px', 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 30, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array( 'editor' => $deep_dive_html ),
						),
					),
				),
			),
		);
	}

	/**
	 * Build High-Impact Metrics & Stats Section
	 */
	private static function build_stats_section( $sec, $primary, $secondary, $bg_light ) {
		$items = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();
		if ( empty( $items ) ) {
			$items = array(
				array( 'number' => '90', 'suffix' => '%', 'title' => 'GRC Efficiency Boost' ),
				array( 'number' => '500', 'suffix' => 'k+', 'title' => 'Active Enterprise Users' ),
				array( 'number' => '99.9', 'suffix' => '%', 'title' => 'Audit Accuracy & SLA' ),
				array( 'number' => '10', 'suffix' => 'x', 'title' => 'Faster ROI Delivery' ),
			);
		}

		$stats_cards_html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px; text-align:center;">';
		foreach ( $items as $item ) {
			$num    = ! empty( $item['number'] ) ? $item['number'] : '100';
			$suffix = ! empty( $item['suffix'] ) ? $item['suffix'] : '+';
			$st_title = ! empty( $item['title'] ) ? $item['title'] : 'Key Performance Metric';

			$stats_cards_html .= '
			<div style="background:rgba(255,255,255,0.06); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.12); border-radius:16px; padding:28px 20px;">
				<div style="font-size:44px; font-weight:900; line-height:1; color:#ffffff; margin-bottom:8px; letter-spacing:-0.02em;">
					' . esc_html( $num ) . '<span style="color:' . esc_attr( $primary ) . ';">' . esc_html( $suffix ) . '</span>
				</div>
				<div style="font-size:14px; font-weight:600; color:#94a3b8; line-height:1.4;">
					' . esc_html( $st_title ) . '
				</div>
			</div>';
		}
		$stats_cards_html .= '</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 60, 'right' => 20, 'bottom' => 60, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => ! empty( $secondary ) ? $secondary : '#0f172a',
				'_css_id'               => 'stats',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array( 'editor' => $stats_cards_html ),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Star-Rated Executive Testimonials Section
	 */
	private static function build_testimonials_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Trusted by Visionary Industry Leaders';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Real feedback from Fortune 500 executives, general counsels, and compliance directors.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		if ( empty( $items ) ) {
			$items = array(
				array( 'quote' => 'Mitratech completely transformed our legal operations and GRC visibility across 40 global subsidiaries in under 6 months.', 'author' => 'Sarah Jenkins', 'role' => 'Chief Legal Officer at Apex Global' ),
				array( 'quote' => 'The return on investment was immediate. Our compliance audit preparation time dropped from 3 weeks to 2 hours.', 'author' => 'Marcus Vance', 'role' => 'VP of Risk & Governance at CloudScale' ),
				array( 'quote' => 'Unmatched speed, reliability, and precision engineering. It is the gold standard for enterprise compliance.', 'author' => 'Dr. Elena Rostova', 'role' => 'Director of Corporate Compliance at Veloce' ),
			);
		}

		$avatars = array(
			'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80',
			'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&auto=format&fit=crop&q=80',
			'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=300&auto=format&fit=crop&q=80',
		);

		$reviews_html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:24px; margin-top:40px;">';
		foreach ( $items as $i => $item ) {
			$quote  = ! empty( $item['quote'] ) ? $item['quote'] : 'Incredible product and experience!';
			$author = ! empty( $item['author'] ) ? $item['author'] : 'Enterprise Leader';
			$role   = ! empty( $item['role'] ) ? $item['role'] : 'Executive';
			$avatar = $avatars[ $i % count( $avatars ) ];

			$reviews_html .= '
			<div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:18px; padding:32px; box-shadow:0 10px 25px rgba(0,0,0,0.04); display:flex; flex-direction:column; justify-content:space-between;">
				<div>
					<div style="color:#f59e0b; font-size:16px; margin-bottom:14px; letter-spacing:2px;">★★★★★</div>
					<p style="font-size:15px; line-height:1.65; color:#334155; font-style:italic; margin:0 0 24px 0;">
						“' . esc_html( $quote ) . '”
					</p>
				</div>
				<div style="display:flex; align-items:center; gap:14px; border-top:1px solid #f1f5f9; padding-top:16px;">
					<img src="' . esc_url( $avatar ) . '" alt="' . esc_attr( $author ) . '" style="width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid ' . esc_attr( $primary ) . ';">
					<div>
						<strong style="color:#0f172a; font-size:15px; display:block;">' . esc_html( $author ) . '</strong>
						<span style="color:#64748b; font-size:13px;">' . esc_html( $role ) . '</span>
					</div>
				</div>
			</div>';
		}
		$reviews_html .= '</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#f8fafc',
				'_css_id'               => 'testimonials',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 0 auto;">' . esc_html( $subtitle ) . '</p>' . $reviews_html,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Transparent Pricing Section with Full Feature Checklist Cards
	 */
	private static function build_pricing_section( $sec, $primary, $secondary, $accent, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Transparent, Flexible Investment Plans';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Scalable deployment models tailored to mid-market and global enterprise teams.';
		$plans    = ! empty( $sec['plans'] ) && is_array( $sec['plans'] ) ? $sec['plans'] : array();

		if ( empty( $plans ) ) {
			$plans = array(
				array( 'name' => 'Essential Compliance', 'price' => '$499/mo', 'features' => array( 'Core Matter & Spend Management', 'Standard Compliance Audit Logs', 'Up to 25 Active Team Seats', 'REST API & Webhook Access', 'Email & Standard Support' ), 'is_featured' => false ),
				array( 'name' => 'Enterprise Suite', 'price' => '$1,299/mo', 'features' => array( 'Full GRC & Legal Operations Suite', 'AI-Powered Contract Review Engine', 'Unlimited Global Users & Workspaces', 'Automated Third-Party Risk Audits', '24/7 Dedicated Concierge SLA' ), 'is_featured' => true ),
				array( 'name' => 'Custom Global SLA', 'price' => 'Custom', 'features' => array( 'Dedicated On-Premise / Hybrid Cloud', 'Custom Machine Learning Models', 'Custom ERP & SAP Connectors', 'Enterprise Data Residency Guarantee', 'Executive Solution Architect Support' ), 'is_featured' => false ),
			);
		}

		$pricing_cards_html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:24px; margin-top:40px; align-items:stretch;">';
		foreach ( $plans as $plan ) {
			$p_name     = ! empty( $plan['name'] ) ? $plan['name'] : 'Enterprise Plan';
			$p_price    = ! empty( $plan['price'] ) ? $plan['price'] : '$99/mo';
			$p_features = ! empty( $plan['features'] ) && is_array( $plan['features'] ) ? $plan['features'] : array( 'Enterprise Platform Access', 'Dedicated Concierge', 'API Integrations' );
			$is_feat    = ! empty( $plan['is_featured'] );

			$card_border = $is_feat ? '2px solid ' . esc_attr( $primary ) : '1px solid #e2e8f0';
			$badge_html  = $is_feat ? '<span style="background:' . esc_attr( $primary ) . '; color:#fff; font-size:11px; font-weight:800; text-transform:uppercase; padding:4px 12px; border-radius:999px; display:inline-block; margin-bottom:12px;">★ Most Popular Choice</span>' : '<span style="font-size:11px; font-weight:800; text-transform:uppercase; color:#94a3b8; padding:4px 0; display:inline-block; margin-bottom:12px;">Standard Deployment</span>';
			$btn_bg      = $is_feat ? 'background:' . esc_attr( $primary ) . '; color:#fff;' : 'background:#f8fafc; color:#0f172a; border:1px solid #cbd5e1;';

			$features_list = '';
			foreach ( $p_features as $feat ) {
				$features_list .= '<li style="display:flex; align-items:center; gap:10px; font-size:14px; color:#334155; margin-bottom:10px;"><i class="fa-solid fa-check" style="color:' . esc_attr( $primary ) . '; font-size:13px;"></i> ' . esc_html( $feat ) . '</li>';
			}

			$pricing_cards_html .= '
			<div style="background:#ffffff; border:' . $card_border . '; border-radius:20px; padding:36px 28px; box-shadow:0 15px 35px rgba(0,0,0,0.05); display:flex; flex-direction:column; justify-content:space-between; position:relative;">
				<div>
					' . $badge_html . '
					<h3 style="font-size:22px; font-weight:800; color:#0f172a; margin:0 0 8px 0;">' . esc_html( $p_name ) . '</h3>
					<div style="font-size:38px; font-weight:900; color:' . esc_attr( $secondary ) . '; margin:12px 0 20px 0;">' . esc_html( $p_price ) . '</div>
					<hr style="border:none; border-top:1px solid #f1f5f9; margin:20px 0;">
					<ul style="list-style:none; padding:0; margin:0 0 28px 0;">
						' . $features_list . '
					</ul>
				</div>
				<div>
					<a href="#contact" style="' . $btn_bg . ' width:100%; display:block; text-align:center; padding:14px 0; border-radius:10px; font-weight:700; font-size:15px; text-decoration:none; box-sizing:border-box;">
						Choose ' . esc_html( $p_name ) . '
					</a>
				</div>
			</div>';
		}
		$pricing_cards_html .= '</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'_css_id'               => 'pricing',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 0 auto;">' . esc_html( $subtitle ) . '</p>' . $pricing_cards_html,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build FAQ Section
	 */
	private static function build_faq_section( $sec, $primary, $secondary, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Frequently Asked Questions';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Everything you need to know about deployment, security, and migration.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		if ( empty( $items ) ) {
			$items = array(
				array( 'question' => 'How does the platform integrate with our existing ERP and legal systems?', 'answer' => 'We offer 50+ pre-built native connectors for major ERPs, SAP, Oracle, Workday, and Salesforce, with full bidirectional sync.' ),
				array( 'question' => 'What compliance and security certifications are supported?', 'answer' => 'Our platform is certified for SOC2 Type II, ISO27001, HIPAA, and GDPR compliance, featuring bank-grade AES-256 encryption at rest and in transit.' ),
				array( 'question' => 'Can we customize workflows without custom engineering?', 'answer' => 'Yes, our visual workflow builder allows operations leaders to configure approval chains, risk thresholds, and notification rules with zero code.' ),
				array( 'question' => 'What is the average enterprise onboarding timeframe?', 'answer' => 'Standard enterprise deployments are live within 2 to 4 weeks, supported by our dedicated implementation engineers.' ),
			);
		}

		$accordion_tabs = array();
		foreach ( $items as $item ) {
			$accordion_tabs[] = array(
				'_id'            => self::generate_id(),
				'tab_title'      => ! empty( $item['question'] ) ? esc_html( $item['question'] ) : 'Question',
				'tab_content'    => ! empty( $item['answer'] ) ? esc_html( $item['answer'] ) : 'Answer details.',
			);
		}

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'_css_id'               => 'faq',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 100,
						'padding'      => array( 'unit' => 'px', 'top' => 0, 'right' => 60, 'bottom' => 0, 'left' => 60, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 35px auto;">' . esc_html( $subtitle ) . '</p>',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'accordion',
							'settings'   => array(
								'tabs'              => $accordion_tabs,
								'title_color'       => $secondary,
								'tab_active_color'  => $primary,
								'border_color'      => '#e2e8f0',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build High-Converting Call to Action (CTA) Section
	 */
	private static function build_cta_section( $sec, $primary, $secondary, $accent ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Ready to Transform Your Legal, Risk, and HR Operations?';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Join thousands of leading global enterprises and unite your teams with automated, AI-driven compliance today.';
		$cta_text = ! empty( $sec['cta_text'] ) ? $sec['cta_text'] : 'Schedule an Executive Demo';
		$cta_url  = ! empty( $sec['cta_url'] ) ? $sec['cta_url'] : '#contact';

		$cta_html = '
		<div style="background:linear-gradient(135deg, ' . esc_attr( $secondary ) . ' 0%, #001233 100%); border-radius:24px; padding:70px 40px; text-align:center; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); position:relative; overflow:hidden;">
			<div style="display:inline-block; margin-bottom:16px;">
				<span style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:1px; padding:6px 16px; border-radius:999px;">
					✦ UNLEASH ENTERPRISE AGILITY
				</span>
			</div>
			<h2 style="font-size:42px; font-weight:850; color:#ffffff; max-width:780px; margin:0 auto 16px auto; line-height:1.2; letter-spacing:-0.02em;">
				' . esc_html( $title ) . '
			</h2>
			<p style="font-size:18px; line-height:1.6; color:#94a3b8; max-width:620px; margin:0 auto 36px auto;">
				' . esc_html( $subtitle ) . '
			</p>
			<div style="display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
				<a href="' . esc_url( $cta_url ) . '" style="background:' . esc_attr( $primary ) . '; color:#ffffff; padding:16px 36px; border-radius:10px; font-weight:700; font-size:16px; text-decoration:none; box-shadow:0 10px 25px rgba(0,0,0,0.2); display:inline-flex; align-items:center; gap:8px;">
					' . esc_html( $cta_text ) . ' <i class="fa-solid fa-arrow-right"></i>
				</a>
				<a href="#solutions" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.2); color:#ffffff; padding:15px 30px; border-radius:10px; font-weight:600; font-size:16px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
					Explore All Solutions
				</a>
			</div>
			<div style="display:flex; align-items:center; justify-content:center; gap:24px; margin-top:32px; font-size:13px; color:#64748b; font-weight:600; flex-wrap:wrap;">
				<span style="color:#94a3b8;"><i class="fa-solid fa-check" style="color:' . esc_attr( $primary ) . '; margin-right:6px;"></i> SOC2 Type II Certified</span>
				<span style="color:#94a3b8;"><i class="fa-solid fa-check" style="color:' . esc_attr( $primary ) . '; margin-right:6px;"></i> 14-Day Enterprise Evaluation</span>
				<span style="color:#94a3b8;"><i class="fa-solid fa-check" style="color:' . esc_attr( $primary ) . '; margin-right:6px;"></i> Dedicated Implementation Team</span>
			</div>
		</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#f8fafc',
				'_css_id'               => 'cta',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array( 'editor' => $cta_html ),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Generic / Custom Section Fallback
	 */
	private static function build_generic_section( $sec, $primary, $secondary, $text_dark ) {
		$title   = ! empty( $sec['title'] ) ? $sec['title'] : 'Section Title';
		$content = ! empty( $sec['content'] ) ? $sec['content'] : ( ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Detailed information regarding this section.' );

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 60, 'right' => 20, 'bottom' => 60, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<div style="text-align: center; color: #475569; font-size: 16px; margin-top: 15px;">' . wpautop( esc_html( $content ) ) . '</div>',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Modern Navigation Header / Navbar Section
	 */
	private static function build_header_section( $site_data, $primary, $secondary ) {
		$brand_title = ! empty( $site_data['page_title'] ) ? $site_data['page_title'] : 'Brand';
		$short_brand = explode( '—', $brand_title )[0];
		$short_brand = explode( '-', $short_brand )[0];
		$short_brand = trim( $short_brand );

		$nav_links_html = '<div style="display:flex; justify-content:center; align-items:center; gap:28px; font-weight:600; font-size:15px;">' .
			'<a href="#features" style="color:#475569; text-decoration:none; transition:color 0.2s;">Features</a>' .
			'<a href="#about" style="color:#475569; text-decoration:none; transition:color 0.2s;">About</a>' .
			'<a href="#pricing" style="color:#475569; text-decoration:none; transition:color 0.2s;">Pricing</a>' .
			'<a href="#faq" style="color:#475569; text-decoration:none; transition:color 0.2s;">FAQ</a>' .
			'<a href="#reviews" style="color:#475569; text-decoration:none; transition:color 0.2s;">Reviews</a>' .
			'</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 18, 'right' => 30, 'bottom' => 18, 'left' => 30, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'border_border'         => 'solid',
				'border_width'          => array( 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0, 'isLinked' => false ),
				'border_color'          => '#e2e8f0',
				'_css_id'               => 'navbar',
			),
			'elements' => array(
				// Brand Logo/Name Column
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 30,
						'vertical_align' => 'middle',
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => '<span style="color:' . esc_attr( $primary ) . '; margin-right:4px;">✦</span> ' . esc_html( $short_brand ),
								'header_size' => 'h3',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 22 ),
								'typography_font_weight' => '800',
							),
						),
					),
				),
				// Nav Menu Links Column
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 45,
						'vertical_align' => 'middle',
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => $nav_links_html,
							),
						),
					),
				),
				// CTA Button Column
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 25,
						'vertical_align' => 'middle',
						'align'        => 'right',
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'button',
							'settings'   => array(
								'text'               => 'Get Started',
								'link'               => array( 'url' => '#contact' ),
								'align'              => 'right',
								'size'               => 'sm',
								'button_text_color'  => '#ffffff',
								'background_color'   => $primary,
								'border_radius'      => array( 'unit' => 'px', 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6 ),
								'typography_font_weight' => '600',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Modern Rich Footer Section
	 */
	private static function build_footer_section( $site_data, $primary, $secondary, $text_dark ) {
		$brand_title = ! empty( $site_data['page_title'] ) ? $site_data['page_title'] : 'Brand';
		$short_brand = explode( '—', $brand_title )[0];
		$short_brand = explode( '-', $short_brand )[0];
		$short_brand = trim( $short_brand );
		$year = gmdate( 'Y' );

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 70, 'right' => 30, 'bottom' => 30, 'left' => 30, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#0f172a',
				'_css_id'               => 'footer',
			),
			'elements' => array(
				// Column 1: Brand & Tagline
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 40,
						'padding'      => array( 'unit' => 'px', 'top' => 0, 'right' => 30, 'bottom' => 20, 'left' => 0, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => '<span style="color:' . esc_attr( $primary ) . '; margin-right:4px;">✦</span> ' . esc_html( $short_brand ),
								'header_size' => 'h3',
								'title_color' => '#ffffff',
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 22 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="color:#94a3b8; font-size:14px; line-height:1.7; margin-top:12px;">Next-generation solutions crafted for exceptional digital experiences and conversion excellence.</p>',
							),
						),
					),
				),
				// Column 2: Quick Links
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 30,
						'padding'      => array( 'unit' => 'px', 'top' => 0, 'right' => 20, 'bottom' => 20, 'left' => 20, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => 'Navigation',
								'header_size' => 'h4',
								'title_color' => '#ffffff',
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 16 ),
								'typography_font_weight' => '700',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<div style="display:flex; flex-direction:column; gap:8px; margin-top:12px; font-size:14px;">' .
									'<a href="#features" style="color:#94a3b8; text-decoration:none;">Features & Benefits</a>' .
									'<a href="#about" style="color:#94a3b8; text-decoration:none;">Our Story & Purpose</a>' .
									'<a href="#pricing" style="color:#94a3b8; text-decoration:none;">Pricing Plans</a>' .
									'<a href="#faq" style="color:#94a3b8; text-decoration:none;">FAQ & Support</a>' .
									'</div>',
							),
						),
					),
				),
				// Column 3: Contact & Legal
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 30,
						'padding'      => array( 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 20, 'left' => 20, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => 'Connect',
								'header_size' => 'h4',
								'title_color' => '#ffffff',
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 16 ),
								'typography_font_weight' => '700',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<div style="margin-top:12px; color:#94a3b8; font-size:14px; line-height:1.7;">' .
									'<p style="margin:0 0 10px 0;">Empowering ambitious teams worldwide.</p>' .
									'<p style="margin:0; font-size:12px; color:#64748b;">© ' . esc_html( $year ) . ' ' . esc_html( $short_brand ) . '. All rights reserved.</p>' .
									'</div>',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Interactive Showcase / Slider / Gallery Section
	 */
	private static function build_slider_section( $sec, $primary, $secondary, $accent, $bg_light ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Featured Work & Showcase';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Explore our high-impact digital solutions and client success stories.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$default_slides = array(
			array( 'url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=80', 'id' => 1 ),
			array( 'url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80', 'id' => 2 ),
			array( 'url' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80', 'id' => 3 ),
			array( 'url' => 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=800&auto=format&fit=crop&q=80', 'id' => 4 ),
		);

		$carousel_images = array();
		if ( ! empty( $items ) ) {
			foreach ( $items as $i => $item ) {
				$img_url = ! empty( $item['image_url'] ) ? $item['image_url'] : $default_slides[ $i % count( $default_slides ) ]['url'];
				$carousel_images[] = array( 'url' => $img_url, 'id' => $i + 1 );
			}
		} else {
			$carousel_images = $default_slides;
		}

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'_css_id'               => 'showcase',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						// Header Title
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						// Subtitle
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 40px auto;">' . esc_html( $subtitle ) . '</p>',
							),
						),
						// Interactive Image Carousel Slider
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'image-carousel',
							'settings'   => array(
								'carousel'         => $carousel_images,
								'slides_to_show'   => '3',
								'slides_to_scroll' => '1',
								'autoplay'         => 'yes',
								'autoplay_speed'   => 3000,
								'pause_on_hover'   => 'yes',
								'infinite'         => 'yes',
								'effect'           => 'slide',
								'speed'            => 500,
								'navigation'       => 'both',
								'thumbnail_size'   => 'full',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Interactive Map & Location Headquarters Section
	 */
	private static function build_map_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title     = ! empty( $sec['title'] ) ? $sec['title'] : 'Our Global Headquarters';
		$subtitle  = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Visit our engineering campus or schedule an on-site executive strategy session.';
		$address   = ! empty( $sec['address'] ) ? $sec['address'] : '100 Innovation Boulevard, Tech District, Suite 400';
		$phone     = ! empty( $sec['phone'] ) ? $sec['phone'] : '+1 (555) 234-5678';
		$email     = ! empty( $sec['email'] ) ? $sec['email'] : 'contact@enterprise.com';
		$hours     = ! empty( $sec['hours'] ) ? $sec['hours'] : 'Mon - Fri: 8:30 AM - 6:00 PM';
		$map_query = ! empty( $sec['map_query'] ) ? $sec['map_query'] : 'Silicon Valley, California';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'_css_id'               => 'location',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				// Left Info Column
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 45,
						'padding'      => array( 'unit' => 'px', 'top' => 0, 'right' => 30, 'bottom' => 0, 'left' => 0, 'isLinked' => false ),
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 32 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="color:#64748b; font-size:16px; line-height:1.6; margin-top:12px; margin-bottom:24px;">' . esc_html( $subtitle ) . '</p>' .
									'<div style="display:flex; flex-direction:column; gap:16px; background:#f8fafc; padding:24px; border-radius:16px; border:1px solid #e2e8f0;">' .
									'<div style="display:flex; align-items:flex-start; gap:14px;"><i class="fa-solid fa-location-dot" style="color:' . esc_attr( $primary ) . '; font-size:18px; margin-top:3px;"></i><div><strong style="color:#0f172a; display:block; font-size:14px;">Campus Address</strong><span style="color:#475569; font-size:14px;">' . esc_html( $address ) . '</span></div></div>' .
									'<div style="display:flex; align-items:flex-start; gap:14px;"><i class="fa-solid fa-phone" style="color:' . esc_attr( $primary ) . '; font-size:18px; margin-top:3px;"></i><div><strong style="color:#0f172a; display:block; font-size:14px;">Phone Inquiries</strong><span style="color:#475569; font-size:14px;">' . esc_html( $phone ) . '</span></div></div>' .
									'<div style="display:flex; align-items:flex-start; gap:14px;"><i class="fa-solid fa-envelope" style="color:' . esc_attr( $primary ) . '; font-size:18px; margin-top:3px;"></i><div><strong style="color:#0f172a; display:block; font-size:14px;">Direct Email</strong><span style="color:#475569; font-size:14px;">' . esc_html( $email ) . '</span></div></div>' .
									'<div style="display:flex; align-items:flex-start; gap:14px;"><i class="fa-solid fa-clock" style="color:' . esc_attr( $primary ) . '; font-size:18px; margin-top:3px;"></i><div><strong style="color:#0f172a; display:block; font-size:14px;">Operating Hours</strong><span style="color:#475569; font-size:14px;">' . esc_html( $hours ) . '</span></div></div>' .
									'</div>',
							),
						),
					),
				),
				// Right Map Column
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 55,
					),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'google_maps',
							'settings'   => array(
								'address' => $map_query,
								'zoom'    => array( 'size' => 14 ),
								'height'  => array( 'size' => 420 ),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Interactive Multi-Step Lead Generation & Quote Form Section
	 */
	private static function build_multi_step_form_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Request an Executive Strategy Blueprint';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Complete this quick 3-step intake form to receive a tailored architecture proposal within 24 hours.';
		$cta_text = ! empty( $sec['cta_text'] ) ? $sec['cta_text'] : 'Submit Proposal Request';

		$form_html = '
		<div style="background:#ffffff; border-radius:20px; box-shadow:0 20px 40px -15px rgba(0,0,0,0.08); border:1px solid #e2e8f0; padding:40px; max-width:760px; margin:0 auto;">
			<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; border-bottom:1px solid #f1f5f9; padding-bottom:20px;">
				<div style="display:flex; align-items:center; gap:8px;">
					<span style="width:28px; height:28px; border-radius:50%; background:' . esc_attr( $primary ) . '; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px;">1</span>
					<strong style="color:#0f172a; font-size:14px;">Contact Info</strong>
				</div>
				<span style="color:#cbd5e1;">———</span>
				<div style="display:flex; align-items:center; gap:8px;">
					<span style="width:28px; height:28px; border-radius:50%; background:#e2e8f0; color:#64748b; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px;">2</span>
					<strong style="color:#64748b; font-size:14px;">Project Scope</strong>
				</div>
				<span style="color:#cbd5e1;">———</span>
				<div style="display:flex; align-items:center; gap:8px;">
					<span style="width:28px; height:28px; border-radius:50%; background:#e2e8f0; color:#64748b; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px;">3</span>
					<strong style="color:#64748b; font-size:14px;">Budget & Timeline</strong>
				</div>
			</div>

			<form onsubmit="event.preventDefault(); alert(\'Thank you! Your proposal request has been received.\');" style="display:flex; flex-direction:column; gap:20px;">
				<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
					<div>
						<label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">Full Name *</label>
						<input type="text" required placeholder="Sarah Jenkins" style="width:100%; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; box-sizing:border-box;">
					</div>
					<div>
						<label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">Corporate Email *</label>
						<input type="email" required placeholder="s.jenkins@company.com" style="width:100%; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; box-sizing:border-box;">
					</div>
				</div>

				<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
					<div>
						<label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">Company / Organization</label>
						<input type="text" placeholder="Acme Technologies" style="width:100%; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; box-sizing:border-box;">
					</div>
					<div>
						<label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">Project Type</label>
						<select style="width:100%; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; box-sizing:border-box; background:#fff;">
							<option>Full Digital Transformation</option>
							<option>Custom Cloud Architecture</option>
							<option>AI & Machine Learning Integration</option>
							<option>Mobile & Web Engineering</option>
						</select>
					</div>
				</div>

				<div>
					<label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">Project Objectives & Timeline</label>
					<textarea rows="3" placeholder="Tell us about your core technical requirements, current stack, and target launch window..." style="width:100%; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; box-sizing:border-box;"></textarea>
				</div>

				<div style="text-align:center; margin-top:10px;">
					<button type="submit" style="background:' . esc_attr( $primary ) . '; color:#ffffff; border:none; padding:16px 36px; border-radius:10px; font-weight:700; font-size:16px; cursor:pointer; box-shadow:0 10px 25px rgba(99,102,241,0.25); transition:transform 0.2s;">
						' . esc_html( $cta_text ) . ' <i class="fa-solid fa-arrow-right" style="margin-left:8px;"></i>
					</button>
				</div>
			</form>
		</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => $bg_light,
				'_css_id'               => 'form-step',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 35px auto;">' . esc_html( $subtitle ) . '</p>' . $form_html,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build High-Resolution Visual Project Gallery Section
	 */
	private static function build_gallery_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Featured Execution Gallery';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'A curated portfolio of high-performance builds, scalable designs, and enterprise platforms.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$default_gallery = array(
			array( 'title' => 'Cloud Infrastructure', 'category' => 'Architecture', 'image_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=80' ),
			array( 'title' => 'Mobile Banking Experience', 'category' => 'Mobile App', 'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80' ),
			array( 'title' => 'AI Analytics Hub', 'category' => 'Machine Learning', 'image_url' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80' ),
			array( 'title' => 'SaaS Dashboard Redesign', 'category' => 'UI/UX Strategy', 'image_url' => 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=800&auto=format&fit=crop&q=80' ),
		);

		if ( empty( $items ) ) {
			$items = $default_gallery;
		}

		$gallery_cards_html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:24px; margin-top:30px;">';
		foreach ( $items as $item ) {
			$img = ! empty( $item['image_url'] ) ? $item['image_url'] : $default_gallery[0]['image_url'];
			$item_title = ! empty( $item['title'] ) ? $item['title'] : 'Project Showcase';
			$cat = ! empty( $item['category'] ) ? $item['category'] : 'Digital Solution';

			$gallery_cards_html .= '
			<div style="border-radius:16px; overflow:hidden; background:#ffffff; box-shadow:0 10px 25px rgba(0,0,0,0.06); border:1px solid #e2e8f0; transition:all 0.3s ease;">
				<div style="height:200px; overflow:hidden; position:relative;">
					<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $item_title ) . '" style="width:100%; height:100%; object-fit:cover; transition:transform 0.5s ease;">
					<span style="position:absolute; top:12px; left:12px; background:rgba(15,23,42,0.75); backdrop-filter:blur(4px); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:6px;">' . esc_html( $cat ) . '</span>
				</div>
				<div style="padding:18px;">
					<h4 style="margin:0 0 6px 0; color:#0f172a; font-size:17px; font-weight:700;">' . esc_html( $item_title ) . '</h4>
					<span style="color:' . esc_attr( $primary ) . '; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">View Case Study <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i></span>
				</div>
			</div>';
		}
		$gallery_cards_html .= '</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'_css_id'               => 'gallery',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 20px auto;">' . esc_html( $subtitle ) . '</p>' . $gallery_cards_html,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Leadership & Team Members Showcase Section
	 */
	private static function build_team_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Meet Our Leadership & Engineering Team';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Seasoned executives and principal architects driving technological breakthroughs.';
		$members  = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$default_members = array(
			array( 'name' => 'Alexander Hayes', 'role' => 'Founder & CEO', 'bio' => '15+ years scaling enterprise architectures and distributed software platforms.', 'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80' ),
			array( 'name' => 'Dr. Elena Rostova', 'role' => 'Head of AI Research', 'bio' => 'PhD in Computer Vision specializing in multi-modal generative intelligence.', 'image_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=500&auto=format&fit=crop&q=80' ),
			array( 'name' => 'Marcus Vance', 'role' => 'VP of Cloud Infrastructure', 'bio' => 'Specialist in zero-latency microservices, Kubernetes, and global hybrid cloud.', 'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&auto=format&fit=crop&q=80' ),
		);

		if ( empty( $members ) ) {
			$members = $default_members;
		}

		$team_cards_html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:24px; margin-top:35px;">';
		foreach ( $members as $m ) {
			$name = ! empty( $m['name'] ) ? $m['name'] : 'Team Member';
			$role = ! empty( $m['role'] ) ? $m['role'] : 'Principal Engineer';
			$bio  = ! empty( $m['bio'] ) ? $m['bio'] : 'Dedicated to software excellence and scalable systems.';
			$img  = ! empty( $m['image_url'] ) ? $m['image_url'] : $default_members[0]['image_url'];

			$team_cards_html .= '
			<div style="background:#ffffff; border-radius:18px; padding:28px; text-align:center; border:1px solid #e2e8f0; box-shadow:0 10px 25px rgba(0,0,0,0.05); transition:all 0.3s ease;">
				<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $name ) . '" style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:16px; border:3px solid ' . esc_attr( $primary ) . ';">
				<h4 style="margin:0 0 4px 0; font-size:18px; font-weight:700; color:#0f172a;">' . esc_html( $name ) . '</h4>
				<span style="color:' . esc_attr( $primary ) . '; font-size:13px; font-weight:600; display:block; margin-bottom:12px;">' . esc_html( $role ) . '</span>
				<p style="color:#64748b; font-size:13.5px; line-height:1.6; margin:0 0 16px 0;">' . esc_html( $bio ) . '</p>
				<div style="display:flex; justify-content:center; gap:12px; color:#94a3b8; font-size:15px;"><i class="fa-brands fa-linkedin"></i><i class="fa-brands fa-x-twitter"></i><i class="fa-brands fa-github"></i></div>
			</div>';
		}
		$team_cards_html .= '</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => $bg_light,
				'_css_id'               => 'team',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 20px auto;">' . esc_html( $subtitle ) . '</p>' . $team_cards_html,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Ecosystem & Tool Integrations Section
	 */
	private static function build_integrations_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Enterprise Ecosystem & Tool Connectors';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Connect seamlessly with 50+ enterprise services, databases, and CI/CD pipelines.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$default_tools = array(
			array( 'name' => 'Slack & Teams', 'desc' => 'Real-time incident and event alerts delivered directly to your chat channels.' ),
			array( 'name' => 'Salesforce & HubSpot', 'desc' => 'Instant bi-directional customer sync and pipeline automation.' ),
			array( 'name' => 'GitHub & GitLab', 'desc' => 'Automated deployment pipelines and PR preview environments.' ),
			array( 'name' => 'AWS & Google Cloud', 'desc' => 'Multi-region cloud infrastructure with auto-scaling microservices.' ),
			array( 'name' => 'Stripe & Paddle', 'desc' => 'Global payment gateways, tax compliance, and automated billing sync.' ),
			array( 'name' => 'PostgreSQL & Snowflake', 'desc' => 'High-throughput data pipelines and enterprise analytics data lakes.' ),
		);

		if ( empty( $items ) ) {
			$items = $default_tools;
		}

		$cards_html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-top:35px;">';
		foreach ( $items as $tool ) {
			$name = ! empty( $tool['name'] ) ? $tool['name'] : ( ! empty( $tool['title'] ) ? $tool['title'] : 'Integration Tool' );
			$desc = ! empty( $tool['desc'] ) ? $tool['desc'] : ( ! empty( $tool['description'] ) ? $tool['description'] : 'Seamless 1-click cloud sync and webhook support.' );

			$cards_html .= '
			<div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:22px; display:flex; align-items:flex-start; gap:16px; box-shadow:0 4px 15px rgba(0,0,0,0.03); transition:all 0.2s ease;">
				<div style="width:40px; height:40px; border-radius:10px; background:' . esc_attr( $primary ) . '15; color:' . esc_attr( $primary ) . '; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;"><i class="fa-solid fa-bolt"></i></div>
				<div>
					<h4 style="margin:0 0 4px 0; font-size:16px; font-weight:700; color:#0f172a;">' . esc_html( $name ) . '</h4>
					<p style="margin:0; font-size:13px; color:#64748b; line-height:1.5;">' . esc_html( $desc ) . '</p>
				</div>
			</div>';
		}
		$cards_html .= '</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
				'_css_id'               => 'integrations',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 20px auto;">' . esc_html( $subtitle ) . '</p>' . $cards_html,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Step-by-Step Roadmap & Timeline Section
	 */
	private static function build_timeline_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Execution Roadmap & Milestones';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'A phased blueprint for predictable, high-speed project delivery.';
		$steps    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$default_steps = array(
			array( 'phase' => 'Phase 01', 'title' => 'Discovery & Technical Audit', 'description' => 'Comprehensive analysis of existing architecture, requirements, and KPI goals.' ),
			array( 'phase' => 'Phase 02', 'title' => 'Prototyping & Architecture Design', 'description' => 'High-fidelity wireframes, UI design systems, and cloud schema definition.' ),
			array( 'phase' => 'Phase 03', 'title' => 'Engineering & Continuous Integration', 'description' => 'Agile sprints with automated test coverage and weekly staging deployments.' ),
			array( 'phase' => 'Phase 04', 'title' => 'Production Launch & Scale', 'description' => 'Zero-downtime deployment, performance tuning, and 24/7 observability monitoring.' ),
		);

		if ( empty( $steps ) ) {
			$steps = $default_steps;
		}

		$timeline_html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:24px; margin-top:40px;">';
		foreach ( $steps as $i => $st ) {
			$phase = ! empty( $st['phase'] ) ? $st['phase'] : 'Step 0' . ( $i + 1 );
			$st_title = ! empty( $st['title'] ) ? $st['title'] : 'Milestone Objective';
			$st_desc  = ! empty( $st['description'] ) ? $st['description'] : 'Comprehensive execution step.';

			$timeline_html .= '
			<div style="background:#ffffff; border-radius:16px; padding:26px; border:1px solid #e2e8f0; position:relative; box-shadow:0 8px 20px rgba(0,0,0,0.04);">' .
				'<span style="background:' . esc_attr( $primary ) . '; color:#ffffff; font-size:11px; font-weight:800; padding:4px 10px; border-radius:999px; display:inline-block; margin-bottom:14px;">' . esc_html( $phase ) . '</span>' .
				'<h4 style="margin:0 0 8px 0; font-size:17px; font-weight:700; color:#0f172a;">' . esc_html( $st_title ) . '</h4>' .
				'<p style="margin:0; font-size:13.5px; color:#64748b; line-height:1.6;">' . esc_html( $st_desc ) . '</p>' .
			'</div>';
		}
		$timeline_html .= '</div>';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => $bg_light,
				'_css_id'               => 'roadmap',
				'_animation'            => 'fadeInUp',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 36 ),
								'typography_font_weight' => '800',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center; color:#64748b; font-size:17px; max-width:650px; margin:12px auto 20px auto;">' . esc_html( $subtitle ) . '</p>' . $timeline_html,
							),
						),
					),
				),
			),
		);
	}
}
