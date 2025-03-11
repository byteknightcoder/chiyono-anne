<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

  <?php do_action( 'woocommerce_before_cart_totals' ); ?>

  <div class="order__summary">
    <h2 class="heading heading--small"><?php _e( 'Order Summary', 'woocommerce' ); ?></h2>
    <?php if ( wc_coupons_enabled() ) { ?>
    <?php //esc_html_e( 'Coupon:', 'woocommerce' ); ?>
    <div style="display:none;" class="order__summary__coupon cart-coupon-code">
      <p class="order__summary__coupon__title"><span class="cta--underlined"><?php esc_html_e( 'Do you have coupon code?', 'zoa' ); ?></span></p>
      <div class="form-row order__summary__coupon__entry">
        <div class="input-button-group input-button-group--outside">
          <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
        </div>
      </div>
      <?php do_action( 'woocommerce_cart_coupon' ); ?>
    </div>
    <?php } ?>

    <div class="order__summary__contents order-totals-table">
      <p class="order__summary__row heading m-no_topmargin heading--small"><?php _e( 'Cart totals', 'woocommerce' ); ?></p>
      <div class="order__summary__row order-subtotal">
        <span class="label txt--upper ja"><?php _e( 'Subtotal', 'woocommerce' ); ?><span class="smaller">(<?php echo WC()->cart->get_cart_contents_count();?> <?php _e('items', 'zoa')?>)&lrm;</span></span>
        <span class="value price-amount"><?php wc_cart_totals_subtotal_html(); ?></span>
      </div>
      <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
      <div class="order__summary__row order-coupon">
        <span class="label txt--upper ja"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
        <span class="value price-amount price-discount"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
      </div>
                  <?php
                $the_coupon = new WC_Coupon($code);
                $coupon_id = $the_coupon->get_id();
                $subtitle_copn = get_post_meta($coupon_id, 'kia_subtitle', true);
                if(!empty($coupon->get_description())||!empty($subtitle_copn)){
            ?>
                <div class="order__summary__row description desc_coupon">
                    <?php 
                    echo $coupon->get_description(); 
                    if(!empty($subtitle_copn)){
                        echo '<small>'.$subtitle_copn.'</small>';
                    }
                    ?>
                </div>
                <?php } ?>
<?php
                $amount = get_post_meta($coupon_id, 'coupon_amount', true);
                $coupon_discount_amount = WC()->cart->get_coupon_discount_amount($code, WC()->cart->display_cart_ex_tax);
                $res_amount = $amount - $coupon_discount_amount;
                if ($res_amount > 0) {
                    echo '<p class="red_coupon_amount" style="color:red;">' . sprintf(__('You still have %s for gift card', 'zoa'), wc_price($res_amount)) . '<p>';
                }
      
      endforeach; ?>
      <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

      <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

      <?php wc_cart_totals_shipping_html(); ?>

      <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

      <?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
      <div class="order__summary__row order-shipping">
        <span class="label txt--upper ja"><?php _e( 'Shipping', 'woocommerce' ); ?></span>
        <span class="value price-amount price-shipping"><?php woocommerce_shipping_calculator(); ?></span>
      </div>
      <?php endif; ?>


      <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
      <div class="order__summary__row order-fee">
        <span class="label txt--upper ja"><?php echo esc_html( $fee->name ); ?></span>
        <span class="value price-amount price-discount"><?php wc_cart_totals_fee_html( $fee ); ?></span>
      </div>
      <?php endforeach; ?>

      <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) :
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
					? sprintf( ' <small>' . __( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] )
					: '';

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
      <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
      <div class="order__summary__row order-tax-rate">
        <span class="label txt--upper ja"><?php echo esc_html( $tax->label ) . $estimated_text; ?></span>
        <span class="value price-amount price-tax"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
      </div>
      <?php endforeach; ?>
      <?php else : ?>
      <div class="order__summary__row order-tax-total">
        <span class="label txt--upper ja"><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; ?></span>
        <span class="value price-amount price-tax"><?php wc_cart_totals_taxes_total_html(); ?></span>
      </div>
      <?php endif; ?>
      <?php endif; ?>

      <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

      <div class="order__summary__totals">
        <div class="order__summary__row order-total">
          <span class="label txt--upper ja"><?php _e( 'Total', 'woocommerce' ); ?></span>
          <span class="value price-amount bigger order-value"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
        <!--/.order-total-->
        <?php if ( wc_tax_enabled() && WC()->cart->display_prices_including_tax() ) { ?>
        <p class="order__summary__row__descr p6"><?php _e( 'All prices are inclusive of taxes.', 'zoa' ); ?></p>
        <?php } ?>
      </div>
      <!--/.order__summary__totals-->

      <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
    </div>
    <!--/order-totals-table-->

  </div>
  <div class="coupon_below_total">
    <!--    <div class="woocommerce-form-coupon-toggle">
    <?php //wc_print_notice(apply_filters('woocommerce_checkout_coupon_message', __('Have a coupon?', 'woocommerce') . ' <a href="#" class="showcoupon">' . __('Click here to enter your code', 'woocommerce') . '</a>'), 'notice'); ?>
</div>-->
    <form class="checkout_coupon woocommerce-form-coupon" method="post">

      <p><?php esc_html_e('If you have a coupon code, please apply it below.', 'woocommerce'); ?></p>
      <div class="flex_wrap form-row-coupon">
        <div class="form-row form-row-input">
          <input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" id="coupon_code" value="" />
        </div>

        <div class="form-row form-row-button">
          <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply coupon', 'woocommerce'); ?></button>
        </div>
      </div>
    </form>
  </div>
  <div class="order__actions order__actions--bottom cart-actions">
    <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
  </div>

  <?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
