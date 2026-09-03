<?php
/**
 * Prevent logo / top-nav flash on page load and navigation.
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Critical header CSS — inlined early so layout is stable before civicone.css loads.
 */
function civicone_enqueue_critical_header_styles(): void {
	/*
	 * Load after global-styles so layout-constrained block-gap rules do not
	 * win first paint, then get overridden (title band / H1 / rule shift).
	 */
	wp_register_style( 'civicone-critical-header', false, array( 'global-styles' ), null );
	wp_enqueue_style( 'civicone-critical-header' );

	$path = CIVIC_1_DIR . '/assets/civicone-header-critical.css';
	if ( ! is_readable( $path ) ) {
		return;
	}

	$css = file_get_contents( $path );
	if ( false === $css || '' === $css ) {
		return;
	}

	wp_add_inline_style( 'civicone-critical-header', $css );
}
add_action( 'wp_enqueue_scripts', 'civicone_enqueue_critical_header_styles', 100 );

/**
 * Preload the site logo so it does not pop in after the header paints.
 */
function civicone_preload_site_logo(): void {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id <= 0 ) {
		return;
	}

	$url = wp_get_attachment_image_url( $logo_id, 'full' );
	if ( ! is_string( $url ) || '' === $url ) {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%s" fetchpriority="high">%s',
		esc_url( $url ),
		"\n"
	);
}
add_action( 'wp_head', 'civicone_preload_site_logo', 1 );

/**
 * Keep logo visible on first paint (avoid lazy-load deferral in the header).
 *
 * @param string               $block_content Block HTML.
 * @param array<string, mixed> $block         Parsed block.
 */
function civicone_header_site_logo_priority( string $block_content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/site-logo' ) {
		return $block_content;
	}

	if ( ! str_contains( $block_content, 'civicone-header-bar__logo' ) && ! str_contains( $block_content, 'civicone-header-bar' ) ) {
		return $block_content;
	}

	if ( ! str_contains( $block_content, 'fetchpriority=' ) ) {
		$block_content = (string) preg_replace(
			'/<img\b/',
			'<img fetchpriority="high" loading="eager"',
			$block_content,
			1
		);
	}

	return $block_content;
}
add_filter( 'render_block', 'civicone_header_site_logo_priority', 10, 2 );
