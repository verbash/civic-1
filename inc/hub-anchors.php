<?php
/**
 * Hub “On this page” anchor strip — fillable block pattern, hoisted into section subnav.
 *
 * Editors insert the civic-1/hub-on-page-anchors pattern on a section hub page.
 * The group uses class cv1-hub-on-page-anchors; links may live in a list or paragraph.
 * Front-end: links are parsed and rendered in .cv1-section-subnav__anchors; the group is hidden.
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a block is the hub on-page anchors container.
 *
 * @param array<string, mixed> $block Block data.
 */
function civic_1_is_hub_on_page_anchors_block( array $block ): bool {
	if ( ( $block['blockName'] ?? '' ) !== 'core/group' ) {
		return false;
	}

	$class = (string) ( $block['attrs']['className'] ?? '' );

	return str_contains( $class, 'cv1-hub-on-page-anchors' );
}

/**
 * Parse anchor links from post content (hub pattern).
 *
 * @param int|null $post_id Page ID; defaults to queried object.
 * @return list<array{label: string, url: string}>
 */
function civic_1_get_hub_on_page_anchors( ?int $post_id = null ): array {
	$post_id = $post_id ?? (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return array();
	}

	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post || '' === $post->post_content ) {
		return array();
	}

	$container = civic_1_find_hub_on_page_anchors_block( parse_blocks( $post->post_content ) );
	if ( null === $container ) {
		return array();
	}

	$links = civic_1_collect_anchor_links_from_blocks( array( $container ), $post_id );
	$out   = array();

	foreach ( $links as $link ) {
		$label = trim( wp_strip_all_tags( $link['label'] ) );
		$url   = $link['url'];
		if ( '' === $label || '' === $url ) {
			continue;
		}
		$out[] = array(
			'label' => $label,
			'url'   => $url,
		);
	}

	return $out;
}

/**
 * Find the hub anchors group block in a block tree.
 *
 * @param list<array<string, mixed>> $blocks Blocks.
 * @return array<string, mixed>|null
 */
function civic_1_find_hub_on_page_anchors_block( array $blocks ): ?array {
	foreach ( $blocks as $block ) {
		if ( civic_1_is_hub_on_page_anchors_block( $block ) ) {
			return $block;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = civic_1_find_hub_on_page_anchors_block( $block['innerBlocks'] );
			if ( null !== $found ) {
				return $found;
			}
		}
	}

	return null;
}

/**
 * Collect raw links from blocks inside the anchors container.
 *
 * @param list<array<string, mixed>> $blocks  Blocks to walk.
 * @param int                        $post_id Page ID for resolving hash-only hrefs.
 * @return list<array{label: string, url: string}>
 */
function civic_1_collect_anchor_links_from_blocks( array $blocks, int $post_id ): array {
	$links = array();

	foreach ( $blocks as $block ) {
		if ( civic_1_is_hub_on_page_anchors_block( $block ) ) {
			$inner = $block['innerBlocks'] ?? array();
			if ( is_array( $inner ) && ! empty( $inner ) ) {
				$links = array_merge( $links, civic_1_collect_anchor_links_from_blocks( $inner, $post_id ) );
			}
			$html = (string) ( $block['innerHTML'] ?? '' );
			if ( '' !== $html ) {
				$links = array_merge( $links, civic_1_parse_anchor_links_from_html( $html, $post_id ) );
			}
			continue;
		}

		$name = (string) ( $block['blockName'] ?? '' );
		if ( in_array( $name, array( 'core/list', 'core/paragraph' ), true ) ) {
			$html  = (string) ( $block['innerHTML'] ?? '' );
			$links = array_merge( $links, civic_1_parse_anchor_links_from_html( $html, $post_id ) );
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$links = array_merge( $links, civic_1_collect_anchor_links_from_blocks( $block['innerBlocks'], $post_id ) );
		}
	}

	return $links;
}

/**
 * Extract anchor links from HTML (paragraph or list).
 *
 * @param string $html    Block inner HTML.
 * @param int    $post_id Page ID.
 * @return list<array{label: string, url: string}>
 */
function civic_1_parse_anchor_links_from_html( string $html, int $post_id ): array {
	$links = array();

	if ( ! preg_match_all( '/<a\s[^>]*href=(["\'])([^"\']+)\1[^>]*>(.*?)<\/a>/is', $html, $matches, PREG_SET_ORDER ) ) {
		return $links;
	}

	$permalink = get_permalink( $post_id );
	if ( ! is_string( $permalink ) ) {
		$permalink = '';
	}

	foreach ( $matches as $match ) {
		$href  = html_entity_decode( (string) $match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$label = (string) $match[3];
		$url   = civic_1_resolve_hub_anchor_href( $href, $permalink );

		if ( '' !== $url ) {
			$links[] = array(
				'label' => $label,
				'url'   => $url,
			);
		}
	}

	return $links;
}

/**
 * Resolve href to a full URL (hash-only links use the hub permalink).
 *
 * @param string $href      Link href from editor.
 * @param string $permalink Hub page permalink.
 */
function civic_1_resolve_hub_anchor_href( string $href, string $permalink ): string {
	$href = trim( $href );
	if ( '' === $href ) {
		return '';
	}

	if ( str_starts_with( $href, '#' ) ) {
		return '' !== $permalink ? $permalink . $href : $href;
	}

	if ( str_starts_with( $href, '/' ) && str_contains( $href, '#' ) ) {
		return home_url( $href );
	}

	return $href;
}

/**
 * Ensure core/heading block output includes id from the anchor attribute.
 *
 * @param string               $content Rendered block HTML.
 * @param array<string, mixed> $block   Block data.
 */
function civic_1_heading_anchor_id( string $content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/heading' ) {
		return $content;
	}

	$anchor = isset( $block['attrs']['anchor'] ) ? sanitize_title( (string) $block['attrs']['anchor'] ) : '';
	if ( '' === $anchor || preg_match( '/\sid=["\']/', $content ) ) {
		return $content;
	}

	$replaced = preg_replace(
		'/(<h[1-6])(\s)/i',
		'$1 id="' . esc_attr( $anchor ) . '"$2',
		$content,
		1
	);

	return is_string( $replaced ) ? $replaced : $content;
}
add_filter( 'render_block', 'civic_1_heading_anchor_id', 10, 2 );

/**
 * Hide the fillable anchors group on the front end (markup is hoisted to section subnav).
 *
 * @param string               $content Rendered block HTML.
 * @param array<string, mixed> $block   Block data.
 */
function civic_1_hide_hub_on_page_anchors_block( string $content, array $block ): string {
	if ( is_admin() || ! civic_1_is_hub_on_page_anchors_block( $block ) ) {
		return $content;
	}

	return '';
}
add_filter( 'render_block', 'civic_1_hide_hub_on_page_anchors_block', 10, 2 );
