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

	<p><?php printf( __( 'ご注文商品（#%d）の返却を確認いたしました。', 'woocommerce' ), $order->get_order_number() ); ?><br>ご対応いただき誠にありがとうございました。</p>
	<p>本日より３営業日以内にアトリエにてサンプルの確認を行い、紛失・ダメージがない場合は保証金全額が支払い元のクレジットカードに返金されます。</p>
	<p>※紛失・ダメージがある場合は、保証金を差し引き、該当商品の損害金の残高を頂戴いたします。損害金が発生する際は、担当者から別途ご連絡させていただきます。</p>
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