<?php

// Add script to frontend
add_action('wp_enqueue_scripts', 'add_scripts_try_fit');
function add_scripts_try_fit() {
    wp_enqueue_style('scripts_try_fit_css', get_stylesheet_directory_uri() . '/wc-try-fit-your-size/css/try_fit_your_size.css', array(), time());
    wp_enqueue_script('scripts_try_fit_js', get_stylesheet_directory_uri() . '/wc-try-fit-your-size/js/try_fit_your_size.js', array(), time(), true);
}

// Add script to admin
add_action('admin_enqueue_scripts', 'add_scripts_try_fit_admin');
function add_scripts_try_fit_admin() {
    wp_enqueue_script('scripts_try_fit_js', get_stylesheet_directory_uri() . '/wc-try-fit-your-size/js/try_fit_your_size_admin.js', array(), time(), true);
}

// Function to check if product has category for TBYB
function is_product_in_cat_fit($product_id, $category_slug) {
    $accessories = ch_get_taxonomy_hierarchy_fit('product_cat', 0, $category_slug);
    $product_cats = get_the_terms($product_id, 'product_cat');

    if (!empty($product_cats) && is_array($product_cats)) {
        foreach ($product_cats as $product_cat) {
            if (in_array($product_cat->term_id, array_keys($accessories))) {
                return true;
            }
        }
    }
    
    return false;
}

// Function to get all categories for TBYB
function ch_get_taxonomy_hierarchy_fit($taxonomy, $parent = 0, $slug = '', &$children = array()) {
    $taxonomy = is_array($taxonomy) ? array_shift($taxonomy) : $taxonomy;
    if ($slug) {
        $terms = get_term_by('slug', $slug, $taxonomy);
        if (!empty($terms)) {
            $terms = array($terms);
        }
    } else {
        $terms = get_terms($taxonomy, array('parent' => $parent));
    }
    $children = !empty($children) ? $children : array();
    foreach ($terms as $term) {
        ch_get_taxonomy_hierarchy_fit($taxonomy, $term->term_id, '', $children);
        $children[$term->term_id] = $term;
    }
    return $children;
}

/// Co the 2 ham tren ko can khi update len chiyono vi 2 ham do co trong function.php roi
// Add fee when in cart has product in 'special-service' catgory slug
add_action('woocommerce_cart_calculate_fees', 'ch_add_checkout_fee_for_gateway');
function ch_add_checkout_fee_for_gateway() {
    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    $plunge_bra = get_option('ch_plunge_bra', ''); //'plunge-bra'; //category slug
    $soft_bra = get_option('ch_soft_bra', ''); //'soft-bra'; //category slug
    $deposit = 0;
    $ch_plunge_bra_deposit = 0;
    $ch_soft_bra_deposit = 0;
    $online_consultation = false;
    $sizes = 0;
    $plunge_bra_size = array();
    $soft_bra_size = array();
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (is_product_in_cat_fit($cart_item['product_id'], $special_service_category_slug)) {
            if (isset($cart_item['bra_product_id']) && !empty($cart_item['bra_product_id'])) {
                foreach ($cart_item['bra_product_id'] as $value_id) { //products, that custome choosed
                    if ($value_id == 'plunge-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); // fixed for all case 10000
                    } elseif ($value_id == 'soft-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); // fixed for all case 10000
                    } elseif ($value_id == 'full-cup-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); // fixed for all case 10000
                    }
                }
            }
            // if (isset($cart_item['bra_size'])) {
            //     $sizes= explode(",", $cart_item['bra_size']);
            //     $plunge_bra_size = array_merge($plunge_bra_size,$sizes);
            // }
            // if (isset($cart_item['bra_size_soft'])) {
            //     $sizes= explode(",", $cart_item['bra_size_soft']);
            //     $soft_bra_size= array_merge($soft_bra_size,$sizes);
            // }
            // if (isset($cart_item['bra_size'])) {
            //     $sizes= count(explode(",", $cart_item['bra_size']));
            // }
            // if (isset($cart_item['online_consultation']) && $cart_item['online_consultation'] != '') {
            //     $online_consultation = true;
            //     $_SESSION['online_consultation'] = 'yes';
            // }
        }
    }
    //    $_SESSION['plunge_bra_size'] = $plunge_bra_size;
    //    $_SESSION['soft_bra_size'] = $soft_bra_size;
    if ($ch_plunge_bra_deposit > 0 || $ch_soft_bra_deposit > 0) {
        $deposit = get_option('ch_soft_bra_deposit', 0); // fixed for all case 10000
    }
    if ($deposit > 0) {
        WC()->cart->add_fee(__('Product fee', 'zoa'), $deposit);
        // $_SESSION['_ch_product_fee'] = $deposit;
        // $_SESSION['ch_is_special_service'] = 'yes';
    }
}

// Add TBYB fee in the cart
add_action('woocommerce_before_calculate_totals', 'ch_add_custom_price_try_fit');
function ch_add_custom_price_try_fit($cart_object) {
    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    $online_price = get_online_price();
    $is_online_consultation = false;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (is_product_in_cat_fit($cart_item['product_id'], $special_service_category_slug)) {
            if (isset($cart_item['online_consultation']) && $cart_item['online_consultation'] != '') {
                $is_online_consultation = true;
                $product = wc_get_product($cart_item['product_id']);
                break;
            }
        }
    }
    if ($is_online_consultation == true) {
        $cart_item['data']->set_price($online_price + $product->get_price());
    }
}

// show only stripe payment when in cart has product in 'special-service' catgory slug
add_filter('woocommerce_available_payment_gateways', 'ch_filter_available_payment_gateways_per_category', 10, 1);
function ch_filter_available_payment_gateways_per_category($available_gateways) {
    if (!function_exists('WC') || !isset(WC()->cart) || WC()->cart->is_empty() || empty($available_gateways)) {
        return $available_gateways;
    }

    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    $exist = false;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (is_product_in_cat_fit($cart_item['product_id'], $special_service_category_slug)) {
            $exist = true;
            break;
        }
    }
    foreach ($available_gateways as $id => $gateway) {
        if ( $exist && !( 'stripe' == $id || 'stripe_cc' == $id ) ) {
            unset($available_gateways[$id]);
        }
    }

    return $available_gateways;
}


// Register new order status for TBYB
add_action('init', 'register_ch_my_new_order_statuses');
function register_ch_my_new_order_statuses() {
    register_post_status('wc-prepare-to-send', array(
        'label' => __('Prepare to send', 'zoa'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Prepare to send <span class="count">(%s)</span>', 'Prepare to send <span class="count">(%s)</span>', 'zoa')
    ));
    register_post_status('wc-sent-sample', array(
        'label' => __('Sent sample & waiting return', 'zoa'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Sent sample & waiting return <span class="count">(%s)</span>', 'Sent sample&waiting return <span class="count">(%s)</span>', 'zoa')
    ));
    register_post_status('wc-sample-returned', array(
        'label' => __('Sample returned', 'zoa'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Sample returned <span class="count">(%s)</span>', 'Sample returned <span class="count">(%s)</span>', 'zoa')
    ));
    register_post_status('wc-completenotrefund', array(
        'label' => __('Complete(not refund)', 'zoa'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Complete(not refund) <span class="count">(%s)</span>', 'Complete(not refund) <span class="count">(%s)</span>', 'zoa')
    ));
}

// Add to list of WC Order statuses
add_filter('wc_order_statuses', 'ch_my_new_wc_order_statuses');
function ch_my_new_wc_order_statuses( $order_statuses ) {
    $order_statuses['wc-prepare-to-send'] = __('Prepare to send', 'zoa');
    $order_statuses['wc-sent-sample'] = __('Sent sample & waiting return', 'zoa');
    $order_statuses['wc-sample-returned'] = __('Sample returned', 'zoa');
    $order_statuses['wc-completenotrefund'] = __('Complete(not refund)', 'zoa');
    return $order_statuses;
}

/**
 * Class Custom_WC_Email
 */
class Ch_Custom_WC_Email {

    /**
     * Custom_WC_Email constructor.
     */
    public function __construct() {
        // Filtering the emails and adding our own email.
        add_filter('woocommerce_email_classes', array($this, 'register_email'), 90, 1);
        // Absolute path to the plugin folder.
    }

    /**
     * @param array $emails
     *
     * @return array
     */
    public function register_email($emails) {
        require_once dirname(__FILE__) . '/class-wc-customer-sent-sample-order.php';
        require_once dirname(__FILE__) . '/class-wc-customer-sample-returned-order.php';
        require_once dirname(__FILE__) . '/class-wc-customer-warning-return-sample.php';
        require_once dirname(__FILE__) . '/class-wc-customer-reminder-zoom-meeting.php';
        require_once dirname(__FILE__) . '/class-wc-admin-reminder-zoom-meeting.php';
        require_once dirname(__FILE__) . '/class-wc-admin-warning-return-sample.php';
        //require_once dirname(__FILE__) . '/class-wc-admin-reminder-i-returned.php';

        $emails['WC_Customer_Sent_Sample_Order'] = new WC_Customer_Sent_Sample_Order();
        $emails['WC_Customer_Sample_Returned_Order'] = new WC_Customer_Sample_Returned_Order();
        $emails['WC_Customer_Warning_Return_Sample'] = new WC_Customer_Warning_Return_Sample();
        $emails['WC_Customer_Reminder_Zoom_Meeting'] = new WC_Customer_Reminder_Zoom_Meeting();
        $emails['WC_Admin_Reminder_Zoom_Meeting'] = new WC_Admin_Reminder_Zoom_Meeting();
        $emails['WC_Admin_Warning_Return_Sample'] = new WC_Admin_Warning_Return_Sample();
        //$emails['WC_Admin_Reminder_I_Returned'] = new WC_Admin_Reminder_I_Returned();
        return $emails;
    }
}

new Ch_Custom_WC_Email();

// auto update processing status to prepare-to-send status only for stripe payment and order include product of 'special-service' category
add_action('woocommerce_order_status_pending_to_processing', 'ch_send_update_processing_to_prepare_to_send');
function ch_send_update_processing_to_prepare_to_send($order_id) {
    $order = new WC_Order($order_id);
    if ( strpos($order->get_payment_method(), 'stripe') !== false ) {
        $special_service_category_slug = get_option('ch_special_service', ''); //'special-service';
        $exist = false;
        $is_online_consultation = false;
        $plunge_bra_size = array();
        $soft_bra_size = array();
        $full_cup_bra_size = array();
        $_ch_product_fee = 0;
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
                $exist = true;
                $data = $item->get_data();
                if (!empty($data)) {
                    if (isset($data['meta_data'])) {
                        foreach ($data['meta_data'] as $k => $value) {
                            if ($value->key == 'Online Consultation' && $value->value != '') {
                                $is_online_consultation = true;
                            }
                            if (($value->key == 'Plunge Bra size' || $value->key == 'プランジブラ選択サイズ') && $value->value != '') {
                                $plunge_bra_size = explode(",", $value->value);
                            }
                            if (($value->key == 'Soft Bra size' || $value->key == 'ソフトブラ選択サイズ') && $value->value != '') {
                                $soft_bra_size = explode(",", $value->value);
                            }
                            if (($value->key == 'Full cup Bra size') && $value->value != '') {
                                $full_cup_bra_size = explode(",", $value->value);
                            }
                        }
                    }
                }
                // file_put_contents(dirname(__FILE__).'/data.txt', json_encode($item->get_data())."\n",FILE_APPEND);
                $_ch_product_fee = get_option('ch_soft_bra_deposit', 0); //fixed for all case 10000
                break;
            }
        }
        if ( $exist ) {
            if (isset($_ch_product_fee)) {
                update_post_meta($order_id, '_ch_product_fee', $_ch_product_fee);
            }
            if (isset($is_online_consultation) && $is_online_consultation == true) {
                update_post_meta($order_id, '_ch_online_consultation', 'yes');
            }
            if (isset($plunge_bra_size)) {
                //update plunge stock
                if (!empty($plunge_bra_size)) {
                    foreach ($plunge_bra_size as $value) {
                        if (!empty($value)) {
                            $value = trim($value);
                            $current_stock = get_option('ch_stock_plunge_' . $value, 0);
                            if ($current_stock > 0) {
                                $current_stock = $current_stock - 1;
                            }
                            update_option('ch_stock_plunge_' . $value, $current_stock);
                        }
                    }
                }
                update_post_meta($order_id, 'plunge_bra_size', json_encode($plunge_bra_size));
            }
            if (isset($soft_bra_size)) {
                //update soft stock
                if (!empty($soft_bra_size)) {
                    foreach ($soft_bra_size as $value) {
                        if (!empty($value)) {
                            $value = trim($value);
                            $current_stock = get_option('ch_stock_soft_' . $value, 0);
                            if ($current_stock > 0) {
                                $current_stock = $current_stock - 1;
                            }
                            update_option('ch_stock_soft_' . $value, $current_stock);
                        }
                    }
                }
                update_post_meta($order_id, 'soft_bra_size', json_encode($soft_bra_size));
            }
            if (isset($full_cup_bra_size)) {
                //update full cup stock
                if (!empty($full_cup_bra_size)) {
                    foreach ($full_cup_bra_size as $value) {
                        if (!empty($value)) {
                            $value = trim($value);
                            $current_stock = get_option('ch_stock_full_cup_' . $value, 0);
                            if ($current_stock > 0) {
                                $current_stock = $current_stock - 1;
                            }
                            update_option('ch_stock_full_cup_' . $value, $current_stock);
                        }
                    }
                }
                update_post_meta($order_id, 'full_cup_bra_size', json_encode($full_cup_bra_size));
            }
            $stock = array(
                'soft_1' => get_option('ch_stock_soft_1', 0),
                'soft_2' => get_option('ch_stock_soft_2', 0),
                'soft_3' => get_option('ch_stock_soft_3', 0),
                'soft_4' => get_option('ch_stock_soft_4', 0),
                'plunge_1' => get_option('ch_stock_plunge_1', 0),
                'plunge_2' => get_option('ch_stock_plunge_2', 0),
                'plunge_3' => get_option('ch_stock_plunge_3', 0),
                'plunge_4' => get_option('ch_stock_plunge_4', 0),
                'full_cup_1' => get_option('ch_stock_full_cup_1', 0),
                'full_cup_2' => get_option('ch_stock_full_cup_2', 0),
                'full_cup_3' => get_option('ch_stock_full_cup_3', 0),
                'full_cup_4' => get_option('ch_stock_full_cup_4', 0)
            );
            file_put_contents(dirname(__FILE__) . '/log_stock.txt', '#' . $order_id . ': ' . date_i18n('Y-m-d H:i:s') . "\n" . json_encode($stock) . "\n", FILE_APPEND);
            $passcode = strtolower(wp_generate_password(10, false)) . str_replace(array(" ", "."), "", microtime());
            update_post_meta($order_id, '_ch_passcode', $passcode); //save passcode to show in email and check when go to on /reservation-form/ to show only 1 service
            $order->update_status('prepare-to-send');
        }
    }
}

// send email when status from prepare-to-send to sent-sample
add_action('woocommerce_order_status_prepare-to-send_to_sent-sample', 'ch_send_email_sent_sample');
function ch_send_email_sent_sample($order_id) {
    $order = new WC_Order($order_id);
    if ( strpos($order->get_payment_method(), 'stripe') !== false ) {
        $special_service_category_slug = get_option('ch_special_service', ''); //'special-service';
        $exist = false;
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
                $exist = true;
                break;
            }
        }

        if ( $exist ) {
            $mailer = WC()->mailer();
            $mails = $mailer->get_emails();
            $email_to_send = 'WC_Customer_Sent_Sample_Order';
            if (!empty($mails)) {
                foreach ($mails as $mail) {
                    if ($mail->id == $email_to_send) {
                        $mail->trigger($order_id);
                    }
                }
                //save date time, that sent sample
                update_post_meta($order_id, '_ch_datetime_sent_sample', date_i18n('Y-m-d H:i:s'));
            }

        }
    }
}

// send email when status from sent-sample to completenotrefund
add_action('woocommerce_order_status_sent-sample_to_completenotrefund', 'ch_send_email_complete_not_refund');
function ch_send_email_complete_not_refund($order_id) {
    $order = new WC_Order($order_id);
    if ( strpos($order->get_payment_method(), 'stripe') !== false ) {
        $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
        $exist = false;
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
                $exist = true;
                break;
            }
        }
        if ( $exist ) {
            $mailer = WC()->mailer();
            $mails = $mailer->get_emails();
            $email_to_send = 'customer_completed_order';
            if (!empty($mails)) {
                foreach ($mails as $mail) {
                    if ($mail->id == $email_to_send) {
                        $mail->trigger($order_id);
                    }
                }
            }
        }
    }
}

// send email when status from sent-sample to sample-returned
add_action('woocommerce_order_status_sent-sample_to_sample-returned', 'ch_send_email_sample_returned');
function ch_send_email_sample_returned($order_id) {
    $order = new WC_Order($order_id);
    if ( strpos($order->get_payment_method(), 'stripe') !== false ) {
        $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
        $exist = false;
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
                $exist = true;
                break;
            }
        }
        if ( $exist ) {
            $mailer = WC()->mailer();
            $mails = $mailer->get_emails();
            $email_to_send = 'WC_Customer_Sample_Returned_Order';
            if (!empty($mails)) {
                foreach ($mails as $mail) {
                    if ($mail->id == $email_to_send) {
                        $mail->trigger($order_id);
                    }
                }
            }
        }
    }
}

// auto refund product fee to customer
add_action('woocommerce_order_status_sample-returned_to_completed', 'ch_sample_returned_to_completed_auto_refund');
function ch_sample_returned_to_completed_auto_refund($order_id) {
    $order = new WC_Order($order_id);
    if ( strpos($order->get_payment_method(), 'stripe') !== false ) {
        $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
        $exist = false;
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
                $exist = true;
                break;
            }
        }
        if ( $exist ) {
            $_order_total = get_post_meta($order_id, '_ch_product_fee', true);
            $refund_amount = $_order_total;
            $refunded_amount = 0;
            $refund_reason = '返金金額:';
            $line_item_qtys = array();
            $line_item_totals = array();
            $line_item_tax_totals = array();
            $api_refund = true;
            $restock_refunded_items = false;
            $refund = false;
            $response = array();
            $order = wc_get_order($order_id);
            $line_items = array();
            $items = $order->get_items(array('fee'));
            foreach ($items as $item_id => $item) {
                $line_items[$item_id] = array(
                    'qty' => 0,
                    'refund_total' => 0,
                    'refund_tax' => array(),
                );
            }
            foreach ($line_item_qtys as $item_id => $qty) {
                $line_items[$item_id]['qty'] = max($qty, 0);
            }
            foreach ($line_item_totals as $item_id => $total) {
                $line_items[$item_id]['refund_total'] = wc_format_decimal($refund_amount);
            }
            foreach ($line_item_tax_totals as $item_id => $tax_totals) {
                $line_items[$item_id]['refund_tax'] = array_filter(array_map('wc_format_decimal', $tax_totals));
            }
            // Create the refund object.
            $refund = wc_create_refund(
                array(
                    'amount' => $refund_amount,
                    'reason' => $refund_reason,
                    'order_id' => $order_id,
                    'line_items' => $line_items,
                    'refund_payment' => $api_refund,
                    'restock_items' => $restock_refunded_items,
                )
            );
            // end
        }
    }
}

// get all order sent sample to send warning
function get_all_orders_sent_sample_send_email_key_023JwqfHNY487555CH() {
    $args = array(
        'post_type' => 'shop_order',
        "post_status" => array('wc-sent-sample'),
        'posts_per_page' => '-1',
    );
    $query = new WP_Query($args);
    $order_sent = array();
    $dealine = get_option('ch_deadline_return', 0);
    $date_return = get_option('ch_auto_send_before_days_return', 0);
    $date_before_send = $dealine - $date_return; // example: 10-2
    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
            $order_id = get_the_ID();
            //check to send only for order 2days before deadline (Deadline is 10days from the day shop shipped), so need +8 when check cronjob
            $time_send_sample = get_post_meta($order_id, '_ch_datetime_sent_sample', true);
            $_ch_sent_email_warning = get_post_meta($order_id, '_ch_sent_email_warning', true);
            if (!empty($time_send_sample) && $_ch_sent_email_warning != 'yes') {
                $current_time = current_time('timestamp');
                $date_expires_rest = date_i18n('Y-m-d 00:00:00', strtotime('+' . $date_before_send . ' days', strtotime($time_send_sample)));
                $date_expires = date_i18n('Y-m-d 23:59:59', strtotime('+' . $dealine . ' days', strtotime($time_send_sample)));
                // echo $date_expires_rest.'<br/>';
                // echo $date_expires.'</br>';
                if (($current_time >= strtotime($date_expires_rest) && $current_time <= strtotime($date_expires)) || ($dealine == 0 || $date_return == 0)) {
                    $mailer = WC()->mailer();
                    $mails = $mailer->get_emails();
                    $email_to_send = 'WC_Customer_Warning_Return_Sample';
                    $email_to_send_to_admin = 'WC_Admin_Warning_Return_Sample';
                    if (!empty($mails)) {
                        foreach ($mails as $mail) {
                            if ($mail->id == $email_to_send || $mail->id == $email_to_send_to_admin) {
                                $mail->trigger($order_id);
                            }
                        }
                    }
                    update_post_meta($order_id, '_ch_sent_email_warning', 'yes');
                    $order_sent[] = $order_id;
                }
            }
        endwhile;
    }
    if (!empty($order_sent)) {
        mail('chien.lexuan@gmail.com', 'Log sent email cronjob to warning return sample product: ', 'order_id:' . json_encode($order_sent));
        echo json_encode($order_sent);
    }
    exit();
}

// build url to set cronjob to send email warning to return sample product.
// wp-admin/admin-ajax.php?action=get_all_orders_sent_sample_send_email_key_023JwqfHNY487555CH
add_action('wp_ajax_get_all_orders_sent_sample_send_email_key_023JwqfHNY487555CH', 'get_all_orders_sent_sample_send_email_key_023JwqfHNY487555CH');
add_action('wp_ajax_nopriv_get_all_orders_sent_sample_send_email_key_023JwqfHNY487555CH', 'get_all_orders_sent_sample_send_email_key_023JwqfHNY487555CH');

// test add texbox
/**

 * @snippet       Add an input field to products - WooCommerce

 */
// -----------------------------------------
// 1. Show custom input field above Add to Cart
function data_bra_items() {
    $upload_dir_link = wp_upload_dir()['baseurl'];
    $products = array(
        'soft-bra' => array(
            'title' => 'Soft Bra',
            'description' => 'Some text Soft Bra here',
            'image' => $upload_dir_link . '/2022/10/CAE-16-WHT_01-225x300.jpg'
        ),
        'plunge-bra' => array(
            'title' => 'Plunge Bra',
            'description' => 'Some text Plunge Bra here',
            'image' => $upload_dir_link . '/2019/11/IMG_0035-225x300.jpg'
        ),
        'full-cup-bra' => array(
            'title' => 'Full cup Bra',
            'description' => 'Some text Full cup Bra here',
            'image' => $upload_dir_link . '/2021/09/CAI-D-07-FRONT-225x300.jpg'
        ),
    );
    return $products;
}

add_action('woocommerce_before_add_to_cart_button', 'add_try_fit_options');
function add_try_fit_options() {
    global $post;
    $product_main = wc_get_product($post->ID);
    $special_service = get_option('ch_special_service', '');
    $plunge_bra = get_option('ch_plunge_bra', ''); //'plunge-bra'; //category slug
    $soft_bra = get_option('ch_soft_bra', ''); //'soft-bra'; //category slug
    if (is_product_in_cat_fit($post->ID, $special_service)) { //only for special-service product of special-service allow this
        $products = data_bra_items();
        if (!empty($products)) {
            $online_price = get_online_price();
?>
            <div class="custom_option fit_data_options">
                <div class="custom_option__container bra_you_prefer">
                    <div class="fit_title acc_title fit_title_bra"><?php esc_html_e('Choose your favourite bra shapes', 'zoa'); ?></div>
                    <div class="fit_body">
                        <p class="help_txt"><?php esc_html_e('Choose a bra type you prefer at least one.', 'zoa'); ?></p>
                        <div class="grids__card">
                            <?php foreach ($products as $key_p => $product) : ?>
                                <div class="card__style <?php echo $key_p; ?>">
                                    <div class="card__body fit_bra_product_item image-checkbox">
                                        <div class="card__body-img"><img class="img-responsive" src="<?php echo $product['image']; ?>" /></div>
                                        <div class="card__body-header">
                                            <h4 class="card__body-header-title"><?php echo $product['title']; ?></h4>
                                            <div class="cat_desc" style="display:none;"><?php echo $product['description']; ?></div>
                                        </div>
                                        <input type="checkbox" class="bra_product_id required" name="bra_product_id[]" value="<?php echo $key_p; ?>" />
                                        <span class="icon_check"><span></span></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a class="ch_next button" href="javascript:void(0);"><?php esc_html_e('Next', 'zoa'); ?></a>
                        <input ajaxurl="<?php echo admin_url('admin-ajax.php'); ?>" type="hidden" value="" id="try_fit_type" name="try_fit_type" />
                        <input type="hidden" value="<?php esc_html_e('Sorry, can choose 2 bra as max', 'zoa'); ?>" id="alert_ja_max6" />
                    </div>
                </div>
                <div class="custom_option__container choose_bra_size">
                    <div class="fit_title acc_title fit_tite_size"><?php esc_html_e('Choose your bra size', 'zoa'); ?></div>
                    <div class="fit_body">
                        <p class="help_txt">サイズ（最大2つまで）を選択してください。</p>
                        <!--show here size pop up link text-->
                        <?php
                            $chart = new productsize_chart_Public('productsize-chart-for-woocommerce', 123);
                            $chart_id = $chart->productsize_chart_id($post->ID);
                            if ($chart_id) : ?>
                                <span class="cta about_size pop-up-button-remodal"><?php esc_html_e('About Size', 'zoa') ?></span>
                            <?php endif; ?>
                        <!--                        end-->
                        <div class="plung_bra_area">
                            <p class="help_txt"><?php esc_html_e('Choose plunge bra sizes you want to try for fitting.', 'zoa'); ?></p>
                            <div class="swatch__list">
                                <?php
                                $size = 4;
                                for ($i = 1; $i <= $size; $i++) {
                                    $stock_plunge = get_option('ch_stock_plunge_' . $i, 0);
                                    if ($stock_plunge <= 0) {
                                        $stock_plunge = 'outstock';
                                    }
                                ?>
                                    <div class="fit_bra_size <?php echo $stock_plunge; ?>">
                                        <label for="bra_size[]" class="swatch__radio swatch__radio-text">
                                            <input class="bra_size required" type="checkbox" name="bra_size[]" value="<?php echo $i; ?>" /><?php echo $i; ?>
                                        </label>
                                    </div>
                                <?php } // endfor ?>
                            </div>
                            <p class="outofstock" id="alert_plunge"><?php esc_html_e('alert', 'zoa'); ?></p>
                            <p id="seleted_size" class="seleted_size selected_output"><span class="underline"><span class="selected_lbl seleted_size_lbl"><?php esc_html_e('Plunge Bra Size', 'zoa') ?>: </span><span class="selected_value seleted_size_value"></span></span></p>
                        </div>
                        <div class="soft_bra_area">
                            <p class="help_txt help_txt_soft"><?php esc_html_e('Choose soft bra sizes you want to try for fitting.', 'zoa'); ?></p>
                            <div class="swatch__list">
                                <?php
                                    $size = 4;
                                    for ($i = 1; $i <= $size; $i++) {
                                        $stock_soft = get_option('ch_stock_soft_' . $i, 0);
                                        if ($stock_soft <= 0) {
                                            $stock_soft = 'outstock';
                                        }
                                    ?>
                                    <div class="fit_bra_size <?php echo $stock_soft; ?>">
                                        <label for="bra_size_soft[]" class="swatch__radio swatch__radio-text">
                                            <input class="bra_size_soft required" type="checkbox" name="bra_size_soft[]" value="<?php echo $i; ?>" /><?php echo $i; ?>
                                        </label>
                                    </div>
                                <?php } // endfor ?>
                            </div>
                            <p class="outofstock" id="alert_soft">alert</p>
                            <p id="seleted_size_soft" class="seleted_size selected_output"><span class="underline"><span class="selected_lbl seleted_size_lbl"><?php esc_html_e('Soft Bra Size', 'zoa') ?>: </span><span class="selected_value seleted_size_value"></span></span></p>
                        </div>
                        <div class="full_cup_bra_area">
                            <p class="help_txt help_txt_soft"><?php esc_html_e('Choose full cup bra sizes you want to try for fitting.', 'zoa'); ?></p>
                            <div class="swatch__list">
                                <?php
                                $size = 4;
                                for ($i = 1; $i <= $size; $i++) {
                                    $stock_soft = get_option('ch_stock_soft_' . $i, 0);
                                    if ($stock_soft <= 0) {
                                        $stock_soft = 'outstock';
                                    }
                                ?>
                                    <div class="fit_bra_size <?php echo $stock_soft; ?>">
                                        <label for="bra_size_full_cup[]" class="swatch__radio swatch__radio-text">
                                            <input class="bra_size_full_cup required" type="checkbox" name="bra_size_full_cup[]" value="<?php echo $i; ?>" /><?php echo $i; ?>
                                        </label>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            <p class="outofstock" id="alert_full_cup"><?php esc_html_e('alert', 'zoa'); ?></p>
                            <p id="seleted_size_full_cup" class="seleted_size selected_output"><span class="underline"><span class="selected_lbl seleted_size_lbl"><?php esc_html_e('Full Cup Bra Size', 'zoa') ?>: </span><span class="selected_value seleted_size_value"></span></span></p>
                        </div>
                        <a class="ch_next button" href="javascript:void(0);"><?php esc_html_e('Next', 'zoa'); ?></a>
                        <p class="option_note"><input type="hidden" value="<?php esc_html_e('Sorry, can choose 2 sizes as max', 'zoa'); ?>" id="alert_ja_max2" /></p>
                    </div>
                </div>
                <div class="custom_option__container choose_online_onsultation">
                    <div class="fit_title acc_title fit_tite_online"><?php esc_html_e('Online Consultation', 'zoa'); ?></div>
                    <?php
                        $current_time_sv = current_time('timestamp');
                        $temp_hide = '';
                        if ($current_time_sv >= strtotime('2022-08-01') && $current_time_sv <= strtotime('2022-10-31')) {
                            $temp_hide = ' style="display:none;"';
                        ?>
                        <div class="fit_body">
                            <p class="help_txt">申し訳ございませんが、現在ご利用できません。</p>
                        </div>
                    <?php
                    }
                    ?>
                    <div <?php echo $temp_hide; ?> class="fit_body">
                        <p class="help_txt"><?php esc_html_e('If you want to take an online conslutation, please check.', 'zoa'); ?></p>
                        <label class="checkbox__simple" for="online_consultation"><input type="checkbox" class="online_consultation" name="online_consultation" value="yes" /><?php echo '<span class="opt_name ja">' . __('I would like to request!', 'zoa') . '</span>'; ?><span class="extra_fee">(+ &yen;<?php echo number_format($online_price + ($online_price * 0.1)); ?>)</span></label>
                        <?php
                            if (is_user_logged_in() ) : // only for customer logged in
                                $_user_id = get_current_user_id();
                                $rank_and_amount = mr_get_member_rank($_user_id);
                                $rank = $rank_and_amount['rank'];
                                if ( 'royal' == $rank ) :
                            ?>
                                    <div class="free_online_cons">
                                        <?php printf(__('Free Anytime by %s Member Ship!', 'zoa'), $rank); ?>
                                    </div>
                                    <?php
                                elseif ( 'gold' == $rank ) :
                                    $online_con_free = get_user_meta($_user_id, 'online_consultation_free', true);
                                    if ( isset($online_con_free) && 'yes' == $online_con_free ) :
                                        // this mean customer used online_consultation_free
                                    else :
                                    ?>
                                        <div class="free_online_cons">
                                            <?php printf(__('Free %s by %s membership!', 'zoa'), 1, $rank); ?>
                                        </div>
                                    <?php
                                    endif;
                                endif;
                            endif;
                        ?>
                        <div class="selected_note">
                            <p class="ttl underlined"><i class="oecicon oecicon-alert-circle-exc"></i> <?php esc_html_e('Notion for Online Consultation', 'zoa'); ?></p>
                            <p><?php echo do_shortcode(stripslashes(get_option('ch_online_con_notice'))); ?></p>
                        </div>
                    </div>
                </div>
                <div class="custom_option__total">
                    <table class="custom_option__total_table">
                        <tbody>
                            <tr class="custom_option__tr_base_price">
                                <td class="tr_label"><?php esc_html_e('Product price', 'zoa'); ?></td>
                                <td class="tr_value"><span class="price-amount"><?php echo wc_price($product_main->get_price() + $product_main->get_price() * 0.1); ?></span><small>(税込)</small></td>
                            </tr>
                            <tr class="custom_option__tr_additional_options">
                                <td class="tr_label"><?php esc_html_e('Additional options total', 'zoa'); ?></td>
                                <td class="tr_value"><span class="price-amount"><?php echo wc_price($online_price + ($online_price * 0.1)); ?></span><small>(税込)</small></td>
                            </tr>
                            <tr class="custom_option__tr_order_totals">
                                <td class="tr_label"><?php esc_html_e('Order total', 'zoa'); ?></td>
                                <?php
                                    $total = $product_main->get_price() + $online_price;
                                    $total_includetax = $total + $total * 0.1;
                                ?>
                                <td class="tr_value"><span class="price-amount"><?php echo wc_price($total_includetax); ?></span><small>(税込)</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row_option row_agreement">
                    <p class="form-row validate-required">
                        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                            <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="term_of_use_try_fit_your_size" id="term_of_use_try_fit" />
                            <?php
                                $post_term = get_page_by_path('term-of-use-try-fit-your-size', OBJECT, 'page');
                            ?>
                            <span class="woocommerce-terms-and-conditions-checkbox-text read_term_of_use_try_fit"><?php echo $post_term->post_title; ?>に同意します</span>&nbsp;<span class="required">*</span>
                        </label>
                    </p>
                </div>

            </div>
        <?php
        }
    }
}

// add_filter('woocommerce_add_to_cart_validation', 'ch_add_to_cart_validation', 10, 4);
function ch_add_to_cart_validation($passed, $product_id, $quantity, $variation_id = null) {
    // file_put_contents(dirname(__FILE__).'/request.txt', json_encode($_REQUEST));
    //    $special_service = get_option('ch_special_service', '');
    //    if (is_product_in_cat_fit($product_id, $special_service)) {//only for special-service product of special-service allow this
    //        if ($_REQUEST['bra_product_id'] == null || empty($_REQUEST['bra_product_id'])) {
    //            $passed = false;
    //            wc_add_notice(__('Please select bra product.', 'zoa'), 'error');
    //        }
    //    }
    return $passed;
}

add_filter('woocommerce_add_cart_item_data', 'ch_add_cart_item_data', 10, 3);
function ch_add_cart_item_data($cart_item_data, $product_id, $variation_id) {
    $special_service = get_option('ch_special_service', '');
    if (is_product_in_cat_fit($product_id, $special_service)) { // only for special-service product of special-service allow this
        if (!is_array($_REQUEST['bra_product_id']) && !empty($_REQUEST['bra_product_id'])) {
            $_REQUEST['bra_product_id'] = explode(",", $_REQUEST['bra_product_id']);
            $_REQUEST['bra_product_id'] = array_filter($_REQUEST['bra_product_id'], 'strlen');
            $_REQUEST['bra_size'] = explode(",", $_REQUEST['bra_size']);
            $_REQUEST['bra_size'] = array_filter($_REQUEST['bra_size'], 'strlen');
            $_REQUEST['bra_size_soft'] = explode(",", $_REQUEST['bra_size_soft']);
            $_REQUEST['bra_size_soft'] = array_filter($_REQUEST['bra_size_soft'], 'strlen');
            $_REQUEST['bra_size_full_cup'] = explode(",", $_REQUEST['bra_size_full_cup']);
            $_REQUEST['bra_size_full_cup'] = array_filter($_REQUEST['bra_size_full_cup'], 'strlen');
        }

        // file_put_contents(dirname(__FILE__).'/data.txt', json_encode($_REQUEST));
        if (isset($_REQUEST['bra_product_id']) && !empty($_REQUEST['bra_product_id'])) {
            // $bra_you_prefer = array();
            // foreach ($_REQUEST['bra_product_id'] as $value) {
            //     if (!empty($value)) {
            //         $product = wc_get_product($value);
            //         $bra_you_prefer[] = '[#' . $value . '] ' . $product->name;
            //     }
            // }
            $cart_item_data['bra_product_id'] = $_REQUEST['bra_product_id']; // this to use when check to get deposit
            // $cart_item_data['bra_you_prefer'] = implode(", ", $bra_you_prefer);
        }
        if (isset($_REQUEST['try_fit_type']) && !empty($_REQUEST['try_fit_type'])) {
            $cart_item_data['try_fit_type'] = $_REQUEST['try_fit_type'];
        }
        if (isset($_REQUEST['bra_size']) && !empty($_REQUEST['bra_size'])) {
            $cart_item_data['bra_size'] = implode(", ", $_REQUEST['bra_size']);
        }
        if (isset($_REQUEST['bra_size_soft']) && !empty($_REQUEST['bra_size_soft'])) {
            $cart_item_data['bra_size_soft'] = implode(", ", $_REQUEST['bra_size_soft']);
        }
        if (isset($_REQUEST['bra_size_full_cup']) && !empty($_REQUEST['bra_size_full_cup'])) {
            $cart_item_data['bra_size_full_cup'] = implode(", ", $_REQUEST['bra_size_full_cup']);
        }
        if (isset($_REQUEST['online_consultation']) && 'yes' == $_REQUEST['online_consultation'] ) {
            $cart_item_data['online_consultation'] = __('yes', 'zoa');
        }
    }
    return $cart_item_data;
}

function get_online_price() {
    $online_price = get_option('ch_price_online_con', 0);
    if (is_user_logged_in() ) { // only for customer logged in
        $_user_id = get_current_user_id();
        $rank_and_amount = mr_get_member_rank($_user_id);
        $rank = $rank_and_amount['rank'];
        if ( 'royal' == $rank ) {
            $online_price = 0;
        } elseif ( 'gold' == $rank ) {
            $online_con_free = get_user_meta($_user_id, 'online_consultation_free', true);
            if ( isset($online_con_free) && 'yes' == $online_con_free ) {
                // this mean customer used online_consultation_free
            } else {
                $online_price = 0; // apply 1time
            }
        }
    }
    return $online_price;
}

add_filter('woocommerce_get_item_data', 'ch_get_item_data', 10, 2);
function ch_get_item_data($item_data, $cart_item_data) {
    $online_price = get_online_price();
    // if (isset($cart_item_data['bra_you_prefer'])) {
    //     $item_data[] = array(
    //         'key' => __('Bra you prefer', 'zoa'),
    //         'value' => wc_clean($cart_item_data['bra_you_prefer'])
    //     );
    // }
    if (isset($cart_item_data['try_fit_type'])) {
        $item_data[] = array(
            'key' => __('Selected type', 'zoa'),
            'value' => wc_clean($cart_item_data['try_fit_type'])
        );
    }
    if (isset($cart_item_data['bra_size'])) {
        $item_data[] = array(
            'key' => __('Plunge Bra size', 'zoa'),
            'value' => wc_clean($cart_item_data['bra_size'])
        );
    }
    if (isset($cart_item_data['bra_size_soft'])) {
        $item_data[] = array(
            'key' => __('Soft Bra size', 'zoa'),
            'value' => wc_clean($cart_item_data['bra_size_soft'])
        );
    }
    if (isset($cart_item_data['bra_size_full_cup'])) {
        $item_data[] = array(
            'key' => __('Full cup Bra size', 'zoa'),
            'value' => wc_clean($cart_item_data['bra_size_full_cup'])
        );
    }
    if (isset($cart_item_data['online_consultation'])) {
        $item_data[] = array(
            'key' => __('Online Consultation', 'zoa'),
            'value' => '希望する'
        );
        $item_data[] = array(
            'key' => '',
            'value' => wc_clean('+&yen;' . number_format($online_price + ($online_price * 0.1)))
        );
    }
    return $item_data;
}

add_action('woocommerce_checkout_create_order_line_item', 'checkout_create_order_line_item', 10, 4);
function checkout_create_order_line_item($item, $cart_item_key, $values, $order) {
    $online_price = get_online_price();
    // if (isset($values['bra_you_prefer'])) {
    //     $item->add_meta_data(
    //             __('Bra you prefer', 'zoa'), $values['bra_you_prefer'], true
    //     );
    // }
    if (isset($values['try_fit_type'])) {
        $item->add_meta_data(
            __('Your type', 'zoa'),
            $values['try_fit_type'],
            true
        );
    }
    if (isset($values['bra_size'])) {
        $item->add_meta_data(
            __('Plunge Bra size', 'zoa'),
            $values['bra_size'],
            true
        );
    }
    if (isset($values['bra_size_soft'])) {
        $item->add_meta_data(
            __('Soft Bra size', 'zoa'),
            $values['bra_size_soft'],
            true
        );
    }
    if (isset($values['bra_size_full_cup'])) {
        $item->add_meta_data(
            __('Full cup Bra size', 'zoa'),
            $values['bra_size_full_cup'],
            true
        );
    }
    if (isset($values['online_consultation'])) {
        $item->add_meta_data(
            __('Online Consultation', 'zoa'),
            '希望する',
            true
        );
        $item->add_meta_data(
            '',
            '+&yen;' . number_format($online_price + ($online_price * 0.1)),
            true
        );
    }
}


// get ch_get_category_name
add_action('wp_ajax_ch_get_category_name', 'ch_get_category_name');
add_action('wp_ajax_nopriv_ch_get_category_name', 'ch_get_category_name');
function ch_get_category_name() {
    $ids = $_REQUEST['product_id'];
    $res = array();
    $cat_detect = array();
    if (isset($ids) && !empty($ids)) {
        foreach ($ids as $value) {
            if (!empty($value)) {
                if ( 'plunge-bra' == $value ) {
                    $res[] = __('Plunge Bra', 'zoa');
                    $cat_detect[] = $value;
                } elseif ( 'soft-bra' == $value ) {
                    $res[] = __('Soft Bra', 'zoa');
                    $cat_detect[] = $value;
                } elseif ( 'full-cup-bra' == $value ) {
                    $res[] = __('Full cup Bra', 'zoa');
                    $cat_detect[] = $value;
                }
            }
        }
    }
    if (!empty($res)) {
        $res = array_unique($res);
        sort($res);
        $value = implode(',', $res);
        $text = __('Selected type: ', 'zoa') . implode(',', $res);
        echo json_encode(array('value' => $value, 'text' => $text, 'cat_detect' => implode(',', $cat_detect)));
    } else {
        echo json_encode(array('value' => '', 'text' => '', 'cat_detect' => ''));
    }

    exit();
}

// When the order includes “Online Consultation”, need to input “ZOOM Meeting ID” in order edit page.
if (!function_exists('ch_zoom_meeting_id')) {

    function ch_zoom_meeting_id() {
        global $post;
        $online_consultation = get_post_meta($post->ID, '_ch_online_consultation', true) ? get_post_meta($post->ID, '_ch_online_consultation', true) : '';
        if ( isset($online_consultation) && 'yes' == $online_consultation ) {
            $zoom_metting_id = get_post_meta($post->ID, '_zoom_metting_id', true) ? get_post_meta($post->ID, '_zoom_metting_id', true) : '';
            $zoom_metting_passcode = get_post_meta($post->ID, 'zoom_metting_passcode', true) ? get_post_meta($post->ID, 'zoom_metting_passcode', true) : '';
            $zoom_metting_url = get_post_meta($post->ID, 'zoom_metting_url', true) ? get_post_meta($post->ID, 'zoom_metting_url', true) : '';
            ob_start();
        ?>
            <input type="hidden" name="chzoom_meta_field_nonce" value="<?php echo wp_create_nonce(); ?>">
            <!--            <div>ID:<br/><input type="text" id="zoom_metting_id" name="zoom_metting_id" value="<?php //echo $zoom_metting_id; 
                                                                                                                ?>"/></div>-->
            <!--            <div>
                <?php //echo __('Passcode:','zoa') 
                ?><br/><input type="text" id="zoom_metting_passcode" name="zoom_metting_passcode" value="<?php //echo $zoom_metting_passcode; 
                                                                                                            ?>"/>
            </div>-->
            <div>
                <?php esc_html_e('Url:', 'zoa') ?><br /><textarea id="zoom_metting_url" cols="35" rows="3" name="zoom_metting_url"><?php echo $zoom_metting_url; ?></textarea>
            </div>
    <?php
            $contents = ob_get_contents();
            ob_end_clean();
            echo $contents;
        }
    }
}

add_action('add_meta_boxes', 'ch_zoom_meeting_id_mtbox');
if (!function_exists('ch_zoom_meeting_id_mtbox')) {

    function ch_zoom_meeting_id_mtbox() {
        global $post;
        $online_consultation = get_post_meta($post->ID, '_ch_online_consultation', true) ? get_post_meta($post->ID, '_ch_online_consultation', true) : '';
        if (isset($online_consultation) && $online_consultation == 'yes') {
            add_meta_box('ch_zoom_meeting_id_mtbox', __('ZOOM Meeting ID', 'zoa'), 'ch_zoom_meeting_id', 'shop_order', 'side', 'core');
        }
    }
}
add_action('save_post', 'ch_save_try_fit_size_metabox', 10, 1);
if (!function_exists('ch_save_try_fit_size_metabox')) {

    function ch_save_try_fit_size_metabox($post_id) {
        if (!isset($_REQUEST['chzoom_meta_field_nonce'])) {
            return $post_id;
        }
        $nonce = $_REQUEST['chzoom_meta_field_nonce'];
        if (!wp_verify_nonce($nonce)) {
            return $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if ('page' == $_REQUEST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        if (isset($_REQUEST['zoom_metting_id']) || isset($_REQUEST['zoom_metting_passcode']) || isset($_REQUEST['zoom_metting_url'])) {
            update_post_meta($post_id, '_zoom_metting_id', $_REQUEST['zoom_metting_id']);
            update_post_meta($post_id, 'zoom_metting_passcode', $_REQUEST['zoom_metting_passcode']);
            update_post_meta($post_id, 'zoom_metting_url', $_REQUEST['zoom_metting_url']);
        }
    }
}

// get all order sent sample to send reminder zoom meeting
function get_all_orders_sent_reminder_zoom_meeting_csak235GHRwe43fewFE() {
    $args = array(
        'post_type' => 'shop_order',
        "post_status" => array('wc-sent-sample'),
        'posts_per_page' => '-1',
    );
    $query = new WP_Query($args);
    $order_sent = array();
    $hours_before_auto_send_zoom = get_option('ch_auto_send_before_zoom_meeting', 0);
    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
            $order_id = get_the_ID();
            $_ch_sent_email_warning = get_post_meta($order_id, '_ch_sent_email_reminder_zoom_meeting', true);
            if ($_ch_sent_email_warning != 'yes') {
                $appt_id = get_post_meta($order_id, '_ch_booked_id', true); //it is booked id
                if (!empty($appt_id)) {
                    $timestamp = get_post_meta($appt_id, '_appointment_timestamp', true);
                    $timeslot = get_post_meta($appt_id, '_appointment_timeslot', true);
                    $timeslots = explode('-', $timeslot);
                    $timestamp_start = strtotime(date_i18n('Y-m-d', $timestamp) . ' ' . $timeslots[0]);
                    $time_text_org = date_i18n("g:i:s A", $timestamp_start);
                    $datetime_booked_start = date_i18n('Y-m-d', $timestamp) . ' ' . $time_text_org;
                    $time_text_rest = date_i18n("g:i:s A", strtotime('-' . $hours_before_auto_send_zoom . ' hour', $timestamp_start)); //send before 1h when start meeting
                    $datetime_booked_rest1h = date_i18n('Y-m-d', $timestamp) . ' ' . $time_text_rest;
                    $current_time = current_time('timestamp');
                    // echo $datetime_booked_start.'<br/>';
                    // echo $datetime_booked_rest1h.'<br/>';
                    if (($current_time >= strtotime($datetime_booked_rest1h) && $current_time <= strtotime($datetime_booked_start)) || ($hours_before_auto_send_zoom == 0)) {
                        $mailer = WC()->mailer();
                        $mails = $mailer->get_emails();
                        //send to customer and admin
                        $email_to_send = 'WC_Customer_Reminder_Zoom_Meeting';
                        $email_to_send_admin = 'WC_Admin_Reminder_Zoom_Meeting';
                        if (!empty($mails)) {
                            foreach ($mails as $mail) {
                                if ($mail->id == $email_to_send || $mail->id == $email_to_send_admin) {
                                    $mail->trigger($order_id);
                                }
                            }
                        }
                        update_post_meta($order_id, '_ch_sent_email_reminder_zoom_meeting', 'yes');
                        $order_sent[] = $order_id;
                    }
                }
            }
        endwhile;
    }
    if (!empty($order_sent)) {
        mail('chien.lexuan@gmail.com', 'Log sent email cronjob reminder zoom meeting', 'order_id:' . json_encode($order_sent));
        echo json_encode($order_sent);
    }
    exit();
}

//build url to set cronjob to send reminder zoom meeting
//wp-admin/admin-ajax.php?action=get_all_orders_sent_reminder_zoom_meeting_csak235GHRwe43fewFE
add_action('wp_ajax_get_all_orders_sent_reminder_zoom_meeting_csak235GHRwe43fewFE', 'get_all_orders_sent_reminder_zoom_meeting_csak235GHRwe43fewFE');
add_action('wp_ajax_nopriv_get_all_orders_sent_reminder_zoom_meeting_csak235GHRwe43fewFE', 'get_all_orders_sent_reminder_zoom_meeting_csak235GHRwe43fewFE');

// save booked id to meta of order to use when get info to send reminder zoom id
add_action('booked_new_appointment_created', 'ch_booked_new_appointment_created', 99, 1);
function ch_booked_new_appointment_created($post_id) {
    if (isset($_SESSION['order_id_booked']) && !empty($_SESSION['order_id_booked'])) {
        update_post_meta($_SESSION['order_id_booked'], '_ch_booked_id', $post_id); //save booked id to order woo
        update_post_meta($post_id, '_ch_wc_order_id', $_SESSION['order_id_booked']); //save order id to booked
        unset($_SESSION['order_id_booked']);
    }
}

/**
 * Call back function for setting page
 */
function ch_try_fit_settings() {
    if (isset($_POST['ch_save_bd_setting'])) {
        update_option('ch_special_service', $_REQUEST['ch_special_service']);
        update_option('ch_plunge_bra', $_REQUEST['ch_plunge_bra']);
        update_option('ch_soft_bra', $_REQUEST['ch_soft_bra']);
        update_option('ch_plunge_bra_deposit', $_REQUEST['ch_plunge_bra_deposit']);
        update_option('ch_soft_bra_deposit', $_REQUEST['ch_soft_bra_deposit']);
        update_option('ch_deadline_return', $_REQUEST['ch_deadline_return']);
        update_option('ch_auto_send_before_days_return', $_REQUEST['ch_auto_send_before_days_return']);
        update_option('ch_auto_send_before_zoom_meeting', $_REQUEST['ch_auto_send_before_zoom_meeting']);
        update_option('ch_price_online_con', $_REQUEST['ch_price_online_con']);
        update_option('ch_online_con_notice', $_REQUEST['ch_online_con_notice']);
        //save stock manager
        update_option('ch_stock_soft_1', $_REQUEST['soft_1']);
        update_option('ch_stock_soft_2', $_REQUEST['soft_2']);
        update_option('ch_stock_soft_3', $_REQUEST['soft_3']);
        update_option('ch_stock_soft_4', $_REQUEST['soft_4']);
        update_option('ch_stock_plunge_1', $_REQUEST['plunge_1']);
        update_option('ch_stock_plunge_2', $_REQUEST['plunge_2']);
        update_option('ch_stock_plunge_3', $_REQUEST['plunge_3']);
        update_option('ch_stock_plunge_4', $_REQUEST['plunge_4']);
        update_option('ch_stock_full_cup_1', $_REQUEST['full_cup_1']);
        update_option('ch_stock_full_cup_2', $_REQUEST['full_cup_2']);
        update_option('ch_stock_full_cup_3', $_REQUEST['full_cup_3']);
        update_option('ch_stock_full_cup_4', $_REQUEST['full_cup_4']);
        $stock = array(
            'soft_1' => get_option('ch_stock_soft_1', 0),
            'soft_2' => get_option('ch_stock_soft_2', 0),
            'soft_3' => get_option('ch_stock_soft_3', 0),
            'soft_4' => get_option('ch_stock_soft_4', 0),
            'plunge_1' => get_option('ch_stock_plunge_1', 0),
            'plunge_2' => get_option('ch_stock_plunge_2', 0),
            'plunge_3' => get_option('ch_stock_plunge_3', 0),
            'plunge_4' => get_option('ch_stock_plunge_4', 0),
            'full_cup_1' => get_option('ch_stock_full_cup_1', 0),
            'full_cup_2' => get_option('ch_stock_full_cup_2', 0),
            'full_cup_3' => get_option('ch_stock_full_cup_3', 0),
            'full_cup_4' => get_option('ch_stock_full_cup_4', 0)
        );
        $current_user = wp_get_current_user();
        file_put_contents(dirname(__FILE__) . '/log_stock.txt', $current_user->user_login . ': ' . date_i18n('Y-m-d H:i:s') . "\n" . json_encode($stock) . "\n", FILE_APPEND);
    }
    ob_start();
    if (isset($_POST['ch_save_bd_setting'])) {
    ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
        </div>
    <?php
    }
    ?>
    <h3><?php esc_html_e('Try fit your size setting Page', 'zoa'); ?>:</h3>
    <hr />
    <form action="" method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Category slug include products will show Try fit your size function', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="text" name="ch_special_service" value="<?php echo get_option('ch_special_service'); ?>" /><i><?php esc_html_e('all products in this category will show Try fit your size function frontend', 'zoa') ?></i>
                    </td>
                </tr>
                <tr style="display: none;">
                    <th scope="row"><label><?php esc_html_e('Plunge Bra category slug', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="text" name="ch_plunge_bra" value="<?php echo get_option('ch_plunge_bra'); ?>" /><i><?php esc_html_e('all products in this category will show to customer choose on Choose Bra you prefer section', 'zoa') ?></i>
                    </td>
                </tr>
                <tr style="display: none;">
                    <th scope="row"><label><?php esc_html_e('Plunge Bra deposit', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="number" name="ch_plunge_bra_deposit" value="<?php echo get_option('ch_plunge_bra_deposit'); ?>" />&yen;
                    </td>
                </tr>
                <tr style="display: none;">
                    <th scope="row"><label><?php esc_html_e('Soft Bra category slug', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="text" name="ch_soft_bra" value="<?php echo get_option('ch_soft_bra'); ?>" /><i><?php esc_html_e('all products in this category will show to customer choose on Choose Bra you prefer section', 'zoa'); ?></i>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Bra deposit', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="number" name="ch_soft_bra_deposit" value="<?php echo get_option('ch_soft_bra_deposit'); ?>" />&yen;
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Price for online consultation', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="number" name="ch_price_online_con" value="<?php echo get_option('ch_price_online_con', 0); ?>" />&yen;
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Stock for sample bras', 'zoa'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <a target="_blank" href="<?php echo get_stylesheet_directory_uri() . '/wc-try-fit-your-size/log_stock.txt'; ?>"><?php esc_html_e('See log change stock:', 'zoa'); ?></a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table>
                                        <tr>
                                            <td><?php esc_html_e('Soft Bra size 1', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_soft_1', 0); ?>" name="soft_1" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Soft Bra size 2', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_soft_2', 0); ?>" name="soft_2" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Soft Bra size 3', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_soft_3', 0); ?>" name="soft_3" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Soft Bra size 4', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_soft_4', 0); ?>" name="soft_4" /></td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        <tr>
                                            <td><?php esc_html_e('Plunge Bra size 1', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_plunge_1', 0); ?>" name="plunge_1" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Plunge Bra size 2', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_plunge_2', 0); ?>" name="plunge_2" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Plunge Bra size 3', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_plunge_3', 0); ?>" name="plunge_3" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Plunge Bra size 4', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_plunge_4', 0); ?>" name="plunge_4" /></td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        <tr>
                                            <td><?php esc_html_e('Full cup Bra size 1', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_full_cup_1', 0); ?>" name="full_cup_1" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Full cup Bra size 2', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_full_cup_2', 0); ?>" name="full_cup_2" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Full cup Bra size 3', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_full_cup_3', 0); ?>" name="full_cup_3" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php esc_html_e('Full cup Bra size 4', 'zoa') ?></td>
                                            <td><input type="number" value="<?php echo get_option('ch_stock_full_cup_4', 0); ?>" name="full_cup_4" /></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Online consultation notice', 'zoa'); ?></label></th>
                    <td>
                        <textarea name="ch_online_con_notice" cols="100" rows="5"><?php echo stripslashes(get_option('ch_online_con_notice')); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Deadline allow return sample product', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="number" name="ch_deadline_return" value="<?php echo get_option('ch_deadline_return'); ?>" /><?php esc_html_e('days', 'zoa'); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Auto Send Waring Email before x (day) before deadline', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="number" name="ch_auto_send_before_days_return" value="<?php echo get_option('ch_auto_send_before_days_return'); ?>" /><?php esc_html_e('days', 'zoa'); ?> - <i><?php esc_html_e('Url to setup cron job on server:', 'zoa') ?><span style="background: aqua;"> <?php echo admin_url('admin-ajax.php?action=get_all_orders_sent_sample_send_email_key_023JwqfHNY487555CH'); ?><span></i>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Auto Send Waring Email before x (hours) appointment time/date', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="number" name="ch_auto_send_before_zoom_meeting" value="<?php echo get_option('ch_auto_send_before_zoom_meeting'); ?>" /><?php esc_html_e('hours', 'zoa') ?> - <i><?php esc_html_e('Url to setup cron job on server:', 'zoa') ?><span style="background: aqua;"> <?php echo admin_url('admin-ajax.php?action=get_all_orders_sent_reminder_zoom_meeting_csak235GHRwe43fewFE'); ?><span></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="submit" name="ch_save_bd_setting" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'zoa'); ?>">
    </form>
    <?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

add_action('admin_menu', 'setup_menu_fit_setting');
function setup_menu_fit_setting() {
    add_submenu_page('woocommerce', __('Try fit your size setting', 'zoa'), __('Try fit your size setting', 'zoa'), 'manage_options', 'ch_try_fit_settings', 'ch_try_fit_settings', '', 15);
}

/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'ch_validate_checkout');

function ch_validate_checkout() {
    $ch_soft_bra_deposit = 0;
    $is_bra_size = false;
    $is_bra_size_soft = false;
    $is_bra_size_full_cup = false;
    $exist = false;
    $plunge_bra_size = array();
    $soft_bra_size = array();
    $full_cup_bra_size = array();
    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (is_product_in_cat_fit($cart_item['product_id'], $special_service_category_slug)) {
            if ($exist == false) {
                $exist = true;
            }
            if (isset($cart_item['bra_product_id']) && !empty($cart_item['bra_product_id'])) {
                foreach ($cart_item['bra_product_id'] as $value_id) { //products, that custome choosed
                    if ($value_id == 'plunge-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); //fixed for all case 10000
                    } elseif ($value_id == 'soft-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); //fixed for all case 10000
                    } elseif ($value_id == 'full-cup-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); //fixed for all case 10000
                    }
                }
            }
            if (isset($cart_item['bra_size']) && !empty($cart_item['bra_size'])) {
                $is_bra_size = true;
                $sizes = explode(",", $cart_item['bra_size']);
                $plunge_bra_size = array_merge($plunge_bra_size, $sizes);
            }
            if (isset($cart_item['bra_size_soft']) && !empty($cart_item['bra_size_soft'])) {
                $is_bra_size_soft = true;
                $sizes = explode(",", $cart_item['bra_size_soft']);
                $soft_bra_size = array_merge($soft_bra_size, $sizes);
            }
            if (isset($cart_item['bra_size_full_cup']) && !empty($cart_item['bra_size_full_cup'])) {
                $is_bra_size_full_cup = true;
                $sizes = explode(",", $cart_item['bra_size_full_cup']);
                $full_cup_bra_size = array_merge($full_cup_bra_size, $sizes);
            }
        }
    }
    if ($exist) {
        if ($ch_soft_bra_deposit == 0) {
            wc_add_notice(__('気になっているブラタイプの選択は必須です', 'zoa'), 'error');
        }
        if ($is_bra_size == false && $is_bra_size_soft == false && $is_bra_size_full_cup == false) {
            wc_add_notice(__('サンプルブラサイズの選択は必須です', 'zoa'), 'error');
        }
        if (!empty($plunge_bra_size)) {
            foreach ($plunge_bra_size as $value) {
                if (!empty(trim($value))) {
                    $current_stock = get_option('ch_stock_plunge_' . trim($value), 0);
                    if ($current_stock == 0) {
                        wc_add_notice(sprintf(__('Plunge bra sample size %s out of stock.', 'zoa'), $value), 'error');
                    }
                }
            }
        }
        if (!empty($soft_bra_size)) {
            foreach ($soft_bra_size as $value) {
                if (!empty(trim($value))) {
                    $current_stock = get_option('ch_stock_soft_' . trim($value), 0);
                    if ($current_stock == 0) {
                        wc_add_notice(sprintf(__('Soft bra sample size %s out of stock.', 'zoa'), $value), 'error');
                    }
                }
            }
        }
    }
}

add_action('woocommerce_review_order_before_order_total', 'ch_woocommerce_review_order_after_shipping', 10, 0);
add_action('woocommerce_cart_totals_before_order_total', 'ch_woocommerce_review_order_after_shipping', 10, 0);
function ch_woocommerce_review_order_after_shipping() {
    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    $plunge_bra = get_option('ch_plunge_bra', ''); // 'plunge-bra'; //category slug
    $soft_bra = get_option('ch_soft_bra', ''); // 'soft-bra'; //category slug
    $ch_plunge_bra_deposit = 0;
    $ch_soft_bra_deposit = 0;
    $sizes = 0;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (is_product_in_cat_fit($cart_item['product_id'], $special_service_category_slug)) {
            if (isset($cart_item['bra_product_id']) && !empty($cart_item['bra_product_id'])) {
                foreach ($cart_item['bra_product_id'] as $value_id) { //products, that custome choosed
                    if ($value_id == 'plunge-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); //fixed for all case 10000
                    } elseif ($value_id == 'soft-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); //fixed for all case 10000
                    } elseif ($value_id == 'full-cup-bra') {
                        $ch_soft_bra_deposit = get_option('ch_soft_bra_deposit', 0); //fixed for all case 10000
                    }
                }
            }
            if (isset($cart_item['bra_size'])) {
                $sizes = 1; //count(explode(",", $cart_item['bra_size']));
            }
        }
    }
    // if ($ch_plunge_bra_deposit > 0) {
    //     echo '<div class="deposit__item">
    //     <span class="label">'.__('Plunge Bra', 'zoa').' &times; '.$sizes.'</span>
    //     <span class="value price-amount"> '.wc_price($ch_plunge_bra_deposit*$sizes).'</span>
    //     </div>';
    // }
    // if ($ch_soft_bra_deposit > 0) {
    //     echo '<div class="deposit__item">
    //     <span class="label">'.__('Soft Bra', 'zoa').' &times; '.$sizes.'</span>
    //     <span class="value price-amount"> '.wc_price($ch_soft_bra_deposit).'</span>
    //     </div>';
    // }
    if ($ch_plunge_bra_deposit > 0 || $ch_soft_bra_deposit > 0) {
        echo '<div class="order__summary__row order-deposit-notice">' . __('*This deposit will be refund after we receive your return.', 'zoa') . '</div>';
    }
}

//Tracking code for try fit order. this metabox only for try fit size order.
if (!function_exists('ch_try_fit_tracking')) {

    function ch_try_fit_tracking() {
        global $post;
        $_try_fit_tracking_code = get_post_meta($post->ID, '_try_fit_tracking_code', true) ? get_post_meta($post->ID, '_try_fit_tracking_code', true) : '';
        echo '<input type="hidden" name="chtraking_meta_field_nonce" value="' . wp_create_nonce() . '">';
        echo '<input type="text" id="_try_fit_tracking_code" name="_try_fit_tracking_code" value="' . $_try_fit_tracking_code . '"/>';
    }
}

//add_action('add_meta_boxes', 'ch_tracking_mtbox');
if (!function_exists('ch_tracking_mtbox')) {

    function ch_tracking_mtbox() {
        global $post;
        $is_try_fit_order = get_post_meta($post->ID, '_ch_product_fee', true) ? get_post_meta($post->ID, '_ch_product_fee', true) : '';
        if ($is_try_fit_order > 0) {
            add_meta_box('ch_tracking_code_mtbox', __('Try fit order tracking code', 'zoa'), 'ch_try_fit_tracking', 'shop_order', 'side', 'core');
        }
    }
}
//add_action('save_post', 'ch_save_try_fit_tracking_metabox', 10, 1);
if (!function_exists('ch_save_try_fit_tracking_metabox')) {

    function ch_save_try_fit_tracking_metabox($post_id) {
        if (!isset($_REQUEST['chtraking_meta_field_nonce'])) {
            return $post_id;
        }
        $nonce = $_REQUEST['chtraking_meta_field_nonce'];
        if (!wp_verify_nonce($nonce)) {
            return $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if ('page' == $_REQUEST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        if (isset($_REQUEST['_try_fit_tracking_code'])) {
            update_post_meta($post_id, '_try_fit_tracking_code', $_REQUEST['_try_fit_tracking_code']);
        }
    }
}

// add_action('wp_ajax_try_fit_customer_notify_to_admin', 'try_fit_customer_notify_to_admin');
// add_action('wp_ajax_nopriv_try_fit_customer_notify_to_admin', 'try_fit_customer_notify_to_admin');
// https://staging-chiyonoanne.kinsta.cloud/wp-admin/admin-ajax.php?action=try_fit_customer_notify_to_admin&id=MzY3NjQ=&type=email&passcode=325235
function try_fit_customer_notify_to_admin() {
    $type = $_REQUEST['type'];
    if (isset($type) && 'email' == $type) {
        $id = $_REQUEST['id'];
        $passcode = $_REQUEST['passcode'];
        $order_id = base64_decode($id);
        $passcode_org = get_post_meta($order_id, '_ch_passcode', true);
        if ($passcode_org == $passcode) {
            $order = wc_get_order($order_id);
            // notify i returned to admin 
            $_payment_method = trim(get_post_meta($order_id, '_payment_method', true));
            if ( strpos($_payment_method, 'stripe') !== false && $order->has_status('sent-sample')) {
                $mailer = WC()->mailer();
                $mails = $mailer->get_emails();
                //send to admin
                $email_to_send_admin = 'WC_Admin_Reminder_I_Returned';
                if (!empty($mails)) {
                    foreach ($mails as $mail) {
                        if ($mail->id == $email_to_send_admin) {
                            $mail->trigger($order_id);
                        }
                    }
                }
                wp_redirect(home_url('thank-you-for-your-nofity'), 301);
                exit();
            } else {
                wp_redirect(home_url(), 301);
                exit();
            }
        } else {
            wp_redirect(home_url(), 301);
            exit();
        }
    } else {
        $response = array();
        $order_id = $_REQUEST['order_id'];
        $order = wc_get_order($order_id);

        // notify i returned to admin 
        $_payment_method = trim(get_post_meta($order_id, '_payment_method', true));
        if ( strpos($_payment_method, 'stripe') !== false && $order->has_status('sent-sample')) {
            $mailer = WC()->mailer();
            $mails = $mailer->get_emails();
            //send to admin
            $email_to_send_admin = 'WC_Admin_Reminder_I_Returned';
            if (!empty($mails)) {
                foreach ($mails as $mail) {
                    if ($mail->id == $email_to_send_admin) {
                        $mail->trigger($order_id);
                    }
                }
            }
            $response['success'] = 1;
            $response['msg'] = __('Completed. You sent notify to admin of shop.', 'zoa');
        } else { //end
            $response['success'] = 1;
            $response['msg'] = __('Do not allow on this order', 'zoa');
        }
        echo json_encode($response);
        die;
    }
}

// for coupon code in completed email
function generate_coupon_code_each_try_fit() {
    global $wpdb;
    // Get an array of all existing coupon codes
    $coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon'");
    for ($i = 0; $i < 1; $i++) {
        $generated_code = strtolower(wp_generate_password(10, false));

        // Check if the generated code doesn't exist yet
        if (in_array($generated_code, $coupon_codes)) {
            $i--; // continue the loop and generate a new code
        } else {
            break; // stop the loop: The generated coupon code doesn't exist already
        }
    }
    return $generated_code;
}

// use this shortcode: [coupon_code_each_try_fit order_id='[order_number]']  in 'Completed' email in email builder.
add_shortcode('coupon_code_each_try_fit', 'coupon_code_each_try_fit');
function coupon_code_each_try_fit($atts, $content = null) {
    $enable_coupn = false; // Stop coupon offer for TBYB. Will use when need.
    // Here below define your coupons discount ammount
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    $exist = false;
    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    $price_include_online = 0;
    $price_not_include_online = 0;
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
            $exist = true;
            $price_not_include_online = $product->get_price() + $product->get_price() * 0.1;
            $price_include_online = $product->get_price() + get_online_price();
            $price_include_online = $price_include_online + $price_include_online * 0.1;
            break;
        }
    }
    if ($exist && $enable_coupn) { // only for try fit order
        $online_consultation = get_post_meta($order_id, '_ch_online_consultation', true);
        if (isset($online_consultation) && 'yes' == $online_consultation) { //include online consultation
            $coupon_price = $price_include_online;
        } else {
            $coupon_price = $price_not_include_online;
        }
        $discount_amounts = array($coupon_price);

        // Set some coupon data by default
        $date_expires = date_i18n('Y-m-d', strtotime('+93 days'));
        $discount_type = 'fixed_cart';
        $category = get_term_by('slug', $special_service_category_slug, 'product_cat');
        $cat_id = $category->term_id;
        $except_categoryis = array($cat_id);

        // Loop through the defined array of coupon discount amounts
        foreach ($discount_amounts as $coupon_amount) {
            // Get an emty instance of the WC_Coupon Object
            $coupon = new WC_Coupon();

            // Generate a non existing coupon code name
            $coupon_code = generate_coupon_code_each_try_fit();

            // Set the necessary coupon data (since WC 3+)
            $coupon->set_code($coupon_code);
            $coupon->set_discount_type($discount_type);
            $coupon->set_amount($coupon_amount);

            $coupon->set_date_expires($date_expires);
            $coupon->set_usage_limit(1);
            $coupon->set_usage_limit_per_user(1);
            $coupon->set_individual_use(false);
            $coupon->set_excluded_product_categories($except_categoryis);

            // Create, publish and save coupon (data)
            $coupon->save();
        }
        return '<span style="font-weight:bold;display:inline-block;line-height:1;padding: 6px 12px;border:1px solid #000;color:#000;">' . strtoupper($coupon_code) . '</span>';
    } else {
        return '';
    }
}

add_filter('woocommerce_email_subject_customer_completed_order', 'ch_email_subject_customer_completed_order', 999, 2);
function ch_email_subject_customer_completed_order($subject, $order) {
    $exist = false;
    $special_service_category_slug = get_option('ch_special_service', ''); //'special-service';
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
            $exist = true;
            break;
        }
    }
    if ($exist) { // for try fit your site order
        global $woocommerce;
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $online_consultation = get_post_meta($order->id, '_ch_online_consultation', true);
        if (isset($online_consultation) && 'yes' == $online_consultation) {
            // including zoom
            $subject = sprintf('T.B.Y.B ご注文(#%s) 手続き完了のお知らせ |  %s', $order->id, $blogname);
        } else {
            $subject = sprintf('T.B.Y.B ご注文(#%s) 手続き完了のお知らせ |  %s', $order->id, $blogname);
        }
    }
    return $subject;
}

// use this shortcode: [ch_completed_order_text order_id='[order_number]']  in 'Completed' email in email builder.
add_shortcode('ch_completed_order_text', 'ch_completed_order_text');
function ch_completed_order_text($atts, $content = null) {
    if ( !function_exists('wc_get_order') ) {
        return '';
    }
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    $exist = false;
    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
            $exist = true;
            break;
        }
    }
    $is_digitalcard = false;
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        $tags_arr = array();
        $tags     = get_the_terms($product->get_id(), 'product_tag');
        if ($tags && !is_wp_error($tags)) {
            $tags_arr = wp_list_pluck($tags, 'slug');
        }
        if (in_array('digitalcard', $tags_arr)) {
            $is_digitalcard = true;
            break;
        }
    }
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    if ($exist) { // for try fit your site order
        $online_consultation = get_post_meta($order_id, '_ch_online_consultation', true);
        if (isset($online_consultation) && $online_consultation == 'yes') { // for zoom
            ob_start();
    ?>
            <h1 style="text-align: center;"><span style="font-family: Courier, monospace; font-weight: 400; font-size: 28px;">Thank you for using T.B.Y.B!</span></h1>
            <p style="text-align: center;">この度は 「<?php echo $blogname; ?>」 をご利用いただきありがとうございます。<br>フィットサンプルブラレンタルサービスがお役に立てたら幸いです！</p>
            <!--only for coupon offer -->
            <!--<p style="text-align: center;">次回のご注文にご利用いただけるクーポンコードをお送りいたします。</p>
            <p style="text-align:center;margin-bottom: 5px;letter-spacing: .05em;"><strong>クーポンコード</strong></p>-->
            <!--end-->
        <?php
            } else { // for not zoom
        ?>
            <h1 style="text-align: left;"><span style="font-family: Courier, monospace; font-weight: 400; font-size: 28px;">Thank you for using T.B.Y.B!</span></h1>
            <p style="text-align: left;">この度は<?php echo $blogname; ?> をご利用いただきありがとうございます。<br>フィットサンプルブラレンタルサービス、オンラインコンサルテーションがお役に立てたら幸いです！</p>
            <!--only for coupon offer -->
            <!--<p style="text-align: left;">次回のご注文にご利用いただけるクーポンコードをお送りいたします。</p><br>
            <p style="text-align: center;margin-bottom: 5px;letter-spacing: .05em;"><strong>クーポンコード</strong></p>-->
            <!--end-->
        <?php
        }
        $contents = ob_get_contents();
        ob_end_clean();
    } else if ($is_digitalcard) {
        ob_start();
        ?>
        <h1 style="text-align: center;"><span style="font-family: Courier, monospace; font-weight: 400; font-size: 28px;">Your code has been sent!</span></h1>
        <p style="text-align: left">この度は<?php echo $blogname; ?> をご利用いただきありがとうございます。</p>
        <p style="text-align: left;">ご注文いただきましたデジタル商品の注文処理が完了し、コードがメール送信されましたのでお知らせいたします。</p>
        <p style="text-align: left;">コードは「受取人メールアドレス」へのメール送信となります。</p>
        <p style="text-align: left;">注文詳細は以下です。</p>
    <?php
        $contents = ob_get_contents();
        ob_end_clean();
    } else {
        ob_start();
    ?>
        <h1 style="text-align: center;"><span style="font-family: Courier, monospace; font-weight: 400; font-size: 28px;">Your order has been shipped!</span></h1>
        <p style="text-align: left">この度は<?php echo $blogname; ?> をご利用いただきありがとうございます。</p>
        <p style="text-align: left;">ご注文いただきました商品の発送が完了いたしましたのでお知らせいたします。</p>
        <p style="text-align: left;">お手元へ届きましたら、すぐに内容をお確かめください。</p>
        <p style="text-align: left;">注文詳細は以下です。</p>
    <?php
        $contents = ob_get_contents();
        ob_end_clean();
    }
    return $contents;
}

// use this shortcode: [ch_survey_link order_id='[order_number]']  in 'Completed' email in email builder.
add_shortcode('ch_survey_link', 'ch_survey_link');
function ch_survey_link($atts, $content = null) {
    if ( !function_exists('wc_get_order') ) {
        return '';
    }
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    $exist = false;
    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
            $exist = true;
            break;
        }
    }
    if ($exist) {
        return '<br><p style="text-align: center;">また、今後のサービス及び品質向上のため、<br>下記アンケートにご協力いただけますと幸いです。</p><p style="text-align: center;"><a style="color: #FFFFFF;font-style:normal;text-decoration: none;font-weight: bold;border: 1px solid #000000;background: #000000;line-height: 1;padding: 8px 13px;display: inline-block;letter-spacing: .1em;" href="' . home_url('/survey/') . '">' . __('アンケートに答える', 'zoa') . '</a></p>';
    } else {
        return '';
    }
}

// [tbyb_shortcode slug="product-for-try-fit-your-size"]
add_shortcode('tbyb_shortcode', 'tbyb_shortcode');
function tbyb_shortcode($atts) {
    $slug = $atts['slug'];
    $post = get_page_by_path($slug, OBJECT, 'product');
    if ($post) {
        return '<a href="' . get_permalink($post->ID) . '" class="btn icon-btn icon-btn__goto"><span>' . __('Try it now', 'zoa') . '</span></a>';
    } else {
        return '';
    }
}

// use this shortcode: [coupon_notion_tbty order_id='[order_number]']  in 'Completed' email in email builder.
add_shortcode('coupon_notion_tbty', 'coupon_notion_tbty');
function coupon_notion_tbty($atts, $content = null) {
    if ( !function_exists('wc_get_order') ) {
        return '';
    }
    $enable_coupn = false; // Temp stop and use when need
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    $exist = false;
    $special_service_category_slug = get_option('ch_special_service', ''); //'special-service';
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (is_product_in_cat_fit($product->get_id(), $special_service_category_slug)) {
            $exist = true;
            break;
        }
    }
    ob_start();
    if ($exist && $enable_coupn) { // for try fit your size order
    ?>
        <p style="text-align: center;"><b>&diams; クーポンご利用に関するご注意点　&diams;</b></p>
        <ul>
            <li>5万円（税別）以上のお買い上げにのみ有効です</li>
            <li>クーポンの有効期限はオンラインショップにてご利用の場合は、T.B.Y.Bキットの返却確認日から3ヶ月、アトリエの場合は、返却確認日から6ヶ月となります(28日以降到着の場合はそれぞれ3ヶ月後/6ヶ月後の末日まで有効とします)</li>
            <li>本クーポンは遅延金/損害金発生の有無に関わらず、すべての利用者に有効です</li>
        </ul>
    <?php
    }
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

add_action('restrict_manage_posts', 'add_extra_ch_order_type_try_fit');
function add_extra_ch_order_type_try_fit($post_type) {

    global $wpdb;
    if ($post_type !== 'shop_order')
        return;

    if (isset($_GET['ch_order_try_fit']) && $_GET['ch_order_try_fit'] != '') {
        $selectedName = $_GET['ch_order_try_fit'];
    } else {
        $selectedName = -1;
    }
    $options[] = sprintf('<option value="-1">%1$s</option>', __('All', 'zoa'));
    if ('yes' == $selectedName) {
        $options[] = '<option value="yes" selected>' . __('(T.B.T.Y)online consultation', 'zoa') . '</option>';
    } else {
        $options[] = '<option value="yes">' . __('(T.B.T.Y)online consultation', 'zoa') . '</option>';
    }

    echo '<select class="" id="ch_order_try_fit" name="ch_order_try_fit">';
    echo join("\n", $options);
    echo '</select>';
}

add_action('request', 'request_applicant_filter_try_fit_online');
function request_applicant_filter_try_fit_online($request) {
    global $pagenow;
    $current_page = isset($_GET['post_type']) ? $_GET['post_type'] : '';
    if (is_admin() && 'shop_order' == $current_page && 'edit.php' == $pagenow && isset($_REQUEST['ch_order_try_fit'])) {
        if (!empty($_REQUEST['ch_order_try_fit']) && $_REQUEST['ch_order_try_fit'] != '-1' && isset($_REQUEST['filter_action'])) {
            $ch_order_type = $_REQUEST['ch_order_try_fit'];
            $request['meta_key'] = '_ch_online_consultation';
            $request['meta_value'] = $ch_order_type;
        }
    }
    return $request;
}

add_filter('woocommerce_email_subject_customer_refunded_order', 'ch_woocommerce_email_subject_customer_refunded_order', 999, 2);
function ch_woocommerce_email_subject_customer_refunded_order($subject, $order) {
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    if ($order->get_total_refunded() != $order->get_total()) {
        $subject = 'ご注文(#' . $order->id . ')一部返金完了のお知らせ | ' . $blogname;
    }
    return $subject;
}

// use this shortcode: [shortcode_refund_text order_id='[order_number]']  in 'CUSTOMER REFUNDED ORDER' email in email builder.
add_shortcode('shortcode_refund_text', 'shortcode_refund_text');
function shortcode_refund_text($atts, $content = null) {
    if ( !function_exists('wc_get_order') ) {
        return '';
    }
    ob_start();
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    if ($order->get_total_refunded() != $order->get_total()) { // for partial refund
    ?>
        <h1 style="text-align: center;"><span style="font-family: Helvetica, Arial, sans-serif;">一部返金が完了しました</span></h1>
        <p style="text-align: center;">この度は<?php echo $blogname; ?> をご利用いただき、<br>まことにありがとうございます。</p>
        <p style="text-align: center;">ご注文(#<?php echo $order_id; ?>)の一部返金が完了しましたので、<br>お知らせいたします。</p>
    <?php
    } else { // For full refund
    ?>
        <h1 style="text-align: center;"><span style="font-family: Helvetica, Arial, sans-serif;">返金が完了しました</span></h1>
        <p style="text-align: center;">この度は<?php echo $blogname; ?> をご利用いただき、<br>まことにありがとうございます。</p>
        <p style="text-align: center;">ご注文(#<?php echo $order_id; ?>)の返金が完了しましたので、<br>お知らせいたします。</p>
<?php
    }
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

add_action('woocommerce_order_actions', 'ch_sv_wc_add_order_meta_box_action');
function ch_sv_wc_add_order_meta_box_action($actions) {
    $actions['wc_resend_fisrt_email_action'] = __('Resend first email', 'zoa');
    return $actions;
}

add_action('woocommerce_order_action_wc_resend_fisrt_email_action', 'ch_sv_wc_process_resend_first_email');
function ch_sv_wc_process_resend_first_email($order) {
    $message = __('Resend first email completed.', 'zoa');
    $order->add_order_note($message);
    $mailer = WC()->mailer();
    $mails = $mailer->get_emails();
    $email_to_send = 'customer_processing_order';
    if (!empty($mails)) {
        foreach ($mails as $mail) {
            if ($mail->id == $email_to_send) {
                $mail->trigger($order->id);
            }
        }
    }
}

// add_filter( 'woocommerce_pre_remove_cart_item_from_session', 'ch_pre_remove_items_from_a_specific_category', 10, 3 );
// function ch_pre_remove_items_from_a_specific_category( $remove, $key, $values ){
//    $special_service_category_slug = get_option('ch_special_service', ''); // 'special-service';
//    if (is_product_in_cat_fit($values['product_id'], $special_service_category_slug)) {
//    // if ( has_term( $categories, 'product_cat', $values['product_id'] ) ) {
//        $remove = true;
//    }
//    return $remove;
// }

add_action('clear_auth_cookie', 'log_function_cart');
function log_function_cart() {
    $special_service_category_slug = get_option('ch_special_service', ''); //'special-service';
    $exist = false;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (is_product_in_cat_fit($cart_item['product_id'], $special_service_category_slug)) {
            $exist = true;
            break;
        }
    }
    if ($exist) {
        WC()->cart->empty_cart();
    }
}

add_action('wp_login', 'clear_persistent_cart_after_login', 10, 2);
function clear_persistent_cart_after_login($user_login, $user) {
    $blog_id = get_current_blog_id();
    if (metadata_exists('user', $user->ID, '_woocommerce_persistent_cart_' . $blog_id)) {
        delete_user_meta($user->ID, '_woocommerce_persistent_cart_' . $blog_id);
    }
}
