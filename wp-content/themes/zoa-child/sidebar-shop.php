<?php
	$shop_sidebar = get_theme_mod( 'shop_sidebar', 'full' );
	if ( ! is_active_sidebar( 'shop-widget' ) || 'full' === $shop_sidebar || is_product() || post_password_required()||is_page('special-event')) {
		return;
	}
?>
<div class="shop-sidebar refinements col-lg-3 col-6">
	<!--<div class="lds-dual-ring">Loading...</div>-->
	<span id="refinementsBarTrigger" class="filter-by display--mid-only toggle__link toggle__link--cta flex-justify-between icon"><?php esc_html_e('Filter By', 'zoa'); ?></span>
	<?php dynamic_sidebar( 'shop-widget' ); ?>
</div>
