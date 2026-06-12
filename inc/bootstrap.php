<?php
/**
 * Civic-1 — load components, register assets, theme integration hooks.
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CIVIC_1_DIR . '/inc/config.php';
require_once CIVIC_1_DIR . '/inc/section-registry.php';
require_once CIVIC_1_DIR . '/inc/header-bar.php';
require_once CIVIC_1_DIR . '/inc/drawer-nav.php';
require_once CIVIC_1_DIR . '/inc/header-stability.php';
require_once CIVIC_1_DIR . '/inc/header-cta.php';
require_once CIVIC_1_DIR . '/inc/section-nav.php';
require_once CIVIC_1_DIR . '/inc/section-title.php';
require_once CIVIC_1_DIR . '/inc/embed-wrapper.php';
require_once CIVIC_1_DIR . '/inc/hub-anchors.php';
require_once CIVIC_1_DIR . '/inc/heading-styles.php';
require_once CIVIC_1_DIR . '/inc/patterns.php';

/**
 * Enqueue Civic-1 component stylesheets (drawer, mobile, CTA).
 */
function civic_1_enqueue_component_styles(): void {
	$deps_base = array( 'civic-1' );

	$mobile = CIVIC_1_DIR . '/assets/cv1-components-mobile.css';
	if ( file_exists( $mobile ) ) {
		wp_enqueue_style(
			'civic-1-mobile',
			CIVIC_1_URL . '/assets/cv1-components-mobile.css',
			$deps_base,
			(string) filemtime( $mobile )
		);
	}

	$drawer = CIVIC_1_DIR . '/assets/cv1-drawer-nav.css';
	if ( file_exists( $drawer ) && function_exists( 'cv1_should_load_drawer_nav' ) && cv1_should_load_drawer_nav() ) {
		wp_enqueue_style(
			'cv1-drawer-nav',
			CIVIC_1_URL . '/assets/cv1-drawer-nav.css',
			$deps_base,
			(string) filemtime( $drawer )
		);
	}

	$cta = CIVIC_1_DIR . '/assets/cv1-header-cta.css';
	if ( file_exists( $cta ) ) {
		wp_enqueue_style(
			'cv1-header-cta',
			CIVIC_1_URL . '/assets/cv1-header-cta.css',
			array( 'civic-1', 'cv1-critical-header' ),
			(string) filemtime( $cta )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'civic_1_enqueue_component_styles', 15 );

/**
 * Drawer navigation script.
 */
function civic_1_enqueue_component_scripts(): void {
	if ( ! function_exists( 'cv1_should_load_drawer_nav' ) || ! cv1_should_load_drawer_nav() ) {
		return;
	}

	$js = CIVIC_1_DIR . '/assets/cv1-drawer-nav.js';
	if ( ! file_exists( $js ) ) {
		return;
	}

	wp_enqueue_script(
		'cv1-drawer-nav',
		CIVIC_1_URL . '/assets/cv1-drawer-nav.js',
		array(),
		(string) filemtime( $js ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'civic_1_enqueue_component_scripts', 15 );

/**
 * Editor styles for component CSS (in addition to cv1.css).
 */
function civic_1_editor_component_styles(): void {
	foreach ( array( 'cv1-drawer-nav.css', 'cv1-components-mobile.css', 'cv1-header-cta.css' ) as $file ) {
		$path = CIVIC_1_DIR . '/assets/' . $file;
		if ( file_exists( $path ) ) {
			add_editor_style( CIVIC_1_URL . '/assets/' . $file );
		}
	}
}
add_action( 'after_setup_theme', 'civic_1_editor_component_styles', 11 );

/**
 * No public WordPress generator meta on the front end.
 */
function civic_1_remove_public_wp_branding(): void {
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'civic_1_remove_public_wp_branding' );
