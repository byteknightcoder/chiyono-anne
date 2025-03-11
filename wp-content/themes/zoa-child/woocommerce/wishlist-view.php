<?php
/**
 * VictorTheme Custom Changes - Table Structure Changed
 */

/**
 * Wishlist page template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.12
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php do_action( 'yith_wcwl_before_wishlist_form', $wishlist_meta ); ?>

<form id="yith-wcwl-form" action="<?php echo esc_url($form_action); ?>" method="post" class="woocommerce">

    <?php wp_nonce_field( 'yith-wcwl-form', 'yith_wcwl_form_nonce' ) ?>

    <!-- TITLE -->
    <?php
    do_action( 'yith_wcwl_before_wishlist_title' );

    if( ! empty( $page_title ) ) :
    ?>
        <div class="account__heading wishlist-title <?php echo ( $is_custom_list ) ? 'wishlist-title-with-form' : ''?>">
            <?php echo apply_filters( 'yith_wcwl_wishlist_title', '<h1 class="heading heading--xlarge serif">' . $page_title . '</h1>' ); ?>
            <?php if( $is_custom_list ): ?>
                <a class="btn button show-title-form">
                    <?php echo apply_filters( 'yith_wcwl_edit_title_icon', '<i class="fa fa-pencil"></i>' )?>
                    <?php esc_html_e( 'Edit title', 'zoa' ) ?>
                </a>
            <?php endif; ?>
			<?php
		        do_action( 'yith_wcwl_before_wishlist_share', $wishlist_meta );

		        if ( is_user_logged_in() && $is_user_owner && ! $is_private && $share_enabled ){
			        yith_wcwl_get_template( 'share.php', $share_atts );
		        }

		        do_action( 'yith_wcwl_after_wishlist_share', $wishlist_meta );
		        ?>
        </div>
        <?php if( $is_custom_list ): ?>
            <div class="hidden-title-form">
                <input type="text" value="<?php echo esc_attr($page_title); ?>" name="wishlist_name"/>
                <button>
                    <?php echo apply_filters( 'yith_wcwl_save_wishlist_title_icon', '<i class="fa fa-check"></i>' )?>
                    <?php esc_html_e( 'Save', 'zoa' )?>
                </button>
                <a class="hide-title-form btn button">
                    <?php echo apply_filters( 'yith_wcwl_cancel_wishlist_title_icon', '<i class="fa fa-remove"></i>' )?>
                    <?php esc_html_e( 'Cancel', 'zoa' )?>
                </a>
            </div>
        <?php endif; ?>
    <?php
    endif;

    do_action( 'yith_wcwl_before_wishlist' ); ?>

    <!-- WISHLIST TABLE -->
	
		<?php
        if( count( $wishlist_items ) > 0 ) :
	         ?>
	<div id="wishlistList" class="product-list wishlist__list item-list" data-pagination="<?php echo esc_attr( $pagination )?>" data-per-page="<?php echo esc_attr( $per_page )?>" data-page="<?php echo esc_attr( $current_page )?>" data-id="<?php echo esc_attr($wishlist_id); ?>" data-token="<?php echo $wishlist_token; ?>">

	    <?php $column_count = 2; ?>

		<?php 
		$added_items = array();
            foreach( $wishlist_items as $item ) :
                global $product;

	            $item['prod_id'] = yit_wpml_object_id ( $item['prod_id'], 'product', true );

	            if( in_array( $item['prod_id'], $added_items ) ) {
		            continue;
	            }

	            $added_items[] = $item['prod_id'];
	            $product = wc_get_product( $item['prod_id'] );
	            $availability = $product->get_availability();
	            $stock_status = $availability['class'];
	            $serie_cat = get_the_terms($item['prod_id'], 'series');

                if( $product && $product->exists() ) :
		?>
		

        

                    <div id="yith-wcwl-row-<?php echo esc_attr($item['prod_id']); ?>" data-row-id="<?php echo esc_attr($item['prod_id']); ?>" class="product-list__item wishlist__item item">

						<div class="wishlist__item__details mini-product--group">
                        
                            <a class="mini-product__link" href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>">
                                <?php echo $product->get_image() ?>
                            </a>
							<div class="mini-product__info">
								<?php if (!empty($serie_cat)) {
								echo '<div class="mini-product__item mini-product__series heading heading--small">'. $serie_cat[0]->name .'</div>';
								}
								?>
								<p class="mini-product__item mini-product__name"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ?></p>
								<?php do_action( 'yith_wcwl_table_after_product_name', $item ); ?>
								<?php if( $show_stock_status ) : ?>
							<div class="mini-product__item mini-product__attribute mini-product__stock display--small-only">
								<span class="label">Stock: </span><?php echo $stock_status == 'out-of-stock' ? '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of Stock', 'zoa' ) . '</span>' : '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'zoa' ) . '</span>'; ?>
							</div>
							<?php endif ?>
							<?php if( $show_price ) : ?>
							<div class="mini-product__item mini-product__attribute mini-product__price display--small-only">
								<span class="label">Price: </span><?php echo $product->get_price_html(); ?>
							</div>
							<?php endif ?>
							</div>
							
						</div><!--/mini-product--group-->
						<div class="wishlist__item__extras item-dashboard">
							<?php if( $show_stock_status ) : ?>
                            <div class="wishlist__item__extras__col display--small-up" title="Stock: ">
                                <?php echo $stock_status == 'out-of-stock' ? '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of Stock', 'zoa' ) . '</span>' : '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'zoa' ) . '</span>'; ?>
                            </div>
							<?php endif ?>
							<?php if( $show_price ) : ?>
							<div class="wishlist__item__extras__col display--small-up" title="Price: ">
								<?php echo $product->get_price_html(); ?>
							</div>
							<?php endif ?>
							
							
							<div class="wishlist__item__extras__actions align--center">
							<!-- Add to cart button -->
                            <?php if( $show_add_to_cart && isset( $stock_status ) && $stock_status != 'out-of-stock' ): ?>
                                <span class="button-full-div"><?php woocommerce_template_loop_add_to_cart(); ?></span>
                            <?php endif ?>

	                        <!-- Change wishlist -->
							<?php if( $available_multi_wishlist && is_user_logged_in() && count( $users_wishlists ) > 1 && $move_to_another_wishlist && $is_user_owner ): ?>
	                        <select class="change-wishlist selectBox">
		                        <option value=""><?php esc_html_e( 'Move', 'zoa' ) ?></option>
		                        <?php
		                        foreach( $users_wishlists as $wl ):
			                        if( $wl['wishlist_token'] == $wishlist_meta['wishlist_token'] ){
				                        continue;
			                        }
		                        ?>
			                        <option value="<?php echo esc_attr( $wl['wishlist_token'] ) ?>">
				                        <?php
				                        $wl_title = ! empty( $wl['wishlist_name'] ) ? esc_html( $wl['wishlist_name'] ) : esc_html( $default_wishlsit_title );
				                        if( $wl['wishlist_privacy'] == 1 ){
					                        $wl_privacy = esc_html__( 'Shared', 'zoa' );
				                        }
				                        elseif( $wl['wishlist_privacy'] == 2 ){
					                        $wl_privacy = esc_html__( 'Private', 'zoa' );
				                        }
				                        else{
					                        $wl_privacy = esc_html__( 'Public', 'zoa' );
				                        }

				                        echo sprintf( '%s - %s', $wl_title, $wl_privacy );
				                        ?>
			                        </option>
		                        <?php
		                        endforeach;
		                        ?>
	                        </select>
	                        <?php endif; ?>
							<div class="product-list__item__actions">
							<!-- Remove from wishlist -->
	                        <?php if( $is_user_owner && $repeat_remove_button ): ?>
                                <a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item['prod_id'] ) ) ?>" class="remove_from_wishlist product-list__item__action cta cta--underlined" title="<?php esc_html_e( 'Remove From Wishlist', 'zoa' ) ?>"><?php esc_html_e( 'Remove From Wishlist', 'zoa' ) ?></a>
                            <?php endif; ?>
							<?php if( $is_user_owner ): ?>
                                    <a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item['prod_id'] ) ) ?>" class="remove_from_wishlist product-list__item__action cta cta--underlined" title="<?php esc_html_e( 'Remove From Wishlist', 'zoa' ) ?>"><?php esc_html_e( 'Remove From Wishlist', 'zoa' ) ?></a>
							<?php endif; ?>
							</div>
							</div><!--/wishlist__item__extras__actions-->
						</div><!--/item-dashboard-->

                        
                    
					</div>
		<?php
                endif;
            endforeach;
         ?>
		</div>
                <?php
        else: ?>
            <div class="wishlist-empty align--center">
                <h2 class="ja heading--xlarge spacing--normal"><?php echo apply_filters( 'yith_wcwl_no_product_to_remove_message', esc_html__( 'No products were added to the wishlist', 'zoa' ) ) ?></h2>
				<p class="wishlist_desc"><?php esc_html_e( 'あなたの気になる商品のハートマークをタップするとリストに追加されます。', 'zoa' ) ?></p>
				<p class="return-to-shop">
		<a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Return to shop', 'zoa' ) ?>
		</a>
	</p>
            </div>
        <?php
        endif;

        if( ! empty( $page_links ) ) : ?>
            <div class="pagination-row"><?php echo $page_links; ?></div>
        <?php endif ?>
		
		

        <!-- Custom Changes: tfoot Removed -->


    <?php wp_nonce_field( 'yith_wcwl_edit_wishlist_action', 'yith_wcwl_edit_wishlist' ); ?>

    <?php if( ! $is_default ): ?>
        <input type="hidden" value="<?php echo $wishlist_token; ?>" name="wishlist_id" id="wishlist_id">
    <?php endif; ?>

    <?php do_action( 'yith_wcwl_after_wishlist' ); ?>

</form>

<?php do_action( 'yith_wcwl_after_wishlist_form', $wishlist_meta ); ?>

<?php if( $show_ask_estimate_button && ( ! is_user_logged_in() || $additional_info ) ): ?>
	<div id="ask_an_estimate_popup">
		<form action="<?php echo esc_url($ask_estimate_url); ?>" method="post" class="wishlist-ask-an-estimate-popup">
			<?php if( ! is_user_logged_in() ): ?>
				<label for="reply_email"><?php echo apply_filters( 'yith_wcwl_ask_estimate_reply_mail_label', esc_html__( 'Your email', 'zoa' ) ) ?></label>
				<input type="email" value="" name="reply_email" id="reply_email">
			<?php endif; ?>
			<?php if( ! empty( $additional_info_label ) ):?>
				<label for="additional_notes"><?php echo esc_html( $additional_info_label ) ?></label>
			<?php endif; ?>
			<textarea id="additional_notes" name="additional_notes"></textarea>

			<button class="btn button ask-an-estimate-button ask-an-estimate-button-popup" >
				<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fa fa-shopping-cart"></i>' )?>
				<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_text', esc_html__( 'Ask for an estimate', 'zoa' ) ) ?>
			</button>
		</form>
	</div>
<?php endif; ?>