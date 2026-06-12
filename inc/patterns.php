<?php
/**
 * Civic-1 — block pattern category and pattern registration.
 *
 * All content patterns live here (plugin) rather than in the child theme so
 * the library is portable to any TT2 / FSE child theme.
 *
 * NOTE: The header-bar pattern (`twentytwentytwo-d4c/header-bar`) is intentionally
 * kept in the child theme because it requires PHP rendering via
 * `cv1_header_bar_markup()`, which is defined in `plugins/civic-1/inc/header-bar.php`. That
 * function must exist on the site for the pattern to work.
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Civic-1 block pattern categories.
 */
function civic_1_register_pattern_categories(): void {
	register_block_pattern_category(
		'civic-1',
		array(
			'label'       => __( 'Civic-1', 'civic-1' ),
			'description' => __( 'Reusable UI components from the Civic-1 (cv1) design kit.', 'civic-1' ),
		)
	);
	register_block_pattern_category(
		'cv1-layout',
		array(
			'label' => __( 'Civic-1 layout', 'civic-1' ),
		)
	);
	register_block_pattern_category(
		'cv1-section',
		array(
			'label' => __( 'Civic-1 section', 'civic-1' ),
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
			esc_html( "Civic-1: pattern file not readable: {$path}" ),
			E_USER_NOTICE
		);
		return '';
	}

	$content = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return is_string( $content ) ? $content : '';
}

/**
 * Register all Civic-1 block patterns.
 *
 * Slugs use the `civic-1/` namespace so they are decoupled from the child
 * theme slug. The child theme's `twentytwentytwo-d4c/*` patterns (header-bar)
 * remain registered separately via file-based discovery.
 */
function civic_1_register_patterns(): void {
	$patterns = array(
		array(
			'slug'        => 'civic-1/content-heading-h2',
			'title'       => __( 'cv1 H2 heading', 'civic-1' ),
			'description' => __( 'Pre-styled H2 for interior page content (cv1-page-title class, x-large). Start post content here — the page template renders the H1.', 'civic-1' ),
			'categories'  => array( 'civic-1', 'text' ),
			'keywords'    => array( 'heading', 'h2', 'title', 'section' ),
			'file'        => 'content-heading-h2.html',
		),
		array(
			'slug'        => 'civic-1/content-heading-h3',
			'title'       => __( 'cv1 H3 heading', 'civic-1' ),
			'description' => __( 'Pre-styled H3 for in-page subsections (cv1-section-heading class, large). Use inside content below a cv1 H2.', 'civic-1' ),
			'categories'  => array( 'civic-1', 'text' ),
			'keywords'    => array( 'heading', 'h3', 'subsection' ),
			'file'        => 'content-heading-h3.html',
		),
		array(
			'slug'        => 'civic-1/callout',
			'title'       => __( 'Civic-1 callout', 'civic-1' ),
			'description' => __( 'Newsletter-style notice box — flat fill, 1 px border, no shadow.', 'civic-1' ),
			'categories'  => array( 'civic-1', 'text' ),
			'file'        => 'callout.html',
		),
		array(
			'slug'        => 'civic-1/hub-card-topic',
			'title'       => __( 'Hub topic card', 'civic-1' ),
			'description' => __( 'Linked H3 card for section hub grids (Resources topics, Programs paths, etc.).', 'civic-1' ),
			'categories'  => array( 'civic-1' ),
			'file'        => 'hub-card-topic.html',
		),
		array(
			'slug'        => 'civic-1/hub-section-intro',
			'title'       => __( 'Hub section intro', 'civic-1' ),
			'description' => __( 'H2 hub intro below page tabs — e.g. Volunteer resources on the Resources landing page.', 'civic-1' ),
			'categories'  => array( 'civic-1' ),
			'file'        => 'hub-section-intro.html',
		),
		array(
			'slug'        => 'civic-1/hub-on-page-anchors',
			'title'       => __( 'Hub on-page anchors', 'civic-1' ),
			'description' => __( '“On this page” jump links for section hubs. Place at the top of hub content; links hoist into the section subnav. Match href hashes to heading anchor IDs in the page.', 'civic-1' ),
			'categories'  => array( 'civic-1' ),
			'file'        => 'hub-on-page-anchors.html',
		),
		array(
			'slug'        => 'civic-1/resource-rules-callout',
			'title'       => __( 'Resource rules callout', 'civic-1' ),
			'description' => __( 'H3 section title + H4 rule items in a callout box. Resources hub pattern.', 'civic-1' ),
			'categories'  => array( 'civic-1' ),
			'file'        => 'resource-rules-callout.html',
		),
		array(
			'slug'        => 'civic-1/section-heading-h3',
			'title'       => __( 'Section heading (H3)', 'civic-1' ),
			'description' => __( 'Major in-content subsection — H3 with optional anchor ID for hub anchor tabs.', 'civic-1' ),
			'categories'  => array( 'civic-1', 'text' ),
			'file'        => 'section-heading-h3.html',
		),
		array(
			'slug'        => 'civic-1/topic-page-header',
			'title'       => __( 'Topic page header', 'civic-1' ),
			'description' => __( 'H2 page title — use on About, Programs, and Resources child pages.', 'civic-1' ),
			'categories'  => array( 'civic-1' ),
			'file'        => 'topic-page-header.html',
		),
		array(
			'slug'        => 'civic-1/blog-category-filter',
			'title'       => __( 'Blog category filter', 'civic-1' ),
			'description' => __( 'Text links to news category archives (All, News, Land use, Safety).', 'civic-1' ),
			'categories'  => array( 'civic-1' ),
			'file'        => 'blog-category-filter.html',
		),
		array(
			'slug'        => 'civic-1/service-card',
			'title'       => __( 'Service card', 'civic-1' ),
			'description' => __( 'Image + heading + description + button. Use inside a Columns block for multi-card rows. Replace the placeholder image with your own.', 'civic-1' ),
			'categories'  => array( 'civic-1' ),
			'keywords'    => array( 'card', 'service', 'program', 'image' ),
			'file'        => 'service-card.html',
		),
		array(
			'slug'        => 'civic-1/page-template',
			'title'       => __( 'Civic-1 page template', 'civic-1' ),
			'description' => __( 'Full-width page layout: site header, constrained content area, site footer. Insert on a new page set to the "Blank" template to avoid duplicate header/footer.', 'civic-1' ),
			'categories'  => array( 'civic-1' ),
			'keywords'    => array( 'page', 'template', 'layout', 'full width' ),
			'file'        => 'page-template.html',
		),
		array(
			'slug'        => 'civic-1/feature-page',
			'title'       => __( 'Feature page (Community / embeds)', 'civic-1' ),
			'description' => __( 'Header + H2 intro + open area with wpForo shortcode placeholder. Use on a page set to the Blank template; edit title, intro, and replace or keep [wpforo].', 'civic-1' ),
			'categories'  => array( 'civic-1', 'cv1-layout' ),
			'keywords'    => array( 'feature', 'page', 'community', 'embed', 'wpforo', 'template' ),
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
				'categories'  => $pattern['categories'] ?? array( 'civic-1' ),
				'keywords'    => $pattern['keywords'] ?? array(),
				'content'     => $content,
			)
		);
	}
}
add_action( 'init', 'civic_1_register_patterns' );
