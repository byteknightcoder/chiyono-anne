<?php
/**
 * Gift Card product add to cart
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

?>

<div class="gift-card-content-editor step-content">
	<!--<span class="ywgc-editor-section-title"><?php //echo apply_filters('ywgc_editor_section_title', __( "Gift card details", 'yith-woocommerce-gift-cards' )); ?></span>-->
	<div class="form-row">
        <div class="field-wrapper">
	

	<label class="form-row__label light-copy ywgc-editor-section-title" for="ywgc-recipient-email">
		<?php if ( $mandatory_recipient ) {
			echo apply_filters('ywgc_editor_mandatory_recipient_email_label',__( "Recipient's details (*)", 'yith-woocommerce-gift-cards') );
		} else {
			echo apply_filters('ywgc_editor_recipient_email_label',__( "Recipient's details", 'yith-woocommerce-gift-cards' ));
		}
		?></label>

	<div class="ywgc-single-recipient">

		<input type="email"
       name="ywgc-recipient-email[]" <?php echo ( $mandatory_recipient && ! $gift_this_product ) ? 'required' : ''; ?>
       class="ywgc-recipient yith_wc_gift_card_input_recipient_details" placeholder="<?php _e( "email", 'yith-woocommerce-gift-cards' ); ?>"/>

		<input type="text" name="ywgc-recipient-name[]" placeholder="<?php _e( "name", 'yith-woocommerce-gift-cards' ); ?>" <?php echo ( $mandatory_recipient && ! $gift_this_product ) ? 'required' : ''; ?> class="yith_wc_gift_card_input_recipient_details">

		<a href="#" class="ywgc-remove-recipient hide-if-alone">x</a>
		<?php if ( ! $mandatory_recipient ): ?>
			<span class="ywgc-empty-recipient-note"><?php echo apply_filters( 'ywgc_empty_recipient_note', __( "If empty, will be sent to your email address", 'yith-woocommerce-gift-cards' ) ); ?></span>
		<?php endif; ?>
	</div>
	<?php
	//Only with gift card product type you can use multiple recipients
	if ( $allow_multiple_recipients ) : ?>
		<a href="#" class="add-recipient"
		   id="add_recipient"><?php _e( "Add another recipient", 'yith-woocommerce-gift-cards' ); ?></a>
	<?php endif; ?>
</div><!--/field-wrapper-->
</div><!--/form-row-->
	<div class="form-row">
		<div class="field-wrapper ywgc-sender-name">
		<label class="form-row__label light-copy ywgc-editor-section-title" for="ywgc-sender-name"><?php echo apply_filters('ywgc_sender_name_label',__( "Your name", 'yith-woocommerce-gift-cards' )); ?></label>
		<input type="text" name="ywgc-sender-name" id="ywgc-sender-name" value="<?php echo apply_filters('ywgc_sender_name_value','') ?>">
	</div>
	</div><!--/form-row-->
	<div class="form-row">
	<div class="field-wrapper ywgc-message">
		<label class="form-row__label light-copy ywgc-editor-section-title" for="ywgc-edit-message"><?php echo apply_filters('ywgc_edit_message_label',__( "Message", 'yith-woocommerce-gift-cards' )); ?></label>
		<textarea id="ywgc-edit-message" name="ywgc-edit-message" rows="5"
		          placeholder="<?php echo apply_filters( 'ywgc_edit_message_placeholder', __( "Your message...", 'yith-woocommerce-gift-cards' )); ?>"></textarea>
	</div>
</div><!--/form-row-->
	<?php if ( $allow_send_later ) : ?>
	<div class="form-row">
		<div class="field-wrapper ywgc-postdated">
			<label class="form-row__label light-copy ywgc-editor-section-title" for="ywgc-postdated"><?php echo apply_filters('ywgc_postdated_field_label',__( "Postpone delivery", 'yith-woocommerce-gift-cards' )); ?></label>
			<input type="checkbox" id="ywgc-postdated" name="ywgc-postdated">
			<input type="text" id="ywgc-delivery-date" name="ywgc-delivery-date"
			       class="datepicker ywgc-hidden" placeholder="<?php echo apply_filters( 'ywgc_choose_delivery_date_placeholder',__( 'Choose the delivery date (' . $date_format . ')', 'yith-woocommerce-gift-cards' )); ?>">
		</div>
	</div><!--/form-row-->
	<?php endif; ?>
</div>