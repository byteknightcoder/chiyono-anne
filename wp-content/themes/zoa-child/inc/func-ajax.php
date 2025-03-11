<?php


add_action('wp_ajax_bulk_acf_tabs_32432GWEGwkrh5wWegl', 'bulk_acf_tabs_32432GWEGwkrh5wWegl');
add_action('wp_ajax_nopriv_bulk_acf_tabs_32432GWEGwkrh5wWegl', 'bulk_acf_tabs_32432GWEGwkrh5wWegl');
//https://chiyono-anne.com/wp-admin/admin-ajax.php?action=bulk_acf_tabs_32432GWEGwkrh5wWegl&cat_slug=4353353535353523525535
function bulk_acf_tabs_32432GWEGwkrh5wWegl() {
    if(!empty($_REQUEST['cat_slug'])){
        $cat_slug=$_REQUEST['cat_slug'];
        $product_ids = get_posts( array(
           'post_type' => 'product',
           'numberposts' => -1,
           'post_status' => 'publish',
           'fields' => 'ids',
           'tax_query' => array(
             array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $cat_slug,
                'operator' => 'IN',
             )
          ),
        ) );
        if(!empty($product_ids)){
            $tab=array(
                array(
                    'title'=>'ご注意点',
                    'text'=>'<ul><li>ファミリーセール商品の在庫には数に限りがございます。先着順とさせていただきます。</li><li>無くなり次第終了となりますので、予めご了承ください。</li><li>新品の状態ではないことをご了承した上でご購入をお願いいたします。</li><li>サンプルによっては、出展や撮影により着用済み（モデル着用やお客様の試着など）のアイテムも含まれます。</li><li>ファミリーセール商品の返品、交換はお受けできかねます。</li><li>サイズ等のお直しはできかねます。</li><li>商品間違い・不良品(破れ、ほつれ、明らかなシミのみ)に関しましては、お直し、または返金対応をさせて頂きますので、お手数ですがお問合せフォームから商品の到着から7日以内にご連絡をお願いいたします。</li></ul>'
                )
            );
            foreach ($product_ids as $value) {
                update_field( 'accordion_prd', $tab,$value );
            }
        }
        echo 'completed:'.json_encode($product_ids);
    }else{
        echo 'please provide all value in params: cat_slug and text';
    }
    exit();
}

add_action('wp_ajax_update_delivery_date_70324WEGEkafASGaja94Gsg7', 'update_delivery_date_70324WEGEkafASGaja94Gsg7');
add_action('wp_ajax_nopriv_update_delivery_date_70324WEGEkafASGaja94Gsg7', 'update_delivery_date_70324WEGEkafASGaja94Gsg7');

//https://chiyono-anne.com/wp-admin/admin-ajax.php?action=update_delivery_date_70324WEGEkafASGaja94Gsg7&cat_slug=4353353535353523525535&text=23423422525525252525
function update_delivery_date_70324WEGEkafASGaja94Gsg7() {
    if(!empty($_REQUEST['cat_slug'])&&!empty($_REQUEST['text'])){
        $cat_slug=$_REQUEST['cat_slug'];
        $product_ids = get_posts( array(
           'post_type' => 'product',
           'numberposts' => -1,
           'post_status' => 'publish',
           'fields' => 'ids',
           'tax_query' => array(
             array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $cat_slug,
                'operator' => 'IN',
             )
          ),
        ) );
        if(!empty($product_ids)){
            $delivery_text=$_REQUEST['text'];
            foreach ($product_ids as $value) {
                //update_post_meta($value, 'deliver_date', '');
                update_post_meta($value, 'specific_deliver_date', $delivery_text);
            }
        }
        echo 'completed:'.json_encode($product_ids);
    }else{
        echo 'please provide all value in params: cat_slug and text';
    }
    exit();
}

add_action('wp_ajax_ch_check_email_exists', 'ch_check_email_exists');
add_action('wp_ajax_nopriv_ch_check_email_exists', 'ch_check_email_exists');

function ch_check_email_exists() {
    $email=$_REQUEST['email'];
    if(empty($email)){
        echo "That E-mail doesn't belong to any registered users on this site";
    }
    $exists = email_exists( $email );
    if ( $exists ) {
        echo __("That E-mail is registered to user.",'zoa');
    } else {
        echo "ok";
    }
    exit();
}


add_action('wp_ajax_load_customer_kana_info', 'load_customer_kana_info');
add_action('wp_ajax_nopriv_load_customer_kana_info', 'load_customer_kana_info');
function load_customer_kana_info(){
    $user_id=$_REQUEST['user_id'];
    $billing_last_name_kana = get_user_meta($user_id, 'billing_last_name_kana', true);
    $billing_first_name_kana = get_user_meta($user_id, 'billing_first_name_kana', true);
    $shipping_last_name_kana = get_user_meta($user_id, 'shipping_last_name_kana', true);
    $shipping_first_name_kana = get_user_meta($user_id, 'shipping_first_name_kana', true);
    if(!empty($billing_last_name_kana)){
        $data['billing_last_name_kana']=$billing_last_name_kana;
    }else{
        $data['billing_last_name_kana']='';
    }
    if(!empty($billing_first_name_kana)){
        $data['billing_first_name_kana']=$billing_first_name_kana;
    }else{
        $data['billing_first_name_kana']='';
    }
    if(!empty($shipping_last_name_kana)){
        $data['shipping_last_name_kana']=$shipping_last_name_kana;
    }else{
        $data['shipping_last_name_kana']='';
    }
    if(!empty($shipping_first_name_kana)){
        $data['shipping_first_name_kana']=$shipping_first_name_kana;
    }else{
        $data['shipping_first_name_kana']='';
    }
    echo json_encode($data);
    exit();
}

add_action('wp_ajax_get_portfolio', 'zoa_get_portfolio');
add_action('wp_ajax_nopriv_get_portfolio', 'zoa_get_portfolio');

function zoa_get_portfolio() {
    $pID = $_REQUEST['id'];
    $GLOBALS['post'] = $post = get_post($pID);
    setup_postdata($post);
    set_query_var('post', $post);
    get_template_part('single', 'portfolio');
    die;
}

add_action('wp_ajax_cancel_appointment', 'zoa_cancel_appointment');
add_action('wp_ajax_nopriv_cancel_appointment', 'zoa_cancel_appointment');

function zoa_cancel_appointment() {
    $appointment_id = $_REQUEST['appointment_id'];
    $is_allow_cancel = zoa_is_allow_cancel_appointment($appointment_id);
    if ($is_allow_cancel) {
        if (!$appointment_id) {
            $success = 0;
            $status = __('Active', 'zoa');
        } else {

            update_post_meta($appointment_id, '_birs_appointment_status', 'cancelled');
            $success = 1;
            $status = __('Cancelled', 'zoa');
        }
    } else {
        $success = 0;
        $status = __('Active', 'zoa');
    }
    $response = array('success' => $success, 'status' => $status);
    print_r(json_encode($response));
    die;
}


function get_banner_post_handler() {
    $post = get_post($_REQUEST['id']);
    include(dirname(dirname(__FILE__)).'/template-parts/content-banner.php');
    wp_die();
}

add_action('wp_ajax_get_banner_post', 'get_banner_post_handler');
add_action('wp_ajax_nopriv_get_banner_post', 'get_banner_post_handler');


add_action('wp_ajax_remove_booking_photo', 'zoa_remove_booking_photo');
add_action('wp_ajax_nopriv_remove_booking_photo', 'zoa_remove_booking_photo');

function zoa_remove_booking_photo() {
    unset($_SESSION['pid']);
    unset($_SESSION['p_image']);
    echo json_encode(array('success' => 1));
    die;
}


add_action('wp_ajax_customer_cancel_order', 'customer_cancel_order');
add_action('wp_ajax_nopriv_customer_cancel_order', 'customer_cancel_order');

function customer_cancel_order() {
    $response = array();
    $order_id = $_POST['order_id'];
    $order = wc_get_order($order_id);
    $statuses = wc_get_order_statuses();

    if (isOrderAllowCancel($order)) {
        // Cancel order
        //process auto refund Stripe payment method
        $_payment_method = trim(get_post_meta($order_id, '_payment_method', true));
        if($_payment_method=='stripe'){

            $_order_total=get_post_meta($order_id, '_order_total', true);

            $refund = wc_create_refund(array(
                'amount' => $_order_total,
                'reason' => __('This order is cancelled by customer self','zoa'),
                'order_id' => $order_id,
                'line_items' => array(),
                'refund_payment' => true,
                'restock_items' => false,
            ));
            if ( is_wp_error( $refund ) ) {
                $order->add_order_note('Stripe refund error: '.$refund->get_error_message());
                $response['error']=$refund->get_error_message();
                $response['success'] = 0;
                $response['payment']='stripe';
            }elseif(! $refund){
                $order->add_order_note('Stripe refund error: '.__( 'Cannot create order refund, please try again.', 'woocommerce' ));
                $response['error']=__( 'Cannot create order refund, please try again.', 'woocommerce' );
                $response['success'] = 0;
                $response['payment']='stripe';
            }else{
//                $order->add_order_note('Stripe refund completed (Refund ID: '.$refund->id.')');
//                update_post_meta($order_id, '_stripe_refund_id', $refund->id);
                $response['success'] = 1;
                $response['status'] = $statuses['wc-refunded'];
                $response['payment']='stripe';
                $item_count = $order->get_item_count();
                $render_total=$item_count.'点 <del>&yen;'.number_format($_order_total).'</del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&yen;</span>0</span></ins>';
                $response['render_total']=$render_total;
                            //Send notify email to admin
                $mailer = WC()->mailer();
                $mails = $mailer->get_emails();
                $email_to_send = 'WC_Admin_Refunded_Order';
                $email_to_send_to_admin='WC_Admin_Refunded_Order';
                if (!empty($mails)) {
                    foreach ($mails as $mail) {
                        if ($mail->id == $email_to_send || $mail->id==$email_to_send_to_admin) {
                            $mail->trigger($order_id);
                        }
                    }
                }
            }
        
        } else if ($_payment_method == 'payid') {
            $_order_total=$order->get_total();
            $refund = wc_create_refund(array(
                'amount' => $_order_total,
                'reason' => __('This order is cancelled by customer self','zoa'),
                'order_id' => $order_id,
                'line_items' => array(),
                'refund_payment' => false,
                'restock_items' => false,
            ));
            if ( is_wp_error( $refund ) ) {
                $order->add_order_note('Payid refund error: '.$refund->get_error_message());
                $response['error']=$refund->get_error_message();
                $response['success'] = 0;
                $response['payment']='payid';
            }elseif(! $refund){
                $order->add_order_note('Payid refund error: '.__( 'Cannot create order refund, please try again.', 'woocommerce' ));
                $response['error']=__( 'Cannot create order refund, please try again.', 'woocommerce' );
                $response['success'] = 0;
                $response['payment']='payid';
            }else{
                $response['success'] = 1;
                $response['status'] = $statuses['wc-refunded'];
                $response['payment']='payid';
                $item_count = $order->get_item_count();
                $render_total=$item_count.'点 <del>&yen;'.number_format($_order_total).'</del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&yen;</span>0</span></ins>';
                $response['render_total']=$render_total;
            //Send notify email to admin
            $mailer = WC()->mailer();
            $mails = $mailer->get_emails();
            $email_to_send = 'WC_Admin_Refunded_Order';
            $email_to_send_to_admin='WC_Admin_Refunded_Order';
            if (!empty($mails)) {
                foreach ($mails as $mail) {
                    if ($mail->id == $email_to_send || $mail->id==$email_to_send_to_admin) {
                        $mail->trigger($order_id);
                    }
                }
            }
            }
    }else{//end
            $response['success'] = 1;
            $response['status'] = $statuses['wc-cancelled'];
            $order->update_status('cancelled');
            //Send notify email to admin
            $mailer = WC()->mailer();
            $mails = $mailer->get_emails();
            $email_to_send = 'WC_Admin_Refunded_Order';
            $email_to_send_to_admin='WC_Admin_Refunded_Order';
            if (!empty($mails)) {
                foreach ($mails as $mail) {
                    if ($mail->id == $email_to_send || $mail->id==$email_to_send_to_admin) {
                        $mail->trigger($order_id);
                    }
                }
            }
            //send email
//            global $woocommerce;
//            $mailer = $woocommerce->mailer();
//            $refund = $mailer->emails['WC_Email_Customer_Refunded_Order'];
//            $refund->trigger($order_id);
        }
    } else {
        $response['success'] = 0;
    }
    echo json_encode($response);
    die;
}


add_action('wp_ajax_select_shipping_delivery_option', 'ajax_select_shipping_delivery_option');
add_action('wp_ajax_nopriv_select_shipping_delivery_option', 'ajax_select_shipping_delivery_option');

function ajax_select_shipping_delivery_option() {
    $shipping_delivery_option = $_POST['shipping_delivery_option'];
    $packages = WC()->cart->get_shipping_packages();
    foreach ($packages as $package_key => $package) {
        $session_key = 'shipping_for_package_' . $package_key;
        if ( is_user_logged_in() ) {
            //WC()->session->destroy_session($session_key);
            WC()->session->__unset('shipping_for_package_'.$package_key); // Remove
            unset($packages[$package_key]); // Remove
        }else{
            WC()->session->__unset('shipping_for_package_'.$package_key); // Remove
            unset($packages[$package_key]); // Remove
        }
    }

    $_SESSION['shipping_delivery_option'] = $shipping_delivery_option;
    WC()->session->set( 'shipping_delivery_option' , $shipping_delivery_option );

    $response = array('success' => 1);
    echo json_encode($response);
    die;
}


function validate_cart_ajax_request() {
    $gift_card_category_slug='mwb_wgm_giftcard';
    $product_id=$_REQUEST['product_id'];
    $variation_id=$_REQUEST['variation_id'];
    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    $message = "ギフトカードは納期が異なる商品と同時に注文ができません。申し訳ございませんが、別々にご注文ください。";
    $msg_tbty=__('You can not buy this item more than 1, please remove current added item from cart.','zoa');
    $msg_tbty_with_other=__('Service product and regular products cannot be ordered together. Please order separately','zoa');
    $msg_familysale=__('This product can not be ordered with a product that is in a cart. Please order separately.','zoa');
    
    if(is_product_in_cat($product_id, $special_service_category_slug)){
        if(empty($_REQUEST['try_fit_type'])){
            echo '気になっているブラとサンプルブラサイズの選択は必須です';
            exit();
        }
    }
    //for event products
    if (isset($_REQUEST['ch_type_orf']) && $_REQUEST['ch_type_orf'] == order_type_for_event() && is_expired_orf()) {
        echo __('Event is expired, so you can not add this product to cart.', 'zoa');
        exit();
    }
    //end
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (is_product_in_cat($cart_item['product_id'], $gift_card_category_slug) && !is_product_in_cat($product_id, $gift_card_category_slug)) {
            echo $message;
            exit();
        } elseif (!is_product_in_cat($cart_item['product_id'], $gift_card_category_slug) && is_product_in_cat($product_id, $gift_card_category_slug)) {
            echo $message;
            exit();
        }
        //for tbty
        if (is_product_in_cat($cart_item['product_id'], $special_service_category_slug) && !is_product_in_cat($product_id, $special_service_category_slug)) {
            echo $msg_tbty_with_other;
            exit();
        } elseif (!is_product_in_cat($cart_item['product_id'], $special_service_category_slug) && is_product_in_cat($product_id, $special_service_category_slug)) {
            echo $msg_tbty_with_other;
            exit();
        }
        if(is_product_in_cat($product_id, $special_service_category_slug)){
            echo $msg_tbty;
            exit();
        }
        //for familysale
        if (is_product_in_cat($cart_item['product_id'], 'familysale') && !is_product_in_cat($product_id, 'familysale')) {
            echo $msg_familysale;
            exit();
        } elseif (!is_product_in_cat($cart_item['product_id'], 'familysale') && is_product_in_cat($product_id, 'familysale')) {
            echo $msg_familysale;
            exit();
        }
        //end
        //for event product
        //file_put_contents(dirname(__FILE__) . '/cart2.txt', json_encode($cart_item) . "\n", FILE_APPEND);
//        $msg_event_product = __('You can not buy product of event with other product.', 'zoa');
//        if (isset($cart_item['ch_type_orf']) && $cart_item['ch_type_orf'] == order_type_for_event() && (!isset($_REQUEST['ch_type_orf']) || empty($_REQUEST['ch_type_orf']))) {
//            echo $msg_event_product;
//            exit();
//        } elseif (isset($_REQUEST['ch_type_orf']) && $_REQUEST['ch_type_orf'] == order_type_for_event() && (!isset($cart_item['ch_type_orf']) || empty($cart_item['ch_type_orf']))) {
//            echo $msg_event_product;
//            exit();
//        }

        //end
    }
    if ( $__product = wc_get_product( $product_id ) ) {
        if ( $__product->is_type( 'variable' ) && $__product->get_meta( '_sold_individually_apply_variations' ) == 'yes' ) {
            foreach (WC()->cart->get_cart() as $cart_item) {
                if($product_id==$cart_item['product_id']||$variation_id==$cart_item['variation_id']){
                    echo __('You can not buy this item more than 1, please remove current added item from cart.','zoa');
                    exit();
                }
            }
        }
    }
    echo 'ok';
    exit();
}
 
add_action( 'wp_ajax_validate_cart_ajax_request', 'validate_cart_ajax_request' );
add_action( 'wp_ajax_nopriv_validate_cart_ajax_request', 'validate_cart_ajax_request' );

//this url to run bulk update for showing swatch in catalog "Yes" for all items which have individual swatches
//https://test.chiyono-anne.com/wp-admin/admin-ajax.php?action=bulk_update_for_showing_swatch_in_catalog&token=@r-v3_unLX$D!fVMQw9DAwxunVP!
    add_action('wp_ajax_bulk_update_for_showing_swatch_in_catalog', 'bulk_update_for_showing_swatch_in_catalog');

    function bulk_update_for_showing_swatch_in_catalog() {
        if(isset($_REQUEST['token'])&&$_REQUEST['token']=='@r-v3_unLX$D!fVMQw9DAwxunVP!'){
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1
            );
            $loop = new WP_Query( $args );
            $result=array();
            while ( $loop->have_posts() ) : $loop->the_post();
                $post_id=get_the_ID();
                $product_settings = array();
                $swatch_meta = get_post_meta($post_id,'_iconic-was', true );
                if(!empty($swatch_meta)){
                    foreach ($swatch_meta as $key => $value) {
                        if(!empty($value)){
                            if(!empty($value['swatch_type'])){
                                $value['loop']="1";
                                $result[]=$post_id;
                            }
                            $product_settings[$key]=$value;
                        }
                    }
                    update_post_meta($post_id, '_iconic-was', $product_settings);
                }
            endwhile;
            $result=array_unique($result);
            echo 'Completed Ids: '.implode(", ", $result);
            wp_reset_query();
            exit();
        }else{
            echo "Don't have access to this request";
            exit();
        }
    }
    
    
//add_action('wp_ajax_update_delivery_date_product', 'update_delivery_date_product');
//add_action('wp_ajax_update_delivery_date_product', 'update_delivery_date_product');
//https://test.chiyono-anne.com/chiyono/wp-admin/admin-ajax.php?action=update_delivery_date_product
function update_delivery_date_product(){
     $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
    );

    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        $post_id= get_the_ID();
        $custom_field_type = get_post_meta($post_id, 'from_to', true);
        if($custom_field_type[0]==3&&$custom_field_type[1]=='months'&&$custom_field_type[2]==''&&$custom_field_type[3]=='months'){
            $custom_field_type =  array('','months','','months');
            update_post_meta( $post_id, 'from_to', $custom_field_type );
        }
    endwhile;
    echo 'updated ok';
    exit();
}


//delete customer imported when need
function del_customer_imported(){
    if(isset($_REQUEST['token'])&&$_REQUEST['token']=='ASclh2343AsNMMwql'){
        $blogusers = get_users(['role__in' => ['customer'], 'fields' => array('ID', 'user_email')]);
        $total=0;
        foreach ($blogusers as $value) {
            $check= get_user_meta($value->ID, 'is_user_imported',true);
            if($check=='yes'){
                wp_delete_user($value->ID);
                $total++;
            }
        }
        echo 'Total deleted:'.$total;
        exit();
    }else{
        echo 'No access';
    }
}


//https://chiyono-anne.com/chiyono/wp-admin/admin-ajax.php?action=del_customer_imported&token=ASclh2343AsNMMwql
//when need run please remove comment 2 lines bellow
//add_action('wp_ajax_del_customer_imported', 'del_customer_imported');
//add_action('wp_ajax_nopriv_del_customer_imported', 'del_customer_imported');

add_action('wp_ajax_nopriv_schedule_send_email_bulk_by_csv', 'schedule_send_email_bulk_by_csv');
add_action('wp_ajax_schedule_send_email_bulk_by_csv', 'schedule_send_email_bulk_by_csv');
//https://test.chiyono-anne.com/chiyono/wp-admin/admin-ajax.php?action=schedule_send_email_bulk_by_csv
function schedule_send_email_bulk_by_csv(){
    $upload_dir   = wp_upload_dir();
    $csv_dirname = $upload_dir['basedir'].'/csv_email_send_schedule/';
    $target_file = $csv_dirname . 'email_list.csv'; 
    if(file_exists($target_file)){
    $send_type=2;
if (($handle = fopen($target_file, 'r')) !== FALSE) {
                        set_time_limit(0);
                        $row = 0;
                        $head = array();
                        $log_send_emails_csv= get_option('log_send_emails_csv', '');
                        $emails_in_log=array();
                        if(!empty($log_send_emails_csv)){
                            $log_send_emails_csv_arr= json_decode($log_send_emails_csv,true);
                            foreach ($log_send_emails_csv_arr as $key_log => $value_log) {
                                $emails_in_log = array_merge($emails_in_log,$value_log);
                            }
                        }
                        while (($data = fgetcsv($handle, 10000, ',')) !== FALSE) { //echo $row;
                            //get key by hear
                            if ($row == 0) {
                                $head = $data;
                                $head_key = array_flip($head);
                            }
                            //end
                            if ($row <> 0) {
                                if($head_key['email']==''){
                                    $head_key['email']=0;
                                }
                                if(!empty($data[$head_key['email']])){
                                    if($send_type==1){
                                        if(!in_array(trim($data[$head_key['email']]), $emails_in_log)){
                                            $all_emails[]=trim($data[$head_key['email']]);
                                        }
                                    }else{
                                        $all_emails[]=trim($data[$head_key['email']]);
                                    }
                                }
                            }
                            $row++;
                        }
                    }
                    if (!empty($all_emails)) {
                        $all_emails= array_unique($all_emails);
                        $site_title = get_bloginfo('name');
                        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
                        $attachments = array();

                        $subject = trim(get_option('send_email_csv_subject_email', false));
                        $message = trim(get_option('send_email_csv_content_email', false));
                        $message = stripslashes($message);
                        $check = 0;
                        if (!empty($subject) && !empty($message)) {
                            add_filter('wp_mail_content_type', 'ch_set_html_content_type');
                            foreach ($all_emails as $u_id=>$value) {
                                $value=trim($value);
                                $sent_message=wp_mail($value, $subject, $message, $headers, $attachments);
                                if ( $sent_message ) {
                                    $emails_sent[]=$value;
                                }
                            }
                            if(!empty($emails_sent)){
                                $ready_log=false;
                                $log_send_emails_csv= get_option('log_send_emails_csv', '');
                                $log=array(date_i18n('Y-m-d')=>$emails_sent);
                                if(!empty($log_send_emails_csv)){
                                    $log_send_emails_csv_arr= json_decode($log_send_emails_csv,true);
                                    foreach ($log_send_emails_csv_arr as $key_log => $value_log) {
                                        if(date_i18n('Y-m-d')==$key_log){
                                            $value_log= array_merge($value_log,$emails_sent);
                                            $log_send_emails_csv_arr[$key_log]=$value_log;
                                            $ready_log=true;
                                            break;
                                        }
                                    }
                                    if($ready_log==false){
                                        $log_send_emails_csv_arr= array_merge($log_send_emails_csv_arr, $log);
                                    }
                                    update_option('log_send_emails_csv', json_encode($log_send_emails_csv_arr));
                                }else{
                                    update_option('log_send_emails_csv', json_encode($log));
                                }
                            }
                            $check = 1;
                            remove_filter('wp_mail_content_type', 'ch_set_html_content_type');
                        }
                        if ($check == 1) {
                            $err_send = __("Send emails completed.", 'zoa');
                        }
                    } else {
                        $err_send = __("Don't have emails to send emails OR all emails sent before.", 'zoa');
                    }
    }
                    mail('chien.lexuan@gmail.com', 'Log send email csv cronjob', json_encode($all_emails));
    echo $err_send;
    exit();
}