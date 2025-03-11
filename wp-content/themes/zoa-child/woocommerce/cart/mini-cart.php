<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				/**
				 * This filter is documented in woocommerce/templates/cart/cart.php.
				 *
				 * @since 2.1.0
				 */
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
			?>
				<li class="woocommerce-mini-cart-item minicart__product <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
					<div class="mini-product--group">
					<?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_attr__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key );
					?>
					<?php if ( empty( $product_permalink ) ) : ?>
						<span class="mini-product__link"><?php echo $thumbnail; ?></span>
					<?php else : ?>
						<a class="mini-product__link" href="<?php echo esc_url( $product_permalink ); ?>">
							<?php echo $thumbnail; ?>
						</a>
					<?php endif; ?>
						<div class="mini-product__info">
							<div class="mini-product__item mini-product__name p4">
								<a class="link" href="<?php echo esc_url( $product_permalink ); ?>"><?php echo $product_name; ?></a>
							</div>
							<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>

							<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
						</div>

					</div>
					<!--/mini-product--group-->
				</li>
			<?php
				}
			}
				do_action( 'woocommerce_mini_cart_contents' );
			?>
	</ul>
	<div class="order__summary__contents minicart__totals">
		<div class="order__summary__row">
			<span class="label upper"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
			<span class="value bigger"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
		</div>
		<?php
			$special_service_category_slug_check = get_option('ch_special_service', ''); // 'special-service';
			$exist = false;
			$ch_fee = 0;
			foreach (WC()->cart->get_cart() as $cart_item) {
				if (is_product_in_cat($cart_item['product_id'], $special_service_category_slug_check)) {
					$exist = true;
					$ch_fee=get_option('ch_soft_bra_deposit', 0);
					break;
				}
			}
			if ( $exist && isset($ch_fee) && $ch_fee > 0 ) {
		?>
			<div class="order__summary__row">
				<span class="label upper"><?php echo __('Product fee', 'zoa'); ?></span>
				<span class="value bigger"><?php echo wc_price($ch_fee); ?><small class="tax_label">(税込)</small></span>
			</div>
			<div class="order__summary__row order-deposit-notice">*この保証金はサンプルブラ返送時に自動返金されます。</div>
			<?php } ?>
		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

			<div class="order__actions--bottom"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></div>
		</div>
		<?php else : ?>
			<p class="woocommerce-mini-cart__empty-message"><?php _e( 'No products in the cart.', 'woocommerce' ); ?></p>
		<?php endif; ?>
		<?php do_action( 'woocommerce_after_mini_cart' ); ?>
