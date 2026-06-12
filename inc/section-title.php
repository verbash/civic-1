<?php
/**
 * Section H1 title band + child-page hub label.
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Primary nav sections that use a shared top title on child pages.
 *
 * @return list<array{slug: string, label: string}>
 */
function cv1_get_primary_nav_sections(): array {
	return array_map(
		static fn( array $section ): array => array(
			'slug'  => (string) $section['slug'],
			'label' => (string) $section['label'],
		),
		cv1_get_section_configs()
	);
}

/**
 * When viewing a child of a primary nav section, return that section's hub label.
 *
 * @return array{slug: string, label: string}|null
 */
function cv1_get_primary_nav_section_for_current_page(): ?array {
	$section = cv1_get_current_section_config();
	if ( null === $section ) {
		return null;
	}

	if ( cv1_is_section_hub( $section ) ) {
		return null;
	}

	return array(
		'slug'  => (string) $section['slug'],
		'label' => (string) $section['label'],
	);
}

/**
 * Strip legacy template inline title margin (6rem) so tab navigation does not reflow.
 *
 * @param string $content Block HTML.
 * @param array  $block   Block data.
 */
function cv1_stabilize_post_title_markup( string $content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/post-title' || ! is_page() ) {
		return $content;
	}

	// Template stores margin-bottom: var(--wp--custom--spacing--medium, 6rem) inline (semicolon optional).
	return (string) preg_replace(
		'/\sstyle="margin-bottom:var\(--wp--custom--spacing--medium,\s*6rem\);?"/',
		'',
		$content,
		1
	);
}
add_filter( 'render_block', 'cv1_stabilize_post_title_markup', 7, 2 );

/**
 * Shared H1 class on all page title bands.
 *
 * @param string $content Block HTML.
 * @param array  $block   Block data.
 */
function cv1_render_section_h1_class( string $content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/post-title' || ! is_page() ) {
		return $content;
	}

	// Query-loop entry titles render as h2; only the template title band uses h1.
	$level = (int) ( $block['attrs']['level'] ?? 1 );
	if ( 1 !== $level || ! str_contains( $content, '<h1' ) ) {
		return $content;
	}

	if ( str_contains( $content, 'cv1-section-h1' ) ) {
		return $content;
	}

	if ( preg_match( '/class="([^"]*wp-block-post-title[^"]*)"/', $content ) ) {
		return (string) preg_replace(
			'/class="([^"]*wp-block-post-title[^"]*)"/',
			'class="$1 cv1-section-h1"',
			$content,
			1
		);
	}

	return (string) preg_replace(
		'/(<h1\b)/',
		'$1 class="wp-block-post-title cv1-section-h1"',
		$content,
		1
	);
}
add_filter( 'render_block', 'cv1_render_section_h1_class', 8, 2 );

/**
 * Show the primary nav section label in the template title band on child pages.
 *
 * @param string $content Block HTML.
 * @param array  $block   Block data.
 */
function cv1_render_primary_section_post_title( string $content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/post-title' ) {
		return $content;
	}

	$section = cv1_get_primary_nav_section_for_current_page();
	if ( null === $section ) {
		return $content;
	}

	$label = $section['label'];

	return (string) preg_replace(
		'/(<h1[^>]*class="[^"]*wp-block-post-title[^"]*"[^>]*>)(.*?)(<\/h1>)/s',
		'$1' . esc_html( $label ) . '$3',
		$content,
		1
	);
}
add_filter( 'render_block', 'cv1_render_primary_section_post_title', 10, 2 );

/**
 * Body classes for section pages.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function cv1_section_body_class( array $classes ): array {
	$section = cv1_get_current_section_config();
	if ( null === $section ) {
		return $classes;
	}

	$classes[] = 'cv1-section-page';
	$classes[] = 'cv1-section-page--' . sanitize_html_class( (string) $section['slug'] );

	if ( cv1_is_section_hub( $section ) ) {
		$classes[] = 'cv1-section-hub';
	} else {
		$classes[] = 'cv1-section-child';
	}

	return $classes;
}
add_filter( 'body_class', 'cv1_section_body_class' );
