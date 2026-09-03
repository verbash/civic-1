<?php
/**
 * CivicOne — block pattern category and pattern registration.
 *
 * All content patterns live here (plugin) rather than in the child theme so
 * the library is portable to any TT2 / FSE child theme.
 *
 * NOTE: The header-bar pattern (`twentytwentytwo-d4c/header-bar`) is intentionally
 * kept in the child theme because it requires PHP rendering via
 * `civicone_header_bar_markup()`, which is defined in `plugins/civicone/inc/header-bar.php`. That
 * function must exist on the site for the pattern to work.
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register CivicOne block pattern categories.
 */
function civic_1_register_pattern_categories(): void {
	register_block_pattern_category(
		'civicone',
		array(
			'label'       => __( 'CivicOne', 'civicone' ),
			'description' => __( 'Reusable UI components from the CivicOne (civicone) design kit.', 'civicone' ),
		)
	);
	register_block_pattern_category(
		'civicone-layout',
		array(
			'label' => __( 'CivicOne layout', 'civicone' ),
		)
	);
	register_block_pattern_category(
		'civicone-section',
		array(
			'label' => __( 'CivicOne section', 'civicone' ),
		)
	);
}
add_action( 'init', 'civic_1_register_pattern_categories' );

/**
 * Load a pattern HTML file and return its contents.
 *
 * Returns an empty string (and logs a notice) if the file is unreadable.
 *
 * @param string $filename Filename relative to the plugin's patterns/ directory.
 * @return string Block HTML.
 */
function civic_1_get_pattern_content( string $filename ): string {
	$path = CIVIC_1_DIR . '/patterns/' . $filename;

	if ( ! is_readable( $path ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error(
			esc_html( "CivicOne: pattern file not readable: {$path}" ),
			E_USER_NOTICE
		);
		return '';
	}

	$content = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return is_string( $content ) ? $content : '';
}

/**
 * Register all CivicOne block patterns.
 *
 * Slugs use the `civicone/` namespace so they are decoupled from the child
 * theme slug. The child theme's `twentytwentytwo-d4c/*` patterns (header-bar)
 * remain registered separately via file-based discovery.
 */
function civic_1_register_patterns(): void {
	$patterns = array(
		array(
			'slug'        => 'civicone/content-heading-h2',
			'title'       => __( 'civicone H2 heading', 'civicone' ),
			'description' => __( 'Pre-styled H2 for interior page content (civicone-page-title class, x-large). Start post content here — the page template renders the H1.', 'civicone' ),
			'categories'  => array( 'civicone', 'text' ),
			'keywords'    => array( 'heading', 'h2', 'title', 'section' ),
			'file'        => 'content-heading-h2.html',
		),
		array(
			'slug'        => 'civicone/content-heading-h3',
			'title'       => __( 'civicone H3 heading — inline', 'civicone' ),
			'description' => __( 'H3 subsection heading for flowing page content (no anchor ID). Use below a civicone H2 when you do not need hub anchor-tab navigation.', 'civicone' ),
			'categories'  => array( 'civicone', 'text' ),
			'keywords'    => array( 'heading', 'h3', 'subsection' ),
			'file'        => 'content-heading-h3.html',
		),
		array(
			'slug'        => 'civicone/callout',
			'title'       => __( 'CivicOne callout', 'civicone' ),
			'description' => __( 'Newsletter-style notice box — flat fill, 1 px border, no shadow.', 'civicone' ),
			'categories'  => array( 'civicone', 'text' ),
			'file'        => 'callout.html',
		),
		array(
			'slug'        => 'civicone/hub-card-topic',
			'title'       => __( 'Hub topic card', 'civicone' ),
			'description' => __( 'Linked H3 card for section hub grids (Resources topics, Programs paths, etc.).', 'civicone' ),
			'categories'  => array( 'civicone' ),
			'file'        => 'hub-card-topic.html',
		),
		array(
			'slug'        => 'civicone/hub-section-intro',
			'title'       => __( 'Hub section intro', 'civicone' ),
			'description' => __( 'H2 hub intro below page tabs — e.g. Volunteer resources on the Resources landing page.', 'civicone' ),
			'categories'  => array( 'civicone' ),
			'file'        => 'hub-section-intro.html',
		),
		array(
			'slug'        => 'civicone/hub-on-page-anchors',
			'title'       => __( 'Hub on-page anchors', 'civicone' ),
			'description' => __( '“On this page” jump links for section hubs. Place at the top of hub content; links hoist into the section subnav. Match href hashes to heading anchor IDs in the page.', 'civicone' ),
			'categories'  => array( 'civicone' ),
			'file'        => 'hub-on-page-anchors.html',
		),
		array(
			'slug'        => 'civicone/resource-rules-callout',
			'title'       => __( 'Resource rules callout', 'civicone' ),
			'description' => __( 'H3 section title + H4 rule items in a callout box. Resources hub pattern.', 'civicone' ),
			'categories'  => array( 'civicone' ),
			'file'        => 'resource-rules-callout.html',
		),
		array(
			'slug'        => 'civicone/section-heading-h3',
			'title'       => __( 'civicone H3 heading — anchored', 'civicone' ),
			'description' => __( 'H3 with anchor ID for hub anchor-tab navigation. Use on hub pages (Resources, Programs) where on-page jump links are needed. For plain subsections without tabs, use "civicone H3 heading — inline" instead.', 'civicone' ),
			'categories'  => array( 'civicone', 'text' ),
			'file'        => 'section-heading-h3.html',
		),
		array(
			'slug'        => 'civicone/topic-page-header',
			'title'       => __( 'Topic page header', 'civicone' ),
			'description' => __( 'H2 page title — use on About, Programs, and Resources child pages.', 'civicone' ),
			'categories'  => array( 'civicone' ),
			'file'        => 'topic-page-header.html',
		),
		array(
			'slug'        => 'civicone/blog-category-filter',
			'title'       => __( 'Blog category filter', 'civicone' ),
			'description' => __( 'Text links to news category archives (All, News, Land use, Safety).', 'civicone' ),
			'categories'  => array( 'civicone' ),
			'file'        => 'blog-category-filter.html',
		),
		array(
			'slug'        => 'civicone/service-card',
			'title'       => __( 'Service card', 'civicone' ),
			'description' => __( 'Heading + description + button card. Use inside a Columns block for multi-card rows. Add an Image block above the title after inserting.', 'civicone' ),
			'categories'  => array( 'civicone' ),
			'keywords'    => array( 'card', 'service', 'program', 'image' ),
			'file'        => 'service-card.html',
		),
		array(
			'slug'        => 'civicone/page-template',
			'title'       => __( 'civicone blank page', 'civicone' ),
			'description' => __( 'Starting point for any new page — site header, empty content area, site footer. Set the page to the "Blank" WordPress template before inserting to avoid a duplicate header/footer.', 'civicone' ),
			'categories'  => array( 'civicone-layout' ),
			'keywords'    => array( 'page', 'template', 'layout', 'full width', 'blank', 'new page' ),
			'file'        => 'page-template.html',
		),
		array(
			'slug'        => 'civicone/feature-page',
			'title'       => __( 'civicone embed page', 'civicone' ),
			'description' => __( 'Header + H2 title + description + shortcode placeholder. Use for pages that embed a third-party tool (wpForo, CalendarWiz, etc.). Set the page to the "Blank" WordPress template before inserting.', 'civicone' ),
			'categories'  => array( 'civicone-layout' ),
			'keywords'    => array( 'embed', 'page', 'shortcode', 'wpforo', 'calendar', 'template' ),
			'file'        => 'feature-page.html',
		),
	);

	foreach ( $patterns as $pattern ) {
		$content = civic_1_get_pattern_content( $pattern['file'] );
		if ( '' === $content ) {
			continue;
		}

		register_block_pattern(
			$pattern['slug'],
			array(
				'title'       => $pattern['title'],
				'description' => $pattern['description'] ?? '',
				'categories'  => $pattern['categories'] ?? array( 'civicone' ),
				'keywords'    => $pattern['keywords'] ?? array(),
				'content'     => $content,
			)
		);
	}
}
add_action( 'init', 'civic_1_register_patterns' );
