<?php
/**
 * Compiles AI JSON Layout into standard Gutenberg block markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_Gutenberg_Compiler {

	/**
	 * Compile structured site JSON to Gutenberg block HTML string.
	 *
	 * @param array $site_data Structured AI response
	 * @param array $selected_sections Optional list of user-selected section types
	 * @return string
	 */
	public static function compile( $site_data, $selected_sections = array() ) {
		$output = '';
		$primary_color   = ! empty( $site_data['color_palette']['primary'] ) ? $site_data['color_palette']['primary'] : '#6366f1';
		$secondary_color = ! empty( $site_data['color_palette']['secondary'] ) ? $site_data['color_palette']['secondary'] : '#0f172a';
		$bg_light        = ! empty( $site_data['color_palette']['bg_light'] ) ? $site_data['color_palette']['bg_light'] : '#f8fafc';

		$sections = ! empty( $site_data['sections'] ) && is_array( $site_data['sections'] ) ? $site_data['sections'] : array();

		$selected_types = array();
		if ( ! empty( $selected_sections ) && is_array( $selected_sections ) ) {
			foreach ( $selected_sections as $s ) {
				$selected_types[] = is_array( $s ) ? ( isset( $s['type'] ) ? $s['type'] : 'custom' ) : $s;
			}
		}

		// 1. Prepend Modern Header Block if enabled
		$include_navbar = empty( $selected_types ) || in_array( 'navbar', $selected_types, true );
		if ( $include_navbar ) {
			$output .= self::build_header_block( $site_data, $primary_color, $secondary_color ) . "\n\n";
		}

		foreach ( $sections as $section ) {
			$type = ! empty( $section['type'] ) ? strtolower( $section['type'] ) : 'custom';

			switch ( $type ) {
				case 'hero':
					$output .= self::build_hero_block( $section, $primary_color, $secondary_color );
					break;

				case 'features':
				case 'services':
					$output .= self::build_features_block( $section, $primary_color, $secondary_color, $bg_light );
					break;

				case 'about':
					$output .= self::build_about_block( $section, $primary_color, $secondary_color );
					break;

				case 'stats':
				case 'counter':
					$output .= self::build_stats_block( $section, $primary_color, $secondary_color, $bg_light );
					break;

				case 'testimonials':
				case 'reviews':
					$output .= self::build_testimonials_block( $section, $primary_color, $secondary_color, $bg_light );
					break;

				case 'pricing':
					$output .= self::build_pricing_block( $section, $primary_color, $secondary_color, $bg_light );
					break;

				case 'slider':
				case 'gallery':
				case 'showcase':
				case 'portfolio':
				case 'carousel':
					$output .= self::build_slider_block( $section, $primary_color, $secondary_color, $bg_light );
					break;

				case 'faq':
				case 'accordion':
					$output .= self::build_faq_block( $section, $primary_color, $secondary_color );
					break;

				case 'cta':
				case 'contact':
					$output .= self::build_cta_block( $section, $primary_color, $secondary_color );
					break;

				default:
					$output .= self::build_generic_block( $section, $primary_color, $secondary_color );
					break;
			}

			$output .= "\n\n";
		}

		// 2. Append Modern Footer Block if enabled
		$include_footer = empty( $selected_types ) || in_array( 'footer', $selected_types, true );
		if ( $include_footer ) {
			$output .= self::build_footer_block( $site_data, $primary_color, $secondary_color );
		}

		return trim( $output );
	}

	private static function build_header_block( $site_data, $primary, $secondary ) {
		$brand_title = ! empty( $site_data['page_title'] ) ? $site_data['page_title'] : 'Brand';
		$short_brand = explode( '—', $brand_title )[0];
		$short_brand = explode( '-', $short_brand )[0];
		$short_brand = trim( $short_brand );

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"18px","bottom":"18px","left":"30px","right":"30px"}},"color":{"background":"#ffffff"}},"layout":{"type":"constrained"}} -->
<div id="navbar" class="wp-block-group alignfull has-background" style="background-color:#ffffff;border-bottom:1px solid #e2e8f0;padding-top:18px;padding-right:30px;padding-bottom:18px;padding-left:30px">
<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"30%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:30%">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"22px","fontWeight":"800"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h3 class="wp-block-heading has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:22px;font-weight:800;margin:0"><span style="color:' . esc_attr( $primary ) . '">✦</span> ' . esc_html( $short_brand ) . '</h3>
<!-- /wp:heading -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"14px","fontWeight":"600"}}} -->
<p class="has-text-align-center" style="font-size:14px;font-weight:600;margin:0"><a href="#features" style="color:#475569;text-decoration:none;margin:0 12px">Features</a><a href="#about" style="color:#475569;text-decoration:none;margin:0 12px">About</a><a href="#pricing" style="color:#475569;text-decoration:none;margin:0 12px">Pricing</a><a href="#faq" style="color:#475569;text-decoration:none;margin:0 12px">FAQ</a><a href="#reviews" style="color:#475569;text-decoration:none;margin:0 12px">Reviews</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"25%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:25%">
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"style":{"color":{"background":"' . esc_attr( $primary ) . '","text":"#ffffff"},"border":{"radius":"6px"}},"fontSize":"small"} -->
<div class="wp-block-button has-custom-font-size has-small-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="#contact" style="border-radius:6px;background-color:' . esc_attr( $primary ) . ';color:#ffffff;font-weight:600;padding:8px 18px">Get Started</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}

	private static function build_hero_block( $sec, $primary, $secondary ) {
		$title     = ! empty( $sec['title'] ) ? $sec['title'] : 'Build Something Amazing Today';
		$subtitle  = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'The next-generation platform for visionary teams.';
		$cta_text  = ! empty( $sec['cta_text'] ) ? $sec['cta_text'] : 'Get Started Now';
		$cta_url   = ! empty( $sec['cta_url'] ) ? $sec['cta_url'] : '#contact';
		$image_url = ! empty( $sec['image_url'] ) ? $sec['image_url'] : 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1200&auto=format&fit=crop&q=80';
		$badge     = ! empty( $sec['badge'] ) ? $sec['badge'] : '🚀 Next-Gen Innovation';

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"30px","right":"30px"}}},"layout":{"type":"constrained"}} -->
<div id="hero" class="wp-block-group alignfull" style="padding-top:80px;padding-right:30px;padding-bottom:80px;padding-left:30px">
<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"13px","fontWeight":"700","letterSpacing":"0.5px"}}} -->
<p style="display:inline-block;padding:6px 16px;background:rgba(99,102,241,0.1);color:' . esc_attr( $primary ) . ';border-radius:999px;font-size:13px;font-weight:700;text-transform:uppercase;margin-bottom:15px">' . esc_html( $badge ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"46px","lineHeight":"1.15","fontWeight":"800"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h1 class="wp-block-heading has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:46px;font-weight:800;line-height:1.15;margin-top:0">' . esc_html( $title ) . '</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px","lineHeight":"1.6"}},"textColor":"#64748b"} -->
<p class="has-text-color" style="color:#64748b;font-size:18px;line-height:1.6;margin:15px 0 25px 0">' . esc_html( $subtitle ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"style":{"color":{"background":"' . esc_attr( $primary ) . '","text":"#ffffff"},"border":{"radius":"8px"}},"fontSize":"medium"} -->
<div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="' . esc_url( $cta_url ) . '" style="border-radius:8px;background-color:' . esc_attr( $primary ) . ';color:#ffffff;font-weight:600;padding:14px 28px">' . esc_html( $cta_text ) . '</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"16px"},"shadow":"0 20px 40px -10px rgba(0,0,0,0.15)"}} -->
<figure class="wp-block-image size-full" style="border-radius:16px;box-shadow:0 20px 40px -10px rgba(0,0,0,0.15)"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" style="border-radius:16px;width:100%;object-fit:cover;"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}

	private static function build_features_block( $sec, $primary, $secondary, $bg_light ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Powerful Features Designed for Scale';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Everything you need to grow and succeed.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$cols_html = '';
		foreach ( array_slice( $items, 0, 3) as $item ) {
			$f_title = ! empty( $item['title'] ) ? $item['title'] : 'Feature Benefit';
			$f_desc  = ! empty( $item['description'] ) ? $item['description'] : 'Detailed benefit description.';

			$cols_html .= '<!-- wp:column {"style":{"spacing":{"padding":{"top":"30px","bottom":"30px","left":"24px","right":"24px"}},"color":{"background":"#ffffff"},"border":{"radius":"12px"}}} -->
<div class="wp-block-column has-background" style="background-color:#ffffff;border-radius:12px;padding-top:30px;padding-right:24px;padding-bottom:30px;padding-left:24px;box-shadow:0 4px 16px rgba(0,0,0,0.06)">
<div style="width:40px;height:40px;border-radius:8px;background:rgba(99,102,241,0.1);color:' . esc_attr( $primary ) . ';display:flex;align-items:center;justify-content:center;margin-bottom:16px;font-size:18px;">✦</div>
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"20px","fontWeight":"700"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h3 class="wp-block-heading has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:20px;font-weight:700;margin:0 0 10px 0">' . esc_html( $f_title ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"#64748b"} -->
<p class="has-text-color" style="color:#64748b;font-size:15px;line-height:1.6;margin:0">' . esc_html( $f_desc ) . '</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->';
		}

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"30px","right":"30px"}},"color":{"background":"' . esc_attr( $bg_light ) . '"}},"layout":{"type":"constrained"}} -->
<div id="features" class="wp-block-group alignfull has-background" style="background-color:' . esc_attr( $bg_light ) . ';padding-top:80px;padding-right:30px;padding-bottom:80px;padding-left:30px">
<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"36px","fontWeight":"800"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:36px;font-weight:800;margin:0">' . esc_html( $title ) . '</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"#64748b","style":{"spacing":{"margin":{"bottom":"45px","top":"10px"}}}} -->
<p class="has-text-align-center has-text-color" style="color:#64748b;font-size:17px;margin-top:10px;margin-bottom:45px">' . esc_html( $subtitle ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
' . $cols_html . '
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}

	private static function build_about_block( $sec, $primary, $secondary ) {
		$title     = ! empty( $sec['title'] ) ? $sec['title'] : 'Our Story & Purpose';
		$content   = ! empty( $sec['content'] ) ? $sec['content'] : 'Empowering modern brands to scale effortlessly.';
		$image_url = ! empty( $sec['image_url'] ) ? $sec['image_url'] : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1000&auto=format&fit=crop&q=80';

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"30px","right":"30px"}}},"layout":{"type":"constrained"}} -->
<div id="about" class="wp-block-group alignfull" style="padding-top:80px;padding-right:30px;padding-bottom:80px;padding-left:30px">
<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"14px"}}} -->
<figure class="wp-block-image size-full" style="border-radius:14px"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" style="border-radius:14px;width:100%;object-fit:cover;"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%;padding-left:30px">
<!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h2 class="wp-block-heading has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:34px;font-weight:800;margin:0 0 15px 0">' . esc_html( $title ) . '</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"#475569","style":{"typography":{"fontSize":"16px","lineHeight":"1.7"}}} -->
<p class="has-text-color" style="color:#475569;font-size:16px;line-height:1.7">' . esc_html( $content ) . '</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}

	private static function build_stats_block( $sec, $primary, $secondary, $bg_light ) {
		$items = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();
		if ( empty( $items ) ) {
			$items = array(
				array( 'number' => '99.9', 'suffix' => '%', 'title' => 'Accuracy Rate' ),
				array( 'number' => '500', 'suffix' => 'K+', 'title' => 'Happy Users' ),
				array( 'number' => '24', 'suffix' => '/7', 'title' => 'Biometric Sync' ),
				array( 'number' => '10', 'suffix' => 'x', 'title' => 'Faster Recovery' ),
			);
		}

		$cols = '';
		foreach ( $items as $item ) {
			$num = ! empty( $item['number'] ) ? $item['number'] : '100';
			$suf = ! empty( $item['suffix'] ) ? $item['suffix'] : '+';
			$st_title = ! empty( $item['title'] ) ? $item['title'] : 'Metric';

			$cols .= '<!-- wp:column {"style":{"spacing":{"padding":{"top":"20px","bottom":"20px"}}}} -->
<div class="wp-block-column" style="text-align:center;padding:20px 10px;">
<p style="font-size:36px;font-weight:800;color:' . esc_attr( $primary ) . ';margin:0 0 4px 0">' . esc_html( $num . $suf ) . '</p>
<p style="font-size:14px;font-weight:600;color:' . esc_attr( $secondary ) . ';margin:0">' . esc_html( $st_title ) . '</p>
</div>
<!-- /wp:column -->';
		}

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"50px","bottom":"50px","left":"30px","right":"30px"}},"color":{"background":"' . esc_attr( $bg_light ) . '"}},"layout":{"type":"constrained"}} -->
<div id="stats" class="wp-block-group alignfull has-background" style="background-color:' . esc_attr( $bg_light ) . ';padding-top:50px;padding-right:30px;padding-bottom:50px;padding-left:30px;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;">
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
' . $cols . '
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}

	private static function build_testimonials_block( $sec, $primary, $secondary, $bg_light ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'What Our Customers Say';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Real stories from real users.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$cols = '';
		foreach ( array_slice( $items, 0, 3 ) as $item ) {
			$quote  = ! empty( $item['quote'] ) ? $item['quote'] : 'Incredible experience!';
			$author = ! empty( $item['author'] ) ? $item['author'] : 'Satisfied Customer';
			$role   = ! empty( $item['role'] ) ? $item['role'] : 'Client';

			$cols .= '<!-- wp:column {"style":{"spacing":{"padding":{"top":"30px","bottom":"30px","left":"24px","right":"24px"}},"color":{"background":"#ffffff"},"border":{"radius":"12px"}}} -->
<div class="wp-block-column has-background" style="background-color:#ffffff;border-radius:12px;padding-top:30px;padding-right:24px;padding-bottom:30px;padding-left:24px;box-shadow:0 4px 16px rgba(0,0,0,0.06)">
<p style="color:#334155;font-size:15px;font-style:italic;line-height:1.6;margin:0 0 16px 0">“' . esc_html( $quote ) . '”</p>
<p style="color:' . esc_attr( $secondary ) . ';font-weight:700;font-size:15px;margin:0">' . esc_html( $author ) . '</p>
<p style="color:' . esc_attr( $primary ) . ';font-size:13px;font-weight:600;margin:2px 0 0 0">' . esc_html( $role ) . '</p>
</div>
<!-- /wp:column -->';
		}

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"30px","right":"30px"}},"color":{"background":"' . esc_attr( $bg_light ) . '"}},"layout":{"type":"constrained"}} -->
<div id="reviews" class="wp-block-group alignfull has-background" style="background-color:' . esc_attr( $bg_light ) . ';padding-top:80px;padding-right:30px;padding-bottom:80px;padding-left:30px">
<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"36px","fontWeight":"800"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:36px;font-weight:800;margin:0">' . esc_html( $title ) . '</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"#64748b","style":{"spacing":{"margin":{"bottom":"45px","top":"10px"}}}} -->
<p class="has-text-align-center has-text-color" style="color:#64748b;font-size:17px;margin-top:10px;margin-bottom:45px">' . esc_html( $subtitle ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
' . $cols . '
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}

	private static function build_pricing_block( $sec, $primary, $secondary, $bg_light ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Simple, Transparent Pricing';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Choose the plan that fits your ambition.';
		$plans    = ! empty( $sec['plans'] ) && is_array( $sec['plans'] ) ? $sec['plans'] : array();

		if ( empty( $plans ) ) {
			$plans = array(
				array( 'name' => 'Starter', 'price' => '$29/mo', 'features' => array( 'Core Tracking', 'Standard Analytics', 'Community Support' ), 'is_featured' => false ),
				array( 'name' => 'Professional', 'price' => '$79/mo', 'features' => array( 'AI Insights', 'Advanced Analytics', 'Priority Support', 'Full Sync' ), 'is_featured' => true ),
				array( 'name' => 'Enterprise', 'price' => '$199/mo', 'features' => array( 'Dedicated Manager', 'Custom API', 'Unlimited Seats', '24/7 Concierge' ), 'is_featured' => false ),
			);
		}

		$cols = '';
		foreach ( array_slice( $plans, 0, 3 ) as $plan ) {
			$p_name = ! empty( $plan['name'] ) ? $plan['name'] : 'Plan';
			$p_price = ! empty( $plan['price'] ) ? $plan['price'] : '$49';
			$p_feat = ! empty( $plan['features'] ) && is_array( $plan['features'] ) ? $plan['features'] : array( 'Feature 1', 'Feature 2', 'Feature 3' );
			$is_feat = ! empty( $plan['is_featured'] );

			$feat_list = '';
			foreach ( $p_feat as $f ) {
				$feat_list .= '<li style="margin-bottom:8px;font-size:14px;color:#475569;">✓ ' . esc_html( $f ) . '</li>';
			}

			$card_border = $is_feat ? 'border:2px solid ' . esc_attr( $primary ) . ';' : 'border:1px solid #e2e8f0;';
			$badge_html = $is_feat ? '<span style="background:' . esc_attr( $primary ) . ';color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;text-transform:uppercase;margin-bottom:10px;display:inline-block;">Most Popular</span>' : '';

			$cols .= '<!-- wp:column {"style":{"spacing":{"padding":{"top":"35px","bottom":"35px","left":"24px","right":"24px"}},"color":{"background":"#ffffff"},"border":{"radius":"14px"}}} -->
<div class="wp-block-column has-background" style="background-color:#ffffff;border-radius:14px;' . $card_border . 'padding-top:35px;padding-right:24px;padding-bottom:35px;padding-left:24px;box-shadow:0 6px 20px rgba(0,0,0,0.06);display:flex;flex-direction:column;justify-content:space-between;">
<div>
' . $badge_html . '
<h3 style="color:' . esc_attr( $secondary ) . ';font-size:20px;font-weight:700;margin:0 0 10px 0;">' . esc_html( $p_name ) . '</h3>
<p style="color:' . esc_attr( $secondary ) . ';font-size:32px;font-weight:800;margin:0 0 20px 0;">' . esc_html( $p_price ) . '</p>
<ul style="list-style:none;padding:0;margin:0 0 25px 0;">
' . $feat_list . '
</ul>
</div>
<a href="#contact" style="display:block;text-align:center;padding:12px 20px;border-radius:8px;background:' . ( $is_feat ? esc_attr( $primary ) : '#f1f5f9' ) . ';color:' . ( $is_feat ? '#ffffff' : esc_attr( $secondary ) ) . ';font-weight:700;font-size:14px;text-decoration:none;">Choose ' . esc_html( $p_name ) . '</a>
</div>
<!-- /wp:column -->';
		}

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"30px","right":"30px"}}},"layout":{"type":"constrained"}} -->
<div id="pricing" class="wp-block-group alignfull" style="padding-top:80px;padding-right:30px;padding-bottom:80px;padding-left:30px">
<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"36px","fontWeight":"800"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:36px;font-weight:800;margin:0">' . esc_html( $title ) . '</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"#64748b","style":{"spacing":{"margin":{"bottom":"45px","top":"10px"}}}} -->
<p class="has-text-align-center has-text-color" style="color:#64748b;font-size:17px;margin-top:10px;margin-bottom:45px">' . esc_html( $subtitle ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
' . $cols . '
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}

	private static function build_faq_block( $sec, $primary, $secondary ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Frequently Asked Questions';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Quick answers to common questions.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$details_html = '';
		foreach ( $items as $item ) {
			$q = ! empty( $item['question'] ) ? $item['question'] : 'Question';
			$a = ! empty( $item['answer'] ) ? $item['answer'] : 'Answer';

			$details_html .= '<details style="background-color:#f8fafc;border:1px solid #e2e8f0;padding:18px 22px;border-radius:10px;margin-bottom:14px;font-size:15px;cursor:pointer;"><summary style="font-weight:700;color:' . esc_attr( $secondary ) . ';">' . esc_html( $q ) . '</summary><p style="color:#475569;margin:12px 0 0 0;line-height:1.7;font-size:14px;">' . esc_html( $a ) . '</p></details>';
		}

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"30px","right":"30px"}}},"layout":{"type":"constrained"}} -->
<div id="faq" class="wp-block-group alignfull" style="padding-top:80px;padding-right:30px;padding-bottom:80px;padding-left:30px">
<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"36px","fontWeight":"800"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:36px;font-weight:800;margin:0">' . esc_html( $title ) . '</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"#64748b","style":{"spacing":{"margin":{"bottom":"40px","top":"10px"}}}} -->
<p class="has-text-align-center has-text-color" style="color:#64748b;font-size:17px;margin-top:10px;margin-bottom:40px">' . esc_html( $subtitle ) . '</p>
<!-- /wp:paragraph -->
<div style="max-width:780px;margin:0 auto">
' . $details_html . '
</div>
</div>
<!-- /wp:group -->';
	}

	private static function build_cta_block( $sec, $primary, $secondary ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Ready to Transform Your Workflow?';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Join thousands of satisfied teams today.';
		$cta_text = ! empty( $sec['cta_text'] ) ? $sec['cta_text'] : 'Get Started Now';
		$cta_url  = ! empty( $sec['cta_url'] ) ? $sec['cta_url'] : '#contact';

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"90px","bottom":"90px","left":"30px","right":"30px"}},"color":{"background":"' . esc_attr( $primary ) . '"}},"layout":{"type":"constrained"}} -->
<div id="contact" class="wp-block-group alignfull has-background" style="background-color:' . esc_attr( $primary ) . ';padding-top:90px;padding-right:30px;padding-bottom:90px;padding-left:30px">
<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"40px","fontWeight":"800"}},"textColor":"#ffffff"} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#ffffff;font-size:40px;font-weight:800;margin:0">' . esc_html( $title ) . '</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"18px"}},"textColor":"#f1f5f9"} -->
<p class="has-text-align-center has-text-color" style="color:#f1f5f9;font-size:18px;max-width:640px;margin:18px auto 35px auto">' . esc_html( $subtitle ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"style":{"color":{"background":"#ffffff","text":"' . esc_attr( $secondary ) . '"},"border":{"radius":"8px"}},"fontSize":"medium"} -->
<div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="' . esc_url( $cta_url ) . '" style="border-radius:8px;background-color:#ffffff;color:' . esc_attr( $secondary ) . ';font-weight:700;padding:16px 36px">' . esc_html( $cta_text ) . '</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->';
	}

	private static function build_footer_block( $site_data, $primary, $secondary ) {
		$brand_title = ! empty( $site_data['page_title'] ) ? $site_data['page_title'] : 'Brand';
		$short_brand = explode( '—', $brand_title )[0];
		$short_brand = explode( '-', $short_brand )[0];
		$short_brand = trim( $short_brand );
		$year = gmdate( 'Y' );

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"70px","bottom":"35px","left":"30px","right":"30px"}},"color":{"background":"#0f172a"}},"layout":{"type":"constrained"}} -->
<div id="footer" class="wp-block-group alignfull has-background" style="background-color:#0f172a;color:#94a3b8;padding-top:70px;padding-right:30px;padding-bottom:35px;padding-left:30px">
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"22px","fontWeight":"800"}},"textColor":"#ffffff"} -->
<h3 class="wp-block-heading has-text-color" style="color:#ffffff;font-size:22px;font-weight:800;margin:0 0 12px 0"><span style="color:' . esc_attr( $primary ) . '">✦</span> ' . esc_html( $short_brand ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px","lineHeight":"1.7"}},"textColor":"#94a3b8"} -->
<p class="has-text-color" style="color:#94a3b8;font-size:14px;line-height:1.7;margin:0">Next-generation solutions crafted for exceptional digital experiences and conversion excellence.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%;padding-left:20px">
<h4 style="color:#ffffff;font-size:16px;font-weight:700;margin:0 0 14px 0;">Navigation</h4>
<div style="display:flex;flex-direction:column;gap:8px;font-size:14px;">
<a href="#features" style="color:#94a3b8;text-decoration:none;">Features</a>
<a href="#about" style="color:#94a3b8;text-decoration:none;">About</a>
<a href="#pricing" style="color:#94a3b8;text-decoration:none;">Pricing</a>
<a href="#faq" style="color:#94a3b8;text-decoration:none;">FAQ</a>
<a href="#reviews" style="color:#94a3b8;text-decoration:none;">Reviews</a>
</div>
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%">
<h4 style="color:#ffffff;font-size:16px;font-weight:700;margin:0 0 14px 0;">Legal</h4>
<p style="font-size:13px;color:#64748b;margin:0 0 10px 0;">All product names, logos, and brands are property of their respective owners.</p>
<p style="font-size:13px;color:#94a3b8;margin:0">© ' . esc_html( $year ) . ' ' . esc_html( $short_brand ) . '. All rights reserved.</p>
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}

	private static function build_generic_block( $sec, $primary, $secondary ) {
		$title   = ! empty( $sec['title'] ) ? $sec['title'] : 'Section';
		$content = ! empty( $sec['content'] ) ? $sec['content'] : 'Content.';

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"70px","bottom":"70px","left":"30px","right":"30px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:70px;padding-right:30px;padding-bottom:70px;padding-left:30px">
<!-- wp:heading {"textAlign":"center","level":2,"textColor":"' . esc_attr( $secondary ) . '"} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:' . esc_attr( $secondary ) . '">' . esc_html( $title ) . '</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"#475569"} -->
<p class="has-text-align-center has-text-color" style="color:#475569">' . esc_html( $content ) . '</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';
	}

	private static function build_slider_block( $sec, $primary, $secondary, $bg_light ) {
		$title    = ! empty( $sec['title'] ) ? $sec['title'] : 'Featured Projects & Showcase';
		$subtitle = ! empty( $sec['subtitle'] ) ? $sec['subtitle'] : 'Explore our high-impact work and client success stories.';
		$items    = ! empty( $sec['items'] ) && is_array( $sec['items'] ) ? $sec['items'] : array();

		$default_slides = array(
			array( 'url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=80', 'title' => 'Web App Architecture' ),
			array( 'url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80', 'title' => 'Mobile Cloud Ecosystem' ),
			array( 'url' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80', 'title' => 'Digital Product Design' ),
		);

		$cols = '';
		$slides = ! empty( $items ) ? $items : $default_slides;
		foreach ( array_slice( $slides, 0, 3 ) as $i => $slide ) {
			$img = ! empty( $slide['image_url'] ) ? $slide['image_url'] : ( ! empty( $slide['url'] ) ? $slide['url'] : $default_slides[ $i % count( $default_slides ) ]['url'] );
			$card_title = ! empty( $slide['title'] ) ? $slide['title'] : 'Showcase Item ' . ( $i + 1 );

			$cols .= '<!-- wp:column {"style":{"spacing":{"padding":{"top":"0px","bottom":"20px","left":"0px","right":"0px"}},"color":{"background":"#ffffff"},"border":{"radius":"14px"}}} -->
<div class="wp-block-column has-background" style="background-color:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.08);transition:transform 0.3s ease;">
<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $card_title ) . '" style="width:100%;height:220px;object-fit:cover;display:block;" />
<div style="padding:20px 20px 10px 20px;">
<h4 style="color:' . esc_attr( $secondary ) . ';font-size:18px;font-weight:700;margin:0 0 8px 0;">' . esc_html( $card_title ) . '</h4>
<p style="color:#64748b;font-size:14px;margin:0;">Custom designed & engineered for peak performance.</p>
</div>
</div>
<!-- /wp:column -->';
		}

		return '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"30px","right":"30px"}}},"layout":{"type":"constrained"}} -->
<div id="showcase" class="wp-block-group alignfull" style="padding-top:80px;padding-right:30px;padding-bottom:80px;padding-left:30px">
<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"36px","fontWeight":"800"}},"textColor":"' . esc_attr( $secondary ) . '"} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:' . esc_attr( $secondary ) . ';font-size:36px;font-weight:800;margin:0">' . esc_html( $title ) . '</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"#64748b","style":{"spacing":{"margin":{"bottom":"45px","top":"10px"}}}} -->
<p class="has-text-align-center has-text-color" style="color:#64748b;font-size:17px;margin-top:10px;margin-bottom:45px">' . esc_html( $subtitle ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
' . $cols . '
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->';
	}
}
