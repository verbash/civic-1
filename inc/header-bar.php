<?php
/**
 * CivicOne component: Header bar (logo + primary nav).
 *
 * Canonical block markup for the site header. Used by the theme block pattern
 * and template parts; mobile drawer/CTA attach via render_block in drawer-nav.php
 * and header-cta.php.
 *
 * @see civicone-component-library.md → Drawer navigation, Header CTA
 * @package Civic1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block markup for the civicone header bar (logo left, nav right, overlayMenu never).
 */
function civicone_header_bar_markup(): string {
	$nav_ref = civicone_get_primary_nav_ref();

	return <<<HTML
<!-- wp:group {"align":"full","className":"civicone-header-bar","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"0","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull civicone-header-bar">
	<!-- wp:group {"align":"wide","className":"civicone-header-bar__inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group alignwide civicone-header-bar__inner">
		<!-- wp:site-logo {"width":80,"shouldSyncIcon":false,"className":"civicone-header-bar__logo"} /-->

		<!-- wp:navigation {"ref":{$nav_ref},"overlayMenu":"never","layout":{"type":"flex","justifyContent":"right","flexWrap":"nowrap"}} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"wide","className":"civicone-header-bar__wordmark","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide civicone-header-bar__wordmark">
		<!-- wp:site-title {"level":0,"isLink":true,"className":"civicone-header-bar__sitename"} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
}
