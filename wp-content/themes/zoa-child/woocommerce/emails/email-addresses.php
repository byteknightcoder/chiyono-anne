<?php
/**
 * Email Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-addresses.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 8.6.0
 */
if (!defined('ABSPATH')) {
    exit;
}

$text_align = is_rtl() ? 'right' : 'left';
$address = $order->get_formatted_billing_address();
$shipping = $order->get_formatted_shipping_address();
if ($order->has_status('shipped-gb') || $order->has_status('processing-gb')) {
    $email_lbl = 'Email';
    $cb_info = 'Customer details';
} else {
    $cb_info = 'お客様情報';
    $email_lbl = 'メール';
}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-spacing: 0px;">
    <tbody>
        <tr>
            <td style="border-collapse: collapse; color: rgb(0, 0, 0); font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-style: normal; font-weight: normal; line-height: 1.5; text-align: center; padding: 20px 40px;">
                <h3 style="color: rgb(0, 0, 0); font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0; font-size: 20px; font-style: italic; font-weight: normal; text-align: center;"><?php echo $cb_info; ?></h3>
                <p style="text-align: center;">                    
                    <strong><?php echo $email_lbl; ?>:</strong> <span><?php if ($order->get_billing_email()) : ?>
                            <?php echo esc_html($order->get_billing_email()); ?>
                        <?php endif; ?></span><br>
                    <strong>Tel:</strong> <span><?php if ($order->get_billing_phone()) : ?>
                            <?php echo esc_html($order->get_billing_phone()); ?>
                        <?php endif; ?></span>
                </p>
            </td>
        </tr>
    </tbody>
</table>
<table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
    <tr>
        <td style="text-align:<?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" valign="top" width="50%">
            <h2 style="font-size: 18px;"><?php esc_html_e('Billing address', 'woocommerce'); ?></h2>

            <address style="font-style: normal;font-size: 13px;" class="address">
                <?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : esc_html__('N/A', 'woocommerce'); ?>
            </address>
        </td>
        <?php if (!wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() )) : ?>
            <td style="text-align:<?php echo 'right'; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding:0;" valign="top" width="50%">
                <h2 style="text-align: right; font-size: 18px;"><?php esc_html_e('Shipping address', 'woocommerce'); ?></h2>

                <address style="font-style: normal; font-size: 13px;" class="address"><?php echo $shipping; ?></address>
            </td>
        <?php endif; ?>
    </tr>
</table>
