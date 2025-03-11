<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( 'The order #%d need reminder zoom meeting. The order details:', 'woocommerce' ), $order->id ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
//$zoom_metting_id = get_post_meta($order->ID, '_zoom_metting_id', true);
//$zoom_metting_passcode = get_post_meta($order->ID, 'zoom_metting_passcode', true);
$zoom_metting_url = get_post_meta($order->ID, 'zoom_metting_url', true);
//if(!empty($zoom_metting_id)){
//    echo __('ZOOM meeting ID:','zoa').$zoom_metting_id;
//}
//if(!empty($zoom_metting_passcode)){
//    echo '<br/>'.__('Passcode:','zoa').$zoom_metting_passcode;
//}
if(!empty($zoom_metting_url)){
    echo '<br/>'.__('Url:','zoa').$zoom_metting_url;
}
/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );