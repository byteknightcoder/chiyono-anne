<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.4.0
 */

if (!defined('ABSPATH')) {
	exit();
}

global $woocommerce;
$cart_url = $woocommerce->cart->get_cart_url();
wc_print_notices();
do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
	echo apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce'));
	return;
}

?>

<form id="checkout" name="checkout" method="post" class="checkout woocommerce-checkout form--stepped" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">
  <div class="grid order--checkout__wrap">
	<?php if ($checkout->get_checkout_fields()) : ?>
	<div class="order--checkout__col--form">
	  <fieldset class="step1" id="step-11">
		<?php if (isHideShippingByMailGiftCard()) : ?>
		<label class="no-shipping-text" data-card="<?php echo json_encode(getCartGiftCardData()) ?>">
		  <?php esc_html_e('Just go to next step because your item doesn\'t need shipping info.', 'zoa'); ?>
		</label>
		<?php endif; ?>
		<div class="step-1-content" style="<?php echo isHideShippingByMailGiftCard() ? 'display: none;' : '' ?>">
		  <?php do_action('woocommerce_checkout_before_customer_details'); ?>
		  <div class="order--checkout__form__section legend_wrap">
			<legend class="legend-order--checkout__form__section">
			  <h2 class="heading heading--xlarge checkout_heading">
				<span class="order--checkout__title-break"><?php esc_html_e('Delivery Details', 'zoa'); ?></span>
			  </h2>
			</legend>
			<p class="form__description p6"><?php esc_html_e('Please fill in the information below:', 'zoa'); ?></p>
		  </div>
		  <div class="order--checkout--row">
			<?php do_action('woocommerce_checkout_shipping'); ?>
		  </div>
		  <div class="order--checkout--row">
			<?php
				$did_you_find = did_you_find_data();
				$logged_bought = false;
				if (is_user_logged_in()) {
					// retrieve all orders
					$customer_orders=wc_get_customer_order_count(get_current_user_id());
					if ($customer_orders > 0) {
						$logged_bought = true;
					}
				}
				if ( !empty($did_you_find) && ( !is_user_logged_in() || !$logged_bought )) :
			?>
					<div class="did_you_find">
						<h3><?php esc_html_e('当ブランドをどこでお知りになりましたか?', 'zoa'); ?></h3>
						<?php foreach ($did_you_find as $value) : ?>
							<div class="form-row  label-inline">
								<div class="field-wrapper">
									<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
										<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" type="checkbox" value="<?php echo $value; ?>" name="did_you_find[]"><span><?php echo $value; ?></span>
									</label>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
			<?php endif; ?>
			<div class="woocommerce-additional-fields">
				<?php do_action('woocommerce_before_order_notes', $checkout); ?>
				<?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>
					<?php if (!WC()->cart->needs_shipping() || wc_ship_to_billing_address_only()) : ?>
						<h3><?php esc_html_e('Additional information', 'woocommerce'); ?></h3>
					<?php endif; ?>
					<div class="woocommerce-additional-fields__field-wrapper">
						<?php foreach ($checkout->get_checkout_fields('order') as $key => $field) : ?>
							<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
						<?php endforeach; ?>
						<?php $ch_fitting_meta_exists = metadata_exists( 'user', get_current_user_id(), 'ch_fitting' ); ?>
						<div class="form-row ch_fitting validate-required" style="float: none; margin-bottom: 20px;<?php echo $ch_fitting_meta_exists ? 'display: none;' : ''; ?>">
							<div class="field-wrapper">
								<label for="order_fitting" class="">フィッティングのご経験はありますか？<abbr class="required" title="必須">*</abbr></label>
								<span class="woocommerce-input-wrapper">
									<select name="ch_fitting" id="ch_fitting" class="fitting_select input-select styled-select" data-placeholder="">
										<option value="">選択してください</option>
										<option value="いいえ">いいえ</option>
										<option value="Chiyonoによるフィッティング">Chiyonoによるフィッティング</option>
										<option value="T.B.Y.Bでフィッティング">T.B.Y.Bでフィッティング</option>
									</select>
								</span>
							</div>
						</div>
					</div>
				<?php endif; ?>
			  <?php do_action('woocommerce_after_order_notes', $checkout); ?>
			</div>
		  </div>
		</div>
		<!--/order--checkout__footer-->
	  </fieldset>
	  <fieldset class="step2" id="step-22">
		<div class="order--checkout__form__section legend_wrap">
		  <legend class="legend-order--checkout__form__section">
			<h2 class="heading heading--xlarge checkout_heading">
			  <span class="order--checkout__title-break"><?php esc_html_e('Billing Details', 'zoa'); ?></span>
			</h2>
		  </legend>
		  <p class="form__description p6"><?php esc_html_e('Please fill in the information below:', 'zoa'); ?></p>
		</div>
		<div class="order--checkout--row">
		  <?php do_action('woocommerce_checkout_billing'); ?>
		</div>
		<!--/order--checkout__footer-->
	  </fieldset>
	  <fieldset class="step3" id="step-33">
		<div class="order--checkout__form__section legend_wrap">
		  <legend class="legend-order--checkout__form__section">
			<h2 class="heading heading--xlarge checkout_heading">
			  <span class="order--checkout__title-break"><?php esc_html_e('Payment', 'zoa'); ?></span>
			</h2>
		  </legend>
		</div>
		<?php do_action('woocommerce_checkout_after_customer_details'); ?>
	  </fieldset>
	</div>
	<?php endif; ?>
	<div id="secondary" class="order--checkout__col--summary summary toggle--active">
	  <h2
		class="order--checkout__summary__heading heading heading--xlarge serif flex-justify-between icon--plus toggle--active">
		<?php esc_html_e('Order Summary', 'zoa'); ?></h2>
		<?php do_action('woocommerce_checkout_before_order_review'); ?>
	  <div class="order__summary order--checkout__summary toggle--active">
		<?php do_action('woocommerce_checkout_order_review'); ?>
	  </div>
	  <div class="order--checkout__actions--top flex-justify-between">
		<span class="heading heading--small"><?php esc_html_e('Products', 'woocommerce'); ?>
		  (<?php echo WC()->cart->get_cart_contents_count(); ?>)</span>
		<a class="cta cta--underlined txt--upper section-header-note"
		  href="<?php echo $cart_url; ?>"><?php esc_html_e('Edit Cart', 'zoa'); ?></a>
	  </div>
	  <?php do_action('woocommerce_checkout_after_order_review'); ?>
		<?php
			if (is_user_logged_in() && !is_order_familysale()) { // only for customer logged and product in cart is not from family sale category
				$_user_id = get_current_user_id();
				$rank_and_amount = mr_get_member_rank($_user_id);
				$rank = $rank_and_amount['rank'];
				$settings = mr_get_settings();
				if ( 'gold' == $rank ) {
					$coupon = new WC_Coupon($settings['ch_mr_coupon_2times']);
					$usage_limit_per_user = mr_coupon_limit();
					$used = mr_get_coupon_used_by_user($_user_id,$coupon->id);
					$remain = $usage_limit_per_user - $used;
					foreach (WC()->cart->get_coupons() as $code => $coupon) {
						// for silver
						if (strtolower($code) == strtolower($settings['ch_mr_coupon_2times'])) {
							$remain = $remain - 1;
							break;
						}
					}
					if ($remain > 0) :
						$coup_desc = __('Use coupon', 'zoa');
						if ( 'percent' == $coupon->discount_type && !empty($coupon->coupon_amount) ) {
							$coup_desc = __('Use ' . $coupon->coupon_amount . '% off', 'zoa');
						}
					?>
						<div class="notice_block gold_coupon">
							<a coupon="<?php echo $settings['ch_mr_coupon_2times']; ?>" class="btb_ch_apply_coupon btn" href="javascript:void(0);"><?php echo $coup_desc; ?></a>
							<div class="coupon_left">
								<?php
									if ($remain >= 2) {
										printf(__("(You have %s coupons left)", 'zoa'), $remain);
									} else {
										printf(__("(You have only %s coupon left)", 'zoa'), $remain);
									}
								?>
							</div>
						</div><!-- notice_block -->
				<?php endif;
				} elseif ( 'silver' == $rank ) {
					$coupon = new WC_Coupon($settings['ch_mr_coupon_except_bra_2times']);
					$usage_limit_per_user = mr_coupon_limit();
					$used = mr_get_coupon_used_by_user($_user_id,$coupon->id);
					$remain = $usage_limit_per_user - $used;
					foreach (WC()->cart->get_coupons() as $code => $coupon) {
						//for silver
						if (strtolower($code) == strtolower($settings['ch_mr_coupon_except_bra_2times'])) {
							$remain = $remain - 1;
							break;
						}
					}
					if ($remain > 0) :
						$coup_desc = __('Use coupon', 'zoa');
						if ( 'percent' == $coupon->discount_type && !empty($coupon->coupon_amount) ) {
							$coup_desc = sprintf(__('Use %s off','zoa'),$coupon->coupon_amount.'%');
						}
				?>
					<div class="notice_block silver_coupon">
						<a coupon="<?php echo $settings['ch_mr_coupon_except_bra_2times']; ?>" class="btb_ch_apply_coupon btn" href="javascript:;"><?php echo $coup_desc; ?></a>
						<div class="coupon_left">
							<?php
								if ($remain >= 2) {
									printf(__("(You have %s coupons left)", 'zoa'), $remain);
								} else {
									printf(__("(You have only %s coupon left)", 'zoa'), $remain);
								}
							?>
						</div>
					</div>
				<?php endif;
				}
			}
		?>
		<?php if ( false == check_cart_has_no_giftbox() ) : ?>
			<div style="order: 6;" class="giftbox">
				<?php echo do_shortcode('[shortcode_add_to_cart_gifbox]'); ?>
			</div>
		<?php endif; ?>
	</div>
	<!--/#secondary-->
  </div>
  <!--/.row-->
  <div id="ship-to-different-address" style="display: none;">
	<input type="checkbox" name="ship_to_different_address" value="1" checked="1" />
  </div>
</form>
<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
<div class="clear"></div>
