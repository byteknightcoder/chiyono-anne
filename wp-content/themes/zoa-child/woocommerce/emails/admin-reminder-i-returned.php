<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( __( 'The order #%d need reminder i returned for admin. Order Details:', 'woocommerce' ), $order->get_order_number() ); ?></p>
        <?php
        $_try_fit_tracking_code = get_post_meta($order->ID, '_try_fit_tracking_code', true) ? get_post_meta($order->ID, '_try_fit_tracking_code', true) : '';
        if(!empty($_try_fit_tracking_code)){
            ?>
            <a target="_blank" href="https://trackings.post.japanpost.jp/services/srv/search/?requestNo1=<?php echo $_try_fit_tracking_code; ?>&search=%E8%BF%BD%E8%B7%A1%E3%82%B9%E3%82%BF%E3%83%BC%E3%83%88"><?php echo __('Tracking','zoa'); ?></a>
        <?php
        }
        ?>
<?php

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );