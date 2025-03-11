<?php
/**
 * The Template for displaying wishlist if a current user is owner.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist.php.
 *
 * @version             2.3.3
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
wp_enqueue_script( 'tinvwl' );
?>
<div class="tinv-wishlist woocommerce tinv-wishlist-clear">
	<?php do_action( 'tinvwl_before_wishlist', $wishlist ); ?>
	<?php if ( function_exists( 'wc_print_notices' ) && isset( WC()->session ) ) {
		wc_print_notices();
	} ?>
	<form action="<?php echo esc_url( tinv_url_wishlist() ); ?>" method="post" autocomplete="off">
		<?php do_action( 'tinvwl_before_wishlist_table', $wishlist ); ?>
		<div class="tinvwl-table-manage-list product-list wishlist__list item-list">
			<div class="product-list__headings wishlist__list__headings heading heading--xsmall">
				<?php if ( isset( $wishlist_table['colm_checkbox'] ) && $wishlist_table['colm_checkbox'] ) { ?>
					<div class="wishlist__item__check--headings product-cb">
						<span class="checkbox_label"><input type="checkbox" class="global-cb" title="<?php _e( 'Select all for bulk action', 'ti-woocommerce-wishlist' ) ?>"></span>
					</div>
				<?php } ?>
				<span class="wishlist__item__details--headings item-headings-items product-name">
					<?php esc_html_e( 'Item', 'zoa' ); ?>
				</span>
				<div class="wishlist__item__extras--headings">
					<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) { ?>
					<span class="wishlist__item__extras__col">
						<?php esc_html_e( 'Stock ', 'zoa' ); ?>
					</span>
					<?php } ?>
					<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) { ?>
					<span class="wishlist__item__extras__col">
						<?php esc_html_e( 'Price', 'zoa' ); ?>
					</span>
					<?php } ?>
				</div>
			</div>
			<div id="wishlistList" class="product-list wishlist__list item-list">
			<?php do_action( 'tinvwl_wishlist_contents_before' ); ?>

			<?php
			foreach ( $products as $wl_product ) {
				$product = apply_filters( 'tinvwl_wishlist_item', $wl_product['data'] );
				unset( $wl_product['data'] );
				if ( $wl_product['quantity'] > 0 && apply_filters( 'tinvwl_wishlist_item_visible', true, $wl_product, $product ) ) {
					$product_url = apply_filters( 'tinvwl_wishlist_item_url', $product->get_permalink(), $wl_product, $product );
					do_action( 'tinvwl_wishlist_row_before', $wl_product, $product );
					?>
					<div class="product-list__item wishlist__item item <?php echo esc_attr( apply_filters( 'tinvwl_wishlist_item_class', 'wishlist_item', $wl_product, $product ) ); ?>">
						<?php if ( isset( $wishlist_table['colm_checkbox'] ) && $wishlist_table['colm_checkbox'] ) { ?>
							<div class="wishlist__item__col wishlist__item__cb product-cb">
								<?php
								echo apply_filters( 'tinvwl_wishlist_item_cb', sprintf( // WPCS: xss ok.
									'<span class="checkbox_label"><input type="checkbox" name="wishlist_pr[]" value="%d" title="%s"></span>', esc_attr( $wl_product['ID'] ), __( 'Select for bulk action', 'ti-woocommerce-wishlist' )
								), $wl_product, $product );
								?>
							</div>
						<?php } ?>
						<div class="wishlist__item__col wishlist__item__details mini-product--group">
						<?php
							$thumbnail = apply_filters( 'tinvwl_wishlist_item_thumbnail', $product->get_image(), $wl_product, $product );

							if ( ! $product->is_visible() ) {
								echo $thumbnail; // WPCS: xss ok.
							} else {
								printf( '<a class="mini-product__link" href="%s">%s</a>', esc_url( $product_url ), $thumbnail ); // WPCS: xss ok.
							}
						?>
							<div class="mini-product__info">
								<div class="mini-product__item mini-product__name-ja small-text"></div>
								
							<?php
					        echo '<div class="mini-product__item mini-product__name p5">';
							if ( ! $product->is_visible() ) {
								echo apply_filters( 'tinvwl_wishlist_item_name', $product->get_title(), $wl_product, $product ) . '&nbsp;'; // WPCS: xss ok.
							} else {
								echo apply_filters( 'tinvwl_wishlist_item_name', sprintf( '<a class="link" href="%s">%s</a>', esc_url( $product_url ), $product->get_title() ), $wl_product, $product ); // WPCS: xss ok.
							}
							echo '</div>';

							echo apply_filters( 'tinvwl_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product, $wl_product ), $wl_product, $product ); // WPCS: xss ok.
							?>
								
								<p class="mini-product__item mini-product__id light-copy">SKU <?php echo $product->get_sku(); ?></p>
								<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) { ?>
								<div class="mini-product__item mini-product__attribute mini-product__stock display--small-only">
									<?php
								$availability = (array) $product->get_availability();
								if ( ! array_key_exists( 'availability', $availability ) ) {
									$availability['availability'] = '';
								}
								if ( ! array_key_exists( 'class', $availability ) ) {
									$availability['class'] = '';
								}
								$availability_html = empty( $availability['availability'] ) ? '<span class="label">'. esc_html__( 'Stock', 'zoa' ) .'</span><span class="value tinvwl-txt ' . esc_attr( $availability['class'] ) . '">' . esc_html__( 'In stock', 'ti-woocommerce-wishlist' ) . '</span>' : '<span class="label">'. esc_html__( 'Stock', 'zoa' ) .'</span><span class="value tinvwl-txt ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';

								echo apply_filters( 'tinvwl_wishlist_item_status', $availability_html, $availability['availability'], $wl_product, $product ); // WPCS: xss ok.
								?>
								</div>
								<?php } ?>
								<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) { ?>
								<div class="mini-product__item mini-product__attribute mini-product__price display--small-only">
									<span class="label"><?php esc_html_e( 'Price', 'zoa' )?></span>
									<span class="value">
										<?php
											echo apply_filters( 'tinvwl_wishlist_item_price', $product->get_price_html(), $wl_product, $product ); // WPCS: xss ok.
										?>
									</span>
								</div>
								<?php } ?>
							</div>
						</div>
						<div class="wishlist__item__extras item-dashboard">
							<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) { ?>
							<div class="wishlist__item__extras__col display--small-up" title="Stock: ">
								<?php
								$availability = (array) $product->get_availability();
								if ( ! array_key_exists( 'availability', $availability ) ) {
									$availability['availability'] = '';
								}
								if ( ! array_key_exists( 'class', $availability ) ) {
									$availability['class'] = '';
								}
								$availability_html = empty( $availability['availability'] ) ? '<span class="value tinvwl-txt ' . esc_attr( $availability['class'] ) . '">' . esc_html__( 'In stock', 'ti-woocommerce-wishlist' ) . '</span>' : '<span class="value tinvwl-txt ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';

								echo apply_filters( 'tinvwl_wishlist_item_status', $availability_html, $availability['availability'], $wl_product, $product ); // WPCS: xss ok.
								?>
							</div>
						<?php } ?>
						
						<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) { ?>
							<div class="wishlist__item__extras__col display--small-up" title="Price: ">
								<?php
								echo apply_filters( 'tinvwl_wishlist_item_price', $product->get_price_html(), $wl_product, $product ); // WPCS: xss ok.
								?>
							</div>
						<?php } ?>
						
						<?php if ( isset( $wishlist_table_row['add_to_cart'] ) && $wishlist_table_row['add_to_cart'] ) { ?>
							<div class="wishlist__item__extras__actions align--center">
								<?php
								if ( apply_filters( 'tinvwl_wishlist_item_action_add_to_cart', $wishlist_table_row['add_to_cart'], $wl_product, $product ) ) {
									?>
									<button class="button button--primary button--shorter button--full add-to-cart" name="tinvwl-add-to-cart"
									        value="<?php echo esc_attr( $wl_product['ID'] ); ?>"
									        title="<?php echo esc_html( apply_filters( 'tinvwl_wishlist_item_add_to_cart', $wishlist_table_row['text_add_to_cart'], $wl_product, $product ) ); ?>">
										<span class="tinvwl-txt"><?php echo esc_html( apply_filters( 'tinvwl_wishlist_item_add_to_cart', $wishlist_table_row['text_add_to_cart'], $wl_product, $product ) ); ?></span>
									</button>
								<?php } ?>
								<div class="product-list__item__actions">
									<button type="submit" name="tinvwl-remove"
							        value="<?php echo esc_attr( $wl_product['ID'] ); ?>"
							        title="<?php esc_attr_e( 'Remove', 'ti-woocommerce-wishlist' ) ?>" class="remove_from_wishlist product-list__item__action cta cta--underlined">
										<?php esc_html_e( 'Remove From Wishlist', 'zoa' ); ?>
									</button>
								</div>
							</div>
						<?php } ?>
						</div><!--/item-dashboard-->
					</div>
					<?php
					do_action( 'tinvwl_wishlist_row_after', $wl_product, $product );
				} // End if().
			} // End foreach().
			?>
			<?php do_action( 'tinvwl_wishlist_contents_after' ); ?>
			</div>
			<div class="wishlist-footer">
				<div class="wish-footer-col">
					<?php do_action( 'tinvwl_after_wishlist_table', $wishlist ); ?>
					<?php wp_nonce_field( 'tinvwl_wishlist_owner', 'wishlist_nonce' ); ?>
				</div>
			</div>
		</div>
	</form>
	<?php do_action( 'tinvwl_after_wishlist', $wishlist ); ?>
	<div class="tinv-lists-nav tinv-wishlist-clear">
		<?php do_action( 'tinvwl_pagenation_wishlist', $wishlist ); ?>
	</div>
</div>
