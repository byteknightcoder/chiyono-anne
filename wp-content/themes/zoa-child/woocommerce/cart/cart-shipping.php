<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.8.0
 */

defined( 'ABSPATH' ) || exit;

$formatted_destination    = isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' );
$has_calculated_shipping  = ! empty( $has_calculated_shipping );
$show_shipping_calculator = ! empty( $show_shipping_calculator );
$calculator_text          = '';
?>
<div class="order__summary__row order-shipping shipping">
	<span class="label txt--upper ja"><?php echo wp_kses_post( $package_name ); ?>
    <?php if ( 1 === count( $available_methods ) ) : ?>
		<?php
			$method = current( $available_methods );
			printf( '%3$s <input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', $index, esc_attr( $method->id ), wc_cart_totals_shipping_method_label( $method ) );
			do_action( 'woocommerce_after_shipping_rate', $method, $index );
		?>
</div>
<!--/.order-shipping-->
<?php elseif ( 1 < count( $available_methods ) ) :  ?>
<span class="value price-amount price-shipping">
  <ul id="shipping_method">
    <?php foreach ( $available_methods as $method ) : ?>
		<li>
			<?php
				printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />
					<label for="shipping_method_%1$d_%2$s">%5$s</label>',
					$index, sanitize_title( $method->id ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ), wc_cart_totals_shipping_method_label( $method ) );

				do_action( 'woocommerce_after_shipping_rate', $method, $index );
			?>
		</li>
    <?php endforeach; ?>
  </ul>
</span>
</div>
<!--/.order-shipping-->

<?php elseif ( WC()->customer->has_calculated_shipping() ) : ?>
</div>
<!--/.order-shipping-->
<p class="order__summary__row__descr p5">
	<?php
		if ( is_cart() ) {
			echo apply_filters( 'woocommerce_cart_no_shipping_available_html',  __( 'There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' )  );
		} else {
			echo apply_filters( 'woocommerce_no_shipping_available_html', __( 'There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' )  );
		}
	?>
</p>
<?php elseif ( ! is_cart() ) : ?>
</div>
<!--/.order-shipping-->
<p class="order__summary__row__descr p5">
	<?php echo wpautop( __( 'Enter your full address to see shipping costs.', 'zoa' ) ); ?>
</p>
<?php else : ?>
	</span><span class="value no-value">-</span>
</div>
<!--/.order-shipping-->
<p class="order__summary__row__descr p6"><?php esc_html_e( 'Shipping fee will be shown after you input your shipping address.', 'zoa' ); ?></p>
<?php endif; ?>

<?php if ( $show_package_details ) : ?>
	<div class="order__summary__row__descr">
		<?php echo '<p class="woocommerce-shipping-contents p5"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
	</div>
<?php endif; ?>

<?php if ( ! empty( $show_shipping_calculator ) ) : ?>
	<?php if (! isMemberFreeShip()) : ?>
		<div class="order__summary__row__descr order-shipping-calc shipping-calc">
			<?php woocommerce_shipping_calculator(); ?>
		</div>
	<?php endif ?>
<?php endif; ?>
<?php
	if (isMemberFreeShip()) {
		$current_user = wp_get_current_user();
		$rank = mr_get_member_rank($current_user->ID);
		echo '<p class="rm_msg"><span class="rank_label">' .  sprintf(__("(Free for %s member ship!)", 'zoa'), $rank['rank']) . '</span></p>';
	}
?>
