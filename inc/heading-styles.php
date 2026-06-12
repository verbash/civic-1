<?php
/**
 * Civic-1 — named block styles for core/heading.
 *
 * Registers editor-visible style choices that map to cv1 heading classes.
 * Editors see these in the "Styles" panel when a heading is selected.
 *
 * Resulting classes (added by WP alongside is-style-*):
 *   is-style-cv1-page-title    → .cv1-page-title   (H2, section-child page opener)
 *   is-style-cv1-section       → .cv1-section-heading (H3, major in-content subsection)
 *   is-style-cv1-accent        → .cv1-heading-accent  (any level, cv1-blue-link colour)
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register cv1 heading block styles.
 */
function civic_1_register_heading_styles(): void {
	register_block_style(
		'core/heading',
		array(
			'name'  => 'cv1-page-title',
			'label' => __( 'cv1 Page title', 'civic-1' ),
		)
	);

	register_block_style(
		'core/heading',
		array(
			'name'  => 'cv1-section',
			'label' => __( 'cv1 Section', 'civic-1' ),
		)
	);

	register_block_style(
		'core/heading',
		array(
			'name'  => 'cv1-accent',
			'label' => __( 'cv1 Accent', 'civic-1' ),
		)
	);
}
add_action( 'init', 'civic_1_register_heading_styles' );
