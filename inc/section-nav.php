<?php
/**
 * Section page-tabs + anchor-tabs (About, Programs and Services, Resources).
 *
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Markup for section subnav (page tabs + optional hub anchor tabs).
 *
 * @param array<string, mixed> $section Section config.
 */
function cv1_render_section_subnav( array $section ): string {
	$current  = cv1_get_current_section_tab_slug( $section );
	$on_hub   = cv1_is_section_hub( $section );
	$tabs     = $section['tabs'];
	$anchors  = array();
	if ( $on_hub && function_exists( 'civic_1_get_hub_on_page_anchors' ) ) {
		$anchors = civic_1_get_hub_on_page_anchors();
	}
	$modifier = ' cv1-section-subnav--' . sanitize_html_class( (string) $section['slug'] );

	ob_start();
	?>
	<nav class="cv1-section-subnav<?php echo $on_hub ? ' cv1-section-subnav--on-hub' : ''; ?><?php echo esc_attr( $modifier ); ?>" aria-label="<?php echo esc_attr( (string) $section['label'] ); ?>">
		<ul class="cv1-section-subnav__tabs" role="list">
			<?php foreach ( $tabs as $tab ) : ?>
				<?php
				$is_current = ( $current === $tab['slug'] );
				$li_class   = $is_current ? ' is-active' : '';
				?>
				<li class="cv1-section-subnav__item<?php echo esc_attr( $li_class ); ?>" role="listitem">
					<a href="<?php echo esc_url( $tab['url'] ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
						<?php echo esc_html( $tab['label'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( ! empty( $anchors ) ) : ?>
		<ul class="cv1-section-subnav__anchors" role="list" aria-label="<?php esc_attr_e( 'On this page', 'civic-1' ); ?>">
			<?php foreach ( $anchors as $anchor ) : ?>
				<li class="cv1-section-subnav__item cv1-section-subnav__item--anchor" role="listitem">
					<a href="<?php echo esc_url( $anchor['url'] ); ?>"><?php echo esc_html( $anchor['label'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</nav>
	<?php
	return (string) ob_get_clean();
}

/**
 * Prepend section subnav to post content on section pages.
 *
 * @param string $content Block content.
 * @param array  $block   Block data.
 */
function cv1_prepend_section_subnav_to_post_content( string $content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/post-content' ) {
		return $content;
	}

	$section = cv1_get_current_section_config();
	if ( null === $section ) {
		return $content;
	}

	static $rendered = false;
	if ( $rendered ) {
		return $content;
	}
	$rendered = true;

	return cv1_render_section_subnav( $section ) . $content;
}
add_filter( 'render_block', 'cv1_prepend_section_subnav_to_post_content', 9, 2 );

