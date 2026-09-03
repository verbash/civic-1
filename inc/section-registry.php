<?php
/**
 * CivicOne — section hub/tab registry (logic). Data via civic_1_section_configs filter.
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Section definitions registered by the active child theme.
 *
 * @return list<array{
 *   slug: string,
 *   label: string,
 *   hub_slug: string,
 *   tabs: list<array{label: string, url: string, slug: string}>
 * }>
 */
function civicone_get_section_configs(): array {
	$configs = apply_filters( 'civic_1_section_configs', array() );

	return is_array( $configs ) ? $configs : array();
}

/**
 * Config for the current request, if any.
 *
 * @return array<string, mixed>|null
 */
function civicone_get_current_section_config(): ?array {
	if ( ! is_page() ) {
		return null;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return null;
	}

	foreach ( civicone_get_section_configs() as $section ) {
		if ( civicone_page_belongs_to_section( $post, $section ) ) {
			return $section;
		}
	}

	return null;
}

/**
 * Whether a page belongs to a section (hub, tab target, or descendant).
 *
 * @param WP_Post              $post    Page.
 * @param array<string, mixed> $section Section config.
 */
function civicone_page_belongs_to_section( WP_Post $post, array $section ): bool {
	if ( $post->post_name === $section['hub_slug'] ) {
		return true;
	}

	$tab_slugs = array_column( $section['tabs'], 'slug' );
	if ( in_array( $post->post_name, $tab_slugs, true ) ) {
		return true;
	}

	$hub = get_page_by_path( (string) $section['hub_slug'] );
	if ( ! $hub instanceof WP_Post ) {
		return false;
	}

	$ancestors = get_post_ancestors( $post );
	if ( in_array( (int) $hub->ID, $ancestors, true ) ) {
		return true;
	}

	return (int) $post->post_parent === (int) $hub->ID;
}

/**
 * Active page-tab slug for the current section.
 *
 * @param array<string, mixed> $section Section config.
 */
function civicone_get_current_section_tab_slug( array $section ): string {
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	if ( $post->post_name === $section['hub_slug'] ) {
		return (string) $section['hub_slug'];
	}

	$tab_slugs = array_column( $section['tabs'], 'slug' );
	if ( in_array( $post->post_name, $tab_slugs, true ) ) {
		return $post->post_name;
	}

	foreach ( get_post_ancestors( $post ) as $ancestor_id ) {
		$name = get_post_field( 'post_name', $ancestor_id );
		if ( is_string( $name ) && in_array( $name, $tab_slugs, true ) ) {
			return $name;
		}
	}

	return '';
}

/**
 * Whether the current page is a section hub landing page.
 *
 * @param array<string, mixed>|null $section Optional section config.
 */
function civicone_is_section_hub( ?array $section = null ): bool {
	$section = $section ?? civicone_get_current_section_config();
	if ( null === $section || ! is_page() ) {
		return false;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	return $post->post_name === $section['hub_slug'];
}
