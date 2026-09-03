<?php
/**
 * CivicOne component: Header inline donate CTA.
 *
 * Mobile: in header bar between logo and drawer (after nav markup).
 * Desktop: inline with template post-title H1 (below header border).
 *
 * @see civicone-component-library.md → Header CTA
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CTA copy and destination (filterable for local experiments only).
 *
 * @return array{text: string, button: string, url: string}
 */
function civicone_header_cta_config(): array {
	return array(
		'text'   => (string) apply_filters(
			'civicone_header_cta_text',
			__( 'Help support our efforts!', 'civicone' )
		),
		'button' => (string) apply_filters(
			'civicone_header_cta_button_label',
			__( 'Donate to D4C', 'civicone' )
		),
		'url'    => (string) apply_filters( 'civicone_header_cta_url', civicone_get_header_cta_donate_url_default() ),
	);
}

/**
 * Render inline donate CTA markup.
 *
 * @param string $placement `header` (mobile bar) or `title` (desktop H1 band).
 */
function civicone_render_header_cta( string $placement = 'header' ): string {
	$config = civicone_header_cta_config();

	if ( '' === $config['url'] || '' === $config['button'] ) {
		return '';
	}

	$placement = sanitize_html_class( $placement );
	$class     = 'civicone-header-cta civicone-header-cta--' . $placement;

	$text_html = '';
	if ( '' !== $config['text'] ) {
		$text_html = sprintf(
			'<p class="civicone-header-cta__text">%s</p>',
			esc_html( $config['text'] )
		);
	}

	return sprintf(
		'<div class="%1$s" role="region" aria-label="%2$s">%3$s<a class="civicone-header-cta__button" href="%4$s">%5$s</a></div>',
		esc_attr( $class ),
		esc_attr__( 'Support the coalition', 'civicone' ),
		$text_html,
		esc_url( $config['url'] ),
		esc_html( $config['button'] )
	);
}

/**
 * Mobile header: CTA after primary nav (drawer wrap runs at priority 10).
 *
 * @param string               $block_content Block HTML.
 * @param array<string, mixed> $block         Parsed block.
 */
function civicone_filter_primary_navigation_append_cta( string $block_content, array $block ): string {
	if ( ! function_exists( 'civicone_is_primary_header_navigation' ) || ! civicone_is_primary_header_navigation( $block ) ) {
		return $block_content;
	}

	$cta = civicone_render_header_cta( 'header' );
	if ( '' === $cta || str_contains( $block_content, 'civicone-header-cta--header' ) ) {
		return $block_content;
	}

	return $block_content . $cta;
}
add_filter( 'render_block', 'civicone_filter_primary_navigation_append_cta', 15, 2 );

/**
 * Desktop: CTA inline with template post-title H1 (below header rule).
 *
 * @param string               $block_content Block HTML.
 * @param array<string, mixed> $block         Parsed block.
 */
function civicone_filter_page_title_band_inline_cta( string $block_content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/group' ) {
		return $block_content;
	}

	if ( ! is_page() || is_front_page() ) {
		return $block_content;
	}

	if ( ! str_contains( $block_content, 'wp-block-post-title' ) ) {
		return $block_content;
	}

	if ( str_contains( $block_content, 'civicone-page-title-band__row' ) ) {
		return $block_content;
	}

	$cta = civicone_render_header_cta( 'title' );
	if ( '' === $cta ) {
		return $block_content;
	}

	$updated = (string) preg_replace(
		'/(<h1[^>]*class="[^"]*wp-block-post-title[^"]*"[^>]*>.*?<\/h1>)/s',
		'<div class="civicone-page-title-band__row">$1' . $cta . '</div>',
		$block_content,
		1
	);

	return $updated !== $block_content ? $updated : $block_content;
}
add_filter( 'render_block', 'civicone_filter_page_title_band_inline_cta', 11, 2 );

