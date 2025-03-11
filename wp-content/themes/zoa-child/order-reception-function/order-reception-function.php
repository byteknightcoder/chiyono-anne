<?php

include_once dirname(__FILE__) . '/inc/setting.php';
include_once dirname(__FILE__) . '/inc/shortcode.php';

add_action('wp_enqueue_scripts', 'add_scripts_orf');
function add_scripts_orf() {
    wp_enqueue_style('order-reception-function', get_stylesheet_directory_uri() . '/order-reception-function/css/order-reception-function.css', array(), time());
    // wp_enqueue_script('order-reception-function', get_stylesheet_directory_uri() . '/order-reception-function/js/order-reception-function.js', array(), time(), true);
}


function order_type_for_event() {
    return get_option('ch_order_type_even', '');
}

function order_sub_type_for_event() {
    return get_option('ch_orf_sub_type_even', '');
}

function product_has_tag($product_id) {
    $tags = get_the_terms($product_id, 'product_tag');
    $tag_list = '';
    if ($tags && !is_wp_error($tags)) {
        $tag_list = wp_list_pluck($tags, 'name');
        $tags_option = get_option('ch_orf_tag', '');
        if (!empty($tags_option)) {
            $arr_tag_op = explode(",", $tags_option);
            foreach ($arr_tag_op as $value) {
                if (in_array($value, $tag_list)) {
                    return true;
                }
            }
        }
    }
    return false;
}

function is_order_exhibition() {
    if (is_admin())
        return false;
    $exist = false;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (product_has_tag($cart_item['product_id']) && isset($cart_item['ch_type_orf']) && $cart_item['ch_type_orf'] == order_type_for_event()) {
            $exist = true;
            break;
        }
    }
    return $exist;
}

function is_expired_orf() {
    $current_time = current_time('timestamp');
    $end_date = get_option('ch_orf_end_date', '');
    if ($current_time > strtotime($end_date) && !empty($end_date)) { //this mean event expired.
        return true;
    } else {
        return false;
    }
}

add_action('template_redirect', 'orf_redirect_pre_checkout');
function orf_redirect_pre_checkout() {
    if (!function_exists('wc'))
        return;
    //check in case run directly from browser with type when expired
    global $post;
    if (is_expired_orf()) { // this mean event expired.
        if (isset($_REQUEST['type']) && $_REQUEST['type'] == order_type_for_event() && is_product()) {
            wp_redirect(get_permalink($post->ID));
        }
        if (is_page('event')) {
            global $post;
            wp_redirect(home_url('shop-all'));
        }
    } else {
        if (isset($_REQUEST['type']) && is_product() && !product_has_tag($post->ID)) {
            global $post;
            wp_redirect(get_permalink($post->ID));
        }
    }
    // end
    if (is_order_exhibition()) {
        if (is_expired_orf()) { //this mean event expired.
            WC()->cart->empty_cart();
        }
        if (!is_user_logged_in() && (is_checkout() || is_cart())) {
            wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
            die;
        }
    }
}


// http://localhost/wp572/wp-admin/admin-ajax.php?action=orf_get_a_coupon_code
add_action('wp_ajax_orf_get_a_coupon_code', 'orf_get_a_coupon_code');
add_action('wp_ajax_nopriv_orf_get_a_coupon_code', 'orf_get_a_coupon_code');
function orf_get_a_coupon_code() {
    if (is_order_exhibition() && is_expired_orf() == false) {
        $coupon_code = coupon_code_each_orf();
        //remove coupon, that create before to avoid create many coupon code
        foreach (WC()->cart->get_coupons() as $code => $coupon) {
            //WC()->cart->remove_coupon($code);
            $coupon_data = new WC_Coupon($code);
            if (!empty($coupon_data->id)) {
                wp_delete_post($coupon_data->id);
            }
        }
        //end
        if (!empty($coupon_code)) {
            $response['success'] = 'ok';
            $response['code'] = $coupon_code;
        } else {
            $response['success'] = 'error';
            $response['msg'] = __('Have an error when get a coupon code. Please try again.', 'zoa');
        }
    } else {
        $response['success'] = 'error';
        $response['msg'] = __('You can not use this function.', 'zoa');
    }
    echo json_encode($response);
    exit();
}

// for coupon code in completed email
// current don't use this
function generate_coupon_code_orf() {
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

// current don't use this
function coupon_code_each_orf() {
    // Here below define your coupons discount ammount
    $value = 0;
    $discount_amounts = array($value);
    // Set some coupon data by default
    $date_expires = date_i18n('Y-m-d', strtotime('+7 days'));
    $discount_type = 'percent';

    // Loop through the defined array of coupon discount amounts
    foreach ($discount_amounts as $coupon_amount) {
        // Get an emty instance of the WC_Coupon Object
        $coupon = new WC_Coupon();

        // Generate a non existing coupon code name
        $coupon_code = generate_coupon_code_orf();

        // Set the necessary coupon data (since WC 3+)
        $coupon->set_code($coupon_code);
        $coupon->set_discount_type($discount_type);
        $coupon->set_amount($coupon_amount);

        $coupon->set_date_expires($date_expires);
        $coupon->set_usage_limit(1);
        $coupon->set_usage_limit_per_user(1);
        $coupon->set_individual_use(true);
        $coupon->set_free_shipping(true);

        // Create, publish and save coupon (data)
        $coupon->save();
        update_post_meta($coupon->id, 'ch_coupon_type', order_type_for_event()); //save this to check in case customer use coupon code for other products. This coupon only use for products of event
    }
    return strtoupper($coupon_code);
}

add_filter('woocommerce_coupon_is_valid', 'filter_woocommerce_coupon_is_valid_orf', 10, 2);
function filter_woocommerce_coupon_is_valid_orf($true, $instance) {
    if (isset($_REQUEST['coupon_code']) && !empty($_REQUEST['coupon_code'])) {
        $coupon = trim($_REQUEST['coupon_code']);
        $coupon_data = new WC_Coupon($coupon);
        if (!empty($coupon_data->id)) {
            $type = get_post_meta($coupon_data->id, 'ch_coupon_type', true);
            if (isset($type) && $type == order_type_for_event()) {
                if (is_order_exhibition() == false) {
                    return false;
                }
            }
        }
    }
    return $true;
}

// define the woocommerce_cart_is_empty callback 
add_action('woocommerce_cart_is_empty', 'action_woocommerce_cart_is_empty_orf', 10, 0);
function action_woocommerce_cart_is_empty_orf() {
    foreach (WC()->cart->get_coupons() as $code => $coupon) {
        WC()->cart->remove_coupon($code);
    }
    // end
}

// show only stripe and payid payment when in cart has product of of event
add_filter('woocommerce_available_payment_gateways', 'ch_filter_available_payment_gateways_orf', 10, 1);
function ch_filter_available_payment_gateways_orf($available_gateways) {
    if (!function_exists('WC') || !isset(WC()->cart) || WC()->cart->is_empty() || empty($available_gateways)) {
        return $available_gateways;
    }

    foreach ($available_gateways as $id => $gateway) {
        if (is_order_exhibition() == true && $id != 'stripe' && $id != 'payid') {
            unset($available_gateways[$id]);
        }
    }

    return $available_gateways;
}

add_filter('woocommerce_add_cart_item_data', 'ch_add_cart_item_data_orf', 10, 3);
function ch_add_cart_item_data_orf($cart_item_data, $product_id, $variation_id) {
    if (isset($_REQUEST['ch_type_orf']) && $_REQUEST['ch_type_orf'] == order_type_for_event() && is_expired_orf() == false && product_has_tag($product_id)) {
        $cart_item_data['ch_type_orf'] = $_REQUEST['ch_type_orf'];
    }
    if (isset($_REQUEST['ch_orf_sub_type_even']) && $_REQUEST['ch_orf_sub_type_even'] == order_sub_type_for_event() && is_expired_orf() == false && product_has_tag($product_id)) {
        $cart_item_data['ch_orf_sub_type_even'] = $_REQUEST['ch_orf_sub_type_even'];
    }
    // file_put_contents(dirname(__FILE__) . '/cart.txt', json_encode($cart_item_data) . "\n", FILE_APPEND);
    return $cart_item_data;
}

add_action('woocommerce_after_add_to_cart_button', 'create_hidden_input_to_save_type');
function create_hidden_input_to_save_type() {
    global $post;
    if (isset($_REQUEST['type']) && $_REQUEST['type'] == order_type_for_event() && is_expired_orf() == false && product_has_tag($post->ID)) {
        echo ' <input type="hidden" id="ch_type_orf" name="ch_type_orf" value="' . order_type_for_event() . '"/>';
        if (isset($_REQUEST['sub_type']) && !empty($_REQUEST['sub_type'])) {
            echo ' <input type="hidden" id="ch_orf_sub_type_even" name="ch_orf_sub_type_even" value="' . order_sub_type_for_event() . '"/>';
        }
    }
}

add_action('wp_login', 'clear_persistent_cart_after_login_orf', 10, 2);
function clear_persistent_cart_after_login_orf($user_login, $user) {
    $blog_id = get_current_blog_id();
    if (metadata_exists('user', $user->ID, '_woocommerce_persistent_cart_' . $blog_id)) {
        delete_user_meta($user->ID, '_woocommerce_persistent_cart_' . $blog_id);
    }
}

/**
 * Process the checkout
 * */
add_action('woocommerce_checkout_process', 'ch_validate_checkout_orf');
function ch_validate_checkout_orf() {
    if (is_order_exhibition()) {
        if (is_expired_orf()) {
            wc_add_notice(__('Event is expired, so you can not place this order.', 'zoa'), 'error');
        } else {
            // file_put_contents(dirname(__FILE__).'/step.txt', '1'."\n",FILE_APPEND);
            $_SESSION['ch_is_exhibition'] = 'yes';
        }
    }
}

add_action('woocommerce_thankyou', 'custom_woocommerce_auto_complete_order_orf', 99, 1);
function custom_woocommerce_auto_complete_order_orf($order_id) {
    if (!$order_id) {
        return;
    }
    //file_put_contents(dirname(__FILE__).'/step.txt', '2'."\n",FILE_APPEND);
    if (isset($_SESSION['ch_is_exhibition']) && !empty($_SESSION['ch_is_exhibition'])) {
        update_post_meta($order_id, 'ch_order_type', order_type_for_event());
        update_post_meta($order_id, 'order_sub_type', order_sub_type_for_event());
        update_post_meta($order_id, 'ch_event_order', 'yes');
        unset($_SESSION['ch_is_exhibition']);
    }
}

add_action('wp_head', 'ch_myplugin_ajaxurl');
function ch_myplugin_ajaxurl() {
    echo '<script type="text/javascript">var ch_ajaxurl = "' . admin_url('admin-ajax.php') . '"; </script>';
}

add_action('woocommerce_removed_coupon', 'ch_action_woocommerce_removed_coupon', 10, 1);
function ch_action_woocommerce_removed_coupon($coupon_code) {
    $coupon_data = new WC_Coupon($coupon_code);
    if (!empty($coupon_data->id)) {
        $type = get_post_meta($coupon_data->id, 'ch_coupon_type', true);
        if (isset($type) && $type == order_type_for_event()) {
            wp_delete_post($coupon_data->id);
        }
    }
}

add_action('admin_head', 'ch_my_custom_css');
function ch_my_custom_css() {
    // Check if 'post' exists in $_REQUEST before using it
    if (isset($_REQUEST['post'])) {
        $order_id = $_REQUEST['post'];

        if (!empty($order_id)) {
            $type = get_post_meta($order_id, 'ch_event_order', true);

            // Check if 'type' is empty or not set
            if (empty($type) || !isset($type)) {
                echo '<style>
                    #acf-group_611e42415ae48, #acf-group_612cae417da1f {
                        display: none !important;
                    } 
                </style>';
            }
        }
    }
}

add_filter('woocommerce_login_redirect', 'event_order_redirect_login', 10, 2);
function event_order_redirect_login($redirect, $user) {
    if (is_order_exhibition()) {
        return home_url('checkout');
    } else {
        return $redirect;
    }
}

add_action('woocommerce_registration_redirect', 'ch_event_order_redirect_register', 10, 1);
function ch_event_order_redirect_register($redirect) {
    if (is_order_exhibition()) {
        return home_url('checkout');
    } else {
        return $redirect;
    }
}

add_action('woocommerce_before_cart_table', 'ch_apply_discount_to_cart');
add_action('woocommerce_before_checkout_form', 'ch_apply_discount_to_cart');
function ch_apply_discount_to_cart() {
    if (is_order_exhibition() && is_expired_orf() == false) {
        // commend this to don't use free shipping coupon
        //        $coupon_code = coupon_code_each_orf();
        //        if (!WC()->cart->add_discount(sanitize_text_field($coupon_code))) {
        //            //WC()->show_messages();
        //        }
        //
        // This to don't allow any coupon in event item
        WC()->cart->remove_coupons();
    }
}

// don't allow use coupon for any event product
add_filter('woocommerce_coupon_is_valid', 'orf_filter_woocommerce_coupon_is_valid', 10, 2);
function orf_filter_woocommerce_coupon_is_valid($true, $instance) {
    if (is_order_exhibition() && is_expired_orf() == false) {
        return false;
    }
    return $true;
}

// https://staging-chiyonoanne.kinsta.cloud/wp-admin/admin-ajax.php?action=ajax_auto_remove_tag_from_products
add_action('wp_ajax_ajax_auto_remove_tag_from_products', 'ajax_auto_remove_tag_from_products');
add_action('wp_ajax_nopriv_ajax_auto_remove_tag_from_products', 'ajax_auto_remove_tag_from_products');
function ajax_auto_remove_tag_from_products() {
    $tags = get_option('ch_orf_tag', '');
    // Define Query Arguments
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'product_tag' => $tags,
        'post_status' => array('publish')
    );
    // Create the new query
    $the_query = new WP_Query($args);
    // Get products number
    $product_count = $the_query->post_count;
    if ($product_count > 0 && is_expired_orf() == true && !empty($tags)) {
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $p_id = get_the_ID();
                $current_tags = get_the_terms($p_id, 'product_tag');
                $temp_tag = array();
                foreach ($current_tags as $value) {
                    if ($value->slug != $tags) {
                        $temp_tag[] = $value->term_id;
                    }
                }
                wp_set_object_terms($p_id, $temp_tag, 'product_tag');
            }
            wp_reset_postdata();
        }
    }
    exit();
}
