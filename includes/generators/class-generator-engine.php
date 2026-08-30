<?php
/**
 * Main AI Generation Engine and Pipeline Orchestrator.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/providers/class-provider-factory.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-url-scraper.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-credits-manager.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/class-history-logger.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/generators/class-elementor-compiler.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/generators/class-gutenberg-compiler.php';
require_once OMNICRAFT_AI_PLUGIN_DIR . 'includes/generators/class-page-creator.php';

class OmniCraft_AI_Generator_Engine {

	/**
	 * Run full generation pipeline.
	 *
	 * @param array $params
	 * @return array|WP_Error
	 */
	public static function generate( $params = array() ) {
		@set_time_limit( 180 );
		$user_id = get_current_user_id() ? get_current_user_id() : 1;

		// 1. Check Credits / Limits
		$limit_check = OmniCraft_AI_Credits_Manager::check_user_limit( $user_id );
		if ( ! $limit_check['allowed'] ) {
			return new WP_Error( 'limit_reached', $limit_check['message'] );
		}

		$prompt             = ! empty( $params['prompt'] ) ? sanitize_textarea_field( $params['prompt'] ) : '';
		$target_url         = ! empty( $params['target_url'] ) ? esc_url_raw( $params['target_url'] ) : '';
		$screenshot_base64  = ! empty( $params['screenshot_base64'] ) ? sanitize_text_field( $params['screenshot_base64'] ) : '';
		$screenshot_mime    = ! empty( $params['screenshot_mime'] ) ? sanitize_mime_type( $params['screenshot_mime'] ) : 'image/png';
		$builder_type       = ! empty( $params['builder_type'] ) ? sanitize_text_field( $params['builder_type'] ) : 'elementor';
		$tone               = ! empty( $params['tone'] ) ? sanitize_text_field( $params['tone'] ) : 'Modern & Conversational';
		$color_preset       = ! empty( $params['color_preset'] ) ? sanitize_text_field( $params['color_preset'] ) : 'indigo';
		$custom_title       = ! empty( $params['page_title'] ) ? sanitize_text_field( $params['page_title'] ) : '';
		$provider_override  = ! empty( $params['provider'] ) ? sanitize_text_field( $params['provider'] ) : null;

		if ( empty( $prompt ) && empty( $target_url ) && empty( $screenshot_base64 ) ) {
			return new WP_Error( 'empty_input', __( 'Please provide at least one input: a text description, reference website URL, or screenshot.', 'omnicraft-ai-builder' ) );
		}

		// Determine input classification
		$input_type = 'text';
		if ( ! empty( $prompt ) && ( ! empty( $target_url ) || ! empty( $screenshot_base64 ) ) ) {
			$input_type = 'hybrid';
		} elseif ( ! empty( $screenshot_base64 ) ) {
			$input_type = 'screenshot';
		} elseif ( ! empty( $target_url ) ) {
			$input_type = 'url';
		}

		// 2. Scrape reference URL if provided
		$scraped_data = null;
		if ( ! empty( $target_url ) ) {
			$scraped_result = OmniCraft_AI_URL_Scraper::scrape( $target_url );
			if ( ! is_wp_error( $scraped_result ) ) {
				$scraped_data = $scraped_result;
			}
		}

		// 3. Select & Instantiate Provider
		$provider = OmniCraft_AI_Provider_Factory::create( $provider_override );
		if ( is_wp_error( $provider ) ) {
			return $provider;
		}

		$custom_primary   = ! empty( $params['custom_primary'] ) ? sanitize_hex_color( $params['custom_primary'] ) : '';
		$custom_secondary = ! empty( $params['custom_secondary'] ) ? sanitize_hex_color( $params['custom_secondary'] ) : '';
		$selected_sections = ! empty( $params['selected_sections'] ) && is_array( $params['selected_sections'] ) ? array_map( 'sanitize_text_field', $params['selected_sections'] ) : array();

		// 4. Construct Multi-Modal Prompt
		$system_prompt = self::build_system_prompt();
		$user_prompt   = self::build_user_prompt( array(
			'prompt'            => $prompt,
			'scraped_data'      => $scraped_data,
			'has_image'         => ! empty( $screenshot_base64 ),
			'tone'              => $tone,
			'color_preset'      => $color_preset,
			'custom_primary'    => $custom_primary,
			'custom_secondary'  => $custom_secondary,
			'builder_type'      => $builder_type,
			'selected_sections' => $selected_sections,
		) );

		$call_options = array(
			'temperature' => 0.7,
			'max_tokens'  => 4096,
		);

		if ( ! empty( $screenshot_base64 ) ) {
			$call_options['image_base64'] = $screenshot_base64;
			$call_options['image_mime']   = $screenshot_mime;
		}

		// 5. Call LLM
		$llm_response = $provider->generate( $system_prompt, $user_prompt, $call_options );
		if ( is_wp_error( $llm_response ) ) {
			return $llm_response;
		}

		// 6. Clean & Parse JSON
		$site_data = self::parse_llm_json( $llm_response );
		if ( is_wp_error( $site_data ) ) {
			return $site_data;
		}

		// Apply custom color overrides if specified by user
		if ( ! empty( $custom_primary ) ) {
			$site_data['color_palette']['primary'] = $custom_primary;
		}
		if ( ! empty( $custom_secondary ) ) {
			$site_data['color_palette']['secondary'] = $custom_secondary;
		}

		// 7. Determine Final Page Title
		$page_title = ! empty( $custom_title ) ? $custom_title : ( ! empty( $site_data['page_title'] ) ? $site_data['page_title'] : 'AI Generated Landing Page' );

		// 8. Compile Page Structure with Selected Section Filters
		if ( 'elementor' === $builder_type ) {
			$compiled_content = OmniCraft_AI_Elementor_Compiler::compile( $site_data, $selected_sections );
		} else {
			$compiled_content = OmniCraft_AI_Gutenberg_Compiler::compile( $site_data, $selected_sections );
		}

		// 9. Save Page into WordPress
		$page_id = OmniCraft_AI_Page_Creator::create_page( $page_title, $builder_type, $compiled_content );
		if ( is_wp_error( $page_id ) ) {
			return $page_id;
		}

		// 10. Increment Credit Quota
		OmniCraft_AI_Credits_Manager::increment_usage( $user_id );

		// 11. Log Generation to History
		$history_id = OmniCraft_AI_History_Logger::log_generation( array(
			'user_id'        => $user_id,
			'page_id'        => $page_id,
			'page_title'     => $page_title,
			'builder_type'   => $builder_type,
			'input_type'     => $input_type,
			'provider'       => get_class( $provider ),
			'model'          => $provider->get_name(),
			'prompt_summary' => ! empty( $prompt ) ? substr( $prompt, 0, 300 ) : ( ! empty( $target_url ) ? 'Ref URL: ' . $target_url : 'Screenshot Vision' ),
			'target_url'     => $target_url,
			'screenshot_url' => '',
		) );

		$elementor_edit_url = admin_url( 'post.php?post=' . $page_id . '&action=elementor' );
		$classic_edit_url   = get_edit_post_link( $page_id, 'raw' );
		$view_url           = get_permalink( $page_id );

		return array(
			'success'            => true,
			'page_id'            => $page_id,
			'page_title'         => $page_title,
			'builder_type'       => $builder_type,
			'view_url'           => $view_url,
			'edit_url'           => $classic_edit_url,
			'elementor_edit_url' => $elementor_edit_url,
			'site_data'          => $site_data,
			'remaining_credits'  => OmniCraft_AI_Credits_Manager::check_user_limit( $user_id )['remaining'],
			'message'            => __( 'Your website was generated successfully!', 'omnicraft-ai-builder' ),
		);
	}

	/**
	 * Build dynamic system prompt ensuring rich, multi-section architecture with animations and reference fidelity.
	 */
	private static function build_system_prompt() {
		return 'You are a world-class principal website architect, UX designer, and conversion copywriter.
Your task is to generate complete, highly detailed, production-ready landing page data in STRICT JSON format tailored specifically to the user\'s business concept, reference website, and design preferences.

CRITICAL ARCHITECTURAL MANDATES:
1. COMPREHENSIVE LENGTH (MANDATORY): You MUST generate AT LEAST 7 to 9 full-length, richly developed body sections (excluding Header & Footer). Never return a shallow 3 or 4 section site!
2. SECTION DIVERSITY: Every landing page must include a rich mix of:
   - Hero Section (compelling H1 headline, animated badge, 2-sentence value prop, CTA button, high-res dashboard/product photo)
   - Stats / Performance Metrics (4 key quantitative achievements & counters e.g. 99.9% uptime, 500k+ users, 24/7 sync, 10x ROI)
   - Core Features / Solutions Grid (3 to 6 distinct capability cards with crisp benefit descriptions)
   - Deep-Dive Services / Technology Architecture (detailed breakdown of capabilities, workflows, or specialized offerings)
   - Interactive Showcase / Project Slider (3 to 5 visual project slides, case studies, or product angles)
   - About / Our Story / Trust & Mission (2-column narrative with high-res workspace/team imagery)
   - Client Reviews & Social Proof (3 verified testimonials with full name, executive role, company, and enthusiastic quotes)
   - Pricing / Packages / Tiers (3 transparent tiers with feature checklists, popular ribbons, and clear CTA buttons)
   - Interactive FAQ Accordion (4 to 6 detailed, realistic, domain-specific questions & answers)
   - High-Converting Final CTA Banner (urgent closing proposition with direct action button)
3. REFERENCE WEBSITE FIDELITY: When reference website data (scraped headings, services, copy) is provided, you MUST adopt its real services, industry terminology, architectural tone, and color identity.
4. ANIMATION & MOTION FOCUS: Design every section to feel dynamic, premium, and alive with interactive badges, metric counters, project sliders, and floating cards.
5. STRICT JSON ONLY: Return ONLY raw, valid JSON. Do NOT wrap in markdown code blocks or add conversational preamble.

The JSON schema MUST follow this structure:
{
  "page_title": "string (Catchy Brand Name & Tagline)",
  "color_palette": {
    "primary": "#hex (Vibrant accent brand color)",
    "secondary": "#hex (Dark heading/text color)",
    "accent": "#hex (Highlight glow color)",
    "bg_light": "#f8fafc",
    "text_dark": "#0f172a"
  },
  "sections": [
    {
      "type": "hero",
      "badge": "✦ High-Impact Badge Tag",
      "title": "Compelling, Powerful H1 Headline",
      "subtitle": "Clear, persuasive value proposition explaining how it solves customer pain points.",
      "cta_text": "Primary Action CTA",
      "cta_url": "#contact",
      "image_url": "https://images.unsplash.com/photo-... (relevant high quality photo)"
    },
    {
      "type": "stats",
      "items": [
        { "number": "99.9", "suffix": "%", "title": "Uptime / Accuracy" },
        { "number": "500", "suffix": "k+", "title": "Active Users" },
        { "number": "24", "suffix": "/7", "title": "Real-Time Sync" },
        { "number": "10", "suffix": "x", "title": "Performance Boost" }
      ]
    },
    {
      "type": "features",
      "title": "Core Capabilities & Advantages",
      "subtitle": "Everything you need to accelerate growth and outperform competition.",
      "items": [
        { "title": "Feature 1", "description": "Detailed benefit description." },
        { "title": "Feature 2", "description": "Detailed benefit description." },
        { "title": "Feature 3", "description": "Detailed benefit description." }
      ]
    },
    {
      "type": "slider",
      "title": "Featured Projects & Interactive Showcase",
      "subtitle": "Explore our high-impact work and technological breakthroughs.",
      "items": [
        { "title": "Enterprise Cloud System", "image_url": "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=80" },
        { "title": "Mobile App Ecosystem", "image_url": "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80" },
        { "title": "AI Analytics Dashboard", "image_url": "https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80" }
      ]
    },
    {
      "type": "about",
      "title": "Our Story & Architectural Philosophy",
      "content": "Comprehensive 2-to-3 paragraph narrative describing the founders vision, engineering standards, and commitment to client success.",
      "image_url": "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1000&auto=format&fit=crop&q=80"
    },
    {
      "type": "testimonials",
      "title": "Trusted by Visionary Industry Leaders",
      "subtitle": "Real feedback from enterprise clients and executives.",
      "items": [
        { "quote": "Exceptional execution and cutting-edge performance that transformed our entire operations.", "author": "Sarah Jenkins", "role": "Chief Technology Officer at CloudScale" },
        { "quote": "The most seamless and scalable digital infrastructure we have ever integrated.", "author": "Marcus Vance", "role": "Managing Director at Apex Global" },
        { "quote": "Unmatched speed, reliability, and precision engineering across all touchpoints.", "author": "Elena Rostova", "role": "VP of Product at Veloce" }
      ]
    },
    {
      "type": "pricing",
      "title": "Transparent, Flexible Investment Plans",
      "subtitle": "Choose the optimal plan tailored to your team scale.",
      "plans": [
        { "name": "Starter", "price": "$49/mo", "features": ["Core Platform Access", "Standard Analytics", "Community Support", "API Integration"], "is_featured": false },
        { "name": "Professional", "price": "$129/mo", "features": ["Full AI Capabilities", "Advanced Real-Time Metrics", "Priority 24/7 Support", "Unlimited Deployments", "Custom Webhooks"], "is_featured": true },
        { "name": "Enterprise", "price": "$299/mo", "features": ["Dedicated Infrastructure", "Custom SLA & Concierge", "Unlimited Team Seats", "Security Compliance Suite", "Custom Feature Builds"], "is_featured": false }
      ]
    },
    {
      "type": "faq",
      "title": "Frequently Asked Questions",
      "subtitle": "Everything you need to know before getting started.",
      "items": [
        { "question": "How quickly can we onboard and launch?", "answer": "Deployment is instant with automated onboarding workflows that take less than 15 minutes." },
        { "question": "What security and compliance standards are supported?", "answer": "Enterprise-grade end-to-end encryption with SOC2, GDPR, and ISO27001 compliant architecture." },
        { "question": "Can we integrate with our existing stack?", "answer": "Yes, seamless RESTful APIs, webhooks, and turnkey connectors allow immediate integration with your tools." },
        { "question": "What level of support is included?", "answer": "All plans include 24/7 technical assistance, dedicated documentation, and live engineering support." }
      ]
    },
    {
      "type": "cta",
      "title": "Ready to Transform Your Digital Future?",
      "subtitle": "Join thousands of ambitious teams elevating their performance and scaling effortlessly.",
      "cta_text": "Get Started Today",
      "cta_url": "#contact"
    }
  ]
}';
	}

	/**
	 * Build user prompt combining all active inputs with maximum fidelity.
	 */
	private static function build_user_prompt( $args ) {
		$prompt       = $args['prompt'];
		$scraped_data = $args['scraped_data'];
		$has_image    = $args['has_image'];
		$tone         = $args['tone'];
		$color_preset = $args['color_preset'];

		$parts = array();
		$parts[] = "Generate a comprehensive, high-converting 7-to-9 section landing page based on the following exact specifications:";

		if ( ! empty( $prompt ) ) {
			$parts[] = "### 1. USER BUSINESS CONCEPT & CLIENT INSTRUCTIONS (PRIMARY DIRECTIVE):\n" . $prompt;
		}

		if ( ! empty( $scraped_data ) ) {
			$parts[] = "### 2. REFERENCE WEBSITE ARCHITECTURAL BLUEPRINT (MANDATORY TO MODEL & ADAPT):";
			$parts[] = "- Reference Target URL: " . $scraped_data['url'];
			$parts[] = "- Reference Title: " . $scraped_data['title'];
			$parts[] = "- Reference Meta Description: " . $scraped_data['meta_description'];
			if ( ! empty( $scraped_data['h1'] ) ) {
				$parts[] = "- Extracted Core Headings: " . implode( ' | ', $scraped_data['h1'] );
			}
			if ( ! empty( $scraped_data['h2'] ) ) {
				$parts[] = "- Extracted Service Categories & Topics: " . implode( ' | ', $scraped_data['h2'] );
			}
			if ( ! empty( $scraped_data['detected_sections'] ) ) {
				$parts[] = "- Reference Section Structure: " . implode( ', ', $scraped_data['detected_sections'] );
			}
			if ( ! empty( $scraped_data['color_hints'] ) ) {
				$parts[] = "- Reference Color Palette: " . implode( ', ', $scraped_data['color_hints'] );
			}
			if ( ! empty( $scraped_data['body_summary'] ) ) {
				$parts[] = "- Reference Service Content & Excerpts: " . substr( $scraped_data['body_summary'], 0, 1500 );
			}
			$parts[] = "NOTE: You MUST heavily mirror the reference website's actual industry services (e.g. if it is Avant Tech or a digital agency, generate real services like Custom Web Development, Mobile App Engineering, UI/UX Strategy, Cloud Architecture), layout rhythm, and professional caliber.";
		}

		if ( $has_image ) {
			$parts[] = "### 3. SCREENSHOT VISION INSTRUCTION:";
			$parts[] = "Analyze the uploaded design screenshot carefully. Replicate its visual hierarchy, section rhythm, layout balance, and styling elegance into the generated sections.";
		}

		$parts[] = "### 4. DESIGN & INTERACTIVE MOTION PREFERENCES:";
		$parts[] = "- Desired Copywriting Tone: " . $tone;
		if ( ! empty( $args['custom_primary'] ) ) {
			$parts[] = "- Custom Primary Brand Hex: " . $args['custom_primary'];
			$parts[] = "- Custom Secondary/Dark Hex: " . $args['custom_secondary'];
		} else {
			$parts[] = "- Color Palette Theme: " . $color_preset;
		}
		$parts[] = "- Motion & Animation: Incorporate interactive badges, animated stats counters, project showcase slider, and rich hover card elements.";

		if ( ! empty( $args['selected_sections'] ) && is_array( $args['selected_sections'] ) ) {
			$blueprint_lines = array();
			foreach ( $args['selected_sections'] as $sec_item ) {
				if ( is_array( $sec_item ) ) {
					$type  = ! empty( $sec_item['type'] ) ? sanitize_text_field( $sec_item['type'] ) : 'custom';
					$tag   = ! empty( $sec_item['tag'] ) ? sanitize_text_field( $sec_item['tag'] ) : '[' . strtoupper( $type ) . ']';
					$title = ! empty( $sec_item['title'] ) ? sanitize_text_field( $sec_item['title'] ) : '';
					$desc  = ! empty( $sec_item['description'] ) ? sanitize_text_field( $sec_item['description'] ) : '';

					if ( ! in_array( $type, array( 'navbar', 'footer' ), true ) ) {
						$line = "- " . $tag . " (" . $type . "): " . $title;
						if ( ! empty( $desc ) ) {
							$line .= " — Instructions: " . $desc;
						}
						$blueprint_lines[] = $line;
					}
				} else {
					$type = sanitize_text_field( $sec_item );
					if ( ! in_array( $type, array( 'navbar', 'footer' ), true ) ) {
						$blueprint_lines[] = "- [" . strtoupper( $type ) . "] (" . $type . ")";
					}
				}
			}

			if ( ! empty( $blueprint_lines ) ) {
				$parts[] = "### 5. MANDATORY USER-SELECTED SECTION BLUEPRINT & CUSTOM DIRECTIVES:\n" .
					"The user has customized the website blueprint to generate the following sections in exact sequence with custom directives:\n" .
					implode( "\n", $blueprint_lines ) . "\n" .
					"You MUST generate tailored copy and elements corresponding to each requested section in the JSON sections array!";
			}
		}

		$parts[] = "\nCRITICAL: Return ONLY valid JSON adhering to the schema.";

		return implode( "\n\n", $parts );
	}

	/**
	 * Parse JSON string returned from LLM safely.
	 */
	private static function parse_llm_json( $raw_text ) {
		// Strip markdown code fences if wrapped in ```json ... ```
		$clean = trim( $raw_text );
		if ( preg_match( '/```(?:json)?\s*([\s\S]*?)\s*```/i', $clean, $matches ) ) {
			$clean = trim( $matches[1] );
		}

		// Look for outermost JSON object if there's surrounding text
		$first_brace = strpos( $clean, '{' );
		$last_brace  = strrpos( $clean, '}' );

		if ( false !== $first_brace && false !== $last_brace && $last_brace > $first_brace ) {
			$clean = substr( $clean, $first_brace, ( $last_brace - $first_brace ) + 1 );
		}

		$data = json_decode( $clean, true );

		if ( null === $data || ! is_array( $data ) ) {
			return new WP_Error( 'json_parse_error', __( 'Failed to parse AI response into structured JSON. Please try again.', 'omnicraft-ai-builder' ) );
		}

		if ( empty( $data['sections'] ) || ! is_array( $data['sections'] ) ) {
			return new WP_Error( 'missing_sections', __( 'AI response is missing page sections.', 'omnicraft-ai-builder' ) );
		}

		return $data;
	}
}
