<?php
/**
 * Handles safe web scraping and content/layout extraction from reference URLs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniCraft_AI_URL_Scraper {

	/**
	 * Scrape a given URL and extract structured design and content clues.
	 *
	 * @param string $url Target website URL
	 * @return array|WP_Error Extracted structured content or error
	 */
	public static function scrape( $url ) {
		$url = trim( $url );
		if ( ! preg_match( '#^https?://#i', $url ) ) {
			$url = 'https://' . $url;
		}
		$url = esc_url_raw( $url );

		if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return new WP_Error( 'invalid_url', __( 'Please provide a valid reference website URL.', 'omnicraft-ai-builder' ) );
		}

		// Security: Prevent SSRF attacks against internal network / localhost
		$parsed_host = wp_parse_url( $url, PHP_URL_HOST );
		if ( in_array( strtolower( $parsed_host ), array( 'localhost', '127.0.0.1', '0.0.0.0', '::1' ), true ) ) {
			return new WP_Error( 'forbidden_host', __( 'Internal/Localhost URLs cannot be used as external reference sites.', 'omnicraft-ai-builder' ) );
		}

		$args = array(
			'timeout'     => 5,
			'redirection' => 3,
			'httpversion' => '1.1',
			'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 OmniCraftBot/1.0',
			'sslverify'   => false,
			'headers'     => array(
				'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Language' => 'en-US,en;q=0.5',
			),
		);

		$response = wp_safe_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'fetch_failed', sprintf( __( 'Could not fetch reference website: %s', 'omnicraft-ai-builder' ), $response->get_error_message() ) );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			return new WP_Error( 'http_error', sprintf( __( 'Reference site responded with HTTP %d error.', 'omnicraft-ai-builder' ), $response_code ) );
		}

		$html = wp_remote_retrieve_body( $response );
		if ( empty( $html ) ) {
			return new WP_Error( 'empty_response', __( 'Reference site returned an empty HTML document.', 'omnicraft-ai-builder' ) );
		}

		return self::parse_html_structure( $html, $url );
	}

	/**
	 * Parse HTML to extract meta, headings, sections, content summary, and visual hints.
	 *
	 * @param string $html
	 * @param string $url
	 * @return array
	 */
	private static function parse_html_structure( $html, $url ) {
		// Suppress libxml warnings
		libxml_use_internal_errors( true );
		$dom = new DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ) );
		libxml_clear_errors();

		$xpath = new DOMXPath( $dom );

		// 1. Page Title
		$title = '';
		$title_nodes = $xpath->query( '//title' );
		if ( $title_nodes->length > 0 ) {
			$title = trim( $title_nodes->item( 0 )->textContent );
		}

		// 2. Meta Description & Keywords
		$meta_description = '';
		$meta_desc_nodes = $xpath->query( '//meta[@name="description"]/@content | //meta[@property="og:description"]/@content' );
		if ( $meta_desc_nodes->length > 0 ) {
			$meta_description = trim( $meta_desc_nodes->item( 0 )->nodeValue );
		}

		// 3. Headings Hierarchy
		$h1_list = array();
		foreach ( $xpath->query( '//h1' ) as $node ) {
			$text = trim( preg_replace( '/\s+/', ' ', $node->textContent ) );
			if ( ! empty( $text ) && strlen( $text ) < 200 ) {
				$h1_list[] = $text;
			}
		}

		$h2_list = array();
		foreach ( $xpath->query( '//h2' ) as $node ) {
			$text = trim( preg_replace( '/\s+/', ' ', $node->textContent ) );
			if ( ! empty( $text ) && strlen( $text ) < 200 ) {
				$h2_list[] = $text;
			}
		}

		$h3_list = array();
		foreach ( $xpath->query( '//h3' ) as $node ) {
			$text = trim( preg_replace( '/\s+/', ' ', $node->textContent ) );
			if ( ! empty( $text ) && strlen( $text ) < 200 ) {
				$h3_list[] = $text;
			}
		}

		// 4. Detect Section Types (Features, Testimonials, Pricing, FAQ, Hero, CTA)
		$detected_sections = array();
		$raw_lower = strtolower( $html );

		$section_keywords = array(
			'hero'         => array( 'hero', 'banner', 'welcome', 'header' ),
			'features'     => array( 'features', 'services', 'what we do', 'why choose us', 'benefits', 'solutions' ),
			'stats'        => array( 'stats', 'counter', 'numbers', 'achievements', 'metrics' ),
			'about'        => array( 'about us', 'our story', 'who we are', 'mission' ),
			'testimonials' => array( 'testimonial', 'reviews', 'what our clients say', 'case studies', 'feedback' ),
			'pricing'      => array( 'pricing', 'plans', 'packages', 'subscription', 'cost' ),
			'faq'          => array( 'faq', 'frequently asked', 'questions', 'accordion' ),
			'cta'          => array( 'get started', 'sign up', 'contact us', 'book a call', 'ready to', 'start today' ),
			'footer'       => array( 'footer', 'copyright', 'quick links' ),
		);

		foreach ( $section_keywords as $type => $keywords ) {
			foreach ( $keywords as $kw ) {
				if ( false !== strpos( $raw_lower, $kw ) ) {
					$detected_sections[] = $type;
					break;
				}
			}
		}

		// 5. Extract Color Hints from inline CSS / hex codes
		preg_match_all( '/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b/', $html, $color_matches );
		$unique_colors = array();
		if ( ! empty( $color_matches[0] ) ) {
			$counts = array_count_values( $color_matches[0] );
			// Filter out black/white extremes
			unset( $counts['#000000'], $counts['#ffffff'], $counts['#fff'], $counts['#000'] );
			arsort( $counts );
			$unique_colors = array_slice( array_keys( $counts ), 0, 5 );
		}

		// 6. Extract Main Body Text Summary (cleaned up)
		$clean_dom = clone $dom;
		$clean_xpath = new DOMXPath( $clean_dom );
		$junk_nodes = $clean_xpath->query( '//script | //style | //noscript | //svg | //iframe | //nav | //footer' );
		foreach ( $junk_nodes as $node ) {
			$node->parentNode->removeChild( $node );
		}

		$body_nodes = $clean_xpath->query( '//body' );
		$body_text = '';
		if ( $body_nodes->length > 0 ) {
			$body_text = $body_nodes->item( 0 )->textContent;
			$body_text = preg_replace( '/\s+/', ' ', $body_text );
			$body_text = trim( substr( $body_text, 0, 3000 ) );
		}

		// 7. If SPA / JavaScript Rendered site (short body text), extract text from main JS bundle
		if ( strlen( $body_text ) < 200 ) {
			preg_match_all( '/<script[^>]+src=[\x27\x22]([^\x27\x22]+)[\x27\x22]/i', $html, $script_matches );
			if ( ! empty( $script_matches[1] ) ) {
				foreach ( $script_matches[1] as $script_src ) {
					if ( preg_match( '/(main|app|index|bundle|vendor)/i', $script_src ) && ! preg_match( '/(analytics|gtm|googletag|facebook|clarity)/i', $script_src ) ) {
						$js_url = $script_src;
						if ( 0 !== strpos( $js_url, 'http' ) ) {
							$js_url = rtrim( $url, '/' ) . '/' . ltrim( $js_url, '/' );
						}
						$js_res = wp_safe_remote_get( $js_url, array( 'timeout' => 3, 'sslverify' => false ) );
						if ( ! is_wp_error( $js_res ) && 200 === wp_remote_retrieve_response_code( $js_res ) ) {
							$js_content = wp_remote_retrieve_body( $js_res );
							preg_match_all( '/[\x22\x27]([A-Z][A-Za-z0-9\s,&–—]{6,60})[\x22\x27]/', $js_content, $str_matches );
							if ( ! empty( $str_matches[1] ) ) {
								$meaningful = array_filter( $str_matches[1], function( $s ) {
									return preg_match( '/(Tech|Web|Mobile|App|Digital|Software|Solution|Service|About|Design|Cloud|Transform|Agency|Contact|Strategy|Scale|Experience|Work|Project|Feature|Platform)/i', $s );
								} );
								$unique_m = array_slice( array_unique( $meaningful ), 0, 15 );
								if ( ! empty( $unique_m ) ) {
									$body_text .= ' ' . implode( ' | ', $unique_m );
									$h2_list    = array_merge( $h2_list, array_slice( $unique_m, 0, 5 ) );
								}
							}
						}
						break;
					}
				}
			}
		}

		return array(
			'url'               => $url,
			'title'             => $title,
			'meta_description'  => $meta_description,
			'h1'                => array_slice( $h1_list, 0, 5 ),
			'h2'                => array_slice( $h2_list, 0, 10 ),
			'h3'                => array_slice( $h3_list, 0, 12 ),
			'detected_sections' => array_unique( $detected_sections ),
			'color_hints'       => $unique_colors,
			'body_summary'      => $body_text,
		);
	}
}
