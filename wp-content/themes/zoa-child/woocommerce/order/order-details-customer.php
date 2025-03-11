<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;
$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) : ?>
	<div class="order--checkout__review__section order-shipments">
		<h3 class="heading heading--small"><?php esc_html_e( 'Shipping Address', 'zoa' ); ?></h3>
		<div class="summarybox">
			<div class="readonly-address serif">
				<address class="readonly-address__contents"><?php echo ( $address = $order->get_formatted_shipping_address() ) ? $address : __( 'N/A', 'zoa' ); ?></address>
			</div>
		</div>
	</div><!-- /.order-shipments -->
	<div class="order--checkout__review__section order-payment-instruments">
		<div class="order-billing">
			<h3 class="heading heading--small"><?php esc_html_e( 'Customer Details', 'zoa' ); ?></h3>
			<div class="readonly-address serif">
				<address class="readonly-address__contents">
					<div class="readonly-address__details">
						<?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : __( 'N/A', 'zoa' ); ?>
						<?php if ( $order->get_billing_phone() ) : ?>
							<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
						<?php endif; ?>
						<?php if ( $order->get_billing_email() ) : ?>
							<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
						<?php endif; ?>
					</div>
				</address>
			</div>
		</div>
	</div><!-- /.order-payment-instruments -->
<?php endif; ?>
<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>