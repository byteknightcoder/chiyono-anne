<?php

/**
 * Woo Custom Product Shortcode
*/
add_shortcode('product_data', 'shortcode_callback_to_get_productData_by_sku');
function shortcode_callback_to_get_productData_by_sku($atts) {
    if (!isset($atts['id'])) {
        return;
    }
    $skus = $atts['id'];
    if (empty($skus)) {
        return;
    }
    $skus = explode(',', $skus);
    $pids = array();
    foreach ($skus as $key => $sku) {
        global $wpdb;

        $product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku));
        if ($product_id) {
            array_push($pids, $product_id);
        }
    }
    $class = '';
    $data = '';
    if (isset($atts['class']) && !empty($atts['class'])) {
        $class = $atts['class'];
    }
    if (!empty($class)) {
        $data .= '<div class="' . $class . '">';
    }
    foreach ($pids as $key => $pid) {
        $title = get_the_title_product_chiyono($pid);
        $link = get_the_permalink($pid);
        $image = get_the_post_thumbnail($pid);
        $data .= '<div class="product_item"><div class="pi_inner"><div class="pi_content"><a href="' . $link . '">' .
            $image . '<div class="pi_info">' . $title . '</div></a></div></div></div>';
    }
    if (!empty($class)) {
        $data .= '</div>';
    }
    return $data;
}

// call child theme image path
add_shortcode('img', 'ca_img_shortcode');
function ca_img_shortcode() {
    return get_stylesheet_directory_uri() . '/images/';
}

// wrap any class name div
add_shortcode('dvw', 'divwrap_func');
function divwrap_func($atts, $content = null) {
    extract(shortcode_atts(array(
        'class' => 'no-class',
    ), $atts));
    return '<div class="' . esc_attr($class) . '">' . $content . '</div>';
}

// desc
add_shortcode('p_desc', 'div00_func');
function div00_func($atts, $content = null)
{
    return '<div class="p_desc">' . $content . '</div>';
}

// wrap for slicks
// image
add_shortcode('col_thum', 'div01_func');
function div01_func($atts, $content = null) {
    return '<div class="col_thum">' . $content . '</div>';
}

// desc
add_shortcode('col_desc', 'div02_func');
function div02_func($atts, $content = null) {
    extract(shortcode_atts(array(
        'class' => 'align_left',
    ), $atts));
    return '<div class="col_desc"><p class="' . esc_attr($class) . '">' . $content . '</p></div>';
}

// Function to add text shortcode to posts and pages
// auto email text shortcode
add_shortcode('order-process-text', 'email_process_shortcode');
function email_process_shortcode() {
    return '<p style="text-align: center;">' . __('Your order has been received and is now being processed.', 'zoa') . '<p style="text-align: center;">' . __("We'll drop another email when your order ships.", "zoa") . '</p><p style="text-align: center;">' . __('Your order details are shown below for your reference:', 'zoa') . '</p>';
}

// Order Onhold Translated Text
add_shortcode('order-onhold-text', 'email_onhold_shortcode');
function email_onhold_shortcode() {
    return '<p style="text-align: center;">' . __('Your order has been received and is now on hold.', 'zoa') . '<p style="text-align: center;">' . __("We'll drop another email after you purchase for this order.", "zoa") . '</p><p style="text-align: center;">' . __('Your order details are shown below for your reference:', 'zoa') . '</p>';
}

// user login shortcode
add_shortcode('loggedin', 'check_user');
function check_user($params, $content = null) {
    if (!is_user_logged_in() && strpos($_SERVER['REQUEST_URI'], 'my-account') === false) {
        return $content;
    } else {
        return;
    }
}

// Get site url for links 
add_shortcode('homeurl', 'home_url_shortcode');
function home_url_shortcode() {
    return get_bloginfo('url');
}

// woo shop page link shortcode
add_shortcode('shoplink', 'shoplink_shortcode');
function shoplink_shortcode($atts, $content = null) {
    return '<a href="' . home_url('shop-all') . '" class="link_underline upper view_all">' . $content . '</a>';
}

// cancel policy text link shortcode
add_shortcode('linkCancelPolicy', 'CancelPolicylink_shortcode');
function CancelPolicylink_shortcode($atts, $content = null) {
    return '<a href="' . home_url('returns-exchanges') . '" class="underline">' . __('Cancel Policy', 'zoa') . '</a>';
}

// Appointment Section Shortcode
add_shortcode('section-appointment', 'appointment_template_shortcode');
function appointment_template_shortcode() {
    ob_start();
    get_template_part('./template-parts/section-appointment');
    return ob_get_clean();
}

// Contact Section Shortcode
add_shortcode('section-contact', 'contact_template_shortcode');
function contact_template_shortcode() {
    ob_start();
    get_template_part('./template-parts/section-contact');

    return ob_get_clean();
}

/* Shortcode for custom post */
add_shortcode('custom_posts', 'tcb_sc_custom_posts');
function tcb_sc_custom_posts($atts) {
    global $post;

    // Initialize the $return variable
    $return = '';

    $default = array(
        'type' => 'post',
        'post_type' => '',
        'limit' => 10,
        'status' => 'publish'
    );
    $r = shortcode_atts($default, $atts);
    extract($r);

    if (empty($post_type)) {
        $post_type = $type;
    }

    $post_type_ob = get_post_type_object($post_type);
    if (!$post_type_ob) {
        return '<div class="warning"><p>No such post type <em>' . $post_type . '</em> found.</p></div>';
    }

    $args = array(
        'post_type' => $post_type,
        'numberposts' => $limit,
        'post_status' => $status,
        'orderby' => 'rand'
    );

    $posts = get_posts($args);
    if (count($posts)) :
        if ('portfolio' == $post_type) {
            $return .= '<div class="portfolio-grids grid row">';
            foreach ($posts as $post) : setup_postdata($post);
                $images_series = get_field('images_series', $post->ID);
                $has_image_series = !empty($images_series) && !empty($images_series[0]['images']);
                $return .= '<div class="grid-item all-port col-lg-3 col-md-4 col-xs-6"><div class="grid-outer"><a href="' . get_permalink($post->ID) . '" class="pf_link" data-id="' . $post->ID . '" ' . ($has_image_series ? 'data-serie_index="1"' : '') . '>';
                $return .= '<div class="grid-content"><div class="grid-inner">';
                $return .= '<div class="pf_item">';
                //$return .= '<img src="'.get_stylesheet_directory_uri().'/images/pf_sample_thum.jpg" alt="sample" />';
                if ($has_image_series) :
                    $return .= '<img class="portfolio_img" src="' . $images_series[0]['images'][0]['sizes']['portfolio'] . '" alt="' . get_the_title() . '" />';
                elseif (has_post_thumbnail()) :
                    $return .= get_the_post_thumbnail($post->ID, 'portfolio');
                else :
                    $return .= '<img class="portfolio_img" src="' . get_stylesheet_directory_uri() . '/images/pf_sample_thum.jpg" alt="sample" />';
                endif;
                $return .= '</div>';
                $return .= '<div class="pf_caption"><h2 class="pf_title">' . get_the_title() . '</h2><p class="see_more"><span>See details</span></p></div>';
                $return .= '</div></div>';
                $return .= '</a></div></div>';
            endforeach;
            wp_reset_postdata();
            $return .= '</div>';
        } else {
            $return .= '<ul class="grid_post">';
            foreach ($posts as $post) : setup_postdata($post);
                $return .= '<li><a href="' . get_permalink($post->ID) . '">' . get_the_title() . '</a></li>';
            endforeach;
            wp_reset_postdata();
            $return .= '</ul>';
        }

    else :
        $return .= '<p>No posts found.</p>';
    endif;

    return $return;
}


/* * *
 * Appointment Cancel Email Text shortcode
 */

add_shortcode('booking_cancel_text', 'booking_cancel_text');
function booking_cancel_text() {
    $html = __('cancel_appointment_email_text', 'zoa');
    return $html;
}

/* * *
 * Store Phone no shortcode
 */
add_shortcode('store_phone', 'store_phone_callback');
function store_phone_callback() {
    return class_exists('WC_Admin_Settings') ? WC_Admin_Settings::get_option('woocommerce_store_phone') : get_option('woocommerce_store_phone');
}

// Terms & Conditions Link shortcode
add_shortcode('term_conditions', 'zoa_term_shortcode');
function zoa_term_shortcode() {
    $term_page_id = PAGE_TERM_ID;
    $page = get_post($term_page_id);
    return '<a target="_blank" href="' . get_permalink($page) . '">' . $page->post_title . '</a>';
}

// Privacy Policy Link
add_shortcode('privacy_policy', 'zoa_privacy_shortcode');
function zoa_privacy_shortcode() {
    $term_page_id = PAGE_PRIVACY_ID;
    $page = get_post($term_page_id);
    return '<a target="_blank" href="' . get_permalink($page) . '">' . $page->post_title . '</a>';
}

// Cancel Policy Link
add_shortcode('cancel_policy', 'zoa_cancelpolicy_shortcode'); //added Apr14 2020
function zoa_cancelpolicy_shortcode()
{
    $term_page_id = PAGE_CANCEL_ID;
    $page = get_post($term_page_id);
    return '<a target="_blank" href="' . get_permalink($page) . '">' . __('キャンセルポリシー', 'zoa') . '</a>';
}

// Store Address shortcode
add_shortcode('store_address', 'store_address_callback');
function store_address_callback() {
    $address = '';
    if ( class_exists('WC_Admin_Settings') ) {
        global $woocommerce;
        $CountryObj = new WC_Countries();
        $countries_array = $CountryObj->get_countries();
        $countryCode = $CountryObj->get_base_country();
        $stateCode = $CountryObj->get_base_state();
        $country_states_array = $CountryObj->get_states();
        $state = $country_states_array[$countryCode][$stateCode];
        $country = $countries_array[$countryCode];
        if (get_locale() == 'ja') {
            $address .= '〒' . WC_Admin_Settings::get_option('woocommerce_store_postcode') . ' <br>
                        ' . $state . WC_Admin_Settings::get_option('woocommerce_store_city') . WC_Admin_Settings::get_option('woocommerce_store_address') . WC_Admin_Settings::get_option('woocommerce_store_address_2') . '';
        } else {
            $address .= WC_Admin_Settings::get_option('woocommerce_store_address_2') . ' <br>
                        ' . WC_Admin_Settings::get_option('woocommerce_store_address') . WC_Admin_Settings::get_option('woocommerce_store_city') . $state . $country . '<br> ' . WC_Admin_Settings::get_option('woocommerce_store_postcode') . '';
        }
    }

    return $address;
}

// Logged in user name
add_shortcode('loggedin_full_name', 'loggedin_full_name_callback');
function loggedin_full_name_callback() {
    $current_user = wp_get_current_user();
    if ($current_user) {
        return $current_user->user_firstname . ' ' . $current_user->user_lastname;
    } else {
        return '';
    }
}

add_shortcode('shortcode_add_to_cart_gifbox', 'shortcode_add_to_cart_gifbox');
function shortcode_add_to_cart_gifbox() {
    $product_id_parent = get_gift_box_product_id();
    if ($product_id_parent > 0) {
        $product_parent = wc_get_product($product_id_parent);
        if (is_object($product_parent)) {
            $vari = $product_parent->get_children();
            ob_start();
            $thumb_id = get_post_thumbnail_id($product_id_parent);
?>
            <div class="gifbox_product">
                <p class="gtw-add-order-gift-wrapper-content">
                    <button type="button" class="button gtw-popup-order-gift-wrapper ch_gtw_show_modal_option">
                        この注文にギフトボックスをつける
                    </button>
                </p>
                <div class="remodal remodal_hbody" data-remodal-id="gift_wrapper" id="gift_wrapper" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
                    <button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
                    <div class="remodal_wraper">

                        <div class="modal_body">
                            <?php
                                if (is_checkout()) {
                                    $url = wc_get_checkout_url();
                                } else {
                                    $url = wc_get_cart_url();
                                }
                                if (!empty($vari)) :
                            ?>
                                <div class="giftbox_pop">
                                    <div class="giftbox_pop_img img">
                                        <img src="<?php echo wp_get_attachment_image_src($thumb_id, 'large')[0]; ?>" class="img-responsive" />
                                    </div>
                                    <div class="giftbox_pop_content">
                                        <div class="giftbox_pop_title">
                                            <?php echo $product_parent->get_title(); ?>
                                        </div>
                                        <div class="giftbox_pop_price price">
                                            From: <?php echo wc_price(wc_get_price_including_tax($product_parent)); ?>
                                        </div>
                                        <div class="giftbox_pop_select">
                                            <select class="gift_wrap_options" name="add-to-cart">
                                                <?php foreach ($vari as $value) :
                                                    $obj = wc_get_product($value);
                                                ?>
                                                    <option value="<?php echo $url . '?add-to-cart=' . $obj->get_id(); ?>"><?php echo $obj->get_attributes()['box-size'] . ' (' . $obj->get_description() . ')'; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="giftbox_pop_cart">
                                            <button type="button" class="button ch_add_gift_wrap_to_cart">
                                                <?php esc_html_e('Add to cart', 'zoa'); ?>
                                            </button>
                                        </div>
                                    </div><!-- giftbox_pop_content -->
                                </div><!-- giftbox_pop -->
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

add_shortcode('coupon_code_free_shipping', 'coupon_code_free_shipping');
function coupon_code_free_shipping($atts) {
    // Here below define your coupons discount ammount
    $entry_id = $atts['entry_id'];
    if (isset($_SESSION['entry_id_free_shipping' . $entry_id]) && !empty($_SESSION['entry_id_free_shipping' . $entry_id])) {
        $coupon_code = $_SESSION['entry_id_free_shipping' . $entry_id];
    } else {
        $value = 0;
        $discount_amounts = array($value);

        // Set some coupon data by default
        $date_expires     = date_i18n('Y-m-d', strtotime('+90 days'));
        $discount_type    = 'percent';

        // Loop through the defined array of coupon discount amounts
        foreach ($discount_amounts as $coupon_amount) {
            // Get an emty instance of the WC_Coupon Object
            $coupon = new WC_Coupon();

            // Generate a non existing coupon code name
            $coupon_code  = generate_coupon_code();

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
        }
        $_SESSION['entry_id_free_shipping' . $entry_id] = $coupon_code;
    }
    return strtoupper($coupon_code);
}

// Use this shortcode in Email Builder: [review_link order_id='[order_number]']
add_shortcode('review_link', 'review_link_shortcode');
function review_link_shortcode($atts, $content = null) {
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    $special_service_category_slug = get_option('ch_special_service', ''); //'special-service';
    $exist = false;
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (is_object($product)) {
            if (is_product_in_cat($product->get_id(), $special_service_category_slug)) {
                $exist = true;
                break;
            }
        }
    }
    if ($exist === true) { //for TBYB
        // don't show this Review link for TBYB order
        return '';
    } else {
        return '<a style="color: #fff;font-style:normal;text-decoration: none;font-weight: bold;border: 1px solid #000;background: #000;line-height: 1;padding: 8px 13px;display: inline-block;letter-spacing: .1em;" href="' . home_url('review') . '" rel="noopener" target="_blank">' . __('Review', 'zoa') . '</a>';
    }
}

// use this shortcode: [zoomappurl]
add_shortcode('zoomappurl', 'zoomappurl');
function zoomappurl() {
    if (wp_is_mobile()) {
        if (preg_match('/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'])) { //ios
            return 'https://apps.apple.com/jp/app/zoom-cloud-meetings/id546505307';
        } else { //android
            return 'https://play.google.com/store/apps/details?id=us.zoom.videomeetings&hl=ja&gl=US';
        }
    } else { //PC
        return 'https://zoom.us/jp-jp/meetings.html';
    }
}

// use this shortcode: [tracking_url_shortcode order_id='[order_number]']  in 'Completed' email in email builder for other order (nomail order) is not try fit order
add_shortcode('tracking_url_shortcode', 'tracking_url_shortcode');
function tracking_url_shortcode($atts, $content = null) {
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    $exist = false;
    $special_service_category_slug = get_option('ch_special_service', ''); //'special-service';
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (is_product_in_cat($product->get_id(), $special_service_category_slug)) {
            $exist = true;
            break;
        }
    }
    $contents = '';
    if ($exist == true) { //for try fit your size order
        //don't show in completed email because it show in 'sent-sample' email
    } else {
        $contents = get_tracking_url($order_id);
    }
    return $contents;
}

//use in [ch_po_url order_id='[order_number]']  builder email
add_shortcode('ch_po_url', 'pay_for_this_order_shortcode');
function pay_for_this_order_shortcode($atts, $content = null) {
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    $_payment_method = trim(get_post_meta($order_id, '_payment_method', true));
    if (($order->status == 'pending' || $order->status == 'on-hold') && $_payment_method == 'stripe') {
        return '<a href="' . esc_url($order->get_checkout_payment_url()) . '">' . __('Pay for this order', 'zoa') . '</a>';
    } else {
        if (($order->status == 'pending' || $order->status == 'on-hold') && $_payment_method == 'bacs') {
            return ch_get_bacs_account_details_html();
        } else {
            return "";
        }
    }
}

add_shortcode('chiy_fix_emptyemail', 'chiy_fix_emptyemail');
function chiy_fix_emptyemail() {
    return "&nbsp;";
}

// first notion for zoom
add_shortcode('zoom_tips', 'zoomtips_func');
function zoomtips_func() {
    $tn_lists = array('オンラインコンサルテーションは、<a href="https://zoom.us/jp-jp/meetings.html" class="underline" target="_blank">「ZOOM」</a>(ビデオ通話ツール)を利用します', 'スマートフォン・タブレットをご利用の場合は事前にZOOMアプリのダウンロードをお願いします', 'スマートフォン・タブレット・PCのいずれもお持ちでない場合はオンラインコンサルテーションをご利用いただけませんのでご了承ください', 'フィットサンプルブラをご着用いただくため、ローブや前開きトップスなど、脱ぎやすい格好をお勧めします', 'ネット環境のよい場所で実施をお願いします');
    $html = '<ul class="tip_notion">';
    foreach ($tn_lists as $value) {
        $html .= '<li>' . $value . '</li>';
    }
    $html .= '</ul><p class="p_small">オンラインコンサルテーションに関する詳細は<a href="' . home_url('guide-tbyb') . '" class="underline fw_700">こちらをご参照</a>ください。</p><p class="p_small">「ZOOM」(ビデオ通話ツール)を初めてご利用される方は、<a href="' . home_url('guide-tbyb') . '" class="underline fw_700">こちらのインストラクションをご参照</a>ください。</p>';
    return $html;
}

add_shortcode('notice_tbyb_kome01_shortcode', 'notice_tbyb_kome01');
function notice_tbyb_kome01() {
    $page_id = get_tbty_page_id();
    $notice_tbyb_common_acf = get_field('notice_tbyb_kome01_acf', $page_id);
    if (isset($notice_tbyb_common_acf['ul_notion_acf'])) {
        $row = $notice_tbyb_common_acf['ul_notion_acf'];
        ob_start();
        echo '<ul class="ul_notion">';
        foreach ($row as $value) {
            if (!empty($value['li'])) {
            ?>
                <li><?php echo do_shortcode($value['li']); ?></li>
            <?php
            }
        }
        echo '</ul>';
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    } else {
        return '';
    }
}

add_shortcode('notice_tbyb_more_shortcode', 'notice_tbyb_more');
function notice_tbyb_more() {
    return '詳しい利用規約は<a href="' . home_url('term-of-use-try-fit-your-size') . '" class="underline">こちら</a>よりご確認ください';
}

add_shortcode('notice_tbyb_common_p_ttl_shortcode', 'notice_tbyb_common_p_ttl');
function notice_tbyb_common_p_ttl() {
    $page_id = get_tbty_page_id();
    $notice_tbyb_common_acf = get_field('notice_tbyb_common_acf', $page_id);
    if (isset($notice_tbyb_common_acf['p_ttl_acf']) && !empty($notice_tbyb_common_acf['p_ttl_acf'])) {
        return $notice_tbyb_common_acf['p_ttl_acf'];
    }
    return '';
}

add_shortcode('notice_tbyb_common_shortcode', 'notice_tbyb_common');
function notice_tbyb_common() {
    $page_id = get_tbty_page_id();
    $notice_tbyb_common_acf = get_field('notice_tbyb_common_acf', $page_id);
    if (isset($notice_tbyb_common_acf['ul_notion_acf'])) {
        $row = $notice_tbyb_common_acf['ul_notion_acf'];
        ob_start();
        echo '<ul class="ul_notion">';
        foreach ($row as $value) {
            if (!empty($value['li'])) {
            ?>
                <li><?php echo do_shortcode($value['li']); ?></li>
            <?php
            }
        }
        echo '</ul>';
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    } else {
        return '';
    }
}

add_shortcode('notice_tbyb_zoom_p_ttl_shortcode', 'notice_tbyb_zoom_p_ttl');
function notice_tbyb_zoom_p_ttl() {
    $page_id = get_tbty_page_id();
    $notice_tbyb_common_acf = get_field('notice_tbyb_zoom_acf', $page_id);
    if (isset($notice_tbyb_common_acf['p_ttl_acf']) && !empty($notice_tbyb_common_acf['p_ttl_acf'])) {
        return $notice_tbyb_common_acf['p_ttl_acf'];
    } else {
        return '';
    }
}

add_shortcode('notice_tbyb_zoom_shortcode', 'notice_tbyb_zoom');
function notice_tbyb_zoom() {
    $page_id = get_tbty_page_id();
    $notice_tbyb_common_acf = get_field('notice_tbyb_zoom_acf', $page_id);
    if (isset($notice_tbyb_common_acf['ul_notion_acf'])) {
        $row = $notice_tbyb_common_acf['ul_notion_acf'];
        ob_start();
        echo '<ul class="ul_notion">';
        foreach ($row as $value) {
            if (!empty($value['li'])) {
            ?>
                <li><?php echo do_shortcode($value['li']); ?></li>
            <?php
            }
        }
        echo '</ul>';
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    } else {
        return '';
    }
}

add_shortcode('notice_tbyb_refund_p_ttl_shortcode', 'notice_tbyb_refund_p_ttl');
function notice_tbyb_refund_p_ttl() {
    $page_id = get_tbty_page_id();
    $notice_tbyb_common_acf = get_field('notice_tbyb_refund_acf', $page_id);
    if (isset($notice_tbyb_common_acf['p_ttl_acf']) && !empty($notice_tbyb_common_acf['p_ttl_acf'])) {
        return $notice_tbyb_common_acf['p_ttl_acf'];
    } else {
        return '';
    }
}

add_shortcode('notice_tbyb_refund_shortcode', 'notice_tbyb_refund');
function notice_tbyb_refund() {
    $page_id = get_tbty_page_id();
    $notice_tbyb_common_acf = get_field('notice_tbyb_refund_acf', $page_id);
    if (isset($notice_tbyb_common_acf['ul_notion_acf'])) {
        $row = $notice_tbyb_common_acf['ul_notion_acf'];
        ob_start();
        echo '<ul class="ul_notion">';
        foreach ($row as $value) :
            if (!empty($value['li'])) :
            ?>
                <li><?php echo do_shortcode($value['li']); ?></li>
            <?php
            endif;
        endforeach;
        echo '</ul>';
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    } else {
        return '';
    }
}

add_shortcode('notice_tbyb_kome01_p_ttl_shortcode', 'notice_tbyb_kome01_p_ttl');
function notice_tbyb_kome01_p_ttl() {
    $page_id = get_tbty_page_id();
    $notice_tbyb_common_acf = get_field('notice_tbyb_kome01_acf', $page_id);
    if (isset($notice_tbyb_common_acf['p_ttl_acf']) && !empty($notice_tbyb_common_acf['p_ttl_acf'])) {
        return $notice_tbyb_common_acf['p_ttl_acf'];
    } else {
        return '';
    }
}


add_shortcode('menuProduct', 'menuProduct_shortcode');
function menuProduct_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'id' => null,
    ), $atts, 'menuProduct');
    $product = wc_get_product($atts['id']);
    if (is_object($product)) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($atts['id']), 'single-post-thumbnail');
        $html = '<a href="' . get_permalink($atts['id']) . '">
                    <div class="card" style="width: 18rem;">
                        <img class="card-img-top" src="' . $image[0] . '" alt="Card image cap">
                        <div class="card-body">
                            <h3>' . $product->get_title() . '</h3>
                        </div>
                    </div>
                </a>';
        return $html;
    }
}

add_shortcode('reservation-confirm', 'zoa_shortcode_reservation_confirm');
function zoa_shortcode_reservation_confirm($atts) {
    unset($_SESSION['appointment_id']);
    unset($_SESSION['appointment1on1_id']);
    unset($_SESSION['client_id']);

    if (!$_SESSION['appointment_id']) {
        return '';
    }

    ob_start();
    ?>
    <div id="reservationFormConfirm" class="form_entry">
        <div class="confirm-box">
            <?php if ( function_exists('get_booking_confirm_html')) : ?>
                <?php get_booking_confirm_html(); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

add_shortcode('get_store_map', 'zoa_get_store_map');
function zoa_get_store_map($atts = array()) {
    return get_google_map_url_by_address();
}

function get_google_map_url_by_address() {
    $countries_obj = new WC_Countries();
    $country_state = get_option('woocommerce_default_country');
    $aCountry_state = explode(':', $country_state);
    $country_code = $aCountry_state[0];
    $country_name = WC()->countries->countries[$country_code];
    $states_list = $countries_obj->get_states($country_code);
    $state = $states_list[$aCountry_state[1]];
    $city = get_option('woocommerce_store_city');
    $postcode = get_option('woocommerce_store_postcode');
    $address1 = get_option('woocommerce_store_address');
    $address2 = get_option('woocommerce_store_address_2');

    $full_address = $country_name . '+' . $postcode . '+' . $state . '+' . $city . '+ ' . $address1 . $address2;
    $google_map_url = 'https://www.google.co.jp/maps/place/?hl=ja&q=' . $full_address;
    return $google_map_url;
}

/////// hold-on for conv
add_shortcode('message_conv_onhold', 'message_conv_onhold');
function message_conv_onhold($atts, $content = null) {
    $order_id = $atts['order_id'];
    $order = wc_get_order($order_id);
    if ($order->has_status('on-hold') && $order->get_payment_method() == 'paydesign_cs') {
        $paydesign = get_option('woocommerce_paydesign_cs_settings');
        if (isset($paydesign['payment_deadline'])) {
            $payment_deadline = $paydesign['payment_deadline'];
        } else {
            $payment_deadline = 0;
        }
        return '<p style="text-align: center;">お客様のご注文を受けつけました。<br>
    現在ご注文は「保留中」となります。</p>
	<p style="text-align: center;">本日から<strong>' . $payment_deadline . '日以内</strong>にご指定のコンビニにて<br>お支払いをお願い致します。<br>お支払い確認後、ご注文は「' . __('Processing', 'woocommerce') . '」となり、<br>別途自動メールが配信されます。</p>
    <p><small>※お支払い期限を過ぎた場合は自動キャンセルとなりますので、ご了承ください。</small><br><small>※お支払い期限を過ぎた場合は、コンビニにてお支払いが不可となります。</small><br><small>※一度キャンセルになりましたご注文で、注文をご希望される方は再度サイトにてご注文ください。</small></p>';
    } else {
        return '';
    }
}

/////// processing for conv
add_shortcode('message_conv_processing', 'message_conv_processing');
function message_conv_processing($atts, $content = null) {
    $order_id = $atts['order_id'];
    $order = function_exists('wc_get_order') ? wc_get_order($order_id) : null;
    if ($order && $order->has_status('processing') && $order->get_payment_method() == 'paydesign_cs') {
        return '<p style="text-align: center;">お支払いを確認いたしましたのでご連絡致します。<br>現在ご注文は「' . __('Processing', 'woocommerce') . '」となります。</p>
        <p style="text-align: center;">配送時に別途自動メールが配信されますので、<br>お届けまで今しばらくお待ちください。</p>';
    } else {
        return '';
    }
}

/////// processing for CC
add_shortcode('message_cc_processing', 'message_cc_processing');
function message_cc_processing($atts, $content = null) {
    $order_id = $atts['order_id'];
    $order = function_exists('wc_get_order') ? wc_get_order($order_id) : null;
    
    if ( $order && ($order->has_status('processing') || $order->has_status('prepare-to-send')) && strpos($order->get_payment_method(), 'stripe') !== false) {
        $special_service_category_slug = get_option('ch_special_service', ''); //'special-service';
        $exist = false;
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if (is_product_in_cat($product->get_id(), $special_service_category_slug)) {
                $exist = true;
                break;
            }
        }
        if ($exist) { //for try fit your size order
            $html = '<p style="text-align: center;">"Try Before You Buy"サービスをお求めいただきありがとうございます。</p>';
            $online_consultation = get_post_meta($order_id, '_ch_online_consultation', true);
            $passcode = get_post_meta($order_id, '_ch_passcode', true);
            //For booking link
            if (isset($online_consultation) && $online_consultation == 'yes') {
                if (!empty($passcode)) {
                    $html .= '<p style="text-align: center;">まずはオンラインコンサルテーションのご予約をお取りください。<br>ご予約確定後に、T.B.Y.Bキットを発送いたします。</p><br><br><p style="text-align: center;"><a target="_blank" style="display:inline-block;line-height:1;padding:4px 12px;font-weight:bold;background: #000;color: white;" href="' . home_url('reservation-form') . '?passcode=' . $passcode . '&order_id=' . base64_encode($order_id) . '">' . __('BOOKING NOW!', 'zoa') . '</a></p>';
                }
            } else {
                $html = '<p style="text-align: center;">現在ご注文は「' . __('Processing', 'woocommerce') . '」となり、<br>本日から3営業日以内に<br>T.B.Y.Bキットを発送いたします。</p>
                <p style="text-align: center;">配送時に別途自動メールが配信されますので、<br>お届けまで今しばらくお待ちください。</p>';
            }
            //For i returned to admin link
            if (!empty($passcode)) { //comment temp
                //$html.='<p style="text-align: center;"><a target="_blank" style="background: blue;color: white;" href="'.admin_url('admin-ajax.php?action=try_fit_customer_notify_to_admin') . '&type=email&passcode=' . $passcode . '&id=' . base64_encode($order_id).'">'. __('I returned', 'zoa').'</a></p>';
            }
            //Notion for both
            $html .= '<p style="font-size: 14px; font-weight: 600; line-height: 1.5; word-break: break-word; text-align: center; mso-line-height-alt: 21px; margin: 0;letter-spacing: 1px;"><b>&diams; ' . notice_tbyb_common_p_ttl() . ' &diams;</b></p>';
            $html .= notice_tbyb_common();
            $html .= '<br><br>';
            if (isset($online_consultation) && $online_consultation == 'yes') {
                $html .= '<p style="font-size: 14px; font-weight: 600; line-height: 1.5; word-break: break-word; text-align: center; mso-line-height-alt: 21px; margin: 0;letter-spacing: 1px;"><b>&diams; ' . notice_tbyb_zoom_p_ttl() . ' &diams;</b></p>';
                $html .= '';
                $html .= notice_tbyb_zoom();
                $html .= '<br><br>';
            }

            $html .= '<p style="font-size: 14px; font-weight: 600; line-height: 1.5; word-break: break-word; text-align: center; mso-line-height-alt: 21px; margin: 0;letter-spacing: 1px;"><b>' . notice_tbyb_refund_p_ttl() . '</b></p>';

            $html .= notice_tbyb_refund();

            $html .= '<p style="font-size: 14px; font-weight: 600; line-height: 1.5; word-break: break-word; text-align: center; mso-line-height-alt: 21px; margin: 0;letter-spacing: 1px;"><b>' . notice_tbyb_kome01_p_ttl() . '</b></p>';
            $html .= notice_tbyb_kome01();
            $html .= '<br><p style="text-align: center;">' . notice_tbyb_more() . '</p>';
        } else { //for normal order
            $html = '<p style="text-align: center;">お客様のご注文を受けつけました。<br>現在ご注文は「' . __('Processing', 'woocommerce') . '」となります。</p>
            <p style="text-align: center;">配送時に別途自動メールが配信されますので、<br>お届けまで今しばらくお待ちください。</p>';
        }
        return $html;
    } else {
        return '';
    }
}

/////// hold-on for bacs
add_shortcode('message_bacs_onhold', 'message_bacs_onhold');
function message_bacs_onhold($atts, $content = null) {
    $order_id = $atts['order_id'];
    $order = function_exists('wc_get_order') ? wc_get_order($order_id) : null;
    if ($order && $order->has_status('on-hold') && $order->get_payment_method() == 'bacs') {
        return '<p style="text-align: center;">お客様のご注文を受けつけました。<br>現在ご注文は「保留中」となります。<br>以下の振込先までご入金をお願い致します。</p><p style="text-align: center;">ご入金確認後、注文は「' . __('Processing', 'woocommerce') . '」となり、<br>別途自動メールが配信されます。</p>
        <p style="text-align: center;"><small>※お支払い期限を過ぎた場合は自動キャンセルとなりますので、ご了承ください。</small><br><small>※ご入金確認は1~3営業日かかります。</small></p>';
    } else {
        return '';
    }
}

/////// old processing for all
add_shortcode('message_bacs_processing', 'message_bacs_processing');
function message_bacs_processing($atts, $content = null) {
    $order_id = $atts['order_id'];
    $order = function_exists('wc_get_order') ? wc_get_order($order_id) : null;
    if ($order && $order->has_status('processing') && $order->get_payment_method() == 'bacs') {
        return '<p style="text-align: center;">お振り込みを確認いたしましたのでご連絡致します。<br>現在ご注文は「' . __('Processing', 'woocommerce') . '」となります。</p>
        <p style="text-align: center;">配送時に別途自動メールが配信されますので、<br>お届けまで今しばらくお待ちください。</p>';
    } else {
        return '';
    }
}

add_shortcode('fv_img', 'fv_img');
function fv_img() {
    $upload_dir_link = wp_upload_dir()['baseurl'];
    return '<img src="' . $upload_dir_link . '/2018/11/email_logo_small.png" class="fv_img" alt="10%OFF COUPON">';
}

add_shortcode('coupon_code', 'coupon_code');
function coupon_code($atts) {
    // Here below define your coupons discount ammount
    $entry_id = $atts['entry_id'];
    if (isset($_SESSION['entry_id_' . $entry_id]) && !empty($_SESSION['entry_id_' . $entry_id])) {
        $coupon_code = $_SESSION['entry_id_' . $entry_id];
    } else {
        $discount_amounts = array(10);

        // Set some coupon data by default
        $date_expires     = date_i18n('Y-m-d', strtotime('+31 days'));
        $discount_type    = 'percent';

        // Loop through the defined array of coupon discount amounts
        foreach ($discount_amounts as $coupon_amount) {
            // Get an emty instance of the WC_Coupon Object
            $coupon = new WC_Coupon();

            // Generate a non existing coupon code name
            $coupon_code  = generate_coupon_code();

            // Set the necessary coupon data (since WC 3+)
            $coupon->set_code($coupon_code);
            $coupon->set_discount_type($discount_type);
            $coupon->set_amount($coupon_amount);

            $coupon->set_date_expires($date_expires);
            $coupon->set_usage_limit(1);
            $coupon->set_usage_limit_per_user(1);
            $coupon->set_individual_use(false);

            // Create, publish and save coupon (data)
            $coupon->save();
        }
        $_SESSION['entry_id_' . $entry_id] = $coupon_code;
    }
    return strtoupper($coupon_code);
}

add_shortcode('reservation_page', 'reservation_page_shortcode');
function reservation_page_shortcode() {
    return '<a href="' . home_url('reservation-form') . '" class="btn btn_rvnow">' . __('Reservation', 'zoa') . '</a>';
}

add_shortcode('show_latest_blog_posts_shortcode', 'show_latest_blog_posts_shortcode');
function show_latest_blog_posts_shortcode($atts) {
    $limit = 3;
    if (isset($atts['limit'])) {
        $limit = $atts['limit'];
    }
    $content = show_latest_blog_posts($limit);
    return '<div class="list_format">' . $content . '</div>';
}

add_shortcode('mr_define', 'get_mr_define');
function get_mr_define($atts) {
    ob_start();
    get_template_part('./template-parts/mr-define');
    return ob_get_clean();
}

add_shortcode('noveltyCard', 'noveltyCard');
function noveltyCard($atts, $content = null) {
    $upload_dir_link = wp_upload_dir()['baseurl'];
    $current_user = wp_get_current_user();
    $rank_and_amount = mr_get_member_rank($current_user->ID);
    $rank = $rank_and_amount['rank'];
    $default = array(
        'title' => 'some title',
        'class' => 'card__radius mgt_01',
        'img' => $upload_dir_link . '/2022/10/CP22-DEC-FGT-01.jpg',
        'alt' => 'Free Gift!',
        'rank' => ''
    );
    $arr = shortcode_atts($default, $atts);
    if (!isset($atts['rank']) || ($rank == $atts['rank'] && is_user_logged_in()) || (!is_user_logged_in() && $atts['rank'] == 'guest')) {
        if (isset($atts['img']) && !empty($atts['img'])) {
            $img = '<div class="col-thum"><span class="img_crop"><img src="' . $arr['img'] . '" alt="' . $arr['alt'] . '"></span></div>';
        }
        if (isset($atts['title']) && !empty($atts['title'])) {
            $title = '<p class="p_title"><strong>' . $arr['title'] . '</strong></p>';
        }
        return '<div class="' . $arr['class'] . '">
                <div class="row align-items-center">
                ' . $img . '
                <div class="col">
                <div class="desc">
                ' . $title . '
                <p>' . $content . '</p>
                </div>
                </div>
                </div><!--/row-->
                </div><!--/card-->';
    } else {
        return '';
    }
}

add_shortcode('CamCard', 'campaignBlock');
function campaignBlock($atts, $content = null) {
    $upload_dir_link = wp_upload_dir()['baseurl'];
    $default = array(
        'title' => 'some title',
        'more' => 'その他キャンペーン対象商品はこちら',
        'morelink' => site_url() . '/product-category/special-campaign/',
        'alt' => 'Free Gift!',
        'img' => $upload_dir_link . '/2022/10/CP22-DEC-FGT-01.jpg'
    );
    $arr = shortcode_atts($default, $atts);
    if (isset($atts['more']) && !empty($atts['more'])) {
        if (isset($atts['morelink']) && !empty($atts['morelink'])) {
            $morep = '<p><a href="' . $atts['morelink'] . '" class="underline">' . $arr['more'] . '</a></p>';
        } else {
            $morep = '<p><a href="' . $arr['morelink'] . '" class="underline">' . $arr['more'] . '</a></p>';
        }
    }
    if (isset($atts['title']) && !empty($atts['title'])) {
        $title = '<p><strong>' . $arr['title'] . '</strong></p>';
    }
    return '<div class="box_container">
            <div class="box_border_tb">
            <div class="row align-items-center">
            <div class="col-3"><img src="' . $arr['img'] . '" alt="' . $arr['alt'] . '"></div>
            <div class="col">
            <div class="desc">
            ' . $title . '
            <div class="p_content">' . $content . $morep . '</div>
            </div><!--/desc-->
            </div><!--/col-9-->
            </div><!--/row-->
            </div><!--/box_border_tb-->
            </div><!--/box_container-->';
}

add_shortcode('CamCardRact', 'campaignBlockRact');
function campaignBlockRact($atts, $content = null) {
    $upload_dir_link = wp_upload_dir()['baseurl'];
    $default = array(
        'title' => 'some title',
        'more' => 'その他キャンペーン対象商品はこちら',
        'morelink' => site_url() . '/product-category/special-campaign/',
        'alt' => 'Free Gift!',
        // 'class' => 'card__radius mgt_01',
        'img' => $upload_dir_link . '/2022/10/CP22-DEC-FGT-01.jpg',
        'img02' => $upload_dir_link . '/2022/10/CP22-DEC-FGT-01.jpg'
    );
    $arr = shortcode_atts($default, $atts);
    if (isset($atts['more']) && !empty($atts['more'])) {
        if (isset($atts['morelink']) && !empty($atts['morelink'])) {
            $morep = '<p><a href="' . $atts['morelink'] . '" class="underline">' . $arr['more'] . '</a></p>';
        } else {
            $morep = '<p><a href="' . $arr['morelink'] . '" class="underline">' . $arr['more'] . '</a></p>';
        }
    }
    if (isset($atts['img02']) && !empty($atts['img02'])) {
        $img02 = '<div class="column-half"><img src="' . $arr['img02'] . '" alt="' . $arr['alt'] . '"></div>';
        $rowclass = 'columns-half';
        $colclass = 'column-half';
    } else {
        $img02 = '';
        $rowclass = 'columns-full';
        $colclass = 'column-full';
    }
    if (isset($atts['title']) && !empty($atts['title'])) {
        $title = '<p><strong>' . $arr['title'] . '</strong></p>';
    }
    return '<div class="box_container">
            <div class="box_border_tb">
            <div class="block">
            <div class="row columns align-items-center ' . $rowclass . '">
            <div class="' . $colclass . '"><img src="' . $arr['img'] . '" alt="' . $arr['alt'] . '"></div>
            ' . $img02 . '
            </div>
            <div class="col">
            <div class="desc">
            ' . $title . '
            <div class="p_content">' . $content . $morep . '</div>
            </div><!--/desc-->
            </div><!--/col-9-->
            </div><!--/row-->
            </div><!--/box_border_tb-->
            </div><!--/box_container-->';
}

add_shortcode('UserShow', 'UserShow');
function UserShow() {
    ob_start();
    if (is_user_logged_in()) {
    ?>
        Some text for logged
    <?php
    }
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

add_shortcode('GuestShow', 'GuestShow');
function GuestShow() {
    ob_start();
    if (!is_user_logged_in()) { ?>
        Some text for guest
    <?php
    }
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

add_shortcode('get_svg', 'get_inlineSvg');
function get_inlineSvg($atts) {
    $default = array(
        'title' => 'About',
    );
    ob_start();
    $arr = shortcode_atts($default, $atts);
    if (isset($atts['title']) && !empty($atts['title'])) {
        get_template_part('./images/svg' . $arr['title']);
    }
    return ob_get_clean();
}

add_shortcode('noticeUl', 'sc_notice_ul');
function sc_notice_ul($atts, $content = null) {
    return '<ul class="notice_list">' . do_shortcode($content) . '</ul>';
}

add_shortcode('noticeli', 'sc_notice_li');
function sc_notice_li($atts, $content = null) {
    return '<li class="notice_list-item">' . $content . '</li>';
}
