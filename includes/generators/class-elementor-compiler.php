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
	private static function build_hero_section( $sec, $primary, $secondary, $accent, $text_dark ) {
		$title       = ! empty( $sec['title'] ) ? $sec['title'] : 'Build Something Amazing Today';
		$subtitle    = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'The next-generation platform for visionary teams and high-growth businesses.';
		$cta_text    = ! empty( $sec['cta_text'] ) ? $sec['cta_text'] : 'Get Started Now';
		$cta_url     = ! empty( $sec['cta_url'] ) ? $sec['cta_url'] : '#contact';
		$image_url   = ! empty( $sec['image_url'] ) ? $sec['image_url'] : 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1200&auto=format&fit=crop&q=80';
		$badge       = ! empty( $sec['badge'] ) ? $sec['badge'] : '🚀 Introducing OmniCraft AI';

		$left_widgets = array(
			// Badge
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'      => '<span style="display:inline-block; padding: 6px 16px; background: rgba(99,102,241,0.12); color: ' . esc_attr( $primary ) . '; border-radius: 999px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">' . esc_html( $badge ) . '</span>',
					'header_size'=> 'span',
				),
			),
			// Main Title
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'          => esc_html( $title ),
					'header_size'    => 'h1',
					'title_color'    => $secondary,
					'typography_typography' => 'custom',
					'typography_font_size'  => array( 'unit' => 'px', 'size' => 48 ),
					'typography_line_height'=> array( 'unit' => 'em', 'size' => 1.15 ),
					'typography_font_weight'=> '800',
				),
			),
			// Subtitle
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor'     => '<p style="font-size: 18px; line-height: 1.6; color: #64748b; margin-top: 10px;">' . esc_html( $subtitle ) . '</p>',
				),
			),
			// CTA Button
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'button',
				'settings'   => array(
					'text'            => esc_html( $cta_text ),
					'link'            => array( 'url' => esc_url_raw( $cta_url ) ),
					'size'            => 'lg',
					'button_text_color'=> '#ffffff',
					'background_color'=> $primary,
					'border_radius'   => array( 'unit' => 'px', 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8 ),
					'typography_font_weight' => '600',
					'hover_animation' => 'grow',
				),
			),
		);

		$right_widgets = array(
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'image',
				'settings'   => array(
					'image'         => array(
						'url' => esc_url_raw( $image_url ),
						'id'  => '',
					),
					'image_size'    => 'full',
					'border_radius' => array( 'unit' => 'px', 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ),
					'box_shadow_box_shadow_type' => 'yes',
					'box_shadow_box_shadow'      => array( 'horizontal' => 0, 'vertical' => 20, 'blur' => 40, 'spread' => -10, 'color' => 'rgba(0,0,0,0.15)' ),
					'_animation'    => 'fadeInRight',
					'_animation_delay' => 200,
				),
			),
		);

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'          => 'boxed',
				'padding'         => array( 'unit' => 'px', 'top' => 90, 'right' => 20, 'bottom' => 90, 'left' => 20, 'isLinked' => false ),
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
						'_column_size'   => 50,
						'vertical_align' => 'middle',
						'padding'        => array( 'unit' => 'px', 'top' => 10, 'right' => 30, 'bottom' => 10, 'left' => 10, 'isLinked' => false ),
						'_animation'     => 'fadeInLeft',
					),
					'elements' => $left_widgets,
				),
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size'   => 50,
						'vertical_align' => 'middle',
						'padding'        => array( 'unit' => 'px', 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 30, 'isLinked' => false ),
					),
					'elements' => $right_widgets,
				),
			),
		);
	}

	/**
	 * Build Features Grid Section
	 */
	private static function build_features_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Powerful Features Designed for Scale';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Everything you need to grow and succeed, unified in one clean experience.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		if ( empty( $items ) ) {
			$items = array(
				array( 'title' => 'Lightning Fast Performance', 'description' => 'Engineered for speed, stability, and unparalleled optimization.' ),
				array( 'title' => 'Intuitive Modern Design', 'description' => 'Effortlessly intuitive interfaces crafted with precision and care.' ),
				array( 'title' => 'Enterprise Security', 'description' => 'Bank-grade encryption, role-based protection, and 24/7 reliability.' ),
			);
		}

		// Top Heading Section
		$header_widgets = array(
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'          => esc_html( $title ),
					'header_size'    => 'h2',
					'align'          => 'center',
					'title_color'    => $secondary,
					'typography_typography' => 'custom',
					'typography_font_size'  => array( 'unit' => 'px', 'size' => 36 ),
					'typography_font_weight'=> '700',
				),
			),
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p style="text-align: center; max-width: 680px; margin: 0 auto 40px auto; color: #64748b; font-size: 17px;">' . esc_html( $subtitle ) . '</p>',
				),
			),
		);

		// Build columns for features (up to 3 columns per row)
		$columns = array();
		$col_count = count( $items );
		$col_size = $col_count >= 3 ? 33.33 : ( $col_count === 2 ? 50 : 100 );

		foreach ( array_slice( $items, 0, 3 ) as $item ) {
			$f_title = ! empty( $item['title'] ) ? $item['title'] : 'Feature Benefit';
			$f_desc  = ! empty( $item['description'] ) ? $item['description'] : 'Comprehensive description of the feature benefit.';

			$columns[] = array(
				'id'       => self::generate_id(),
				'elType'   => 'column',
				'settings' => array(
					'_column_size' => $col_size,
					'padding'      => array( 'unit' => 'px', 'top' => 32, 'right' => 28, 'bottom' => 32, 'left' => 28, 'isLinked' => false ),
					'background_background' => 'classic',
					'background_color'      => '#ffffff',
					'border_radius'         => array( 'unit' => 'px', 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12 ),
					'box_shadow_box_shadow_type' => 'yes',
					'box_shadow_box_shadow' => array( 'horizontal' => 0, 'vertical' => 8, 'blur' => 20, 'spread' => -4, 'color' => 'rgba(0,0,0,0.06)' ),
				),
				'elements' => array(
					array(
						'id'         => self::generate_id(),
						'elType'     => 'widget',
						'widgetType' => 'icon-box',
						'settings'   => array(
							'title_text'        => esc_html( $f_title ),
							'description_text'  => esc_html( $f_desc ),
							'selected_icon'     => array( 'value' => 'fas fa-check-circle', 'library' => 'fa-solid' ),
							'primary_color'     => $primary,
							'title_color'       => $secondary,
							'description_color' => '#64748b',
							'position'          => 'top',
							'title_size'        => 'h4',
						),
					),
				),
			);
		}

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => $bg_light,
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => $header_widgets,
				),
			),
			'inner_sections' => array(), // Nested elements in standard Elementor format are handled as sequential columns or inner sections
		);
	}

	/**
	 * Build Testimonials Section
	 */
	private static function build_testimonials_section( $sec, $primary, $secondary, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Trusted by Leading Brands';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Discover why top industry leaders choose our platform.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		if ( empty( $items ) ) {
			$items = array(
				array( 'quote' => 'This solution revolutionized our workflow within 24 hours. Truly game-changing.', 'author' => 'Sarah Jenkins', 'role' => 'VP of Product' ),
				array( 'quote' => 'Outstanding speed, clean design, and reliable performance. Highly recommended.', 'author' => 'Michael Chang', 'role' => 'Founder & CEO' ),
				array( 'quote' => 'The return on investment was immediate. Our conversions increased by 40%.', 'author' => 'Elena Rostova', 'role' => 'Marketing Director' ),
			);
		}

		$header_widgets = array(
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'       => esc_html( $title ),
					'header_size' => 'h2',
					'align'       => 'center',
					'title_color' => $secondary,
					'typography_font_size' => array( 'unit' => 'px', 'size' => 36 ),
					'typography_font_weight' => '700',
				),
			),
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p style="text-align: center; color: #64748b; font-size: 17px; margin-bottom: 40px;">' . esc_html( $subtitle ) . '</p>',
				),
			),
		);

		$columns = array();
		foreach ( array_slice( $items, 0, 3 ) as $item ) {
			$quote  = ! empty( $item['quote'] ) ? $item['quote'] : 'Incredible product and experience!';
			$author = ! empty( $item['author'] ) ? $item['author'] : 'Satisfied Customer';
			$role   = ! empty( $item['role'] ) ? $item['role'] : 'Executive';

			$columns[] = array(
				'id'       => self::generate_id(),
				'elType'   => 'column',
				'settings' => array(
					'_column_size' => 33.33,
					'padding'      => array( 'unit' => 'px', 'top' => 28, 'right' => 24, 'bottom' => 28, 'left' => 24, 'isLinked' => false ),
					'background_background' => 'classic',
					'background_color'      => '#ffffff',
					'border_radius'         => array( 'unit' => 'px', 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12 ),
					'box_shadow_box_shadow_type' => 'yes',
					'box_shadow_box_shadow' => array( 'horizontal' => 0, 'vertical' => 4, 'blur' => 16, 'spread' => -2, 'color' => 'rgba(0,0,0,0.05)' ),
				),
				'elements' => array(
					array(
						'id'         => self::generate_id(),
						'elType'     => 'widget',
						'widgetType' => 'testimonial',
						'settings'   => array(
							'testimonial_content' => esc_html( $quote ),
							'testimonial_name'    => esc_html( $author ),
							'testimonial_job'     => esc_html( $role ),
							'testimonial_text_color' => '#334155',
							'testimonial_name_color' => $secondary,
							'testimonial_job_color'  => $primary,
						),
					),
				),
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
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => $header_widgets,
				),
			),
		);
	}

	/**
	 * Build Pricing Section
	 */
	private static function build_pricing_section( $sec, $primary, $secondary, $accent, $bg_light, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Simple, Transparent Pricing';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Choose the plan that fits your ambition. Upgrade or cancel anytime.';
		$plans    = ! empty( $sec['plans'] ) && is_array( $sec['plans'] ) ? $sec['plans'] : array();

		if ( empty( $plans ) ) {
			$plans = array(
				array( 'name' => 'Starter', 'price' => '$29/mo', 'features' => array( 'Up to 5 Projects', 'Standard Analytics', 'Community Support' ), 'is_featured' => false ),
				array( 'name' => 'Professional', 'price' => '$79/mo', 'features' => array( 'Unlimited Projects', 'Advanced AI Engine', 'Priority 24/7 Support', 'Custom Branding' ), 'is_featured' => true ),
				array( 'name' => 'Enterprise', 'price' => '$199/mo', 'features' => array( 'Dedicated Infrastructure', 'Custom SLA', 'White-label APIs', 'Dedicated Account Manager' ), 'is_featured' => false ),
			);
		}

		$header_widgets = array(
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'       => esc_html( $title ),
					'header_size' => 'h2',
					'align'       => 'center',
					'title_color' => $secondary,
					'typography_font_size' => array( 'unit' => 'px', 'size' => 36 ),
					'typography_font_weight' => '700',
				),
			),
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p style="text-align: center; color: #64748b; font-size: 17px; margin-bottom: 40px;">' . esc_html( $subtitle ) . '</p>',
				),
			),
		);

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => $bg_light,
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => $header_widgets,
				),
			),
		);
	}

	/**
	 * Build FAQ Section
	 */
	private static function build_faq_section( $sec, $primary, $secondary, $text_dark ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Frequently Asked Questions';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Got questions? We have answers.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		if ( empty( $items ) ) {
			$items = array(
				array( 'question' => 'How quickly can I launch my site?', 'answer' => 'You can generate and publish your complete site in less than 60 seconds.' ),
				array( 'question' => 'Can I edit the generated content in Elementor?', 'answer' => 'Yes, everything is 100% native Elementor elements that can be customized with drag-and-drop.' ),
				array( 'question' => 'Are placeholder images included?', 'answer' => 'Yes, contextual high-resolution imagery is automatically sourced and placed.' ),
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

		$widgets = array(
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'       => esc_html( $title ),
					'header_size' => 'h2',
					'align'       => 'center',
					'title_color' => $secondary,
					'typography_font_size' => array( 'unit' => 'px', 'size' => 36 ),
					'typography_font_weight' => '700',
				),
			),
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p style="text-align: center; color: #64748b; font-size: 17px; margin-bottom: 30px;">' . esc_html( $subtitle ) . '</p>',
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
		);

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 100,
						'padding'      => array( 'unit' => 'px', 'top' => 0, 'right' => 80, 'bottom' => 0, 'left' => 80, 'isLinked' => false ),
					),
					'elements' => $widgets,
				),
			),
		);
	}

	/**
	 * Build Call to Action (CTA) Section
	 */
	private static function build_cta_section( $sec, $primary, $secondary, $accent ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Ready to Transform Your Online Presence?';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Join thousands of satisfied founders and scale your vision effortlessly today.';
		$cta_text = ! empty( $sec['cta_text'] ) ? $sec['cta_text'] : 'Get Started for Free';
		$cta_url  = ! empty( $sec['cta_url'] ) ? $sec['cta_url'] : '#contact';

		$widgets = array(
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'       => esc_html( $title ),
					'header_size' => 'h2',
					'align'       => 'center',
					'title_color' => '#ffffff',
					'typography_font_size'   => array( 'unit' => 'px', 'size' => 40 ),
					'typography_font_weight' => '800',
				),
			),
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p style="text-align: center; color: #e2e8f0; font-size: 19px; max-width: 600px; margin: 0 auto 30px auto;">' . esc_html( $subtitle ) . '</p>',
				),
			),
			array(
				'id'         => self::generate_id(),
				'elType'     => 'widget',
				'widgetType' => 'button',
				'settings'   => array(
					'text'               => esc_html( $cta_text ),
					'link'               => array( 'url' => esc_url_raw( $cta_url ) ),
					'align'              => 'center',
					'size'               => 'lg',
					'button_text_color'  => $secondary,
					'background_color'   => '#ffffff',
					'border_radius'      => array( 'unit' => 'px', 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8 ),
					'typography_font_weight' => '700',
				),
			),
		);

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 30, 'bottom' => 80, 'left' => 30, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => $primary,
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => $widgets,
				),
			),
		);
	}

	/**
	 * Build About / Split Section
	 */
	private static function build_about_section( $sec, $primary, $secondary, $text_dark ) {
		$title     = ! empty( $sec['title'] ) ? $sec['title'] : 'Our Story & Purpose';
		$content   = ! empty( $sec['content'] ) ? $sec['content'] : 'We build innovative tools to empower modern brands to communicate effectively, convert traffic, and scale faster than ever.';
		$image_url = ! empty( $sec['image_url'] ) ? $sec['image_url'] : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1000&auto=format&fit=crop&q=80';

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 80, 'right' => 20, 'bottom' => 80, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => '#ffffff',
			),
			'elements' => array(
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 50, 'padding' => array( 'unit' => 'px', 'top' => 10, 'right' => 30, 'bottom' => 10, 'left' => 10, 'isLinked' => false ) ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'image',
							'settings'   => array(
								'image'         => array( 'url' => esc_url_raw( $image_url ) ),
								'image_size'    => 'full',
								'border_radius' => array( 'unit' => 'px', 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12 ),
							),
						),
					),
				),
				array(
					'id'       => self::generate_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 50, 'padding' => array( 'unit' => 'px', 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 30, 'isLinked' => false ) ),
					'elements' => array(
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'       => esc_html( $title ),
								'header_size' => 'h2',
								'title_color' => $secondary,
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 34 ),
								'typography_font_weight' => '700',
							),
						),
						array(
							'id'         => self::generate_id(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<div style="font-size: 16px; line-height: 1.7; color: #475569; margin-top: 15px;">' . wpautop( esc_html( $content ) ) . '</div>',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Build Stats Section
	 */
	private static function build_stats_section( $sec, $primary, $secondary, $bg_light ) {
		$items = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();
		if ( empty( $items ) ) {
			$items = array(
				array( 'number' => '99.9', 'suffix' => '%', 'title' => 'Uptime SLA' ),
				array( 'number' => '500', 'suffix' => 'K+', 'title' => 'Happy Users' ),
				array( 'number' => '24', 'suffix' => '/7', 'title' => 'Expert Support' ),
				array( 'number' => '10', 'suffix' => 'x', 'title' => 'Faster Deployments' ),
			);
		}

		$columns = array();
		$col_size = 100 / max( 1, count( $items ) );

		foreach ( $items as $item ) {
			$columns[] = array(
				'id'       => self::generate_id(),
				'elType'   => 'column',
				'settings' => array( '_column_size' => $col_size, 'align' => 'center' ),
				'elements' => array(
					array(
						'id'         => self::generate_id(),
						'elType'     => 'widget',
						'widgetType' => 'counter',
						'settings'   => array(
							'starting_number' => 0,
							'ending_number'   => (int) ( ! empty( $item['number'] ) ? floatval( $item['number'] ) : 100 ),
							'suffix'          => ! empty( $item['suffix'] ) ? $item['suffix'] : '+',
							'title'           => ! empty( $item['title'] ) ? $item['title'] : 'Metric',
							'number_color'    => $primary,
							'title_color'     => $secondary,
						),
					),
				),
			);
		}

		return array(
			'id'       => self::generate_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'                => 'boxed',
				'padding'               => array( 'unit' => 'px', 'top' => 50, 'right' => 20, 'bottom' => 50, 'left' => 20, 'isLinked' => false ),
				'background_background' => 'classic',
				'background_color'      => $bg_light,
			),
			'elements' => $columns,
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
