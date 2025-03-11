<?php
/**
 * Checkout terms and conditions area.
 *
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( apply_filters( 'woocommerce_checkout_show_terms', true ) && function_exists( 'wc_terms_and_conditions_checkbox_enabled' ) ) {
	do_action( 'woocommerce_checkout_before_terms_and_conditions' );

	?>
	<div class="woocommerce-terms-and-conditions-wrapper">
		<?php
		/**
		 * Terms and conditions hook used to inject content.
		 *
		 * @since 3.4.0.
		 * @hooked wc_privacy_policy_text() Shows custom privacy policy text. Priority 20.
		 * @hooked wc_terms_and_conditions_page_content() Shows t&c page content. Priority 30.
		 */
		//do_action( 'woocommerce_checkout_terms_and_conditions' );
		
		?>

		<?php if ( wc_terms_and_conditions_checkbox_enabled() ) : ?>
            		<p class="form-row validate-required">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms_delivery" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms_delivery'] ) ), true ); ?> id="terms" />
					<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo __('Agree with delivery estimated delivery date','zoa'); ?></span>&nbsp;<span class="required">*</span>
				</label>
				<input type="hidden" name="terms_delivery_field" value="1" />
			</p>
			<p class="form-row validate-required">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); // WPCS: input var ok, csrf ok. ?> id="terms" />
					<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo zoa_get_checkout_privacy_policy_text('checkout'); ?></span>&nbsp;<span class="required">*</span>
				</label>
				<input type="hidden" name="terms-field" value="1" />
			</p>
		<?php endif; ?>
                <a id="read_cancel_policy_modal" class="button button--primary"><?php _e('キャンセルポリシーについて', 'zoa'); ?></a>
	</div>
	<?php

	do_action( 'woocommerce_checkout_after_terms_and_conditions' );
}
?>
<div class="remodal" data-remodal-id="read_cancel_policy_modal" id="read_cancel_policy_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
  <button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
  <div class="remodal_wraper">
  <?php 
  if ( $post = get_page_by_path( 'returns-exchanges', OBJECT, 'page' ) )
  {
  	echo $post->post_content;
  }
  ?>
  </div>
  <br>
</div>