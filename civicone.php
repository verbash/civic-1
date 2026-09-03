<?php
/**
 * Plugin Name:       CivicOne
 * Plugin URI:        https://github.com/TuringTools/civicone
 * Description:       Portable UI kit — block patterns, civicone components (header, drawer, section nav), and base styles for CivicOne child themes.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Turing Tools
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       civicone
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
 * Enqueue CivicOne base stylesheet (tokens + global civicone rules).
 */
function civic_1_enqueue_styles(): void {
	$path = CIVIC_1_DIR . '/assets/civicone.css';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : '1.1.0';

	wp_enqueue_style(
		'civicone',
		CIVIC_1_URL . '/assets/civicone.css',
		array(),
		$ver
	);
}
add_action( 'wp_enqueue_scripts', 'civic_1_enqueue_styles' );

/**
 * Register civicone.css for the block editor.
 */
function civic_1_editor_styles(): void {
	$path = CIVIC_1_DIR . '/assets/civicone.css';
	if ( file_exists( $path ) ) {
		add_editor_style( CIVIC_1_URL . '/assets/civicone.css' );
	}
}
add_action( 'after_setup_theme', 'civic_1_editor_styles' );
