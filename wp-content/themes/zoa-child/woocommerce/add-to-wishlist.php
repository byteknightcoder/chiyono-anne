<?php
/**
 * Add to wishlist template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

global $product;
?>

<div class="yith-wcwl-add-to-wishlist add-to-wishlist-<?php echo $product_id ?>">
	<?php if( ! ( $disable_wishlist && ! is_user_logged_in() ) ): ?>
	    <div class="yith-wcwl-add-button <?php echo ( $exists && ! $available_multi_wishlist ) ? 'hide': 'show' ?>" style="display:<?php echo ( $exists && ! $available_multi_wishlist ) ? 'none': 'inline-block' ?>">

	        <?php yith_wcwl_get_template( 'add-to-wishlist-' . $template_part . '.php', $atts ); ?>

	    </div>

	    <div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;">
	        <a class="remove_from_wishlist_custom<?php if (is_product()) {?> cta cta-wish<?php } ?>" href="#" rel="nofollow" data-product-id="<?php echo $product_id ?>">
	            <?php _e( 'Remove from wishlist', 'yith-woocommerce-wishlist' ) ?>
	        </a>
		    <img src="<?php echo esc_url( YITH_WCWL_URL . 'assets/images/wpspin_light.gif' ) ?>" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden" />
	    </div>

	    <div class="yith-wcwl-wishlistexistsbrowse <?php echo ( $exists && ! $available_multi_wishlist ) ? 'show' : 'hide' ?>" style="display:<?php echo ( $exists && ! $available_multi_wishlist ) ? 'inline-block' : 'none' ?>">
	        <a class="remove_from_wishlist_custom<?php if (is_product()) {?> cta cta-wish<?php } ?>" href="#" rel="nofollow" data-product-id="<?php echo $product_id ?>">
		        <?php _e( 'Remove from wishlist', 'yith-woocommerce-wishlist' ) ?>
	        </a>
		    <img src="<?php echo esc_url( YITH_WCWL_URL . 'assets/images/wpspin_light.gif' ) ?>" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden" />
	    </div>

	    
	    <div class="yith-wcwl-wishlistaddresponse"></div>
	<?php else: ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'wishlist_notice' => 'true', 'add_to_wishlist' => $product_id ), get_permalink( wc_get_page_id( 'myaccount' ) ) ) )?>" rel="nofollow" class="<?php echo str_replace( 'add_to_wishlist', '', $link_classes ) ?><?php if (is_product()) {?> cta cta-wish<?php } ?>" >
			<?php echo $icon ?>
			<?php echo $label ?>
		</a>
	<?php endif; ?>

</div>