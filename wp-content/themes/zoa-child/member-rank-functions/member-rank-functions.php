<?php

include_once dirname(__FILE__) . '/inc/setting.php';
include_once dirname(__FILE__) . '/rank-logic.php';

/*
 * Get settings
 */
function mr_get_settings() {
    return array(
        'royal' => get_option('ch_mr_royal', 0),
        'gold_from' => get_option('ch_mr_gold_from', 0),
        'gold_to' => get_option('ch_mr_gold_to', 0),
        'silver_from' => get_option('ch_mr_silver_from', 0),
        'silver_to' => get_option('ch_mr_silver_to', 0),
        'bronze' => get_option('ch_mr_bronze', 0),
        'up_down_rank' => get_option('ch_mr_up_down_rank', 0),
        'ch_mr_coupon_unlimited' => get_option('ch_mr_coupon_unlimited', ''),
        'ch_mr_coupon_2times' => get_option('ch_mr_coupon_2times', ''),
        'ch_mr_coupon_except_bra_2times' => get_option('ch_mr_coupon_except_bra_2times', ''),
        'mr_test_mode' => get_option('mr_test_mode', ''),
    );
}

function mr_coupon_limit() {
    return 2;
}

function mr_generate_coupon_code() {
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

/**
 * create coupon
 * @return type
 */
function mr_coupon_code($value = '', $limit = '', $category = '') {
    // Here below define your coupons discount ammount
    $discount_amounts = array($value);
    // Set some coupon data by default
    // $date_expires = date_i18n('Y-m-d', strtotime('+7 days'));
    $discount_type = 'percent';

    // Loop through the defined array of coupon discount amounts
    foreach ($discount_amounts as $coupon_amount) {
        // Get an emty instance of the WC_Coupon Object
        $coupon = new WC_Coupon();

        // Generate a non existing coupon code name
        $coupon_code = mr_generate_coupon_code();

        // Set the necessary coupon data (since WC 3+)
        $coupon->set_code($coupon_code);
        $coupon->set_discount_type($discount_type);
        $coupon->set_amount($coupon_amount);

        //$coupon->set_date_expires($date_expires);
        //$coupon->set_usage_limit(1);
        if (!empty($limit)) {
            $coupon->set_usage_limit_per_user($limit);
        }
        $coupon->set_individual_use(true);
        // $coupon->set_free_shipping(true);
        // $coupon->set_product_ids(11);
        if (!empty($category)) {
            $coupon->set_excluded_product_categories($category);
        }
        // Create, publish and save coupon (data)
        $coupon->save();
    }
    return strtoupper($coupon_code);
}

/**
 * Auto apply coupon to cart
 */
add_action('woocommerce_before_cart_table', 'mr_apply_discount_to_cart', 11);
add_action('woocommerce_before_checkout_form', 'mr_apply_discount_to_cart', 11);
function mr_apply_discount_to_cart() {
    if (is_user_logged_in()) { // only for customer logged in
        $_user_id = get_current_user_id();
        $settings = mr_get_settings();
        $coupon_code = $settings['ch_mr_coupon_unlimited'];
        $rank_and_amount = mr_get_member_rank($_user_id);
        $rank = $rank_and_amount['rank'];
        if ( 'royal' == $rank ) {
            if (!empty($coupon_code)) {
                if ( !in_array(strtolower($coupon_code), WC()->cart->applied_coupons) ) {
                    if ( !WC()->cart->add_discount(sanitize_text_field($coupon_code)) ) {
                        // WC()->show_messages();
                    }
                }
            }
        } else {
            $coupon_code_gold = $settings['ch_mr_coupon_2times'];
            $coupon_code_silver = $settings['ch_mr_coupon_except_bra_2times'];
            foreach (WC()->cart->get_coupons() as $code => $coupon) {
                if (strtolower($code) == strtolower($coupon_code)) {
                    WC()->cart->remove_coupon($code);
                }
                // for gold
                if (strtolower($code) == strtolower($coupon_code_gold) && $rank != 'gold') {
                    WC()->cart->remove_coupon($code);
                }
                // for silver
                if (strtolower($code) == strtolower($coupon_code_silver) && $rank != 'silver') {
                    WC()->cart->remove_coupon($code);
                }
            }
        }
        add_action('woocommerce_before_shipping_calculator', 'zoa_woocommerce_before_shipping_calculator');
        add_action('woocommerce_review_order_after_shipping', 'zoa_woocommerce_before_shipping_calculator');
    }
}

/**
 * Valid coupon code
 * @param type $true
 * @param type $instance
 * @return boolean
 */
add_filter('woocommerce_coupon_is_valid', 'mr_filter_woocommerce_coupon_is_valid', 10, 2);
function mr_filter_woocommerce_coupon_is_valid($true, $instance) {
    if (isset($_REQUEST['coupon_code']) && !empty($_REQUEST['coupon_code'])) {
        $current_user = wp_get_current_user();
        //if (isset($current_user) && isset($current_user->ID) && $current_user->ID > 0) {//only for customer logged
        $rank_and_amount = mr_get_member_rank($current_user->ID);
        $start_date_rank = get_user_meta($current_user->ID, 'start_date_rank', true);
        $end_date_rank = get_user_meta($current_user->ID, 'end_date_rank', true);
        $rank = get_user_meta($current_user->ID, 'rank', true);
        $coupon = trim($_REQUEST['coupon_code']);
        $settings = mr_get_settings();
        $coupon_unlimit = $settings['ch_mr_coupon_unlimited'];
        $coupon_2times = $settings['ch_mr_coupon_2times'];
        $coupon_ex_2times = $settings['ch_mr_coupon_except_bra_2times'];
        $mr_coupon_limit = mr_coupon_limit();
        if (strtoupper($coupon_unlimit) == strtoupper($coupon)) {
            if ($rank != 'royal') {
                return false;
            }
        } elseif (strtoupper($coupon_2times) == strtoupper($coupon)) {
            $coupon_ob = new WC_Coupon($settings['ch_mr_coupon_2times']);
            $coupon_used = mr_get_coupon_used_by_user($current_user->ID, $coupon_ob->id);
            if ($rank != 'gold' || $coupon_used >= $mr_coupon_limit) {
                return false;
            }
        } elseif (strtoupper($coupon_ex_2times) == strtoupper($coupon)) {
            $coupon_ob = new WC_Coupon($settings['ch_mr_coupon_except_bra_2times']);
            $coupon_used = mr_get_coupon_used_by_user($current_user->ID, $coupon_ob->id);
            if ($rank != 'silver' || $coupon_used >= $mr_coupon_limit) {
                return false;
            }
        }
    }
    return $true;
}

function mr_product_in_cat($product_id, $category_slug) {
    $accessories = mr_get_taxonomy_hierarchy('product_cat', 0, $category_slug);
    $product_cats = get_the_terms($product_id, 'product_cat');
    foreach ($product_cats as $product_cat) {
        if (in_array($product_cat->term_id, array_keys($accessories))) {
            return true;
        }
    }
    return false;
}

function mr_get_taxonomy_hierarchy($taxonomy, $parent = 0, $slug = '', &$children = array()) {
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
        mr_get_taxonomy_hierarchy($taxonomy, $term->term_id, '', $children);
        $children[$term->term_id] = $term;
    }
    return $children;
}

add_action('admin_notices', 'sample_admin_notice__success');
function sample_admin_notice__success() {
    // Check if 'post' exists in the $_REQUEST array
    if (isset($_REQUEST['post'])) {
        $order_id = $_REQUEST['post'];

        // Proceed only if order_id is not empty and is of post type 'shop_order'
        if (!empty($order_id) && get_post_type($order_id) == 'shop_order') {
            $order = wc_get_order($order_id);

            // Ensure the order object is valid
            if ($order) {
                $user_id = $order->get_user_id();

                // Check if the user_id is valid
                if ($user_id > 0) {
                    $rank_and_amount = mr_get_member_rank($user_id, true);

                    // Check if 'rank_and_amount' contains 'rank' before accessing
                    if (isset($rank_and_amount['rank'])) {
                    ?>
                        <div class="notice notice-success">
                            <p><?php esc_html_e('Member rank:', 'zoa'); ?> <?php echo esc_html($rank_and_amount['rank']); ?></p>
                        </div>
                    <?php
                    }
                }
            }
        }
    }
}

function mr_get_coupon_used_by_user($user_id, $coupon_id) {
    $usage = 0;
    if (empty($user_id) && empty($email)) {
        return FALSE;
    }
    $start_date_rank = get_user_meta($user_id, 'start_date_rank', true);
    $end_date_rank = get_user_meta($user_id, 'end_date_rank', true);
    $params = [
        'limit' => -1,
        'type' => 'shop_order'
    ];
    $coupon = new WC_Coupon($coupon_id);
    if (!empty($user_id)) {
        $params['customer_id'] = $user_id;
    } else if (!empty($email)) {
        $params['billing_email'] = $email;
    }

    // Get orders of customer
    $orders = wc_get_orders($params);

    // Check if coupon used
    foreach ($orders as $order) {
        $coupons_used = $order->get_coupon_codes();
        $date_order = $order->get_date_created()->date_i18n('Y-m-d H:i:s');
        if (
            is_array($coupons_used) &&
            in_array($coupon->code, $coupons_used) && strtotime($date_order) <= strtotime($end_date_rank) && strtotime($date_order) > strtotime($start_date_rank)
        ) { // count only coupon used in time range [start_date_rank,end_date_rank]  of current rank
            $usage++;
        }
    }

    return $usage;
}

function ch_is_user_role($role, $user_id = null) {
    $user = is_numeric($user_id) ? get_userdata($user_id) : wp_get_current_user();
    if (!$user) {
        return false;
    }
    return in_array($role, (array) $user->roles);
}

add_action('admin_menu', 'remove_woo_sub_menu_pages', 9999);
function remove_woo_sub_menu_pages() {
    if (ch_is_user_role('administrator')) {
        $current_user = wp_get_current_user();
        if ($current_user->user_login != 'admin') {
            remove_submenu_page('woocommerce', 'wf_pr_rev_csv_im_ex');
            remove_submenu_page('woocommerce', 'wc-order-export');
            remove_submenu_page('woocommerce', 'woocommerce_csv_import_suite');
            remove_submenu_page('woocommerce', 'po_settings_review');
            remove_submenu_page('woocommerce', 'send_email_imported_customers');
            remove_submenu_page('woocommerce', 'send_sms_imported_mobile_customers');
            remove_submenu_page('woocommerce', 'send_email_bulk_by_csv');
            remove_submenu_page('woocommerce', 'send_email_bulk_by_csv_couponcode');
            remove_submenu_page('woocommerce', 'po_settings_review_rm');
        }
    }
}

// https://chiyono-anne.com/wp-admin/admin-ajax.php?action=ch_save_next_time_update_rank_LMngwfhASFj435HKFdh3AsS043e$fEf9
add_action('wp_ajax_ch_save_next_time_update_rank_LMngwfhASFj435HKFdh3AsS043e$fEf9', 'ch_save_next_time_update_rank');
add_action('wp_ajax_nopriv_ch_save_next_time_update_rank_LMngwfhASFj435HKFdh3AsS043e$fEf9', 'ch_save_next_time_update_rank');
function ch_save_next_time_update_rank() {
    $blogusers = get_users(array('fields' => array('ID')));
    foreach ($blogusers as $user) {
        $user_id = $user->ID;
        mr_get_member_rank($user_id);
    }
    exit();
}

add_action('wp_ajax_mr_time_number_coupon', 'mr_time_number_coupon');
add_action('wp_ajax_nopriv_mr_time_number_coupon', 'mr_time_number_coupon');
function mr_time_number_coupon() {
    if (is_user_logged_in() ) { // only for customer logged in
        $_user_id = get_current_user_id();
        $res = '';
        $rank = mr_get_member_rank($_user_id);
        $settings = mr_get_settings();
        $coupon_code_gold = $settings['ch_mr_coupon_2times'];
        $coupon_code_silver = $settings['ch_mr_coupon_except_bra_2times'];
        if ( 'gold' == $rank['rank'] ) {
            $coupon = new WC_Coupon($settings['ch_mr_coupon_2times']);
            $usage_limit_per_user = mr_coupon_limit();
            $used = mr_get_coupon_used_by_user($_user_id, $coupon->id);
            $remain = $usage_limit_per_user - $used;
            foreach (WC()->cart->get_coupons() as $code => $coupon) {
                //for gold
                if (strtolower($code) == strtolower($coupon_code_gold)) {
                    $remain = $remain - 1;
                    break;
                }
            }
            if ($remain > 0) {
                if ($remain >= 2) {
                    $res = sprintf(__("(You have %s coupons left)", 'zoa'), $remain);
                } else {
                    $res = sprintf(__("(You have only %s coupon left)", 'zoa'), $remain);
                }
            }
        } elseif ( 'silver' == $rank['rank'] ) {
            $coupon = new WC_Coupon($settings['ch_mr_coupon_except_bra_2times']);
            $usage_limit_per_user = mr_coupon_limit();
            $used = mr_get_coupon_used_by_user($_user_id, $coupon->id);
            $remain = $usage_limit_per_user - $used;
            foreach (WC()->cart->get_coupons() as $code => $coupon) {
                //for silver
                if (strtolower($code) == strtolower($coupon_code_silver)) {
                    $remain = $remain - 1;
                    break;
                }
            }
            if ($remain > 0) {
                if ($remain >= 2) {
                    $res = sprintf(__("(You have %s coupons left)", 'zoa'), $remain);
                } else {
                    $res = sprintf(__("(You have only %s coupon left)", 'zoa'), $remain);
                }
            }
        }
        echo $res;
    }
    exit();
}

add_action('wp_ajax_ch_update_coupon_order_in_admin_23407efkkgJef23434', 'ch_update_coupon_order_in_admin_23407efkkgJef23434');
add_action('wp_ajax_nopriv_ch_update_coupon_order_in_admin_23407efkkgJef23434', 'ch_update_coupon_order_in_admin_23407efkkgJef23434');
// https://chiyono-anne.com/wp-admin/admin-ajax.php?action=ch_update_coupon_order_in_admin_23407efkkgJef23434&order_id=1&coupon_code=2
function ch_update_coupon_order_in_admin_23407efkkgJef23434() {
    $order_id = $_REQUEST['order_id'];
    $coupon_code = $_REQUEST['coupon_code'];
    if (!empty($order_id) && !empty($coupon_code)) {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM `{$wpdb->prefix}woocommerce_order_items` WHERE `order_item_type` = %s AND `order_item_name` = %s AND `order_id` = %d;",
                'coupon',
                $coupon_code,
                $order_id
            )
        );
        $obj_order = new WC_Order($order_id);
        $obj_order->apply_coupon($coupon_code);
        $obj_order->save();
        echo 'Apply coupon code manual success.';
    } else {
        echo 'Need order id and coupon code';
    }
    exit();
}

add_shortcode('mrf_member_info_shortcode', 'mrf_member_info_shortcode');
function mrf_member_info_shortcode() {
    $group_name = 'ms_definition';
    $base_page = get_page_by_path('about-repair');
    $base_id = $base_page->ID;
    $def_rank = 'rank';
    $def_benefit = 'list';
    $def_repair = 'repair';
    $def_repairship = 'repair_shipfee';
    ob_start();
?>
    <?php if (have_rows($group_name, $base_id)) : ?>
        <table class="rank_table ja table_responsive">
            <thead>
                <tr>
                    <th class="rank_name">会員ステータス</th>
                    <th class="rank_repair">お直し内容</th>
                    <th class="rank_repairship">送料<br /><small>(郵送が必要な場合のみ)</small></th>
                </tr>
            </thead>
            <?php
                while (have_rows($group_name, $base_id)) : the_row();
                    if (have_rows('offer')) :
                ?>
                    <tbody>
                        <?php while (have_rows('offer')): the_row(); ?>
                            <?php
                                $subs = array();
                                $subs = get_sub_field_object($def_rank);
                                $ranks_value = $subs['value'];
                                $ranks = $subs['choices'][$ranks_value];
                                $benefits = get_sub_field($def_benefit);
                                $repairs = get_sub_field($def_repair);
                                $repairship = get_sub_field_object($def_repairship);
                                $repair_value = $repairship['value'];
                                $repair_label = $repairship['choices'][$repair_value];
                            ?>
                            <tr class="rank_tr rank_tr_<?php echo $ranks_value; ?>">
                                <td class="rank_name" data-label="会員ステータス">
                                    <span class="rank_icon">
                                        <?php if ( 'royal' == $ranks_value ) : ?>
                                            <svg id="gradient">
                                                <defs>
                                                    <linearGradient id="linearGradient">
                                                        <stop offset="0%" stop-color="#74ebd5"></stop>
                                                        <stop offset="50%" stop-color="#ffdde1"></stop>
                                                        <stop offset="100%" stop-color="#ACB6E5"></stop>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                            <span class="svg-wrap eq_icon">
                                                <svg class="icoca icoca-RankIcon">
                                                    <use xlink:href="#icoca-RankIcon"></use>
                                                </svg>
                                            </span>
                                        <?php else : ?>
                                            <span class="rank_icon"><span class="svg-wrap eq_icon">
                                                <svg class="icoca icoca-RankIcon">
                                                    <use xlink:href="#icoca-RankIcon"></use>
                                                </svg>
                                                </span>
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="name"><?php echo $ranks; ?></span>
                                </td>
                                <?php if ($repairs) : ?>
                                    <td class="rank_repair" data-label="&lt;&nbsp;お直し内容&nbsp;&gt;">
                                        <ul class="list">
                                            <?php
                                                foreach ($repairs as $repair) {
                                                    echo '<li>' . $repair['item'] . '</li>';
                                                }
                                            ?>
                                        </ul>
                                    </td>
                                <?php endif; ?>
                                <?php if ($repairship) : ?>
                                    <td class="rank_repairship" data-label="&lt;&nbsp;送料(郵送が必要な場合のみ)&nbsp;&gt;">
                                        <?php echo $repair_label; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                <?php endif; ?>
            <?php endwhile; ?>
        </table>
        <!-- <div class="p_xxs p_notice">
        <p>※ゲストとしてご購入されたご注文は換算されませんので、あらかじめご了承ください。</p>
        <p>※キャンセル・返品をされた場合はご購入金額には加算されません。</p>
        <p>※ディスカウントクーポンをご利用の場合は、ディスカウントされた後の支払い金額が累計対象となります。</p>
        </div> -->
<?php
    endif;
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

// Hook to add menu and submenu pages.
add_action('admin_menu', 'zoa_membership_menu');
function zoa_membership_menu() {
    // Add a submenu under the woocommerce customers manager main menu.
    add_submenu_page(
        'woocommerce-customers-manager',            // Parent slug
        __('Customer Details', 'zoa'),              // Page title
        __('Customer Details', 'zoa'),              // Menu title
        'manage_options',                           // Capability
        'woocommerce-customer-levels-details',      // Menu slug
        'woocommerce_customer_levels_details'       // Callback function
    );
}
// Submenu page callback function.
function woocommerce_customer_levels_details() {
    // Create JSON Data View
    $membership_data = [];
    $args = array( 'fields' => array( 'ID', 'display_name', 'user_email' ), 'role' => 'customer', 'number' => -1, 'orderby' => 'user_registered', 'order' => 'desc' );
    $customers = get_users($args);

    foreach ($customers as $customer) {
        $customer_id = $customer->ID;
        $locale = get_user_locale($customer_id);
        $rank = ucwords(get_user_meta($customer_id, 'rank', true));
        $last_rank_updated = ucwords(get_user_meta($customer_id, 'last_rank_updated', true));
        $first_name = get_user_meta($customer_id, 'first_name', true);
        $last_name = get_user_meta($customer_id, 'last_name', true);
        $spent_total = get_user_meta($customer_id, 'spent_total', true);
        $full_name = $first_name . ' ' . $last_name;
        if ( 'ja' == $locale || 'site-default' == $locale ) {
            $full_name = $last_name . ' ' . $first_name;
        }
        $full_name = trim($full_name);
        if ( empty($full_name) ) {
            $full_name = $customer->display_name;
        }

        $total_spend = class_exists('WCCM_CustomerDetails') ? WCCM_CustomerDetails::get_user_total_spent( null, null, zoa_get_all_user_orders($customer_id) ) : get_woocommerce_currency_symbol() . '0';
        $details = admin_url('/admin.php?page=woocommerce-customers-manager&customer='. $customer_id . '&action=customer_details');
        $membership_data[$rank][] = [
            'id' => $customer_id,
            'name' => $full_name,
            'email' => $customer->user_email,
            'spend_in_last_year' => get_woocommerce_currency_symbol() . $spent_total,
            'total_spend' => $total_spend,
            'current_rank' => $rank,
            'last_rank_updated' => $last_rank_updated,
            'detail_link' => $details,
        ];
        
    }
?>
    <style>
        #wpbody-content .notice {
            display: none !important;
        }
        pre {
            font-family: sans-serif !important;
        }
    </style>
    <!-- Display the JSON data -->
    <h2><?php esc_html_e('Customer Details', 'zoa'); ?></h2>
    <?php if ( !empty( $membership_data ) ) : ?>
        <label for="membership-filter"><?php esc_html_e('Filter by Membership Level: ', 'zoa'); ?></label>
        <select id="membership-filter">
            <option value="all"><?php esc_html_e('All', 'zoa'); ?></option>
            <?php foreach (array_keys($membership_data) as $level) : ?>
                <option value="<?php echo strtolower($level); ?>"><?php echo $level; ?></option>
            <?php endforeach; ?>
        </select>
        <div id="membership-data">
            <pre><?php echo json_encode($membership_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_IGNORE|JSON_UNESCAPED_UNICODE); ?></pre>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const filter = document.getElementById('membership-filter');
                const dataContainer = document.getElementById('membership-data');
                const rawData = <?php echo json_encode($membership_data, JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_IGNORE|JSON_UNESCAPED_UNICODE); ?>;

                filter.addEventListener('change', function () {
                    const selectedLevel = this.value;
                    let filteredData = {};

                    if (selectedLevel === 'all') {
                        filteredData = rawData;
                    } else {
                        let selectedLevelKey = selectedLevel.charAt(0).toUpperCase() + selectedLevel.slice(1);
                        filteredData[selectedLevelKey] = rawData[selectedLevelKey];
                    }

                    dataContainer.innerHTML = '<pre>' + JSON.stringify(filteredData, null, 4) + '</pre>';
                });
            });
        </script>
    <?php else : ?>
        <p><?php esc_html_e('No customer data found.', 'zoa'); ?></p>
    <?php endif; ?>
    
<?php
}

function zoa_get_all_user_orders($user_id, $starting_date = null, $ending_date = null, $filter_by_product_id = null) {
    global $wccm_order_model;
    if ( is_object($wccm_order_model) ) {
        return $wccm_order_model->get_all_user_orders($user_id, $starting_date, $ending_date, $filter_by_product_id);
    }
    return '';
}
// End Member ship details page JSON view

// If Forcefully updated the user rank then save the last rank update
add_action( 'admin_init', 'woocommerce_customers_manager_save_rank', 10 );
function woocommerce_customers_manager_save_rank() {
    if (isset($_POST['save_force_rank'])) {
        $customer_id = $_REQUEST['customer_id'];
        $new_rank_f = esc_html($_REQUEST['force_rank']);
        $old_rank = get_user_meta($customer_id, 'rank', true);

        if ( $old_rank != $new_rank_f ) {
            update_user_meta($customer_id, 'last_rank_updated', $old_rank);
        }
    }
}