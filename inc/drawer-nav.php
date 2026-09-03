<?php
/**
 * CivicOne component: Drawer navigation
 *
 * Mobile: first-party drawer (native <dialog>, menu tree from wp_navigation 791).
 * Desktop: core Navigation block (overlay disabled).
 *
 * @see civicone-component-library.md → Drawer navigation
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array{label: string, url: string, children: array<int, array{label: string, url: string}>}>
 */
function civicone_get_primary_nav_tree(): array {
	static $tree = null;

	if ( null !== $tree ) {
		return $tree;
	}

	$post = get_post( civicone_get_primary_nav_ref() );
	if ( ! $post instanceof WP_Post ) {
		$tree = array();
		return $tree;
	}

	$tree = civicone_parse_navigation_blocks( parse_blocks( $post->post_content ) );
	return $tree;
}

/**
 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
 * @return array<int, array{label: string, url: string, children: array<int, array{label: string, url: string}>}>
 */
function civicone_parse_navigation_blocks( array $blocks ): array {
	$items = array();

	foreach ( $blocks as $block ) {
		$name = $block['blockName'] ?? '';
		$attrs = $block['attrs'] ?? array();

		if ( 'core/navigation-link' === $name ) {
			$items[] = array(
				'label'    => (string) ( $attrs['label'] ?? '' ),
				'url'      => (string) ( $attrs['url'] ?? '' ),
				'children' => array(),
			);
			continue;
		}

		if ( 'core/navigation-submenu' === $name ) {
			$children = array();
			foreach ( civicone_parse_navigation_blocks( $block['innerBlocks'] ?? array() ) as $child ) {
				if ( '' === $child['label'] || '' === $child['url'] ) {
					continue;
				}
				$children[] = array(
					'label' => $child['label'],
					'url'   => $child['url'],
				);
			}

			$items[] = array(
				'label'    => (string) ( $attrs['label'] ?? '' ),
				'url'      => (string) ( $attrs['url'] ?? '' ),
				'children' => $children,
			);
		}
	}

	return $items;
}

/**
 * Whether a URL matches the current request path.
 */
function civicone_get_current_request_path(): string {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '/';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$path = untrailingslashit( $path );

	return '' === $path ? '/' : $path;
}

/**
 * Whether a URL matches the current request path.
 */
function civicone_nav_url_is_current( string $url ): bool {
	if ( '' === $url ) {
		return false;
	}

	$current_path = civicone_get_current_request_path();
	$link_path    = (string) wp_parse_url( $url, PHP_URL_PATH );
	$link_path    = untrailingslashit( $link_path );
	$link_path    = '' === $link_path ? '/' : $link_path;

	if ( '/' === $link_path ) {
		return '/' === $current_path;
	}

	return $current_path === $link_path || str_starts_with( $current_path, $link_path . '/' );
}

/**
 * Stable id for accordion panels.
 */
function civicone_drawer_panel_id( string $label ): string {
	return 'civicone-drawer-panel-' . sanitize_title( $label );
}

/**
 * Render mobile drawer markup (dialog + menu list).
 */
function civicone_render_mobile_drawer_nav(): string {
	$items = civicone_get_primary_nav_tree();
	if ( array() === $items ) {
		return '';
	}

	$list = '';
	foreach ( $items as $item ) {
		$list .= civicone_render_drawer_nav_item( $item );
	}

	$dialog_id = 'civicone-drawer-menu';

	ob_start();
	?>
	<div class="civicone-drawer" data-civicone-drawer>
		<button
			type="button"
			class="civicone-drawer__open"
			aria-haspopup="dialog"
			aria-controls="<?php echo esc_attr( $dialog_id ); ?>"
			aria-label="<?php esc_attr_e( 'Open menu', 'civicone' ); ?>"
		>
			<span class="civicone-drawer__open-icon" aria-hidden="true"></span>
		</button>
		<dialog
			id="<?php echo esc_attr( $dialog_id ); ?>"
			class="civicone-drawer__dialog"
			aria-label="<?php esc_attr_e( 'Main menu', 'civicone' ); ?>"
		>
			<div class="civicone-drawer__overlay" data-civicone-drawer-overlay>
				<button
					type="button"
					class="civicone-drawer__scrim"
					data-civicone-drawer-scrim
					tabindex="-1"
					aria-hidden="true"
				></button>
				<button
					type="button"
					class="civicone-drawer__close"
					data-civicone-drawer-close
					aria-label="<?php esc_attr_e( 'Close menu', 'civicone' ); ?>"
				>
					<span class="civicone-drawer__close-icon" aria-hidden="true">&#10005;</span>
				</button>
				<div class="civicone-drawer__panel">
					<nav class="civicone-drawer__nav" aria-label="<?php esc_attr_e( 'Main', 'civicone' ); ?>">
						<ul class="civicone-drawer__list">
							<?php echo $list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in civicone_render_drawer_nav_item. ?>
						</ul>
					</nav>
				</div>
			</div>
		</dialog>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * @param array{label: string, url: string, children: array<int, array{label: string, url: string}>} $item Nav item.
 */
function civicone_render_drawer_nav_item( array $item ): string {
	$label    = $item['label'];
	$url      = $item['url'];
	$children = $item['children'];

	if ( '' === $label ) {
		return '';
	}

	$is_current = civicone_nav_url_is_current( $url );
	$current    = $is_current ? ' aria-current="page"' : '';

	if ( array() === $children ) {
		return sprintf(
			'<li class="civicone-drawer__item"><a class="civicone-drawer__link" href="%s"%s>%s</a></li>',
			esc_url( $url ),
			$current,
			esc_html( $label )
		);
	}

	$panel_id   = civicone_drawer_panel_id( $label );
	$trigger_id = $panel_id . '-trigger';
	$sublist    = '';

	foreach ( $children as $child ) {
		$child_current = civicone_nav_url_is_current( $child['url'] ) ? ' aria-current="page"' : '';
		$sublist      .= sprintf(
			'<li class="civicone-drawer__subitem"><a class="civicone-drawer__link" href="%s"%s>%s</a></li>',
			esc_url( $child['url'] ),
			$child_current,
			esc_html( $child['label'] )
		);
	}

	return sprintf(
		'<li class="civicone-drawer__item civicone-drawer__item--section">
			<div class="civicone-drawer__row">
				<a class="civicone-drawer__link" href="%1$s"%2$s>%3$s</a>
				<button type="button" class="civicone-drawer__disclosure" id="%4$s" aria-expanded="false" aria-controls="%5$s" aria-label="%6$s">
					<span class="civicone-drawer__disclosure-icon" aria-hidden="true"></span>
				</button>
			</div>
			<ul id="%5$s" class="civicone-drawer__sublist" role="list" hidden>%7$s</ul>
		</li>',
		esc_url( $url ),
		$current,
		esc_html( $label ),
		esc_attr( $trigger_id ),
		esc_attr( $panel_id ),
		esc_attr(
			sprintf(
				/* translators: %s: parent menu label */
				__( 'Show %s submenu', 'civicone' ),
				$label
			)
		),
		$sublist
	);
}

/**
 * Whether this navigation block is the primary header menu.
 *
 * @param array<string, mixed> $block Parsed block.
 */
function civicone_is_primary_header_navigation( array $block ): bool {
	if ( 'core/navigation' !== ( $block['blockName'] ?? '' ) ) {
		return false;
	}

	return (int) ( $block['attrs']['ref'] ?? 0 ) === civicone_get_primary_nav_ref();
}

/**
 * Desktop nav only — no core mobile overlay.
 *
 * @param array<string, mixed> $parsed_block Parsed block.
 * @return array<string, mixed>
 */
function civicone_navigation_block_data( array $parsed_block ): array {
	if ( ! civicone_is_primary_header_navigation( $parsed_block ) ) {
		return $parsed_block;
	}

	$parsed_block['attrs']['overlayMenu'] = 'never';
	unset( $parsed_block['attrs']['icon'] );

	return $parsed_block;
}
add_filter( 'render_block_data', 'civicone_navigation_block_data', 10, 1 );

/**
 * Wrap primary navigation: desktop block + mobile drawer.
 *
 * @param string               $block_content Block HTML.
 * @param array<string, mixed> $block         Parsed block.
 */
function civicone_filter_primary_navigation_block( string $block_content, array $block ): string {
	if ( ! civicone_is_primary_header_navigation( $block ) ) {
		return $block_content;
	}

	$drawer = civicone_render_mobile_drawer_nav();
	if ( '' === $drawer ) {
		return $block_content;
	}

	return sprintf(
		'<div class="civicone-nav">%s<div class="civicone-nav__desktop">%s</div></div>',
		$drawer,
		$block_content
	);
}
add_filter( 'render_block', 'civicone_filter_primary_navigation_block', 10, 2 );

/**
 * Whether drawer nav assets should load.
 */
function civicone_should_load_drawer_nav(): bool {
	return true;
}

