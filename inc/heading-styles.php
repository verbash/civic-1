<?php
/**
 * CivicOne — named block styles for core/heading.
 *
 * Registers editor-visible style choices that map to civicone heading classes.
 * Editors see these in the "Styles" panel when a heading is selected.
 *
 * Resulting classes (added by WP alongside is-style-*):
 *   is-style-civicone-page-title    → .civicone-page-title   (H2, section-child page opener)
 *   is-style-civicone-section       → .civicone-section-heading (H3, major in-content subsection)
 *   is-style-civicone-accent        → .civicone-heading-accent  (any level, civicone-blue-link colour)
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register civicone heading block styles.
 */
function civic_1_register_heading_styles(): void {
	register_block_style(
		'core/heading',
		array(
			'name'  => 'civicone-page-title',
			'label' => __( 'civicone Page title', 'civicone' ),
		)
	);

	register_block_style(
		'core/heading',
		array(
			'name'  => 'civicone-section',
			'label' => __( 'civicone Section', 'civicone' ),
		)
	);

	register_block_style(
		'core/heading',
		array(
			'name'  => 'civicone-accent',
			'label' => __( 'civicone Accent', 'civicone' ),
		)
	);
}
add_action( 'init', 'civic_1_register_heading_styles' );
