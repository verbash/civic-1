<?php
/**
 * Reusable embed wrapper for iframes (CalendarWiz, Airtable, etc.).
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrap core/html blocks that contain embeds (iframes or CalendarWiz responsive script).
 *
 * @param string               $block_content Block HTML.
 * @param array<string, mixed> $block         Parsed block.
 */
function cv1_wrap_iframe_html_blocks( string $block_content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/html' ) {
		return $block_content;
	}

	$has_iframe = str_contains( $block_content, '<iframe' );
	$has_calendarwiz_script = str_contains( $block_content, 'cwresponsive.js' )
		&& str_contains( $block_content, 'calendarwiz.com' );

	if ( ! $has_iframe && ! $has_calendarwiz_script ) {
		return $block_content;
	}

	if ( str_contains( $block_content, 'cv1-embed-region' ) ) {
		return $block_content;
	}

	$modifier = '';
	if ( str_contains( $block_content, 'calendarwiz.com' ) ) {
		$modifier = ' cv1-embed-region--calendar';
	} elseif ( str_contains( $block_content, 'airtable.com' ) ) {
		$modifier = ' cv1-embed-region--airtable';
	}

	return '<div class="cv1-embed-region' . esc_attr( $modifier ) . '">' . $block_content . '</div>';
}

add_filter( 'render_block', 'cv1_wrap_iframe_html_blocks', 12, 2 );
