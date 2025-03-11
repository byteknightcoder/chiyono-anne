<?php
include_once dirname(__FILE__) . '/inc/shortcode.php';

/**
 * check product in category
 * @param type $product_id
 * @param type $category_slug
 * @return boolean
 */
function fsl_product_in_cat($product_id, $category_slug) {
    $accessories = fsl_get_taxonomy_hierarchy('product_cat', 0, $category_slug);
    $product_cats = get_the_terms($product_id, 'product_cat');
    foreach ($product_cats as $product_cat) {
        if (in_array($product_cat->term_id, array_keys($accessories))) {
            return true;
        }
    }
    return false;
}

/**
 * get child of category
 * @param type $taxonomy
 * @param type $parent
 * @param type $slug
 * @param type $children
 * @return type
 */
function fsl_get_taxonomy_hierarchy($taxonomy, $parent = 0, $slug = '', &$children = array()) {
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
        fsl_get_taxonomy_hierarchy($taxonomy, $term->term_id, '', $children);
        $children[$term->term_id] = $term;
    }
    return $children;
}

/**
 * Show only Stripe for family sale
 * @param type $available_gateways
 * @return type
 */
function fsl_filter_available_payment_gateways_orf($available_gateways) {
    if (!function_exists('WC') || !isset(WC()->cart) || WC()->cart->is_empty() || empty($available_gateways)) {
        return $available_gateways;
    }

    foreach ($available_gateways as $id => $gateway) {
        if (is_order_familysale() == true && $id != 'stripe' && $id != 'payid') {
            unset($available_gateways[$id]);
        }
    }
    return $available_gateways;
}

add_filter('woocommerce_available_payment_gateways', 'fsl_filter_available_payment_gateways_orf', 10, 1);

add_action('woocommerce_thankyou', 'fsl_woocommerce_auto_complete_order_orf', 99, 1);

/**
 * Add meta to detect is order from family sale
 * @param type $order_id
 * @return type
 */
function fsl_woocommerce_auto_complete_order_orf($order_id) {
    if (!$order_id) {
        return;
    }
    if (fsl_is_order_family_by_order_id($order_id)) {
        update_post_meta($order_id, 'is_familysale_order', 'yes');
    }
}

/**
 * Check is order family sale by order_id
 * @param type $order_id
 * @return boolean
 */
function fsl_is_order_family_by_order_id($order_id) {
    if (!$order_id) {
        return;
    }
    $exist = false;
    $order = wc_get_order($order_id);
    if ($order) {
        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_product_id();
            if (fsl_product_in_cat($product_id, 'familysale')) {
                $exist = true;
                break;
            }
        }
    }
    return $exist;
}

/**
 * Check familysale category have product or not
 * @return boolean
 */
function fsl_check_familysale_has_product() {
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => array('publish')
    );
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'product_cat',
            'terms' => array('familysale'),
            'field' => 'slug',
            'operator' => 'IN',
        ),
    );
    // Create the new query
    $loop = new WP_Query($args);
    // Get products number
    $product_count = $loop->post_count;
    // If results
    if ($product_count > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Redirect to shop page if familysale not have products
 * @global type $post
 * @return type
 */
function fsl_redirect_pre_checkout() {
    if (!function_exists('wc'))
        return;
    if (is_page('special-event') && !fsl_check_familysale_has_product()) {
        wp_redirect(home_url('shop-all'));
        die;
    }
    if (!is_user_logged_in()) {
        if (is_checkout()) {
            $_SESSION['prev_page'] = wc_get_checkout_url();
        } else if (is_cart()) {
            $_SESSION['prev_page'] = wc_get_cart_url();
        }
    }
    if (is_order_familysale()) {
        if (!is_user_logged_in() && (is_checkout() || is_cart())) {
            wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
            die;
        }
    }
}

add_action('template_redirect', 'fsl_redirect_pre_checkout');

function fsl_apply_discount_to_cart() {
    if (is_order_familysale()) {
        WC()->cart->remove_coupons();
    }
}

add_action('woocommerce_before_cart_table', 'fsl_apply_discount_to_cart', 12);
add_action('woocommerce_before_checkout_form', 'fsl_apply_discount_to_cart', 12);

add_filter('woocommerce_coupon_is_valid', 'fsl_filter_woocommerce_coupon_is_valid', 10, 2);

//don't allow use coupon for any family product
function fsl_filter_woocommerce_coupon_is_valid($true, $instance) {
    if (is_order_familysale()) {
        return false;
    }
    return $true;
}

function fsl_model_info() {
    if (is_page('special-event')) {
        $current_time = current_time('timestamp');
        $event_page = get_page_by_path('special-event', OBJECT, 'page');
        $closed_datetime = function_exists('get_field') ? get_field('closed_datetime', $event_page->ID) : '';
        $closed_modal_content_page = function_exists('get_field') ? get_field('closed_modal_content', $event_page->ID) : '';
        if ($current_time < strtotime($closed_datetime)) {
            if (!is_user_logged_in()) {
                $posts = get_page_by_path('family-sale-closed-store', OBJECT, 'post');
            } else {
                $posts = '';
            }
            $expired = 'no';
        } else {
            $expired = 'yes';
            $posts = get_page_by_path(basename($closed_modal_content_page), OBJECT, 'post');
        }
        if (!empty($posts)) {
            ?>
            <div class="remodal remodal_hbody" expired="<?php echo $expired; ?>" data-remodal-id="event_modal" id="event_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
                <div class="remodal_wraper">
                    <div class="modal_image">
                        <img class="img" src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($posts->ID)); ?>" />
                    </div>
                    <div class="modal_head">
                        <?php echo $posts->post_title; ?>
                    </div>
                    <div class="modal_body">
                        <?php
                        $content = apply_filters('the_content', get_the_content(null, false, $posts->ID));
                        echo $content;
                        if ($current_time < strtotime($closed_datetime)) {
                            ?>
                            <button data-remodal-action="close" class="remodal-close-button ja" aria-label="Close" style="margin-top:1rem;"><?php esc_html_e('SHOP NOW', 'zoa') ?></button>
                            <?php
                        } else {
                            ?>
                            <button onclick="location.href = '<?php echo home_url('/shop-all/'); ?>';" class="remodal-close-button ja" aria-label="Close" style="margin-top:1rem;"><?php esc_html_e('Go back store', 'zoa') ?></button>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}

add_action('wp_footer', 'fsl_model_info');

function fsl_custom_widget_cart_btn_view_cart() {
    $wp_button_class = wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '';
    if (is_order_familysale()) {
        $text = esc_html__('Continue shopping', 'woocommerce');
        $link = home_url('special-event');
    } else {
        $text = esc_html__('View cart', 'woocommerce');
        $link = wc_get_cart_url();
    }
    echo '<a href="' . esc_url($link) . '" class="button wc-forward' . esc_attr($wp_button_class) . '">' . $text . '</a>';
}

remove_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10);
add_action('woocommerce_widget_shopping_cart_buttons', 'fsl_custom_widget_cart_btn_view_cart', 12);

function fsl_redirect_login($redirect, $user) {
    if (isset($_SESSION['prev_page'])) {
        $redirect = $_SESSION['prev_page'];
    }
    return $redirect;
}

add_filter('woocommerce_login_redirect', 'fsl_redirect_login', 10, 2);
