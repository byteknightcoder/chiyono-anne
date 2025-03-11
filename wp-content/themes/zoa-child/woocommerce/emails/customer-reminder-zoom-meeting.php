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

	<p><?php printf( __( '<p>オンラインコンサルテーションの2日前をお知らせいたします。<br>事前に下記注意事項、並びに<span style="color:red;font-weight:bold;"><a href="'.home_url('term-of-use-try-fit-your-size').'" style="text-decoration:underline;">利用規約</a>をを必ずご一読ください。</span>', 'woocommerce' ), $order->get_order_number() ); ?></p>
        <?php
//        $zoom_metting_id = get_post_meta($order->ID, '_zoom_metting_id', true);
//        $zoom_metting_passcode = get_post_meta($order->ID, 'zoom_metting_passcode', true);
        $zoom_metting_url = get_post_meta($order->ID, 'zoom_metting_url', true);
//        if(!empty($zoom_metting_id)){
//            echo __('ZOOM meeting ID:','zoa').$zoom_metting_id;
//        }
//        if(!empty($zoom_metting_passcode)){
//            echo '<br/>'.__('Passcode:','zoa').$zoom_metting_passcode;
//        }
        if(!empty($zoom_metting_url)){
            echo '<br/>'.__('Url:','zoa').'<a target="_blank" href="'.$zoom_metting_url.'">'.$zoom_metting_url.'</a>';
        }
        ?>
        <!--insert notion common + zoom here-->
        <p><b>&diams; サービスに関するご注意点 &diams;</b></p>
        <ul>
            <li>フィットサンプルブラをご着用いただくため、ローブや前開きトップスなど、脱ぎやすい格好を推奨</li>
            <li>ビデオ通話をする部屋は適度に明るく</li>
            <li>携帯の場合は携帯ホルダーを使用したり、置ける場所を確保して両手が使えるように</li>
            <li>ネット環境のよい場所での実施</li>
        </ul>

        <p>当日お目にかかえますことを楽しみにしております。</p>
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