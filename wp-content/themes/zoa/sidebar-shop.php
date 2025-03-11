<?php
$shop_sidebar = get_theme_mod( 'shop_sidebar', 'full' );
if ( ! is_active_sidebar( 'shop-widget' ) || 'full' === $shop_sidebar || is_product() ) {
	return;
}
?>

<div class="widget-area shop-sidebar">
	<?php dynamic_sidebar( 'shop-widget' ); ?>
</div>
