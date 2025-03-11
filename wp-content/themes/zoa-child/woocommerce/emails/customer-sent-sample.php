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
 * @hooked WallC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
	<p><?php printf( __( 'ご注文（#%d）の商品が発送されました。注文詳細は以下です。', 'woocommerce' ), $order->get_order_number() ); ?></p>
	<!--start if including ZOOM option-->
        <?php
//            $zoom_metting_id = get_post_meta($order->ID, '_zoom_metting_id', true);
//            $zoom_metting_passcode = get_post_meta($order->ID, 'zoom_metting_passcode', true);
            $zoom_metting_url = $_REQUEST['zoom_metting_url'];
            if(!empty($zoom_metting_url)){
        ?>
	<p><b>★オンラインコンサルテーションのZOOM参加URL</b><br><?php echo '<a target="_blank" href="'.$zoom_metting_url.'">'.$zoom_metting_url.'</a>'; ?></p>
        <?php
//        if(!empty($zoom_metting_id)){
//            echo '<br/>'.__('ZOOM meeting ID:','zoa').$zoom_metting_id;
//        }
//        if(!empty($zoom_metting_passcode)){
//            echo '<br/>'.__('Passcode:','zoa').$zoom_metting_passcode;
//        }
        ?>
            <?php } ?>
	<!--end if including ZOOM option-->
	<p><span style="color:red;font-weight:bold;">※こちらの商品は、返送期限(お客様の元に商品到着から10日以内)がございますので、ご不在票などを必ずご確認いただき、受け取りをお願いいたします。</span></p>
	<p><b>★返却するもの</b><br>&#9312; サンプルブラ<br>&#9313; メジャー<br>&#9314; パッド<br>&#9315; インストラクションシート</p>
<?php
    echo get_tracking_url($order->ID);
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