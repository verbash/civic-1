<?php
/**
 * Plugin Name:       Civic-1
 * Plugin URI:        https://github.com/TuringTools/civic-1
 * Description:       Portable UI kit — block patterns, cv1 components (header, drawer, section nav), and base styles for Civic-1 child themes.
 * Version:           1.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Turing Tools
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       civic-1
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CIVIC_1_FILE', __FILE__ );
define( 'CIVIC_1_DIR', __DIR__ );
define( 'CIVIC_1_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

require_once CIVIC_1_DIR . '/inc/bootstrap.php';

/**
 * Enqueue Civic-1 base stylesheet (tokens + global cv1 rules).
 */
function civic_1_enqueue_styles(): void {
	$path = CIVIC_1_DIR . '/assets/cv1.css';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : '1.1.0';

	wp_enqueue_style(
		'civic-1',
		CIVIC_1_URL . '/assets/cv1.css',
		array(),
		$ver
	);
}
add_action( 'wp_enqueue_scripts', 'civic_1_enqueue_styles' );

/**
 * Register cv1.css for the block editor.
 */
function civic_1_editor_styles(): void {
	$path = CIVIC_1_DIR . '/assets/cv1.css';
	if ( file_exists( $path ) ) {
		add_editor_style( CIVIC_1_URL . '/assets/cv1.css' );
	}
}
add_action( 'after_setup_theme', 'civic_1_editor_styles' );
