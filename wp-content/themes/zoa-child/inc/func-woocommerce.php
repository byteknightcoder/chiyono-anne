<?php

    add_action('woocommerce_before_main_content', 'zoa_child_shop_open_tag', 1);
    function zoa_child_shop_open_tag() {
        $shop_sidebar = !is_active_sidebar('shop-widget') ? 'full' : get_theme_mod('shop_sidebar', 'full');
        $shop_class = is_product() ? 'pdp' : 'with-' . $shop_sidebar . '-sidebar';
        $shop_content_class = is_product() ? 'shop-content' : 'product-grid-container col-12 col-lg-9';
        if (get_theme_mod('flexible_sidebar')) {
            $shop_class .= ' has-flexible-sidebar';
        }
        ob_start();
    ?>
        <div class="row results-container max-width--site gutter-padding <?php echo esc_attr($shop_class); ?>">
            <?php
                if (!is_singular('product')) {
                    do_action('woocommerce_sidebar');
                }
            ?>
            <div class="<?php echo esc_attr($shop_content_class); ?>">
                <?php
                    if (get_theme_mod('flexible_sidebar') && 'full' !== $shop_sidebar && !is_product()) :
                ?>
                    <div class="sidebar-overlay"></div>
                    <a href="#" class="sidebar-toggle js-sidebar-toggle">
                        <span class="screen-reader-text"><?php esc_html_e('Toggle Shop Sidebar', 'zoa'); ?></span>
                        <i class="ion-android-options toggle-icon"></i>
                    </a>
        <?php
            endif;
            $contents = ob_get_contents();
            ob_end_clean();
            echo $contents;
    }

    add_action('woocommerce_after_main_content', 'zoa_child_shop_close_tag', 60);
    function zoa_child_shop_close_tag() {
        echo '</div>';
    }

    // Rename WooCommerce to Shop
    if (!current_user_can('level_10')) {

        add_action('admin_menu', 'rename_woocoomerce', 999);
        function rename_woocoomerce() {
            global $menu;

            // Pinpoint menu item
            $woo = rename_woocommerce('WooCommerce', $menu);

            // Validate
            if (!$woo) {
                return;
            }
            $menu[$woo][0] = __('Store Setting', 'zoa');
        }

        function rename_woocommerce($needle, $haystack) {
            foreach ($haystack as $key => $value) {
                $current_key = $key;
                if (
                    $needle === $value
                    or (is_array($value) && rename_woocommerce($needle, $value) !== false
                    )
                ) {
                    return $current_key;
                }
            }
            return false;
        }
    } // if (!current_user_can('level_10'))

    /* Remove Add to cart option from woo variation swatch pro */
    remove_action('wvs_pro_variation_show_archive_variation_after_cart_button', 'wvs_pro_archive_variation_template', 5);

    /**
     * Change the strength requirement on the woocommerce password
     *
     * Strength Settings
     * 0 = Very Weak / Anything
     * 1 = Password should be at least Weak
     * 2 = Also Weak but a little stronger 
     * 3 = Medium (default) 
     * 4 = Strong
     */
    add_filter('woocommerce_min_password_strength', 'misha_change_password_strength');
    function misha_change_password_strength($strength) {
        return 2;
    }

    add_filter('woocommerce_get_script_data', 'misha_strength_meter_settings', 20, 2);
    function misha_strength_meter_settings($params, $handle) {

        if ( 'wc-password-strength-meter' === $handle ) {
            $params = array_merge($params, array(
                'min_password_strength' => 2,
                'i18n_password_error' => __('make it stronger', 'zoa'),
                'i18n_password_hint' => ''
            ));
        }
        return $params;
    }

    add_action('wp_enqueue_scripts', 'misha_password_messages', 9999);
    function misha_password_messages() {

        wp_localize_script('wc-password-strength-meter', 'pwsL10n', array(
            'short' => __('Too short', 'zoa'),
            'bad' => __('Too bad', 'zoa'),
            'good' => __('Better but not enough', 'zoa'),
            'strong' => __('Better', 'zoa'),
            'mismatch' => __('Your passwords do not match, please re-enter them.', 'zoa')
        ));
    }

    // change added cart message
    add_filter('wc_add_to_cart_message_html', 'custom_add_to_cart_message', 10, 2);
    function custom_add_to_cart_message($message, $products) {
        $message = sprintf('<span class="added_msg">' . __('Products successfully added to cart!', 'zoa') . '</span><a href="%s" class="cta view_cart_link">' . __('View cart', 'zoa') . '</a>', wc_get_cart_url());

        return $message;
    }

    add_action('wp', 'wpse163434_init');
    function wpse163434_init() {
        $position = 22;

        // Avada Theme
        if (class_exists('Avada')) {
            $position = 50;
        }

        // Enfold Theme
        if (defined('AV_FRAMEWORK_VERSION')) {
            $position = 5;
        }
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', $position);
    }

    // change add to cart text
    add_filter('woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text');
    function woo_custom_cart_button_text() {
        return __('Add to cart', 'zoa');
    }

    // gallery thumbnail size
    add_filter('woocommerce_get_image_size_gallery_thumbnail', function ($size) {
        return array(
            'width' => 94,
            'height' => 126,
            //'crop'   => 0,
        );
    });

    // remove dashboard menu in my account page
    add_filter('woocommerce_account_menu_items', 'my_remove_my_account_links');
    function my_remove_my_account_links($menu_links) {

        // unset( $menu_links['edit-address'] ); // Addresses
        unset($menu_links['dashboard']); // Dashboard
        // unset( $menu_links['payment-methods'] ); // Payment Methods
        // unset( $menu_links['orders'] ); // Orders
        unset($menu_links['downloads']); // Downloads
        // unset( $menu_links['edit-account'] ); // Account details
        // unset( $menu_links['customer-logout'] ); // Logout

        return $menu_links;
    }

    // add wishlist
    class My_Custom_My_Account_Endpoint {

        /**
         * Custom endpoint name.
         *
         * @var string
         */
        public static $endpoint = 'my-wishlist';

        /**
         * Plugin actions.
         */
        public function __construct() {
            // Actions used to insert a new endpoint in the WordPress.
            add_action('init', array($this, 'add_endpoints'));
            add_filter('query_vars', array($this, 'add_query_vars'), 0);

            // Change the My Accout page title.
            add_filter('the_title', array($this, 'endpoint_title'));

            // Insering your new tab/page into the My Account page.
            add_filter('woocommerce_account_menu_items', array($this, 'new_menu_items'));
            add_action('woocommerce_account_' . self::$endpoint . '_endpoint', array($this, 'endpoint_content'));
        }

        /**
         * Register new endpoint to use inside My Account page.
         *
         * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
         */
        public function add_endpoints() {
            add_rewrite_endpoint(self::$endpoint, EP_ROOT | EP_PAGES);
        }

        /**
         * Add new query var.
         *
         * @param array $vars
         * @return array
         */
        public function add_query_vars($vars) {
            $vars[] = self::$endpoint;

            return $vars;
        }

        /**
         * Set endpoint title.
         *
         * @param string $title
         * @return string
         */
        public function endpoint_title($title) {
            global $wp_query;

            $is_endpoint = isset($wp_query->query_vars[self::$endpoint]);

            if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
                // New page title.
                $title = __('My Wishlist', 'zoa');
                remove_filter('the_title', array($this, 'endpoint_title'));
            }

            return $title;
        }

        /**
         * Insert the new endpoint into the My Account menu.
         *
         * @param array $items
         * @return array
         */
        public function new_menu_items($items) {
            // Remove the logout menu item.
            $logout = $items['customer-logout'];
            unset($items['customer-logout']);

            // Insert your custom endpoint.
            $items[self::$endpoint] = __('My Wishlist', 'zoa');

            // Insert back the logout item.
            $items['customer-logout'] = $logout;

            return $items;
        }

        /**
         * Endpoint HTML content.
         */
        public function endpoint_content() {
            echo do_shortcode('[ti_wishlistsview]');
        }

        /**
         * Plugin install action.
         * Flush rewrite rules to make our custom endpoint available.
         */
        public static function install() {
            flush_rewrite_rules();
        }
    }
    new My_Custom_My_Account_Endpoint(); // Call the Class.

    // Flush rewrite rules on plugin activation.
    // Amin -> Which Plugin?
    // Disabled by Amin
    // register_activation_hook(__FILE__, array('My_Custom_My_Account_Endpoint', 'install'));

    // change my account menu title
    add_filter('woocommerce_account_menu_items', 'wpb_woo_my_account_order');
    function wpb_woo_my_account_order() {
        $myorder = array(
            'edit-account' => __('My Account Info', 'zoa'),
            'orders' => __('My Orders', 'zoa'),
            'edit-address' => __('My Addresses', 'zoa'),
            'my-wishlist' => __('My Wishlist', 'zoa'),
            'appointment' => __('My Appointments', 'zoa'),
            //'payment-methods'    => __( 'Payment Methods', 'woocommerce' ),
            'customer-logout' => __('Logout', 'woocommerce'),
        );
        return $myorder;
    }

    add_action('woocommerce_before_shop_loop', 'zoa_child_result_count', 30);
    function zoa_child_result_count() {
        ?>
        <div class="shop-top-bar display--mid-up">
            <?php
                woocommerce_result_count();
                woocommerce_catalog_ordering();
            ?>
        </div>
        <?php
    }

    // change related product columns
    /* SET COLUMN FOR RELATED || UPSELL PRODUCT */
    add_filter('woocommerce_output_related_products_args', 'zoa_child_column_related', 9999);
    function zoa_child_column_related($args) {
        $number = (int) get_theme_mod('related_product_item', 4);
        $column = (int) get_theme_mod('related_column', 4);

        $args['posts_per_page'] = $number;
        $args['columns'] = $column;
        return $args;
    }

    // change side mini cart
    /* PRODUCT ACTION */
    if (!function_exists('zoa_product_action')) :
        function zoa_product_action() {
            global $woocommerce;
            $total = $woocommerce->cart->cart_contents_count;
        ?>
            <div id="shop-quick-view" data-view_id='0'>
                <div class="shop-quick-view-container">
                    <div class="quickview__dialog"><button class="quick-view-close-btn ion-ios-close-empty"></button>
                        <div class="quick-view-content"></div>
                    </div>
                </div>
            </div>

            <div id="shop-cart-sidebar">
                <div class="cart-sidebar-wrap">
                    <div class="cart-sidebar-head">
                        <h4 class="cart-sidebar-title"><?php esc_html_e('Shopping cart', 'zoa'); ?></h4>
                        <span class="shop-cart-count"><?php echo esc_attr($total); ?></span>
                        <button id="close-cart-sidebar" class="ion-android-close"></button>
                    </div>
                    <div class="cart-sidebar-content">
                        <?php woocommerce_mini_cart(); ?>
                    </div>
                    <!--/cart-sidebar-content-->
                </div>
                <!--/cart-sidebar-wrap-->
            </div>

            <div id="shop-overlay"></div>
        <?php
        }
    endif; // zoa_product_action

    // change shop container
    /* CONTENT WRAPPER */
    add_action('wp_head', 'product_category_filter_changes');
    function product_category_filter_changes() {
        remove_action('woocommerce_before_main_content', 'zoa_shop_open_tag', 5);
        remove_action('woocommerce_after_main_content', 'zoa_shop_close_tag', 5);
    }

    // change thank you page title
    // remove attribute name from cart item title
    add_filter('woocommerce_product_variation_title_include_attributes', '__return_false');

    // add view full details in quick view
    add_action('woocommerce_after_add_to_cart_button', 'quick_additional_button');
    function quick_additional_button() {
        global $post;
        if ( $post && 'product' == $post->post_type && isset($_REQUEST['action']) && 'quick_view' == $_REQUEST['action'] ) {
            echo '<div class="vf_row"><a class="cta cta--secondary" href="' . get_permalink($post->ID) . '">' . __('View Full Details', 'zoa') . '</a></div>';
        }
    }

    /* REMOVE EMPTY CART ACTION */
    function remove_cart_actions_parent_theme() {
        remove_action('woocommerce_cart_actions', 'zoa_clear_cart_url');
    }

    // change incl tax total array for format from class-wc-order.php
    add_filter('woocommerce_get_formatted_order_total', 'woo_rename_tax_inc_format', 10, 4);
    function woo_rename_tax_inc_format($formatted_total, $order_class, $tax_display, $display_refunded) {
        $formatted_total = wc_price($order_class->get_total(), array('currency' => $order_class->get_currency()));
        $order_total = $order_class->get_total();
        $total_refunded = $order_class->get_total_refunded();
        $tax_string = '';

        // Tax for inclusive prices.
        if (wc_tax_enabled() && 'incl' === $tax_display) {
            $tax_string_array = array();
            $tax_totals = $order_class->get_tax_totals();

            if ('itemized' === get_option('woocommerce_tax_total_display')) {
                foreach ($tax_totals as $code => $tax) {
                    $tax_amount = ($total_refunded && $display_refunded) ? wc_price(WC_Tax::round($tax->amount - $order_class->get_total_tax_refunded_by_rate_id($tax->rate_id)), array('currency' => $order_class->get_currency())) : $tax->formatted_amount;
                    $tax_string_array[] = sprintf('%s %s', $tax_amount, $tax->label);
                }
            } elseif (!empty($tax_totals)) {
                $tax_amount = ($total_refunded && $display_refunded) ? $order_class->get_total_tax() - $order_class->get_total_tax_refunded() : $order_class->get_total_tax();
                $tax_string_array[] = sprintf('%s %s', wc_price($tax_amount, array('currency' => $order_class->get_currency())), WC()->countries->tax_or_vat());
            }

            if (!empty($tax_string_array)) {
                /* translators: %s: taxes */
                $tax_string = ' <small class="includes_tax">' . sprintf(__('(incl. tax)', 'woocommerce')) . '</small>';
            }
        }

        if ($total_refunded && $display_refunded) {
            $formatted_total = '<del>' . strip_tags($formatted_total) . '</del> <ins>' . wc_price($order_total - $total_refunded, array('currency' => $order_class->get_currency())) . $tax_string . '</ins>';
        } else {
            $formatted_total .= $tax_string;
        }
        return $formatted_total;
    }

    // Change Incl tax array
    add_filter('woocommerce_cart_totals_order_total_html', 'custom_cart_totals_order_total_html', 10, 1);
    function custom_cart_totals_order_total_html($value) {
        $value = WC()->cart->get_total();

        // If prices are tax inclusive, show taxes here.
        if (wc_tax_enabled() && WC()->cart->display_prices_including_tax()) {
            //$tax_string_array = array();
            $tax_string_array = array();
            $cart_tax_totals = WC()->cart->get_tax_totals();

            if (get_option('woocommerce_tax_total_display') === 'itemized') {
                foreach ($cart_tax_totals as $code => $tax) {
                    $tax_string_array[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
                }
            } elseif (!empty($cart_tax_totals)) {
                $tax_string_array[] = sprintf('%s %s', wc_price(WC()->cart->get_taxes_total(true, true)), WC()->countries->tax_or_vat());
            }

            // if ( ! empty( $tax_string_array ) ) {
            //     $taxable_address = WC()->customer->get_taxable_address();
            //     $estimated_text = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ? sprintf( ' ' . __( 'estimated for %s', 'woocommerce' ), WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] ) : '';
            //     $value .= '<small class="includes_tax test">' . sprintf( __( '(includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) . $estimated_text ) . '</small>';
            // }
        }

        // Always return $value
        return $value;
    }

    // override function wc_cart_totals_shipping_method_label
    // add_filter( 'woocommerce_shipping_package_name', 'custom_cart_totals_shipping_method_label', 10, 2 );
    add_filter('woocommerce_cart_shipping_method_full_label', 'custom_cart_totals_shipping_method_label', 10, 2);
    function custom_cart_totals_shipping_method_label($label, $method) {
        $label = ': ' . $method->get_label();

        if ($method->cost >= 0 && $method->get_method_id() !== 'free_shipping') {
            if (WC()->cart->display_prices_including_tax()) {
                $label .= '</span><span class="value price-amount price-shipping">' . wc_price($method->cost + $method->get_shipping_tax()) . '</span>';
                if ($method->get_shipping_tax() > 0 && !wc_prices_include_tax()) {
                    $label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
            } else {
                $label .= '</span>' . wc_price($method->cost) . '';
                if ($method->get_shipping_tax() > 0 && wc_prices_include_tax()) {
                    $label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            }
        }

        return $label;
    }

    // change dashboard content in my account
    add_action('woocommerce_account_dashboard', 'custom_woocommerce_account_dashboard');
    function custom_woocommerce_account_dashboard() {
        // of course you can print dynamic content here, one of the most useful functions here is get_current_user_id()
        echo '<div class="col-img-dash"><img src="' . get_stylesheet_directory_uri() . '/images/my-account.jpg" alt="' . esc_attr(get_bloginfo('name', 'display')) . '"></div>';
    }

    // 2. Save field on Customer Created action
    add_action('woocommerce_created_customer', 'save_birthday_register_select_field');
    function save_birthday_register_select_field($customer_id) {
        if (isset($_POST['account_birth_data'])) {
            $data = array(
                'year' => '',
                'month' => '',
                'day' => ''
            );
            if (!empty($_POST['account_birth_data'])) {
                $arr = explode('-', $_POST['account_birth_data']);
                $month = (int) $arr[1];
                $day = (int) $arr[2];
                $data['year'] = $arr[0];
                $data['month'] = (string) $month;
                $data['day'] = (string) $day;
            }
            update_user_meta($customer_id, 'account_birth', $data);
        }
    }

    // 3. Display Select Field @ User Profile (admin) and My Account Edit page (front end)
    add_action('show_user_profile', 'add_birthday_to_edit_account_form', 30);
    add_action('edit_user_profile', 'add_birthday_to_edit_account_form', 30);
    add_action('woocommerce_edit_account_form_below_email', 'add_birthday_to_edit_account_form', 10);
    function add_birthday_to_edit_account_form() {
        // if (empty ($user) ) {
        //     $user_id = get_current_user_id();
        //     $user = get_userdata( $user_id );
        // }
        $user_id = $_REQUEST['user_id'] ? $_REQUEST['user_id'] : get_current_user_id();
        $user = get_userdata($user_id);

        $default = array('day' => '', 'month' => '', 'year' => '',);
        $birth_date = wp_parse_args(get_the_author_meta('account_birth', $user->ID), $default);
        if ($birth_date['year'] == '0' || $birth_date['year'] == '' || $birth_date['month'] == '0' || $birth_date['month'] == '' || $birth_date['day'] == '0' || $birth_date['day'] == '') {
            $v = '';
        } else {
            $v = $birth_date['year'] . '-' . $birth_date['month'] . '-' . $birth_date['day'];
        }
    ?>

        <div class="form-row">
            <div class="field-wrapper">
                <label class="form-row__label" for="account_birth_month"><?php _e('Date of Birth', 'zoa'); ?><span class="required">*</span></label>
                <div class="row row-dayofbirth">
                    <!-- New Birthday Field-->
                    <div class="col-12"><input readonly class="form-control required" name="account_birth_data" type="text" data-parsley-required="true" required="true" value="<?php echo $v; ?>" id="account_birth" placeholder="YYYY-MM-DD" /></div>
                    <!-- /New Birthday Field-->
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="field-wrapper registration-form-mailpoet">
                <?php
                    require_once(WP_PLUGIN_DIR . '/mailpoet/vendor/autoload.php');
                    $mailpoet = 'no';
                    if (class_exists(\MailPoet\API\API::class)) {
                        $mailpoet_api = \MailPoet\API\API::MP('v1');
                        $get_subscriber = $mailpoet_api->getSubscriber($user->user_email);
                        if ( isset($get_subscriber['id'])&&$get_subscriber['id'] > 0 ) {
                            if (isset($get_subscriber['subscriptions'])) {
                                foreach ($get_subscriber['subscriptions'] as $value) {
                                    if ($value['segment_id']==6 && $value['status']!='unsubscribed') { // 配信可能顧客リスト
                                        $mailpoet = 'yes';
                                    }
                                }
                            }
                        }
                    }
                    $checked='';
                    if ( isset($mailpoet) && 'yes' == $mailpoet ) {
                        $checked = 'checked';
                    }
                ?>
                <label class="form-row__label" for="mailpoet_subscribe_on_edit_account"><input <?php echo $checked; ?> id="mailpoet_subscribe_on_edit_account" name="mailpoet_subscribe_on_edit_account" value="1" type="checkbox"/><?php _e('割引や製品情報が記載されたお得なメールを受け取る', 'zoa'); ?></label>
            </div>
        </div>
        <?php
    }

    // with something like:
    // add_action( 'user_profile_update_errors','ch_wooc_validate_custom_field', 10, 1 );
    add_action('woocommerce_save_account_details_errors', 'ch_wooc_validate_custom_field', 10, 1);
    function ch_wooc_validate_custom_field(&$args) {
        if (isset($_POST['account_birth_data'])) {
            if (empty($_POST['account_birth_data'])) {
                $args->add('error', __('Please fill your birthday', 'zoa'), '');
                wc_add_notice(__('Please fill your birthday', 'zoa'), 'error');
            }
        }
    }

    // 4. Save User Field When Changed From the Admin/Front End Forms
    add_action('personal_options_update', 'save_birthday_account_details');
    add_action('edit_user_profile_update', 'save_birthday_account_details');
    add_action('woocommerce_save_account_details', 'save_birthday_account_details');
    function save_birthday_account_details($customer_id) {
        if (isset($_POST['account_birth_data'])) {
            $data = array(
                'year' => '',
                'month' => '',
                'day' => ''
            );
            if (!empty($_POST['account_birth_data'])) {
                $arr = explode("-", $_POST['account_birth_data']);
                $month = (int) $arr[1];
                $day = (int) $arr[2];
                $data['year'] = $arr[0];
                $data['month'] = (string) $month;
                $data['day'] = (string) $day;
            }
            update_user_meta($customer_id, 'account_birth', $data);
        }

        // For Billing email (added related to your comment)
        if (isset($_POST['account_email']))
            update_user_meta($customer_id, 'billing_email', sanitize_text_field($_POST['account_email']));
        if (isset($_POST['mobile/email'])) {
            update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['mobile/email']));
            update_user_meta($customer_id, 'shipping_phone', sanitize_text_field($_POST['mobile/email']));
        }
        $save_fields = array();
        // Check If ACF Plugin is Active
        // Check If BOOKING_FORM_ID defined
        if ( class_exists('ACF') && defined( 'BOOKING_FORM_ID' ) ) {
            $field_group_fields = acf_get_fields(BOOKING_FORM_ID);
            foreach ($field_group_fields as $field) {
                loop_to_get_sub_field($field, $save_fields);
            }
        }

        foreach ($save_fields as $save_field) {
            if ($save_field['parent_name'] == 'q_04') {
                $post_acf = $_POST['acf'];
                if (isset($post_acf[$save_field['key']]) && $post_acf[$save_field['key']]) {
                    update_user_meta($customer_id, $save_field['name'], $post_acf[$save_field['key']]);
                }
            }
        }
    }

    // remove city field from shipping calculator
    add_filter('woocommerce_shipping_calculator_enable_city', '__return_false');
    // remove postcode field from shipping calculator
    add_filter('woocommerce_shipping_calculator_enable_postcode', '__return_false');

    //move tabs under description in single product page
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    add_action('woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60);

    // add subtitle for product
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    add_action('woocommerce_single_product_summary', 'fp_wc_display_subtitle_in_single_product', 5);
    function fp_wc_display_subtitle_in_single_product() {
        echo '<h1 class="product_title entry-title">' . get_the_title_product_chiyono() . '</h1>';
        if ( function_exists('the_subtitle') && '' != get_the_subtitle() ) {
            echo '<h2 class="subtitle">' . get_the_subtitle() . '</h2>';
        }
    }

    // add_action('woocommerce_after_shop_loop_item_title', 'fp_wc_display_subtitle_in_shop_page', 1);
    // function fp_wc_display_subtitle_in_shop_page() {
    //     if (function_exists('the_subtitle')):
    //         if (get_the_subtitle() != ''):
    //             echo '<h4 class="subtitle">' . get_the_subtitle() . '</h4>';
    //         endif;
    //     endif;
    // }

    // remove content editor for product edit page
    add_action('init', 'remove_product_editor');
    function remove_product_editor() {
        remove_post_type_support('product', 'editor');
    }

    // remove tabs
    add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);
    function woo_remove_product_tabs($tabs) {
        unset($tabs['description']); // Remove the description tab
        unset($tabs['reviews']); // Remove the reviews tab
        unset($tabs['additional_information']); // Remove the additional information tab
        return $tabs;
    }

    // remove meta
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    // If WooCommerce Plugin Active
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        // wrap div for title and price in product content
        add_action('woocommerce_shop_loop_item_title', 'output_opening_item_wrap', 10);
        function output_opening_item_wrap() {
            global $post;
            echo '<div class="c-product-item_wrap_info">';

            $p_id = get_the_ID();
            $pbdate = get_the_date('Y-m-d H:i:s', $p_id);
            $date_allow = date_i18n('Y-m-d 00:00:00', strtotime('+7 days', strtotime($pbdate)));
            $current_time = current_time('timestamp');
            $bm_meta = get_post_meta($p_id, '_yith_wcbm_product_meta', true);
            if (strtotime($date_allow) > $current_time && (!isset($bm_meta['id_badge']) || empty($bm_meta['id_badge'])) && !fsl_product_in_cat($p_id, 'familysale')) {
        ?>
                <div class="container-image-and-badge-new ch_bad_single">
                    <div class="yith-wcbm-badge yith-wcbm-badge-37845 yith-wcbm-badge-37157 yith-wcbm-badge--on-product-36718 yith-wcbm-badge--anchor-point-top-left yith-wcbm-badge-custom" data-position="{&quot;top&quot;:0,&quot;bottom&quot;:&quot;auto&quot;,&quot;left&quot;:0,&quot;right&quot;:&quot;auto&quot;}">
                        <div class="yith-wcbm-badge__wrap">
                            <div class="yith-wcbm-badge-text"><?php esc_html_e('NEW', 'zoa'); ?></div>
                        </div>
                    </div>
                </div>
            <?php
            }
            // global $product;
            // if ( $product->is_type( 'variable' )) {
            //     echo '<a href="' . get_permalink($post) . '" class="hover_link">';
            // } else {
            //     echo '<a href="' . get_permalink($post) . '" class="hover_link"></a>';
            // }
        }

        add_action('woocommerce_after_shop_loop_item', 'ouput_closing_item_wrap', 50);
        function ouput_closing_item_wrap() {
            global $product;
            // if ( $product->is_type( 'variable' )) {
            //     echo '</a>';
            // }
            echo '</div><!-- /.c-product-item_wrap_info -->';
        }

        /* CHANGE PERMALINK TO LOOP PRODUCT TITLE */
        // add_action('init', 'child_custom_actions');
        function child_custom_actions() {
            // remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
            // remove_action( 'woocommerce_shop_loop_item_title', 'zoa_template_loop_product_title', 10 );
        }

        // wrap div for title and price in single product
        add_action('woocommerce_single_product_summary', 'output_opening_div', 1);
        function output_opening_div() {
            echo '<div class="prod-info">';
            $tags = get_the_terms(get_the_ID(), 'product_tag');
            $tag_arr = array();

            if ($tags && !is_wp_error($tags)) {
                $tag_arr = wp_list_pluck($tags, 'name');
            }
            $show_tag_front = get_post_meta(get_the_ID(), 'show_tag_front', true) ? get_post_meta(get_the_ID(), 'show_tag_front', true) : '';
            $bg_color_tag = get_post_meta(get_the_ID(), 'bg_color_tag', true) ? get_post_meta(get_the_ID(), 'bg_color_tag', true) : '';
            if (!empty($tag_arr) && $show_tag_front == 'yes') {
                echo '<div class="tag_front" style="background: ' . $bg_color_tag . ';">';
                echo implode(', ', $tag_arr);
                echo '</div>';
            }
        }

        add_action('woocommerce_single_product_summary', 'ouput_closing_div', 12);
        function ouput_closing_div() {
            echo '</div><!-- /.prod-info -->';
        }

        function ws_opening_div() {
            echo '<div class="ws-row">';
        }

        function ws_closing_div() {
            echo '</div><!-- /.ws-row -->';
        }

        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

        add_filter('the_title_product_chiyono', 'zoa_change_product_title_default', 1, 2);
        function zoa_change_product_title_default($title, $id = 0) {
            if (is_cart()) {
                return $title;
            }

            // if (is_singular('product')) {
            $page_id = get_queried_object_id();
            if ($page_id == $id) {
                return zoa_change_loop_product_title($title, $id);
            } else {
                return zoa_change_product_title($title, $id);
            }
        }

        function zoa_change_product_title($title, $id = 0) {
            // should not be use this function for 'the_title', because <a>c-product-item--link_title is not needed for single product title, so may be use 'woocommerce_shop_loop_item_title' hook
            if (!$id) {
                return $title;
            }
            $org_title = $title;
            $product = get_post($id);
            if ( $product && 'product' == $product->post_type && ( !is_admin() || (defined('DOING_AJAX') && DOING_AJAX) ) ) {
                $serie_cat = get_the_terms($product, 'series');
                if (!is_wp_error($serie_cat) && !empty($serie_cat)) {
                    $title = '<div class="mini-product__item mini-product__series heading heading--small"><a href="' . get_term_link($serie_cat[0]->term_id) . '">' . $serie_cat[0]->name . '</a></div>';
                    if (!is_cart()) {
                        $title.='<a class="c-product-item--link_title" href="' . get_permalink() . '"><span class="product_title">' . $org_title . '</span></a>';
                    }
                } else {
                    $title = '<a class="c-product-item--link_title" href="' . get_permalink() . '"><span class="product_title">' . $title . '</span></a>';
                }
            }
            return $title;
        }

        function zoa_change_loop_product_title($title, $id) {
            // this is removed title link code, use for single product page title
            if (!$id) {
                return $title;
            }

            $product = get_post($id);
            if ( $product && 'product' == $product->post_type && ( !is_admin() || (defined('DOING_AJAX') && DOING_AJAX) ) ) {
                $serie_cat = get_the_terms($product, 'series');
                if (!is_wp_error($serie_cat) && !empty($serie_cat)) {
                    $title = '<div class="mini-product__item mini-product__series heading heading--small"><a href="' . get_term_link($serie_cat[0]->term_id) . '">' . $serie_cat[0]->name . '</a></div>
                    <span class="product_title">' . $title . '</span>';
                } else {
                    $title = '<span class="product_title">' . $title . '</span>';
                }
            }
            return $title;
        }

        add_filter('woocommerce_cart_item_name', 'zoa_change_cart_product_title', 1000, 4);
        function zoa_change_cart_product_title($title, $cart_item = '', $cart_item_key = '') {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = $_product->get_id();
            if ($_product->post_type == 'product_variation') {
                $product_id = $_product->get_parent_id();
            }
            $title = zoa_change_loop_product_title($title, $product_id);
            return $title;
        }

        add_filter('woocommerce_order_item_name', 'zoa_change_order_product_title', 1000, 4);
        function zoa_change_order_product_title($title, $item = array(), $order = array()) {
            if ((is_admin() && !(defined('DOING_AJAX') && DOING_AJAX))) {
                return $title;
            }
            $_product = $item->get_product();
            if ( $_product != false) {
                $is_visible = $_product && $_product->is_visible();
                $product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $_product->get_permalink($item) : '', $item, $order);
                $title = $product_permalink ? sprintf('<a href="%s" class="link">%s</a>', $product_permalink, $title) : $title;
                $product_id = $_product->get_id();
                if ( 'product_variation' == $_product->post_type ) {
                    $product_id = $_product->get_parent_id();
                }
                $title = zoa_change_loop_product_title($title, $product_id);
            }
            return $title;
        }

        // Change drophint action
        function dropahint_content_after_addtocart_button_child() {
            global $loop;

            // Ensure $loop and $loop->post are valid before accessing them
            if ( isset( $loop->post ) && is_a( $loop->post, 'WP_Post' ) ) {

                // Get the dropahint widget option
                if ( $op = get_option( 'dropahint_widget' ) ) {

                    // Get the product image
                    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->post->ID ), 'single-post-thumbnail' );

                    // Render the script and span
                    if ( ! empty( $image[0] ) ) {
                    ?>
                        <script src="<?php echo esc_url( $op ); ?>" async></script>
                        <span class="drophint-link" data-product-image="<?= esc_url( $image[0] ); ?>"></span>
                    <?php
                    }
                }
            }
        }

        // remove drophint action
        add_action('init', 'drophint_remove_actions');
        function drophint_remove_actions() {
            remove_action('woocommerce_after_add_to_cart_button', 'dropahint_content_after_addtocart_button');
            // remove_action( 'admin_menu', 'dropahint_custom_menu_page' );
        }

        //remove shortdescription
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        // add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 40 );
        // add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 45 );
        add_action('woocommerce_after_add_to_cart_button', 'ws_opening_div', 20);
        add_action('woocommerce_after_add_to_cart_button', 'dropahint_content_after_addtocart_button_child', 40);

        add_action('woocommerce_after_add_to_cart_button', 'show_ti_addwish_button', 30);
        function show_ti_addwish_button() {
            global $product;
            $id = $product->get_id();
            if (!fsl_product_in_cat($id, 'familysale')) { // hide if product from familysale category
                $variation_id = get_product($product->variation_id);
                echo '<div class="add-to-wishlist-button">' . do_shortcode('[ti_wishlists_addtowishlist product_id="' . $id . '" variation_id="' . $variation_id . '"]') . '</div>';
            }
        }

        add_action('woocommerce_after_add_to_cart_button', 'new_zoa_product_sharing', 50);
        function new_zoa_product_sharing() {
            global $product;
            $id = $product->get_id();
            $url = get_permalink($id);
            $title = get_the_title_product_chiyono($id);
            $img_id = $product->get_image_id();
            $img = wp_get_attachment_image_src($img_id, 'full');
            $tags = get_the_terms($id, 'product_tag');
            $tag_list = '';

            if ($tags && !is_wp_error($tags)) {
                $tag_list = implode(', ', wp_list_pluck($tags, 'name'));
            }
            if (!fsl_product_in_cat($id, 'familysale')) : // hide if product from familysale category
            ?>
                <div class="theme-social-icon p-shared">
                    <span class="sharing-tools">
                        <span class="sharing-tools__layer-1"><span class="link-icon -no-anim -share cta"><?php esc_html_e('Share', 'zoa'); ?></span></span>
                        <span class="sharing-tools__layer-2">
                            <a href="<?php echo esc_url_raw('//facebook.com/sharer.php?u=' . urlencode($url)); ?>" title="<?php echo esc_attr($title); ?>" target="_blank">
                            </a>
                            <a href="<?php echo esc_url_raw('//twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($title) . '&hashtags=' . urlencode($tag_list)); ?>" title="<?php echo esc_attr($title); ?>" target="_blank">
                            </a>
                            <a href="<?php echo esc_url_raw('//pinterest.com/pin/create/button/?url=' . urlencode($url) . '&image_url=' . urlencode($img[0]) . '&description=' . urlencode($title)); ?>" title="<?php echo esc_attr($title); ?>" target="_blank">
                            </a></span>
                        <!--/.sharing-tools-->
                    </span>
                </div>
        <?php
            endif;
        }

        add_action('woocommerce_after_add_to_cart_form', 'ws_closing_div', 51);

        // add accordion tabs
        add_action('woocommerce_single_product_summary', 'output_accordion_tabs', 50);
        function output_accordion_tabs() {
            get_template_part('./woocommerce/single-product/accordions');
        }

    } // end if woocommerce

    // Add Delivery Date field in Shipping tab in admin
    add_action('woocommerce_product_options_shipping', 'woo_deliver_date_field');
    function woo_deliver_date_field() {
        woocommerce_wp_text_input( array(
            'id' => 'deliver_date',
            'label' => __('Delivery Date', 'woocommerce'),
            'desc_tip' => true,
            'description' => __('Estimated delivery date', 'woocommerce'),
            'data_type' => ''
        ) );

        global $post;
        // Custom field Type
        $unit = array('months', 'weeks', 'days');
    ?>
        <p class="form-field deliver_date_field_range">
            <label for="custom_field_type"></label>
            <span class="wrap">
                <?php $custom_field_type = get_post_meta($post->ID, 'from_to', true); ?>
                <input placeholder="" class="" type="number" name="from" value="<?php echo $custom_field_type[0]; ?>" step="any" min="0" style="width: 80px;" />
                <select name="from_unit">
                    <?php foreach ($unit as $value) :?>
                        <option <?php if ($value == $custom_field_type[1]) echo 'selected="true"'; ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
                <input placeholder="<?php echo __('To', 'zoa'); ?>" type="number" name="to" value="<?php echo $custom_field_type[2]; ?>" step="any" min="0" style="width: 80px;" />
                <select name="to_unit">
                    <?php foreach ($unit as $value) : ?>
                        <option <?php if ($value == $custom_field_type[3]) echo 'selected="true"'; ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </span>
            <span class="description"><?php //_e( 'Example values for each input is [2,months,3,months], that mean 2 months to 3months', 'zoa' ); ?></span>
        </p>
    <?php
        woocommerce_wp_text_input( array(
            'id' => 'specific_deliver_date',
            'label' => __('Specific delivery date', 'woocommerce'),
            'desc_tip' => true,
            'description' => '',
            'data_type' => ''
        ) );
    }

    add_action('woocommerce_process_product_meta', 'save_deliver_date_field');
    function save_deliver_date_field($post_id) {

        $custom_field_value = isset($_POST['deliver_date']) ? $_POST['deliver_date'] : '';

        $product = wc_get_product($post_id);
        $product->update_meta_data('deliver_date', $custom_field_value);
        $product->save();
        $custom_field_type = array(esc_attr($_POST['from']), esc_attr($_POST['from_unit']), esc_attr($_POST['to']), esc_attr($_POST['to_unit']));
        update_post_meta($post_id, 'from_to', $custom_field_type);
        $specific_deliver_date = isset($_POST['specific_deliver_date']) ? $_POST['specific_deliver_date'] : '';
        update_post_meta($post_id, 'specific_deliver_date', $specific_deliver_date);
    }

    // remove uncategorized category fomr sidebar
    add_filter('woocommerce_product_categories_widget_args', 'custom_woocommerce_product_subcategories_args');
    function custom_woocommerce_product_subcategories_args($args) {
        $args['exclude'] = get_option('default_product_cat');
        return $args;
    }

    /* Remove Default Theme Swatch  if Woo Variation Swatches Pro is activated */
    add_filter('init', 'remove_zoa_swatch_html_filter', 20);
    function remove_zoa_swatch_html_filter() {
        if (class_exists('Woo_Variation_Swatches_Pro')) :
            // remove the filter
            remove_filter('woocommerce_loop_add_to_cart_link', 'zoa_loop_add_to_cart');
        endif;
    }

    /* Make unchecked for ship to differ addr in checkout page */
    // add_filter('woocommerce_ship_to_different_address_checked', '__return_false');

    // Remove the payment options form from default location
    remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
    // Move the payment options
    add_action('woocommerce_checkout_after_customer_details', 'woocommerce_checkout_payment', 20);
    /* PRODUCT LABEL */
    if (!function_exists('zoa_product_label')) {

        /**
         * Display product label
         *
         * @param      $product  The product
         *
         * @return     $label markup
         */
        function zoa_product_label($product) {
            if (!$product) {
                return;
            }

            $label = '';

            // product option
            if (function_exists('FW')) {
                $pid = $product->get_id();
                $label_txt = fw_get_db_post_option($pid, 'label_txt', '');
                $label_color = fw_get_db_post_option($pid, 'label_color', '#fff');
                $label_bg = fw_get_db_post_option($pid, 'label_bg', '#f00');

                if (!empty($label_txt)) {
                    $style = array(
                        'color' => 'color: ' . esc_attr($label_color),
                        'background-color' => 'background-color: ' . esc_attr($label_bg),
                    );

                    $label = '<span class="zoa-product-label" style="' . implode('; ', $style) . '">' . esc_html($label_txt) . '</span>';
                }
            }

            // out of stock label
            if ($product->is_type('variable')) {
                $count_in_stock = 0;
                $variation_ids = $product->get_children(); // Get product variation IDs

                foreach ($variation_ids as $variation_id) {
                    $variation = wc_get_product($variation_id);
                    if ($variation->is_in_stock())
                        $count_in_stock++;
                }

                $is_soldout = true;
                foreach ($product->get_available_variations() as $variation) {
                    if ($variation['is_in_stock'])
                        $is_soldout = false;
                }

                if ((!$product->is_in_stock() && $count_in_stock == 0) || $is_soldout) {
                    $label = '<span class="zoa-product-label sold-out-label">' . esc_html__('Sold out', 'zoa') . '</span>';
                }
            } else {
                if (!$product->is_in_stock()) {
                    $label = '<span class="zoa-product-label sold-out-label">' . esc_html__('Sold out', 'zoa') . '</span>';
                }
            }

            return $label;
        }
    }

    /**
     * change woocommerce forms wrapper element
     *
     */
    if (!function_exists('woocommerce_form_field')) {

        /**
         * Outputs a checkout/address form field.
         *
         * @param string $key Key.
         * @param mixed  $args Arguments.
         * @param string $value (default: null).
         * @return string
         */
        function woocommerce_form_field($key, $args, $value = null) {
            $defaults = array(
                'type' => 'text',
                'label' => '',
                'description' => '',
                'placeholder' => '',
                'maxlength' => false,
                'required' => false,
                'autocomplete' => false,
                'id' => $key,
                'class' => array(),
                'label_class' => array(),
                'input_class' => array(),
                'return' => false,
                'options' => array(),
                'custom_attributes' => array(),
                'validate' => array(),
                'default' => '',
                'autofocus' => '',
                'priority' => '',
            );

            $args = wp_parse_args($args, $defaults);
            $args = apply_filters('woocommerce_form_field_args', $args, $key, $value);

            if ($args['required']) {
                $args['class'][] = 'validate-required';
                $required = '&nbsp;<abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
            } else {
                $required = '&nbsp;<span class="optional">(' . esc_html__('optional', 'woocommerce') . ')</span>';
            }

            if (is_string($args['label_class'])) {
                $args['label_class'] = array($args['label_class']);
            }

            if (is_null($value)) {
                $value = $args['default'];
            }

            // Custom attribute handling.
            $custom_attributes = array();
            $args['custom_attributes'] = array_filter((array) $args['custom_attributes'], 'strlen');

            if ($args['maxlength']) {
                $args['custom_attributes']['maxlength'] = absint($args['maxlength']);
            }

            if (!empty($args['autocomplete'])) {
                $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
            }

            if (true === $args['autofocus']) {
                $args['custom_attributes']['autofocus'] = 'autofocus';
            }

            if ($args['description']) {
                $args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
            }

            if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
                foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
                    $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
                }
            }

            if (!empty($args['validate'])) {
                foreach ($args['validate'] as $validate) {
                    $args['class'][] = 'validate-' . $validate;
                }
            }

            $field = '';
            $label_id = $args['id'];
            $sort = $args['priority'] ? $args['priority'] : '';
            $field_container = '<div class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '"><div class="field-wrapper">%3$s</div></div>';

            switch ($args['type']) {
                case 'country':
                    $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

                    if (1 === count($countries)) {

                        $field .= '<strong>' . current(array_values($countries)) . '</strong>';

                        $field .= '<input type="hidden" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="' . current(array_keys($countries)) . '" ' . implode(' ', $custom_attributes) . ' class="country_to_state" readonly="readonly" />';
                    } else {

                        $field = '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="country_to_state country_select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . '><option value="">' . esc_html__('Select a country&hellip;', 'woocommerce') . '</option>';

                        foreach ($countries as $ckey => $cvalue) {
                            $field .= '<option value="' . esc_attr($ckey) . '" ' . selected($value, $ckey, false) . '>' . $cvalue . '</option>';
                        }

                        $field .= '</select>';

                        $field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__('Update country', 'woocommerce') . '">' . esc_html__('Update country', 'woocommerce') . '</button></noscript>';
                    }

                    break;
                case 'state':
                    /* Get country this state field is representing */
                    $for_country = isset($args['country']) ? $args['country'] : WC()->checkout->get_value('billing_state' === $key ? 'billing_country' : 'shipping_country');
                    $states = WC()->countries->get_states($for_country);

                    if (is_array($states) && empty($states)) {

                        $field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

                        $field .= '<input type="hidden" class="hidden" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="" ' . implode(' ', $custom_attributes) . ' placeholder="' . esc_attr($args['placeholder']) . '" readonly="readonly" />';
                    } elseif (!is_null($for_country) && is_array($states)) {

                        $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="state_select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' data-placeholder="' . esc_attr($args['placeholder']) . '">
                    <option value="">' . esc_html__('Select a state&hellip;', 'woocommerce') . '</option>';

                        foreach ($states as $ckey => $cvalue) {
                            $field .= '<option value="' . esc_attr($ckey) . '" ' . selected($value, $ckey, false) . '>' . $cvalue . '</option>';
                        }

                        $field .= '</select>';
                    } else {

                        $field .= '<input type="text" class="input-text ' . esc_attr(implode(' ', $args['input_class'])) . '" value="' . esc_attr($value) . '"  placeholder="' . esc_attr($args['placeholder']) . '" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" ' . implode(' ', $custom_attributes) . ' />';
                    }

                    break;
                case 'textarea':
                    $field .= '<textarea name="' . esc_attr($key) . '" class="input-text ' . esc_attr(implode(' ', $args['input_class'])) . '" id="' . esc_attr($args['id']) . '" placeholder="' . esc_attr($args['placeholder']) . '" ' . (empty($args['custom_attributes']['rows']) ? ' rows="2"' : '') . (empty($args['custom_attributes']['cols']) ? ' cols="5"' : '') . implode(' ', $custom_attributes) . '>' . esc_textarea($value) . '</textarea>';

                    break;
                case 'checkbox':
                    $field = '<label class="checkbox ' . implode(' ', $args['label_class']) . '" ' . implode(' ', $custom_attributes) . '>
                    <input type="' . esc_attr($args['type']) . '" class="input-checkbox ' . esc_attr(implode(' ', $args['input_class'])) . '" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="1" ' . checked($value, 1, false) . ' /> ' . $args['label'] . $required . '</label>';

                    break;
                case 'text':
                case 'password':
                case 'datetime':
                case 'datetime-local':
                case 'date':
                case 'month':
                case 'time':
                case 'week':
                case 'number':
                case 'email':
                case 'url':
                case 'tel':
                    $field .= '<input type="' . esc_attr($args['type']) . '" class="input-text ' . esc_attr(implode(' ', $args['input_class'])) . '" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" placeholder="' . esc_attr($args['placeholder']) . '"  value="' . esc_attr($value) . '" ' . implode(' ', $custom_attributes) . ' />';

                    break;
                case 'select':
                    $field = '';
                    $options = '';

                    if (!empty($args['options'])) {
                        foreach ($args['options'] as $option_key => $option_text) {
                            if ('' === $option_key) {
                                // If we have a blank option, select2 needs a placeholder.
                                if (empty($args['placeholder'])) {
                                    $args['placeholder'] = $option_text ? $option_text : __('Choose an option', 'woocommerce');
                                }
                                $custom_attributes[] = 'data-allow_clear="true"';
                            }
                            $options .= '<option value="' . esc_attr($option_key) . '" ' . selected($value, $option_key, false) . '>' . esc_attr($option_text) . '</option>';
                        }

                        $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' data-placeholder="' . esc_attr($args['placeholder']) . '">
                        ' . $options . '
                    </select>';
                    }

                    break;
                case 'radio':
                    $label_id = current(array_keys($args['options']));

                    if (!empty($args['options'])) {
                        foreach ($args['options'] as $option_key => $option_text) {
                            $field .= '<input type="radio" class="input-radio ' . esc_attr(implode(' ', $args['input_class'])) . '" value="' . esc_attr($option_key) . '" name="' . esc_attr($key) . '" ' . implode(' ', $custom_attributes) . ' id="' . esc_attr($args['id']) . '_' . esc_attr($option_key) . '"' . checked($value, $option_key, false) . ' />';
                            $field .= '<label for="' . esc_attr($args['id']) . '_' . esc_attr($option_key) . '" class="radio ' . implode(' ', $args['label_class']) . '">' . $option_text . '</label>';
                        }
                    }

                    break;
            }

            if (!empty($field)) {
                $field_html = '';
                if ($label_id == 'billing_email') {
                    $args['label'] = __('Email', 'zoa');
                }
                if ($label_id == 'billing_phone') {
                    $args['label'] = __('Phone', 'zoa');
                }
                if ($args['label'] && 'checkbox' !== $args['type']) {
                    $field_html .= '<label for="' . esc_attr($label_id) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . $required . '</label>';
                }
                //check CAE-16 qty >=2 && CP22-DEC-FGT-01 = 1 && CP22-DEC-FGT-02=1
                $pass_cae_16 = 0;
                $pass_cpp01 = false;
                $pass_cpp02 = false;
                $cp22n0v = false;
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $product = wc_get_product($cart_item['product_id']);
                    if (strtoupper($product->get_sku()) == 'CAE-16') {
                        $pass_cae_16 += $cart_item['quantity'];
                    }
                    if (strtoupper($product->get_sku()) == 'CP22-DEC-FGT-01' && $cart_item['quantity'] == 1) {
                        $pass_cpp01 = true;
                    }
                    if (strtoupper($product->get_sku()) == 'CP22-DEC-FGT-02' && $cart_item['quantity'] == 1) {
                        $pass_cpp02 = true;
                    }
                    if (strtoupper($product->get_sku()) == 'CP-22NOV-FGT-03') {
                        $cp22n0v = true;
                    }
                }
                if ($pass_cae_16 >= 2 && $pass_cpp01 == true && $pass_cpp02 == true) {
                    if ($label_id == 'order_comments') {
                        $current_time = current_time('timestamp');
                        $from = date_i18n('2022-10-22 17:00:00');
                        $to = date_i18n('2022-11-01 23:59:00');
                        if ($current_time >= strtotime($from) && $current_time <= strtotime($to)) {
                            $field_html .= '<span class="label-help_text">どのカラーにシュシュ、ブラ刺繍を入れるかをご記入ください</span>';
                        }
                    }
                } else {
                    //check exist cp22-dec-fgt-02
                    $exist_cp22 = false;
                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $product = wc_get_product($cart_item['product_id']);
                        if (strtoupper($product->get_sku()) == 'CP22-DEC-FGT-02') {
                            $exist_cp22 = true;
                            break;
                        }
                    }

                    if ($label_id == 'order_comments' && $exist_cp22 == true) {
                        $current_time = current_time('timestamp');
                        $from = date_i18n('2022-10-22 17:00:00');
                        $to = date_i18n('2022-11-01 23:59:00');
                        if ($current_time >= strtotime($from) && $current_time <= strtotime($to)) {
                            $field_html .= '<span class="label-help_text">※ブラ刺繍特典の英字1文字を以下にご記入ください。</span>';
                        }
                    }
                }
                if ($label_id == 'order_comments' && $cp22n0v == true) {
                    $field_html .= '<span class="label-help_text">シルクポーチに刺繍するご希望のイニシャル文字(英語で1文字)をご記入ください。</span>';
                }
                //end
                $field_html .= '<span class="woocommerce-input-wrapper">' . $field;

                if ($args['description']) {
                    $field_html .= '<span class="description" id="' . esc_attr($args['id']) . '-description" aria-hidden="true">' . wp_kses_post($args['description']) . '</span>';
                }

                $field_html .= '</span>';

                $container_class = esc_attr(implode(' ', $args['class']));
                $container_id = esc_attr($args['id']) . '_field';
                $field = sprintf($field_container, $container_class, $container_id, $field_html);
            }

            /**
             * Filter by type.
             */
            $field = apply_filters('woocommerce_form_field_' . $args['type'], $field, $key, $args, $value);

            /**
             * General filter on form fields.
             *
             * @since 3.4.0
             */
            $field = apply_filters('woocommerce_form_field', $field, $key, $args, $value);

            if ($args['return']) {
                return $field;
            } else {
                echo $field; // WPCS: XSS ok.
            }
        }
    }

    /**
     * Display field value on the order edit page
     */
    add_action('woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1);
    function my_custom_checkout_field_display_admin_order_meta($order) {
        echo '<p><strong>' . __('Phone From Checkout Form', 'zoa') . ':</strong> ' . get_post_meta($order->get_id(), '_shipping_phone', true) . '</p>';
    }

    add_filter('woocommerce_general_settings', 'general_settings_shop_phone');
    function general_settings_shop_phone($settings) {
        $key = 0;

        foreach ($settings as $values) {
            $new_settings[$key] = $values;
            $key++;

            // Inserting array just after the post code in "Store Address" section
            if ($values['id'] == 'woocommerce_store_postcode') {
                $new_settings[$key] = array(
                    'title' => __('Phone Number'),
                    'desc' => __('Optional phone number of your business office'),
                    'id' => 'woocommerce_store_phone',
                    'default' => '',
                    'type' => 'text',
                    'desc_tip' => true, // or false
                );
                $key++;
            }
        }
        return $new_settings;
    }

    add_filter('woocommerce_get_privacy_policy_text', 'zoa_woocommerce_get_privacy_policy_text', 10, 2);
    function zoa_woocommerce_get_privacy_policy_text($text, $type) {
        $text = __($text, 'zoa');

        $term_page_id = PAGE_TERM_ID;
        $page_term = get_post($term_page_id);
        $html_term = '<a target="_blank" href="' . get_permalink($page_term) . '">' . __('利用規約', 'zoa') . '</a>';

        $privacy_page_id = PAGE_PRIVACY_ID;
        $page_privacy = get_post($privacy_page_id);
        $html_privacy = '<a target="_blank" href="' . get_permalink($page_privacy) . '">' . __('プライバシーポリシー', 'zoa') . '</a>';
        //added Apr14 2020
        $cancelpolicy_page_id = PAGE_CANCEL_ID;
        $page_cancel = get_post($privacy_page_id);
        $html_cancel = '<a target="_blank" href="' . get_permalink($page_cancel) . '">' . __('キャンセルポリシー', 'zoa') . '</a>';

        $text = str_replace('[term_conditions]', $html_term, $text);
        $text = str_replace('[privacy_policy]', $html_privacy, $text);
        $text = str_replace('[cancel_policy]', $html_cancel, $text); //added Apr14 2020
        return $text;
    }

    add_filter('woocommerce_account_menu_items', 'zoa_add_my_account_links');
    function zoa_add_my_account_links($menu_links) {

        $logout_link = $menu_links['customer-logout'];
        unset($menu_links['customer-logout']);
        unset($menu_links['wishlist']);
        $menu_links['wishlist'] = __('My Wishlist', 'zoa');
        $menu_links['appointment'] = __('My Appointments', 'zoa');
        $menu_links['customer-logout'] = $logout_link;

        return $menu_links;
    }

    /**
     * Add endpoint
     */
    add_action('init', 'zoa_add_my_account_endpoint');
    function zoa_add_my_account_endpoint() {
        add_rewrite_endpoint('appointment', EP_ROOT | EP_PAGES);
        add_rewrite_endpoint('appointment-detail', EP_ROOT | EP_PAGES);
    }

    add_action('woocommerce_account_appointment_endpoint', 'zoa_appointment_endpoint_content');
    function zoa_appointment_endpoint_content() {
        get_template_part('template-parts/content', 'my-booked-appointments');
    }

    add_action('woocommerce_account_appointment-detail_endpoint', 'zoa_appointment_detail_endpoint_content');
    function zoa_appointment_detail_endpoint_content() {
        get_template_part('template-parts/content', 'appointment-detail');
    }

    function zoa_get_checkout_privacy_policy_text($type) {
        ob_start();
        wc_privacy_policy_text($type);
        $policy_text = ob_get_contents();
        ob_end_clean();
        return strip_tags($policy_text);
    }

    add_action('woocommerce_checkout_terms_and_conditions', 'zoa_wc_checkout_privacy_policy_text', 1);
    function zoa_wc_checkout_privacy_policy_text() {
        remove_action('woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20);
        echo '<div class="woocommerce-privacy-policy-text form-row">';
        zoa_get_checkout_privacy_policy_text('checkout');
        echo '</div>';
    }

    add_action('woocommerce_register_form', 'zoa_wc_registration_privacy_policy_text', 1);
    function zoa_wc_registration_privacy_policy_text() {
        remove_action('woocommerce_register_form', 'wc_registration_privacy_policy_text', 20);
        echo '<div class="woocommerce-privacy-policy-text form-row">';
        zoa_get_checkout_privacy_policy_text('registration');
        echo '</div>';
    }

    add_filter('gettext', 'remove_admin_stuff', 20, 3);
    function remove_admin_stuff($translated_text, $untranslated_text, $domain) {
        if ($translated_text == 'Account details changed successfully.') {
            $translated_text = 'アカウント情報が更新されました';
        }
        return $translated_text;
    }

    add_action('woocommerce_after_add_to_cart_button', 'after_cart_button');
    function after_cart_button() {
        global $post;
        if ( $post && 'product' == $post->post_type ) {
            $html = get_field('html', $post->ID);
            if (!empty($html)) {
                echo '<div class="after-cart-content">';
                echo do_shortcode($html);
                echo '</div>';
            }
            // show html from series
            $series_pro = get_the_terms($post->ID, 'series');
            $queried_object = get_queried_object(); 
            if(isset($series_pro[0])){
                $series_campaign=get_field('series_campaign', $series_pro[0]->term_id);
                if(!empty($series_campaign['html_series'])&&!empty($series_campaign['html_series_schedule_start'])&&!empty($series_campaign['html_series_schedule_end'])){
                    $current_time = current_time('timestamp');
                    if ($current_time >= strtotime($series_campaign['html_series_schedule_start']) && $current_time <= strtotime($series_campaign['html_series_schedule_end'])) {
                        echo '<div class="after-cart-content">';
                        echo do_shortcode($series_campaign['html_series']);
                        echo '</div>';
                    }
                }
            }
            // show html from category
            $series_pro_cat = get_the_terms($post->ID, 'product_cat');
            if(isset($series_pro_cat[0])){
                $series_campaign_cat=get_field('series_campaign', $series_pro_cat[0]->term_id);
                if(!empty($series_campaign_cat['html_series'])&&!empty($series_campaign_cat['html_series_schedule_start'])&&!empty($series_campaign_cat['html_series_schedule_end'])){
                    $current_time = current_time('timestamp');
                    if ($current_time >= strtotime($series_campaign_cat['html_series_schedule_start']) && $current_time <= strtotime($series_campaign_cat['html_series_schedule_end'])) {
                        echo '<div class="after-cart-content">';
                        echo do_shortcode($series_campaign_cat['html_series']);
                        echo '</div>';
                    }
                }
            }
            // show html from tag
            $product_tags = get_the_terms($post->ID, 'product_tag');
            if(!empty($product_tags)){
                foreach ($product_tags as $tag) {
                    $campaign_description=get_field('campaign_description', $tag->term_id);
                    $campaign_start=get_field('campaign_start', $tag->term_id);
                    $campaign_end=get_field('campaign_end', $tag->term_id);
                    if(!empty($campaign_description)&&!empty($campaign_start)&&!empty($campaign_end)){
                        $current_time = current_time('timestamp');
                        if ($current_time >= strtotime($campaign_start) && $current_time <= strtotime($campaign_end)) {
                            echo '<div class="after-cart-content">';
                            echo do_shortcode($campaign_description);
                            echo '</div>';
                        }
                    }
                }

            }
        }
    }

    // hide other shipping method if free shipping available
    add_filter('woocommerce_package_rates', 'ch_my_hide_shipping_when_free_is_available', 999);
    function ch_my_hide_shipping_when_free_is_available($rates) {
        $free = array();
        foreach ($rates as $rate_id => $rate) {
            if ('free_shipping' === $rate->method_id) {
                $free[$rate_id] = $rate;
                break;
            }
        }
        return !empty($free) ? $free : $rates;
    }

    // hide exhibition tag product in shop page or search page
    add_action('pre_get_posts', 'ch_search_filter', 9999);
    function ch_search_filter($query)
    {
        if (!$query->is_admin && $query->is_search && $query->is_main_query()) {
            $page_ids = get_posts(array(
                'post_type' => 'product',
                'numberposts' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
                'tax_query' => array(
                    'relation' => 'OR',
                    //    array(
                    //        'taxonomy' => 'product_tag',
                    //        'field' => 'slug',
                    //        'terms' => 'exhibition',
                    //        'operator' => 'IN',
                    //    ),
                    array(
                        'taxonomy' => 'product_cat',
                        'terms' => array('familysale'),
                        'field' => 'slug',
                        'operator' => 'IN',
                    ),
                ),
            ));
            if (!empty($page_ids)) {
                $query->set('post__not_in', $page_ids);
            }
        }
    }

    add_filter('woof_products_query', 'ch_woof_products_query', 9999);
    function ch_woof_products_query($wr) {
        $page_ids = get_posts(array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'tax_query' => array(
                'relation' => 'OR',
                //    array(
                //        'taxonomy' => 'product_tag',
                //        'field' => 'slug',
                //        'terms' => 'exhibition',
                //        'operator' => 'IN',
                //    ),
                array(
                    'taxonomy' => 'product_cat',
                    'terms' => array('familysale'),
                    'field' => 'slug',
                    'operator' => 'IN',
                ),
            ),
        ));
        if (!empty($page_ids) && !isset($_REQUEST['product_tag']) && !is_page('special-event')) {
            $wr['post__not_in'] = $page_ids;
            return $wr;
        } else {
            return $wr;
        }
    }

    add_action('template_redirect', 'redirect_page_shop_if_only_gift_box', 999);
    function redirect_page_shop_if_only_gift_box() {
        if (is_cart() || is_checkout()) {
            global $woocommerce;
            $items = $woocommerce->cart->get_cart();
            if (!empty($items) && count($items) == 1) {
                $product_gift_wrapper_id = get_gift_box_product_id();
                if ($product_gift_wrapper_id > 0) {
                    $gift_wrapper = wc_get_product($product_gift_wrapper_id);
                    if (is_object($gift_wrapper)) {
                        $gift_wrapper_id = $gift_wrapper->get_parent_id();
                    }
                } else {
                    $product_gift_wrapper_id = 0;
                    $gift_wrapper_id = 0;
                }
                foreach ($items as $key => $item) {
                    if ($item['product_id'] == $product_gift_wrapper_id || $item['product_id'] == $gift_wrapper_id) {
                        wc_add_notice(__('Cart has only gift box, so you can not checkout. Thank you.', 'zoa'), 'error');
                        exit(wp_redirect(home_url('shop-all'), 301));
                    }
                }
            }
        }
    }

    add_filter('body_class', 'ch_theme_custom_class');
    function ch_theme_custom_class($classes) {
        if ( function_exists('is_product') && is_product()) {
            global $post;
            $new_swatch = get_post_meta($post->ID, '_swatch_type_options', true);
            $swatch_type = 'default';
            if (isset($new_swatch) && !empty($new_swatch)) {
                foreach ($new_swatch as $options) {
                    $options['type'] != 'default';
                    $swatch_type = $options['type'];
                    break;
                }
            }
            if ($swatch_type != 'default') {
                $classes[] = 'new_swatch';
            }
            $ch_product = wc_get_product($post->ID);
            if (is_object($ch_product) && $ch_product->is_type('bundle')) {
                $classes[] = 'new_swatch';
            }
            $hide = function_exists('get_field') ? get_field('hide', $post->ID) : '';
            if(isset($hide) && 'yes' == $hide ) {
                $classes[]='hide_out_of_stock';
            }
        }
        return $classes;
    }

    /**
     * Change the default country on the checkout for non-existing users only when locale is Japanese
     */
    add_filter('default_checkout_billing_country', 'change_default_checkout_country', 10, 1);
    add_filter('default_checkout_shipping_country', 'change_default_checkout_country', 10, 1);
    function change_default_checkout_country($country) {
        // If the user already exists, don't override country
        if (WC()->customer->get_is_paying_customer()) {
            return $country;
        }

        // Check if the locale is Japanese
        if ( 'ja' === get_locale() ) {
            return 'JP'; // Override default to Japan only for Japanese locale
        }
        return ''; // Return empty string for other locales to make it unselected
    }

    add_action('woocommerce_created_customer', 'action_woocommerce_created_customer', 10, 3);
    function action_woocommerce_created_customer($customer_id, $new_customer_data, $password_generated) {
        update_user_meta($customer_id, 'billing_email', $_POST['email']);
    }

    add_filter('woocommerce_shipping_free_shipping_is_available', 'ch_my_free_shipping_reconnect_series', 20);
    function ch_my_free_shipping_reconnect_series($is_available) {
        global $woocommerce;
        $current_time = current_time('timestamp');
        $end_time = date_i18n('2021-06-21 00:00:00');
        if ($current_time < strtotime($end_time)) {
            $series = 'Reconnect';
            // get cart contents
            $cart_items = $woocommerce->cart->get_cart();

            // loop through the items looking for one in the eligible array
            foreach ($cart_items as $key => $item) {
                $series_pro = get_the_terms($item['product_id'], 'series');
                $slug_seri = $series_pro[0]->name;
                if ($slug_seri == $series) {
                    return true;
                }
            }
        }

        // nothing found return the default value
        return $is_available;
    }

    /* TOP BAR */

    add_action('init', 'shop_topbar_changes');
    function shop_topbar_changes() {
        remove_action('woocommerce_before_shop_loop', 'zoa_result_count', 20);
        remove_action('woocommerce_before_shop_loop', 'zoa_catalog_ordering', 30);
    }

    // Display dropdown
    add_action('restrict_manage_posts', 'zoa_add_html_above_table', 50);
    function zoa_add_html_above_table() {
        global $typenow;

        if ('product' != $typenow || !is_admin()) {
            return;
        }
        $series = get_terms(array(
            'taxonomy' => 'series',
            'hide_empty' => true,
        ));
    ?>
        <span id="series_type_filter_wrap">
            <select name="series_type_filter" id="series_type_filter">
                <option value=""><?php _e('All Series', 'zoa'); ?></option>
                <?php foreach ($series as $serie) : ?>
                    <option value="<?php echo $serie->slug ?>" <?php echo isset($_REQUEST['series_type_filter']) && $_REQUEST['series_type_filter'] == $serie->slug ? 'selected' : '' ?>><?php echo $serie->name; ?></option>
                <?php endforeach; ?>
            </select>
        </span>
    <?php
    }

    add_filter('parse_query', 'zoa_product_filter');
    function zoa_product_filter($query) {
        global $pagenow;
        if (isset($_GET['series_type_filter'])) {
            $serie = $_GET['series_type_filter'];
        } else {
            $serie = '';
        }
        if (isset($_GET['post_type']) && 'product' === $_GET['post_type'] && $serie && is_admin() && $pagenow == 'edit.php') {
            $query->query_vars['tax_query'] = array(
                array(
                    'taxonomy' => 'series',
                    'field' => 'slug',
                    'terms' => array($serie)
                )
            );
        }
    }

    /**
     * rewrite news post
     * @param string $post_link
     * @param number $id
     * @return string $post_link
     */
    add_filter('pre_post_link', 'zoa_news_post_link', 1, 3);
    function zoa_news_post_link($post_link, $id = 0) {
        $post = get_post($id);
        if (is_object($post) && $post->post_type == 'post') {
            $terms = wp_get_object_terms($post->ID, 'category');
            foreach ($terms as $term) {
                if (in_array($term->slug, array('info', 'events'))) {
                    $post_link = str_replace('%category%', 'news/%category%', $post_link);
                    return $post_link;
                    break;
                }
            }
        }
        return $post_link;
    }

    /* show sku in wishlist */
    add_filter('woocommerce_in_cartproduct_obj_title', 'wdm_test', 10, 2);
    function wdm_test($product_title, $product) {
        if (is_a($product, "WC_Product_Variation")) {
            $parent_id = $product->get_parent_id();
            $parent = get_product($parent_id);
            $product_test = get_product($product->variation_id);
            $product_title = $parent->name;
            $product_jatitle = get_the_subtitle($parent_id);
            $attributes = $product->get_attributes();

            $html = '<div class="mini-product__item mini-product__name-ja small-text"><a href="' . esc_url(get_permalink(apply_filters('woocommerce_in_cart_product', $parent->id))) . '">' . $product_jatitle . '</a></div>' .
                '<div class="mini-product__item mini-product__name p5">
                    <a href="' . esc_url(get_permalink(apply_filters('woocommerce_in_cart_product', $parent->id))) . '">' . $product_title . '</a>
                </div>';

            foreach ($attributes as $attribute_key => $attribute_value) {
                $display_key = wc_attribute_label($attribute_key, $product);
                $display_value = $attribute_value;

                if (taxonomy_exists($attribute_key)) {
                    $term = get_term_by('slug', $attribute_value, $attribute_key);
                    if (!is_wp_error($term) && is_object($term) && $term->name) {
                        $display_value = $term->name;
                    }
                }
                $html .= '<div class="mini-product__item mini-product__attribute">
                    <span class="label variation-color">' . $display_key . ':</span>
                    <span class="value variation-color">' . $display_value . '</span>
                </div>';
            }

            $html .= '<p class="mini-product__item mini-product__id light-copy">SKU #' . $product_test->get_sku() . '</p>';
            return $html;
        } elseif (is_a($product, 'WC_Product')) {
            $product_test = new WC_Product($product->id);

            return '<div class="mini-product__item mini-product__name-ja small-text"><a href="' . esc_url(get_permalink(apply_filters('woocommerce_in_cart_product', $product->id))) . '">' . get_post_meta($product->id, '_custom_product_text_field', true) . '</a></div>' .
                '<div class="mini-product__item mini-product__name p5">
        <a href="' . esc_url(get_permalink(apply_filters('woocommerce_in_cart_product', $product->id))) . '">
            ' . $product_title . '
        </a>
        </div>' .
                '<p class="mini-product__item mini-product__id light-copy">SKU #' . $product_test->get_sku() . '</p>';
        } else {
            return $product_title;
        }
    }

    add_filter('manage_edit-product_columns', 'zoa_manage_product_columns', 1000, 1);
    function zoa_manage_product_columns($columns) {
        $columns = (is_array($columns)) ? $columns : array();
        $columns['product_tag'] = __('Series', 'zoa');
        $product_tag_pos = array_search('product_tag', array_keys($columns));
        unset($columns['product_tag']);
        $columns = insertAtSpecificIndex($columns, array('product_series' => __('Series', 'zoa')), $product_tag_pos);
        return $columns;
    }

    add_filter('manage_product_posts_custom_column', 'zoa_modify_product_column', 1000, 2);
    function zoa_modify_product_column($column, $postid) {
        if ( 'product_series' == $column ) {
            $terms = get_the_terms($postid, 'series');
            $series = array();
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $series[] = '<a href="' . site_url('/wp-admin/edit.php?post_type=product&series_type_filter=arabella') . '">' . $term->name . '</a>';
                }
            }
            echo implode(',', $series);
        }
        return $column;
    }

    add_filter('woocommerce_placeholder_img_src', 'zoa_woocommerce_placeholder_img_src', 100, 1);
    function zoa_woocommerce_placeholder_img_src($image_url) {
        return get_stylesheet_directory_uri() . '/images/no_image.jpg';
    }

    add_filter('document_title_parts', 'zoa_wp_title', 10, 1);
    function zoa_wp_title($title_parts) {
        global $wp;
        $request = explode('/', $wp->request);
        if ($request[0] == 'my-account') {
            if (isset($request[2]) && $request[2] == 'billing') {
                $title_parts['title'] = __('Edit Billing Address', 'zoa');
            } elseif (isset($request[2]) && $request[2] == 'shipping') {
                $title_parts['title'] = __('Edit Shipping Address', 'zoa');
            }
        }

        return $title_parts;
    }

    // add_action('woocommerce_before_shop_loop', 'zoa_woocommerce_before_shop_loop');
    add_action('woocommerce_after_single_product_summary', 'zoa_woocommerce_before_shop_loop');
    add_action('woocommerce_before_shop_loop_item', 'zoa_woocommerce_before_shop_loop');
    function zoa_woocommerce_before_shop_loop() {
        // die(current_filter());
        // hide parent theme panel
        remove_action('woocommerce_before_shop_loop_item_title', 'zoa_wrap_product_image', 10);
        // add new panel
        add_action('woocommerce_before_shop_loop_item_title', 'zoa_wrap_product_image_override', 10);
        // hide parent theme product title
        remove_action('woocommerce_shop_loop_item_title', 'zoa_template_loop_product_title', 10);
        // add new panel
        add_action('woocommerce_shop_loop_item_title', 'woo_template_loop_product_title', 10);
        // remove add to cart button box
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    }

    // add_filter('woo_variation_gallery_image_inner_html' , 'woo_variation_gallery_image_inner_html_callback',10,2);
    function woo_variation_gallery_image_inner_html_callback($inner_html, $attachment_id) {
        $dom = new DOMDocument;
        $dom->loadHTML($inner_html);
        $imgs = $dom->getElementsByTagName('img');
        foreach ($imgs as $img) {

            if (strpos($img->getAttribute('class'), 'attachment-woocommerce_single') !== false) {
                $img->setAttribute('class', $img->getAttribute('class') . ' woo-variation-gallery-trigger');
            } else if (!$img->hasAttribute('class')) {
                $img->addAttribute('class', 'woo-variation-gallery-trigger');
            }
        }
        $inner_html = $dom->saveHTML();
        return $inner_html;
    }

    // add wishlist to myaccount menu
    add_filter('woocommerce_account_menu_items', 'zoa_woocommerce_wishlist_menu_items');
    function zoa_woocommerce_wishlist_menu_items($items) {
        $items['wishlist'] = $items['my-wishlist'];
        unset($items['my-wishlist']);
        return $items;
    }

    // render wishlist page content
    add_action('woocommerce_account_wishlist_endpoint', 'zoa_wishlist_endpoint_content');
    function zoa_wishlist_endpoint_content() {
        echo do_shortcode('[ti_wishlistsview]');
    }

    add_filter('tinvwl_wishlist_item_add_to_cart', 'zoa_wishlist_item_add_to_cart', 10, 3);
    function zoa_wishlist_item_add_to_cart($text, $wl_product, $_product) {
        global $product;
        // store global product data.
        $_product_tmp = $product;
        // override global product data.
        $product = $_product;
        if (apply_filters('tinvwl_product_add_to_cart_need_redirect', false, $product, $product->get_permalink(), $wl_product) && in_array((version_compare(WC_VERSION, '3.0.0', '<') ? $product->product_type : $product->get_type()), array(
            'variable',
            'variable-subscription'
        ))) {
            $text = $product->add_to_cart_text();
            $text = 'Select Options';
        }

        return $text;
    }

    // add_filter( 'woocommerce_product_add_to_cart_text' , 'zoa_woocommerce_product_add_to_cart_text' );
    function zoa_woocommerce_product_add_to_cart_text() {
        global $product;
        if ( $product ) {
            switch ( $product->product_type ) {
                case 'external':
                    return __('View Item', 'woocommerce');
                    break;
                case 'grouped':
                    return __('View Group', 'woocommerce');
                    break;
                case 'simple':
                    return __('Add to Cart', 'woocommerce');
                    break;
                case 'variable':
                    return __('Select Options', 'woocommerce');
                    break;
                default:
                    return __('Read more', 'woocommerce');
            }
        }
        return __('Read more', 'woocommerce');
    }

    add_filter('wp_redirect', 'zoa_modify_specific_wp_redirect', 100, 2);
    function zoa_modify_specific_wp_redirect($location, $status) {
        if (isset($_POST['action']) && $_POST['action'] == 'save_account_details') {
            $location = home_url('my-account/edit-account/');
        }
        return $location;
    }

    // add myaccount endpoint for wishlist
    add_action('init', 'zoa_wishlist_endpoint');
    function zoa_wishlist_endpoint() {
        add_rewrite_endpoint('wishlist', EP_ROOT | EP_PAGES);
    }

    function zoa_woocommerce_checkout($atts = array()) {
        $wraper = array(
            'class' => 'woocommerce woocommerce_thanks_wrapper row flex-justify-between',
            'before' => null,
            'after' => null,
        );
        return WC_Shortcodes::shortcode_wrapper(array('WC_Shortcode_Checkout', 'output'), $atts, $wraper);
    }

    add_action('wp', 'zoa_change_checkout_shortcode');
    function zoa_change_checkout_shortcode() {
        global $wp;
        if (isset($wp->query_vars['order-received']) && $wp->query_vars['order-received']) {
            remove_shortcode('woocommerce_checkout');
            add_shortcode('woocommerce_checkout', 'zoa_woocommerce_checkout');
        }
    }

    // change BACS fields
    // original fields from plugins/woocommerce/includes/gateways/bacs/class-wc-gateway-bacs.php

    add_filter('woocommerce_bacs_account_fields', 'custom_bacs_fields');
    function custom_bacs_fields() {
        global $wpdb;
        $account_details = get_option(
            'woocommerce_bacs_accounts',
            array(
                array(
                    'account_name' => get_option('account_name'),
                    'account_number' => get_option('account_number'),
                    'sort_code' => get_option('sort_code'),
                    'bank_name' => get_option('bank_name'),
                    'iban' => get_option('iban'),
                    'bic' => get_option('bic')
                )
            )
        );

        $account_fields = array(
            'bank_name' => array(
                'label' => __('Bank Name', 'zoa'),
                'value' => $account_details[0]['bank_name']
            ),
            'branch_name' => array(
                'label' => __('Branch name', 'zoa'),
                'value' => $account_details[0]['sort_code']
            ),
            'account_type' => array(
                'label' => __('Account Type', 'zoa'),
                'value' => $account_details[0]['iban']
            ),
            'account_number' => array(
                'label' => __('Account Number', 'zoa'),
                'value' => $account_details[0]['account_number']
            ),
            'account_name' => array(
                'label' => __('Account Name', 'zoa'),
                'value' => $account_details[0]['account_name']
            )
        );

        return $account_fields;
    }

    add_action('woocommerce_before_shipping_calculator', 'zoa_woocommerce_before_shipping_calculator');
    add_action('woocommerce_review_order_after_shipping', 'zoa_woocommerce_before_shipping_calculator');
    function zoa_woocommerce_before_shipping_calculator() {
        // Ensure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Get the number of products in the shipping package by delivery date
        $num_cart_product = count(getShippingPackageByDeliverDate());

        // Check if free shipping is applied, and return early if conditions are met
        if ($num_cart_product <= 1 || isMemberFreeShip() || isGrantedCouponFreeShipping()) {
            return;
        }

        // Safely retrieve the session value for shipping_delivery_option, with fallback to default
        $shipping_delivery_option = isset($_SESSION['shipping_delivery_option']) ? $_SESSION['shipping_delivery_option'] : '';

        // Set checked options for radio buttons
        $selected_option_1 = !$shipping_delivery_option ? 'checked' : ($shipping_delivery_option == 1 ? 'checked' : '');
        $selected_option_2 = $shipping_delivery_option == 2 ? 'checked' : '';

        // Render the shipping delivery options on the checkout page
        echo '<div class="choose_shipping_delivery_option_wraper order__summary__row">
            <div><input type="radio" id="shipping_delivery_option_1" name="shipping_delivery_option" value="1" ' . $selected_option_1 . '/><label for="shipping_delivery_option_1" class="label">' . __('Ship together', 'zoa') . '</label></div>
            <div><input id="shipping_delivery_option_2" type="radio" name="shipping_delivery_option" value="2" ' . $selected_option_2 . '/><label for="shipping_delivery_option_2" class="label">' . __('Ship according to completion date', 'zoa') . '(' . vsprintf(__('%s pkg.', 'zoa'), array($num_cart_product)) . ')' . '</label></div> 
        </div>';
    }

    add_filter('wf_woocommerce_shipping_pro_shipping_costs', 'zoa_wf_woocommerce_shipping_pro_shipping_costs', 10, 3);
    function zoa_wf_woocommerce_shipping_pro_shipping_costs($costs, $shipping_pro, $package) {
        // file_put_contents(dirname(__FILE__).'/ch_shipping_costs.txt', json_encode($costs));
        
        // // Note: need setting "Calculation Mode" if setting of woocommerce-shipping-pro is: "Calculate Shipping Cost per Line Item"
        // // And "Minimum or Maximum Cost" setting is: "Choose the Maximum Cost"

        $shipping_develiery_date = getShippingPackageByDeliverDate();
        $shipping_delivery_option = WC()->session->get('shipping_delivery_option');

        $zones = PH_Shipping_Pro_Common_Methods::ph_find_zone($package);

        if ($shipping_delivery_option > 1) {
            $shipping_pro->calc_mode_strategy = WF_Calc_Strategy::get_calc_mode('per_line_item_max_cost', $shipping_pro->rate_matrix);
        }

        $destination_country = $package['destination']['country'];
        $destination_state = $package['destination']['state'];
        $destination_city = $package['destination']['city'];
        $destination_postcode = $package['destination']['postcode'];
        $shipping_classes = $shipping_pro->calc_mode_strategy->wf_find_shipping_classes($package);
        $product_categories = $shipping_pro->calc_mode_strategy->wf_find_product_category($package);

        $rules = PH_Shipping_Pro_Common_Methods::ph_filter_rules($zones, $destination_country, $destination_state, $destination_city, $destination_postcode, $shipping_classes, $product_categories, $package, $shipping_pro->calc_mode_strategy, $shipping_pro->settings);
        $tmp_rules = $rules;
        // Remove the item in reset_product_category if it has specific
        foreach ($rules as $rule_index => $rule) {
            foreach ($tmp_rules as $rule_tmp_index => $rule_tmp) {
                if ($rule_index === $rule_tmp_index) {
                    continue;
                }
                if (!empty($rule['product_category'])) {
                    //pass
                } else {
                    $rule['product_category'] = array();
                }
                if (!empty($rule_tmp['product_category'])) {
                    //pass
                } else {
                    $rule_tmp['product_category'] = array();
                }
                if (!empty($rule['shipping_class'])) {
                    //pass
                } else {
                    $rule['shipping_class'] = array();
                }
                if (!empty($rule_tmp['shipping_class'])) {
                    //pass
                } else {
                    $rule_tmp['shipping_class'] = array();
                }

                $need_check_category = $rule['product_category'] && in_array('rest_product_category', $rule['product_category']) && !in_array('rest_product_category', $rule_tmp['product_category']);
                $need_check_class = $rule['shipping_class'] && in_array('rest_shipping_class', $rule['shipping_class']) && !in_array('rest_shipping_class', $rule_tmp['shipping_class']);

                if ($need_check_category || $need_check_class) {
                    foreach ($rule['item_ids'] as $rule_item_index => $rule_item_id) {
                        if (in_array($rule_item_id, $rule_tmp['item_ids'])) {
                            // Remove the rest item_id if has specific
                            unset($rules[$rule_index]['item_ids'][$rule_item_index]);
                        }
                    }
                }
            }
        }

        $tmp_rules = $rules;
        foreach ($tmp_rules as $rule_index => $rule) {
            if (count($rules[$rule_index]['item_ids']) == 0) {
                unset($rules[$rule_index]);
            }
        }

        // If strategy is per cateogry => Just keep like per order, pick the greatest cost
        if (is_a($shipping_pro->calc_mode_strategy, 'WF_Calc_Per_Category') && count($rules) > 1) {
            $rule_costs = array_map(function ($rule) {
                return ($rule['fee'] || 0) + ($rule['cost'] || 0);
            }, $rules);
            $max_cost = max($rule_costs);
            $max_cost_index = array_search($max_cost, $rule_costs);

            // Set only 1 category for rules to calcualte like per order
            $rules[$max_cost_index]['item_ids'] = [end($rules[$max_cost_index]['item_ids'])];

            foreach ($rule_costs as $rule_index => $rule_cost) {
                if ($rule_index !== $max_cost_index) {
                    unset($rules[$rule_index]);
                }
            }
        }

        if (!$shipping_develiery_date || !$shipping_delivery_option || count($shipping_develiery_date) <= 1 || $shipping_delivery_option == 1) {
            $costs = PH_Shipping_Pro_Common_Methods::ph_calc_cost($rules, $package, $shipping_pro->calc_mode_strategy, $shipping_pro->row_selection_choice, $shipping_pro->title, $shipping_pro->settings);
            //get max shipping
            $max = 0;
            $key = '';
            if (!empty($costs[""]['cost'])) {
                foreach ($costs[""]['cost'] as $key_s => $value_s) {
                    if ($value_s > $max) {
                        $max = $value_s;
                        $key = $key_s;
                    }
                }
                if ($max > 0) {
                    unset($costs[""]['cost']);
                    $costs[""]['cost'][$key] = $max;
                }
            }
            return $costs;
        }

        $delete_items = [];
        $rules_delivery_date = [];
        foreach ($rules as $rule_index => $rule) {
            foreach ($rule['item_ids'] as $rule_item_index => $rule_item_id) {
                $item_delivery_date = getItemDeliveryDate($package, $rule_item_id);
                //file_put_contents(dirname(__FILE__).'/delivery_date.txt', $item_delivery_date."\n",FILE_APPEND);
                if (!$rules_delivery_date[$item_delivery_date]) {
                    $rules_delivery_date[$item_delivery_date] = [];

                    $rules_delivery_date[$item_delivery_date][$rule_item_id] = [
                        'item_id' => $rule_item_id,
                        'fee' => (float)$rule['fee'],
                        'cost' => (float)$rule['cost'],
                    ];
                } else {
                    // Item has same delivery date, now check greater will be keep
                    foreach ($rules_delivery_date[$item_delivery_date] as $item_info) {
                        if ((float)$item_info['fee'] + (float)$item_info['cost'] < (float)$rule['fee'] + (float)$rule['cost']) {
                            unset($rules_delivery_date[$item_delivery_date][$item_info['item_id']]);
                            $delete_items[$item_info['item_id']] = $item_info['item_id'];
                            $rules_delivery_date[$item_delivery_date][$rule_item_id] = [
                                'item_id' => $rule_item_id,
                                'fee' => (float)$rule['fee'],
                                'cost' => (float)$rule['cost'],
                            ];
                        } else {
                            $delete_items[$rule_item_id] = $rule_item_id;
                        }
                    }
                }
            }
        }

        // Remove same date but smaller in rules items_ids
        foreach ($delete_items as $delete_item_id) {
            $tmp_rules = $rules;
            foreach ($tmp_rules as $rule_index => $rule) {
                $item_index = array_search($delete_item_id, $rule['item_ids']);
                if ($item_index !== false) {
                    unset($rules[$rule_index]['item_ids'][$item_index]);
                }

                if (count($rules[$rule_index]['item_ids']) == 0) {
                    unset($rules[$rule_index]);
                }
            }
        }

        $costs = PH_Shipping_Pro_Common_Methods::ph_calc_cost($rules, $package, $shipping_pro->calc_mode_strategy, $shipping_pro->row_selection_choice, $shipping_pro->title, $shipping_pro->settings);
        // get total shipping of all items in cart
        $total = 0;
        $max = 0;
        $key = '';
        if (!empty($costs['']['cost'])) {
            foreach ($costs['']['cost'] as $key_s => $value_s) {
                $total += $value_s;
                if ($value_s > $max) {
                    $max = $value_s;
                    $key = $key_s;
                }
            }
            if ($total > 0) {
                unset($costs['']['cost']);
                $costs['']['cost'][$key] = $total;
            }
        }
        return $costs;
    }

    add_filter('woocommerce_package_rates', 'zoa_free_shipping_with_rank_member', 10, 2);
    function zoa_free_shipping_with_rank_member($rates, $package) {
        $free_ship = array();
        if (isMemberFreeShip()|| isGrantedCouponFreeShipping()||is_order_familysale()) {
            foreach ($rates as $rate_key => $rate) {
                $rate->label = __('Free shipping', 'zoa');
                $free_ship[$rate_key] = $rate;
                $free_ship[$rate_key]->cost = 0;
                $rates = $free_ship;
                return $rates;
            }
        }

        return $rates;
    }

    add_action('woocommerce_thankyou', 'zoa_thank_you', 10, 1);
    function zoa_thank_you($order_id) {
        if (isset($_SESSION['shipping_delivery_option'])) {
            update_post_meta($order_id, 'shipping_delivery_option', $_SESSION['shipping_delivery_option']);
            unset($_SESSION['shipping_delivery_option']);
        }
    }

    add_filter('woocommerce_mail_content', 'zoa_woocommerce_mail_content', 1000, 1);
    function zoa_woocommerce_mail_content($message) {
        $message = str_replace('<h2 class="wc-bacs-bank-details-heading">', '<h2 class="wc-bacs-bank-details-heading" style="text-align: center; font-style: normal;">', $message);
        $message = str_replace('<h3 class="wc-bacs-bank-details-account-name">', '<h3 class="wc-bacs-bank-details-account-name" style="text-align: center; font-style: normal; display: none;">', $message);
        $message = str_replace('<ul class="wc-bacs-bank-details order_details bacs_details">', '<ul class="wc-bacs-bank-details order_details bacs_details" style="text-align: center; list-style-type: none; padding-left: 0;">', $message);

        // check is order invoice email content
        if (strpos($message, 'checkout/order-pay') !== false) {
            $message .= '<div>order invoice page</div>';
            // Get order id by order pay url
            $matches = array();
            preg_match('/checkout\/order-pay\/([0-9]+)\//', $message, $matches);
            if (!empty($matches)) {
                $order_id = $matches[1];
                $order = new WC_Order($order_id);
                if ('bacs' == $order->get_payment_method()) {
                    $message = str_replace('href="' . site_url() . '/checkout/order-pay', ' style="display: none;" href="' . site_url() . '/checkout/order-pay', $message);
                }
            }
        }
        if (strpos($message, 'order_tracking_template') !== false) {
            preg_match('/order_tracking_template_(\d+)/', $message, $matches);
            if (!empty($matches) && isset($matches[1])) {
                $order_id = $matches[1];
                $tracking_template = zoa_order_tracking_email_template(array(), $order_id);
                $message = str_replace('{' . $matches[0] . '}', $tracking_template, $message);
            }
        }
        return $message;
    }

    add_action('woocommerce_email_order_details', 'zoa_woocommerce_email_order_details', 1, 4);
    function zoa_woocommerce_email_order_details($order, $sent_to_admin = false, $plain_text = false, $email = '') {
        // Add tracking in complete email
        if ('completed' == $order->status && $email->template_html == 'emails/customer-completed-order.php') {
            $tracking_number = get_post_meta($order->id, '_aftership_tracking_number', true);
            $tracking_provider = get_post_meta($order->id, '_aftership_tracking_provider_name', true);

            if ($tracking_provider) {
                echo '<div style="margin: 10px 0;">' . __('Your order was shipped via ', 'wc_aftership') . $tracking_provider . '</div>';
            }
            if ($tracking_number) {
                echo '<div style="margin: 10px 0;">' . __('Tracking number is ', 'wc_aftership') . $tracking_number . '</div>';
            }
        }
    }

    add_filter('woocommerce_validate_postcode', 'zoa_woocommerce_validate_postcode', 1000, 3);
    function zoa_woocommerce_validate_postcode($valid, $postcode, $country) {
        switch ($country) {
            case 'JP':
                $valid = (bool) preg_match('/^([0-9]{7})$/', $postcode);
                break;
        }
        return $valid;
    }

    add_filter('woocommerce_format_postcode', 'zoa_woocommerce_format_postcode', 1000, 2);
    function zoa_woocommerce_format_postcode($postcode, $country) {
        switch ($country) {
            case 'JP':
                $postcode = str_replace('-', '', $postcode);
                break;
        }
        return $postcode;
    }

    add_shortcode('order_tracking_template', 'zoa_order_tracking_email_template');
    function zoa_order_tracking_email_template($atts, $order_id = null) {
        global $order;
        $atts = shortcode_atts(array(
            'order_id' => null,
        ), $atts);

        // 8414
        $order_id = $order_id ? $order_id : ($order ? $order->get_id() : $atts['order_id']);
        if ($order_id && class_exists('WCST_Tracking_info_displayer')) {
            ob_start();
            $order = wc_get_order($order_id);
            $tracking = new WCST_Tracking_info_displayer();
            $tracking->email_shipping_details($order);
            $tracking_html = ob_get_contents();
            ob_end_clean();
            return $tracking_html;
        }
    }

    // remove rating sort
    add_filter('woocommerce_catalog_orderby', 'ch_woocommerce_catalog_orderby', 20);
    function ch_woocommerce_catalog_orderby($orderby) {
        unset($orderby["rating"]);
        return $orderby;
    }


    // add_action('wp_ajax_start_manual_square_to_woo_sync', 'zoa_woo_square_plugin_start_manual_square_to_woo_sync', 1);
    // add_action('wp_ajax_nopriv_start_manual_square_to_woo_sync', 'zoa_woo_square_plugin_start_manual_square_to_woo_sync', 1);
    function zoa_woo_square_plugin_start_manual_square_to_woo_sync() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://connect.squareup.com/v2/locations/' . get_option('woo_square_location_id') . '/transactions/ZFJuQKpmBzvmajZjDtovsWieV',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . get_option('woo_square_access_token'),
                "cache-control: no-cache",
                "Accept: application/json",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $customers_obj = json_decode($response, true);
        pr($customers_obj);
        die;
        if (!empty($response)) {
            foreach ($customers_obj['customers'] as $customer) {
                $user_email = $customer['email_address'];
                $user_first_name = $customer['given_name'];
                $user_last_name = $customer['family_name'];

                if ($user_email && is_email($user_email) && !email_exists($user_email)) {
                    $user_id = register_new_user($user_email, $user_email);

                    update_user_meta($user_id, 'square_customer_id', $customer['id']);
                    update_user_meta($user_id, 'first_name', $user_first_name);
                    update_user_meta($user_id, 'last_name', $user_last_name);

                    // Update billing info
                    update_user_meta($user_id, 'billing_first_name', $user_first_name);
                    update_user_meta($user_id, 'billing_last_name', $user_last_name);
                    update_user_meta($user_id, 'billing_email', $customer['email_address']);
                    update_user_meta($user_id, 'billing_phone', $customer['phone_number']);

                    if (isset($customer['address'])) {
                        update_user_meta($user_id, 'billing_address_1', $customer['address']['address_line_1']);
                        update_user_meta($user_id, 'billing_address_2', $customer['address']['address_line_2']);
                        update_user_meta($user_id, 'billing_city', $customer['address']['locality']);
                        update_user_meta($user_id, 'billing_state', $customer['address']['administrative_district_level_1']);
                        update_user_meta($user_id, 'billing_postcode', $customer['address']['postal_code']);
                        update_user_meta($user_id, 'billing_country', $customer['address']['country']);
                    }
                    // Update shipping info
                    update_user_meta($user_id, 'shipping_first_name', $user_first_name);
                    update_user_meta($user_id, 'shipping_last_name', $user_last_name);
                    update_user_meta($user_id, 'shipping_email', $customer['email_address']);
                    update_user_meta($user_id, 'shipping_phone', $customer['phone_number']);

                    if (isset($customer['address'])) {
                        update_user_meta($user_id, 'shipping_address_1', $customer['address']['address_line_1']);
                        update_user_meta($user_id, 'shipping_address_2', $customer['address']['address_line_2']);
                        update_user_meta($user_id, 'shipping_city', $customer['address']['locality']);
                        update_user_meta($user_id, 'shipping_state', $customer['address']['administrative_district_level_1']);
                        update_user_meta($user_id, 'shipping_postcode', $customer['address']['postal_code']);
                        update_user_meta($user_id, 'shipping_country', $customer['address']['country']);
                    }
                }
            }
        }
    }

    add_filter('woocommerce_general_settings', 'add_thank_order_text_message');
    function add_thank_order_text_message($settings) {

        $updated_settings = array();
        foreach ($settings as $section) {

            // at the bottom of the General Options section
            if (
                isset($section['id']) && 'general_options' == $section['id'] &&
                isset($section['type']) && 'sectionend' == $section['type']
            ) {

                $updated_settings[] = array(
                    'name' => __('Thanks order message', 'zoa'),
                    'desc_tip' => __('Thanks order message, after checkout success', 'zoa'),
                    'id' => 'woocommerce_order_thanks_message',
                    'type' => 'textarea',
                    'css' => 'min-width: 300px;min-height: 150px;',
                    'std' => '', // WC < 2.0
                    'default' => '', // WC >= 2.0
                    'desc' => __('', 'zoa'),
                );
            }

            $updated_settings[] = $section;
        }

        return $updated_settings;
    }


    add_action('woocommerce_register_form_end', 'ch_add_register_form_field');
    function ch_add_register_form_field() {
    ?>
        <div class="domain_notice">
            <p>
                ご登録完了時に自動返信メールを送信しております。<br><strong>メールアドレスの間違い</strong>や<strong>迷惑メール設定</strong>などにより、<strong>お客様がメールを受け取れない場合もございます</strong>ので、ご登録前に一度設定をご確認くださいますようお願い申しあげます。
            </p>
            <ul class="notice-list">
                <li><strong>指定受信拒否などお使いのメール設定に問題がある場合</strong>
                    <p>
                        スマートフォンやフィーチャーフォンにて、ドメイン指定受信拒否を設定されている場合には当店のメールが受信することができません。お手数ですが、「@chiyono-anne.com」の受信許可設定をお願いいたします。<br>また指定アドレス設定の場合には「hello@chiyono-anne.com」の受信許可設定をお願いいたします。
                    </p>
                </li>
            </ul>
        </div>
    <?php
    }

    add_filter('woocommerce_add_to_cart_validation', 'ch_validate_specific_product_in_cart', 1, 5);
    function ch_validate_specific_product_in_cart($valid, $product_id, $quantity = 0, $variation_id = 0, $variations = null) {
        $gift_card_category_slug = 'mwb_wgm_giftcard';
        foreach (WC()->cart->get_cart() as $cart_item) {
            $message = "ギフトカードは納期が異なる商品と同時に注文ができません。申し訳ございませんが、別々にご注文ください。";
            if (is_product_in_cat($cart_item['product_id'], $gift_card_category_slug) && !is_product_in_cat($product_id, $gift_card_category_slug)) {
                wc_add_notice(__($message, 'zoa-child'), 'error');
                return false;
            } elseif (!is_product_in_cat($cart_item['product_id'], $gift_card_category_slug) && is_product_in_cat($product_id, $gift_card_category_slug)) {
                wc_add_notice(__($message, 'zoa-child'), 'error');
                return false;
            }
        }
        return $valid;
    }

    /**
     * Process the checkout
     * */
    add_action('woocommerce_checkout_process', 'ch_validate_terms_delivery_field_process');
    function ch_validate_terms_delivery_field_process() {
        // Check if its not set add an error.
        if (!$_POST['terms_delivery']) {
            wc_add_notice(__('Please agree with delivery estimated delivery date', 'zoa'), 'error');
        }
    }

    add_action('save_post', 'mv_save_wc_order_other_fields', 10, 1);
    if (!function_exists('mv_save_wc_order_other_fields')) {

        function mv_save_wc_order_other_fields($post_id) {
            if (!isset($_POST['ch_order_type_mv_other_meta_field_nonce'])) {
                return $post_id;
            }
            $nonce = $_REQUEST['ch_order_type_mv_other_meta_field_nonce'];
            if (!wp_verify_nonce($nonce)) {
                return $post_id;
            }
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }
            if ('page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            } else {
                if (!current_user_can('edit_post', $post_id)) {
                    return $post_id;
                }
            }
            update_post_meta($post_id, 'ch_order_type', $_POST['ch_order_type']);
            update_post_meta($post_id, 'order_sub_type', $_POST['order_sub_type']);
        }
    }

    // Adding Order Type admin shop_order pages
    add_action('add_meta_boxes', 'ch_order_type_mv_add_meta_boxes');
    if (!function_exists('ch_order_type_mv_add_meta_boxes')) {
        function ch_order_type_mv_add_meta_boxes() {
            add_meta_box('mv_other_fields', __('Order Type', 'woocommerce'), 'ch_order_type_mv_add_other_fields', 'shop_order', 'side', 'core');
        }
    }

    if (!function_exists('ch_order_type_mv_add_other_fields')) {
        function ch_order_type_mv_add_other_fields() {
            global $post;
            $meta_field_data = get_post_meta($post->ID, 'ch_order_type', true) ? get_post_meta($post->ID, 'ch_order_type', true) : '';
            $order_sub_type = get_post_meta($post->ID, 'order_sub_type', true) ? get_post_meta($post->ID, 'order_sub_type', true) : '';
            $o_type = array(
                'オンラインストア',
                'アトリエ',
                '展示会',
                '旧オンラインストア',
                '百貨店',
                'メール',
                '受注会',
                'ダイレクト',
                'その他'
            );
            $str_option = '';
            foreach ($o_type as $value) {
                $selected = '';
                if ($value == $meta_field_data) {
                    $selected = 'selected="true"';
                }
                $str_option .= '<option ' . $selected . ' value="' . $value . '">' . $value . '</option>';
            }
            echo '<input type="hidden" name="ch_order_type_mv_other_meta_field_nonce" value="' . wp_create_nonce() . '">
                <p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
                    <select name="ch_order_type">' . $str_option . '</select></p>';
                echo '<p>' . __('Order sub type', 'zoa') . '</p>';
                echo '<input type="text" name="order_sub_type" value="' . $order_sub_type . '"/>';
        }
    }

    add_action('request', 'request_applicant_filter');
    function request_applicant_filter($request) {
        global $pagenow;
        $current_page = isset($_GET['post_type']) ? $_GET['post_type'] : '';
        if (is_admin() && 'shop_order' == $current_page && 'edit.php' == $pagenow && isset($_REQUEST['ch_order_type'])) {
            if (!empty($_REQUEST['ch_order_type']) && $_REQUEST['ch_order_type'] != '-1' && isset($_REQUEST['filter_action'])) {
                $ch_order_type = $_REQUEST['ch_order_type'];
                $request['meta_key'] = 'ch_order_type';
                $request['meta_value'] = $ch_order_type;
            }
        }
        return $request;
    }

    add_action('restrict_manage_posts', 'add_extra_ch_order_type');
    function add_extra_ch_order_type($post_type) {

        global $wpdb;
        if ($post_type !== 'shop_order') {
            return;
        }
        // run only when need update order type for orders by Woo already. /chiyono/wp-admin/edit.php?post_type=shop_order&ch_update_orders_exist=1
        if (isset($_REQUEST['ch_update_orders_exist'])) {
            $query = new WC_Order_Query(array(
                'limit' => 1000,
                'orderby' => 'date',
                'order' => 'DESC',
                'return' => 'ids',
            ));
            $orders = $query->get_orders();
            if (!empty($orders)) {
                foreach ($orders as $value_id) {
                    $meta_field_data = get_post_meta($value_id, 'ch_order_type', true) ? get_post_meta($value_id, 'ch_order_type', true) : '';
                    if (empty($meta_field_data)) {
                        update_post_meta($value_id, 'ch_order_type', 'オンラインストア');
                    } else {
                        $meta_field_data = trim($meta_field_data);
                        update_post_meta($value_id, 'ch_order_type', $meta_field_data);
                    }
                }
            }
        }
        $o_type = array(
            'オンラインストア',
            'アトリエ',
            '展示会',
            '旧オンラインストア',
            '百貨店',
            'メール',
            '受注会',
            'ダイレクト',
            'その他'
        );
        $results = $o_type;

        if (empty($results))
            return;

        if (isset($_GET['ch_order_type']) && $_GET['ch_order_type'] != '') {
            $selectedName = $_GET['ch_order_type'];
        } else {
            $selectedName = -1;
        }
        $ch_query = "SELECT count(ID) as total FROM {$wpdb->prefix}posts WHERE post_type ='shop_order' AND ID IN(SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='ch_order_type'";
        if (isset($_REQUEST['post_status'])) {
            $post_status = trim($_REQUEST['post_status']);
            if ($post_status != 'all') {
                $ch_query .= " AND post_status = '" . $post_status . "'";
            } else {
                $ch_query .= " AND post_status IN ('wc-pending','wc-processing','wc-on-hold','wc-completed','wc-cancelled','wc-refunded','wc-failed','wc-prepare-to-send','wc-sent-sample','wc-sample-returned','wc-completenotrefund') ";
            }
        }
        $options[] = sprintf('<option value="-1">%1$s</option>', __('All Order Type', 'zoa-child'));
        foreach ($results as $result) :
            $get_total = $wpdb->get_var(
                $wpdb->prepare($ch_query . " AND meta_value=%s)", $result)
            );
            $total = $get_total;
            if ($result == $selectedName) {
                $options[] = sprintf('<option value="%1$s" selected>%2$s</option>', esc_attr($result), $result . ' (' . $total . ')');
            } else {
                $options[] = sprintf('<option value="%1$s">%2$s</option>', esc_attr($result), $result . ' (' . $total . ')');
            }
        endforeach;

        echo '<select class="" id="ch_order_type" name="ch_order_type">';
        echo join("\n", $options);
        echo '</select>';
    }

    // use for new order
    add_action('woocommerce_thankyou', 'custom_woocommerce_auto_complete_order');
    function custom_woocommerce_auto_complete_order($order_id) {
        if (!$order_id) {
            return;
        }
        update_post_meta($order_id, 'ch_order_type', 'オンラインストア');
    }

    add_filter('woocommerce_ajax_variation_threshold', 'custom_wc_ajax_variation_threshold', 10, 2);
    function custom_wc_ajax_variation_threshold($int, $product) {
        return 200;
    }

    // define the woocommerce_process_registration_errors callback 
    add_filter('woocommerce_process_registration_errors', 'ch_filter_woocommerce_process_registration_errors', 10, 4);
    function ch_filter_woocommerce_process_registration_errors($validation_error, $username, $password, $email) {
        if (isset($_REQUEST['_wp_http_referer']) && $_REQUEST['_wp_http_referer'] == '/my-account/') {
            $exists = email_exists($_REQUEST['email']);
            if ($exists) {
                $url = home_url('/my-account/lost-password/?exist_email');
                wp_redirect($url, 301);
                exit;
            }
        }
        return $validation_error;
    }

    // Add class to theme body
    add_filter('body_class', 'custom_class');
    function custom_class($classes) {
        if ( function_exists('is_product') && is_product() ) {
            global $post;
            $terms = get_the_terms($post->ID, 'product_cat');
            $classes[] = $terms[0]->slug . '-single-product';
            $tags_arr = array();
            $tags = get_the_terms( $post->ID, 'product_tag' );
            if ( $tags && ! is_wp_error( $tags ) ) {
                $tags_arr = wp_list_pluck($tags, 'slug');
                if (in_array('digitalcard', $tags_arr)) {
                    $classes[] = 'digitalcard_tag';
                }
            }
        }
        return $classes;
    }

    // -----------------------
    // 1. Create extra tab under Reports / Orders

    add_filter('woocommerce_admin_reports', 'bbloomer_admin_add_report_orders_tab');
    function bbloomer_admin_add_report_orders_tab($reports) {
        $array = array(
            'sales_by_order_type' => array(
                'title' => 'Sales by Order type',
                'description' => '',
                'hide_title' => 1,
                'callback' => 'ch_yearly_sales_by_order_type'
            )
        );
        if (!empty($reports['orders']['reports'])) {
            $reports['orders']['reports'] = array_merge($reports['orders']['reports'], $array);
        } else {
            $reports['orders']['reports'] = $array;
        }

        return $reports;
    }

    add_filter('woocommerce_available_payment_gateways', 'conditionally_hide_payment_gateways', 100, 1);
    function conditionally_hide_payment_gateways($available_gateways) {
        $current_time = current_time('timestamp');
        $start_time = date_i18n('2020-10-19 01:30:00');
        $end_time = date_i18n('2020-10-19 06:00:00');
        if (strtotime($start_time) < $current_time && $current_time < strtotime($end_time)) {
            // Disable paydesign_cs
            if (isset($available_gateways['paydesign_cs'])) {
                unset($available_gateways['paydesign_cs']);
            }
        }
        return $available_gateways;
    }

    add_action('validate_password_reset', 'rsm_redirect_after_rest', 20, 2);
    function rsm_redirect_after_rest($errors, $user) {
        global $rp_cookie, $rp_path;
        if ((!$errors->get_error_code()) && isset($_POST['password_1']) && !empty($_POST['password_2'])) {
            reset_password($user, $_POST['password_1']);
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID);
            wp_redirect(home_url('my-account'));
            exit;
        }
    }

    add_action('woocommerce_register_form', 'bbloomer_add_registration_privacy_policy', 11);
    function bbloomer_add_registration_privacy_policy() {
        woocommerce_form_field('privacy_policy_reg', array(
            'type' => 'checkbox',
            'class' => array('form-row label-inline privacy_policy_reg label-inline form-indent'),
            'label_class' => array('form-row__inline-label control-label checkbox icon--tick'),
            'input_class' => array('input-checkbox input-control'),
            'required' => true,
            'label' => sprintf(__('I have read and accept the <a href="%s">Privacy Policy</a>', 'zoa'), '/privacy-policy'),
        ));
    }

    add_filter('woocommerce_process_registration_errors', 'process_registration_errors');
    function process_registration_errors($errors) {
        if (!is_checkout()) {
            if (!(int) isset($_POST['privacy_policy_reg'])) {
                $errors->add('privacy_policy_reg_error', __('Privacy Policy consent is required!', 'zoa'));
            }
        }
        return $errors;
    }

    add_action('woocommerce_register_form', 'add_birthday_to_register_form', 10);
    function add_birthday_to_register_form() {
        $user_id = $_REQUEST['user_id'] ? $_REQUEST['user_id'] : get_current_user_id();
        $user = get_userdata($user_id);
        $months = array(__('January', 'zoa'), __('February', 'zoa'), __('March', 'zoa'), __('April', 'zoa'), __('May', 'zoa'), __('June', 'zoa'), __('July', 'zoa'), __('August', 'zoa'), __('September', 'zoa'), __('October', 'zoa'), __('November', 'zoa'), __('December', 'zoa'));
        $default = array('day' => '', 'month' => '', 'year' => '',);
        $birth_date = wp_parse_args(get_the_author_meta('account_birth', $user->ID), $default);
    ?>
        <div class="form-row">
            <div class="field-wrapper">
                <label class="form-row__label" for="account_birth_month"><?php esc_html_e('Date of Birth', 'zoa'); ?></label>
                <div class="row row-dayofbirth">
                    <!-- New Birthday Field-->
                    <div class="col-12"><input readonly class="form-control" type="text" name="account_birth_data" id="account_birth" placeholder="YYYY-MM-DD"></div>
                    <!-- /New Birthday Field-->
                </div>
            </div>
        </div>
    <?php
    }

    // add_action('woocommerce_before_single_variation', 'insert_text_area', 99);
    function insert_text_area() {
        global $product;
        if (is_product() && get_the_ID() == 32443) {
            // echo '<p class="notion_bf">※1人1点までのご注文となります</p>';
        }
    }

    add_filter('woocommerce_coupon_is_valid', 'filter_woocommerce_coupon_is_valid', 10, 2);
    function filter_woocommerce_coupon_is_valid($true, $instance) {
        if (isset($_REQUEST['coupon_code']) && !empty($_REQUEST['coupon_code']) && $_REQUEST['wc-ajax'] == 'apply_coupon') {
            global $wpdb;
            $table_name = $wpdb->prefix . 'posts';
            $field_name = 'post_content';
            $prepared_statement = $wpdb->prepare("SELECT {$field_name} FROM {$table_name} WHERE  post_name = %s AND post_content LIKE 'GIFTCARD ORDER #%' AND post_type = 'shop_coupon'", strtolower($_REQUEST['coupon_code']));
            $values = $wpdb->get_col($prepared_statement);
            if (!empty($values) && !empty($values[0])) {
                $order_id = (int) filter_var($values[0], FILTER_SANITIZE_NUMBER_INT);
                if ($order_id > 0) {
                    $order = wc_get_order($order_id);
                    if ( $order != false ) {
                        $items = $order->get_items();
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                $product_id = $item->get_product_id();
                                if (in_array($product_id, arr_gift_card_products_use_offline())) {
                                    return false;
                                } else {
                                    return $true;
                                }
                            }
                        } else {
                            return $true;
                        }
                    } else {
                        return $true;
                    }
                } else {
                    return $true;
                }
            } else {
                return $true;
            }
        }
        return $true;
    }

    add_filter('woocommerce_product_get_price', 'custom_price', 10, 2);
    function custom_price($price, $product) {
        if (is_shop() || is_product_category() || is_product_tag() || is_product() || is_checkout() || is_cart()) {
            if (in_array($product->id, arr_gift_card_products_use_offline())) {
                $_SESSION['org_price'] = $price;
                return (float) $price - $price * 0.2; //20%
            } else {
                return $price;
            }
        } else {
            return $price;
        }
    }

    /**
     * Change price format from range to "From:"
     *
     * @param float $price
     * @param obj $product
     * @return str
     */
    add_filter('woocommerce_variable_sale_price_html', 'iconic_variable_price_format', 10, 2);
    add_filter('woocommerce_variable_price_html', 'iconic_variable_price_format', 10, 2);
    function iconic_variable_price_format($price, $product) {

        $prefix = sprintf('%s: ', __('From', 'iconic'));

        $min_price_regular = $product->get_variation_regular_price('min', true);
        $min_price_sale = $product->get_variation_sale_price('min', true);
        $max_price = $product->get_variation_price('max', true);
        $min_price = $product->get_variation_price('min', true);

        $price = ($min_price_sale == $min_price_regular) ?
            wc_price($min_price_regular) :
            '<del>' . wc_price($min_price_regular) . '</del>' . '<ins>' . wc_price($min_price_sale) . '</ins>';
        $product_id = $product->get_id();
        global $wpdb;
        $table_name = $wpdb->prefix . 'iconic_was_fees';

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE product_id = %d",
                $product_id
            ),
            ARRAY_A
        );

        $fees[$product_id] = array();
        if (!empty($results)) {
            foreach ($results as $result) {
                $fees[$product_id][$result['attribute']] = array_map('floatval', (array) maybe_unserialize($result['fees']));
            }
        }
        $is_empty_fee = true;
        //echo json_encode($fees[ $product_id ]);
        if (!empty($fees[$product_id])) {
            foreach ($fees[$product_id] as $key_att => $value_attr_fee) {
                $pref = substr($key_att, 0, 3);
                if (!empty($value_attr_fee) && $pref == 'pa_') {
                    foreach ($value_attr_fee as $key => $value_at) {
                        if (!empty($value_at)) {
                            $is_empty_fee = false;
                            break;
                        }
                    }
                }
                if ($is_empty_fee == false) {
                    break;
                }
            }
        }
        if ($min_price == $max_price) {
            if ( !$is_empty_fee ) {
                return sprintf('%s%s', $prefix, $price);
            } else {
                return $price;
            }
        } else {
            return sprintf('%s%s', $prefix, $price);
        }
    }

    // group product by delivery data and return total item by group delivery date
    function getDeliveryDateGroupInCart() {
        $res_delivery = array();
        $res_qty = array();
        foreach (WC()->cart->get_cart() as $cart_item) {
            if (isset($cart_item['bundled_items']) && !empty($cart_item['bundled_items'])) {
                continue; //this mean if it is bundle don't need count quantity of main product. only count child bundle.
            }
            $product_id = $cart_item['product_id'];

            $delivery_date = get_post_meta($product_id, 'deliver_date', true);
            $specific_deliver_date = get_post_meta($product_id, 'specific_deliver_date', true);
            $custom_field_type = get_post_meta($product_id, 'from_to', true);
            $from_to = '';
            if (!empty($custom_field_type)&& is_array($custom_field_type)) {
                $from_to = implode("", $custom_field_type);
            }
            $key = $delivery_date . $specific_deliver_date . $from_to;
            if (empty(trim($key))) {
                $product = wc_get_product($product_id);
                $shipping_class_id = $product->get_shipping_class_id();
                if(!empty($shipping_class_id) && $shipping_class_id>0){
                    $key=$shipping_class_id;
                }else{
                    $key = 0;
                }
            }
            $res_delivery[$key][] = $product_id;
            $res_qty[$key] += $cart_item['quantity'];
        }
        return array('delivery' => $res_delivery, 'qty' => $res_qty);
    }

    add_filter('woocommerce_product_related_products_heading', 'woocommerce_product_related_products_heading_en', 10, 2);
    function woocommerce_product_related_products_heading_en() {
        return __('Related Items', 'zoa');
    }

    add_filter('woocommerce_shipping_free_shipping_is_available', 'ch_free_shipping_cami_product', 20);
    function ch_free_shipping_cami_product($is_available) {
        global $woocommerce;
        $cart_items = $woocommerce->cart->get_cart();
        // file_put_contents(dirname(__FILE__).'/cart.txt', json_encode($cart_items));
        // cami-lacetote-bundle  AND cami-fallinlace
        $cami = array(2025311, 2025297);
        $is_cami = false;
        // loop through the items looking for one in the eligible array
        foreach ($cart_items as $key => $item) {
            $product_id = $item['product_id'];
            if (in_array($product_id, $cami)) {
                $is_cami = true;
                break;
            }
        }
        // wide-leg-trouser
        $wide = 8872;
        $is_wide = false;
        // loop through the items looking for one in the eligible array
        foreach ($cart_items as $key => $item) {
            $product_id = $item['product_id'];
            if ($product_id === $wide && isset($item['variation']['attribute_pa_color']) && $item['variation']['attribute_pa_color'] === 'white') {
                $is_wide = true;
                break;
            }
        }
        // silk-skirt
        $silk = 8911;
        $is_silk = false;
        // loop through the items looking for one in the eligible array
        foreach ($cart_items as $key => $item) {
            $product_id = $item['product_id'];
            if ($silk === $product_id && isset($item['variation']['attribute_pa_color']) && $item['variation']['attribute_pa_color'] === 'mocha') {
                $is_silk = true;
                break;
            }
        }

        if (($is_wide === true || $is_silk === true) && $is_cami === true) { //allow free shipping
            return true;
        }
        // nothing found return the default value
        return $is_available;
    }

    add_filter('woocommerce_product_cross_sells_products_heading', 'woocommerce_product_cross_sells_products_heading_en', 10, 2);
    function woocommerce_product_cross_sells_products_heading_en() {
        return __('Recommend Style with...', 'zoa');
    }

    add_filter('woocommerce_product_upsells_products_heading', 'woocommerce_product_upsells_products_heading_en', 10, 2);
    function woocommerce_product_upsells_products_heading_en() {
        return __('Recommend Style with...', 'zoa');
    }

    add_filter('woocommerce_upsell_display_args', 'zoa_child_column_upsell', 9999);
    function zoa_child_column_upsell($args) {
        $args['posts_per_page'] = 4;
        $args['columns'] = 4;
        return $args;
    }

    remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

    add_action('woocommerce_after_single_product_summary', 'ch_hide_show_related_upsell_products', 15);
    function ch_hide_show_related_upsell_products() {
        global $product;

        if (isset($product) && is_product()) {
            $upsells = version_compare(WC_VERSION, '3.0', '<') ? $product->get_upsells() : $product->get_upsell_ids();
            if (count($upsells) > 0) {
                woocommerce_upsell_display();
            } else {
                woocommerce_upsell_display();
                woocommerce_output_related_products();
            }
        }
    }

    add_action('woocommerce_before_add_to_cart_button', 'gift_options_product_page');
    function gift_options_product_page() {
        global $post;
        $product_id = $post->ID;
        if (!is_product_in_cat($product_id, 'mwb_wgm_giftcard') && !is_product_in_cat($product_id, 'special-service') && !is_product_in_cat($product_id, 'gift-option')) {
            $products = wc_get_products(array(
                'category' => array('gift-option'),
                'status' => array('publish'),
                'orderby' => 'date',
                'order' => 'ASC',
                'limit' => -1
            ));
            if (!empty($products)) {
        ?>
                <div class="gift_options_area">
                    <div class="show_gift form-row form-row__checkbox">
                        <label><input id="show_hide_gift_options" class="cb" type="checkbox" /><span>ギフトですか?</span></label>
                    </div>
                    <div class="gift_products" style="display: none;">
                        <div class="area_title"><?php esc_html_e('Gift Option', 'zoa'); ?></div>
                        <div class="area_helptext">追加したいオプションにチェックを入れると、カートに一緒に追加されます。</div>
                        <?php
                            foreach ($products as $product) {
                                $thumb_id = get_post_thumbnail_id($product->id);
                                $product_parent = wc_get_product($product->id);
                                $notice = get_field('notice_text_product', $product->id);
                                $vari = array();
                                if (is_object($product_parent) && $product_parent->is_type('variable')) {
                                    $vari = $product_parent->get_children();
                                }
                                $price = strip_tags(wc_price(wc_get_price_including_tax($product_parent)));
                                if (0 == $product_parent->get_price()) {
                                    $price = ' FREE';
                                }
                                if (isset($vari) && !empty($vari)) {
                                    $price = ' from ' . $price;
                                }
                        ?>
                            <div class="gp_option_item">
                                <div class="gp_thumb">
                                    <img class="img-responsive" src="<?php echo wp_get_attachment_image_src($thumb_id, 'medium')[0]; ?>" />
                                </div>
                                <div class="gp_title">
                                    <div class="form-row form-row__checkbox">
                                        <label>
                                            <input name="gp_product_id[]" class="cb" type="checkbox" value="<?php echo $product->id; ?>" />
                                            <?php
                                                echo '<span class="gp_opt__name">';
                                                echo '<span class="gp_opt__title">' . $product->name . '</span>';
                                                if (!empty($price)) {
                                                    echo '<span class="gp_opt__price">' . $price . '</span>';
                                                }
                                                echo '</span>';
                                            ?>
                                        </label>
                                    </div>

                                    <?php if (isset($vari) && !empty($vari)) : ?>
                                        <div class="form-row form-row__select">
                                            <select class="gp_atr" name="gp_atr">
                                                <?php foreach ($vari as $value) :
                                                    $obj = wc_get_product($value);
                                                ?>
                                                    <option price="<?php echo strip_tags(wc_price(wc_get_price_including_tax($obj))); ?>" value="<?php echo $obj->get_id(); ?>"><?php echo $obj->get_attributes()['box-size']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                        if (!empty($notice)) {
                                            echo '<div class="form-row gp_opt__desc">' . do_shortcode($notice) . '</div>';
                                        }
                                    ?>
                                </div>
                                <!--/gp_title-->
                            </div>
                        <?php } ?>
                    </div>
                </div>
        <?php
            }
        }
    }

    add_action('admin_init', 'wc_remove_admin_notice_template_files');
    function wc_remove_admin_notice_template_files() {
        if (class_exists('WC_Admin_Notices')) {
            WC_Admin_Notices::remove_notice('template_files');
        }
    }

    // note: this only run one time
    // add_action('wp_ajax_change_price_bulk', 'change_price_bulk');//Enable this when need again.
    function change_price_bulk() {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post__not_in' => array(7957), //NOT Neko Chan Wink Eye Mask
        );
        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'product_cat',
                'terms' => array('gift', 'special-service'), //NOT gift and tbyb category
                'field' => 'slug',
                'operator' => 'NOT IN',
            ),
            array(
                'taxonomy' => 'series',
                'field' => 'slug',
                'terms' => array('cabychiyonoanne', 'celebrationline', 'reconnect'), //NOT Series of Ca by Chiyono Anne, Celebration Line and Poolside
                'operator' => 'NOT IN',
            )
        );
        $products_query = new \WP_Query($args);
        if ($products_query->have_posts()) {
            while ($products_query->have_posts()) :
                $products_query->the_post();
                $product_id = get_the_ID();
                $product = wc_get_product($product_id);
                if ($product->is_type('variable')) {
                    $variation_ids = $product->get_children(); // Get product variation IDs
                    foreach ($variation_ids as $variation_id) {
                        $price_excl_tax = get_post_meta($variation_id, '_regular_price', true); //wc_get_price_excluding_tax($product);
                        if (empty($price_excl_tax) || $price_excl_tax <= 0) {
                            $price_excl_tax = get_post_meta($variation_id, '_price', true);
                        }
                        $price_excl_tax=(float)$price_excl_tax;
                        $new_price = $price_excl_tax + $price_excl_tax * 0.1;
                        update_post_meta($variation_id, '_regular_price', $new_price);
                        update_post_meta($variation_id, '_price', $new_price);
                        echo 'ID: ' . $variation_id . ' - Old Price: ' . $price_excl_tax . ' - New price: ' . $new_price . '<br/>';
                    }
                } else {
                    $price_excl_tax = get_post_meta($product_id, '_regular_price', true); //wc_get_price_excluding_tax($product);
                    if (empty($price_excl_tax) || $price_excl_tax <= 0) {
                        $price_excl_tax = get_post_meta($product_id, '_price', true);
                    }
                    $price_excl_tax=(float)$price_excl_tax;
                    $new_price = $price_excl_tax + $price_excl_tax * 0.1;
                    update_post_meta($product_id, '_regular_price', $new_price);
                    update_post_meta($product_id, '_price', $new_price);
                    echo 'ID: ' . $product_id . ' - Old Price: ' . $price_excl_tax . ' - New price: ' . $new_price . '<br/>';
                }

            endwhile;
            woocommerce_reset_loop();
        }
        echo 'completed!';
        exit();
    }
        
    // add_action( 'init', 'ch_wc_manual_refund' );//remove commend when need run again in future
    function ch_wc_manual_refund() {
        $order_id = (isset($_REQUEST['order_id']) && strlen($_REQUEST['order_id']) > 0) ? intval($_REQUEST['order_id']) : false;

        if ( $order_id && isset($_REQUEST['chien']) && 'refund' == $_REQUEST['chien'] ) {
            $order = wc_get_order($order_id);
            $amount = 5860;
            if ($order->get_remaining_refund_amount() >= $amount) {
                $refund = wc_create_refund_not_send_email(array(
                    'amount' => $amount,
                    'reason' => 'Royal 特典５％OFFクーポン適用の為',
                    'order_id' => $order_id,
                    'refund_payment' => false
                ));
                if (is_wp_error($refund)) {
                    if ($refund->get_error_message() == 'Invalid refund amount.') {
                        echo 'Refund requested exceeds remaining order balance of ' . $order->get_formatted_order_total();
                    } else {
                        echo $refund->get_error_message();
                    }
                } else {
                    echo $refund->get_id();
                }
            } else {
                echo 'Refund requested exceeds remaining order balance of ' . $order->get_formatted_order_total();
            }       
            exit();
        }
    }

    function wc_create_refund_not_send_email( $args = array() ) {
        $default_args = array(
            'amount'         => 0,
            'reason'         => null,
            'order_id'       => 0,
            'refund_id'      => 0,
            'line_items'     => array(),
            'refund_payment' => false,
            'restock_items'  => false,
        );

        try {
            $args  = wp_parse_args( $args, $default_args );
            $order = wc_get_order( $args['order_id'] );

            if ( ! $order ) {
                throw new Exception( __( 'Invalid order ID.', 'woocommerce' ) );
            }

            $remaining_refund_amount = $order->get_remaining_refund_amount();
            $remaining_refund_items  = $order->get_remaining_refund_items();
            $refund_item_count       = 0;
            $refund                  = new WC_Order_Refund( $args['refund_id'] );

            if ( 0 > $args['amount'] || $args['amount'] > $remaining_refund_amount ) {
                throw new Exception( __( 'Invalid refund amount.', 'woocommerce' ) );
            }

            $refund->set_currency( $order->get_currency() );
            $refund->set_amount( $args['amount'] );
            $refund->set_parent_id( absint( $args['order_id'] ) );
            $refund->set_refunded_by( get_current_user_id() ? get_current_user_id() : 1 );
            $refund->set_prices_include_tax( $order->get_prices_include_tax() );

            if ( ! is_null( $args['reason'] ) ) {
                $refund->set_reason( $args['reason'] );
            }

            // Negative line items.
            if ( count( $args['line_items'] ) > 0 ) {
                $items = $order->get_items( array( 'line_item', 'fee', 'shipping' ) );

                foreach ( $items as $item_id => $item ) {
                    if ( ! isset( $args['line_items'][ $item_id ] ) ) {
                        continue;
                    }

                    $qty          = isset( $args['line_items'][ $item_id ]['qty'] ) ? $args['line_items'][ $item_id ]['qty'] : 0;
                    $refund_total = $args['line_items'][ $item_id ]['refund_total'];
                    $refund_tax   = isset( $args['line_items'][ $item_id ]['refund_tax'] ) ? array_filter( (array) $args['line_items'][ $item_id ]['refund_tax'] ) : array();

                    if ( empty( $qty ) && empty( $refund_total ) && empty( $args['line_items'][ $item_id ]['refund_tax'] ) ) {
                        continue;
                    }

                    $class         = get_class( $item );
                    $refunded_item = new $class( $item );
                    $refunded_item->set_id( 0 );
                    $refunded_item->add_meta_data( '_refunded_item_id', $item_id, true );
                    $refunded_item->set_total( wc_format_refund_total( $refund_total ) );
                    $refunded_item->set_taxes(
                        array(
                            'total'    => array_map( 'wc_format_refund_total', $refund_tax ),
                            'subtotal' => array_map( 'wc_format_refund_total', $refund_tax ),
                        )
                    );

                    if ( is_callable( array( $refunded_item, 'set_subtotal' ) ) ) {
                        $refunded_item->set_subtotal( wc_format_refund_total( $refund_total ) );
                    }

                    if ( is_callable( array( $refunded_item, 'set_quantity' ) ) ) {
                        $refunded_item->set_quantity( $qty * -1 );
                    }

                    $refund->add_item( $refunded_item );
                    $refund_item_count += $qty;
                }
            }

            $refund->update_taxes();
            $refund->calculate_totals( false );
            $refund->set_total( $args['amount'] * -1 );

            // this should remain after update_taxes(), as this will save the order, and write the current date to the db
            // so we must wait until the order is persisted to set the date.
            if ( isset( $args['date_created'] ) ) {
                $refund->set_date_created( $args['date_created'] );
            }

            /**
             * Action hook to adjust refund before save.
             *
             * @since 3.0.0
             */
            do_action( 'woocommerce_create_refund', $refund, $args );

            if ( $refund->save() ) {
                if ( $args['refund_payment'] ) {
                    $result = wc_refund_payment( $order, $refund->get_amount(), $refund->get_reason() );

                    if ( is_wp_error( $result ) ) {
                        $refund->delete();
                        return $result;
                    }

                    $refund->set_refunded_payment( true );
                    $refund->save();
                }

                if ( $args['restock_items'] ) {
                    wc_restock_refunded_items( $order, $args['line_items'] );
                }
                
            }

            do_action( 'woocommerce_refund_created', $refund->get_id(), $args );
            do_action( 'woocommerce_order_refunded', $order->get_id(), $refund->get_id() );

        } catch ( Exception $e ) {
            if ( isset( $refund ) && is_a( $refund, 'WC_Order_Refund' ) ) {
                $refund->delete( true );
            }
            return new WP_Error( 'error', $e->getMessage() );
        }

        return $refund;
    }

    // save ch_fitting
    add_action('save_post', 'ch_fitting', 10, 1);
    if (!function_exists('ch_fitting')) {

        function ch_fitting($post_id) {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }
            if ('page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            } else {
                if (!current_user_can('edit_post', $post_id)) {
                    return $post_id;
                }
            }
            if (isset($_REQUEST['ch_fitting'])) {
                update_post_meta($post_id, 'ch_fitting', $_REQUEST['ch_fitting']);
            }
        }

    }

    add_action('add_meta_boxes', 'ch_fittingadd_meta_boxes');
    if (!function_exists('ch_fittingadd_meta_boxes')) {
        function ch_fittingadd_meta_boxes() {
            add_meta_box('ch_fittingadd_meta_boxes', __('Do you have experience with fittings by us?', 'zoa'), 'ch_fittingadd_meta_boxes_callback', 'shop_order', 'side', 'core');
        }
    }

    if (!function_exists('ch_fittingadd_meta_boxes_callback')) {
        function ch_fittingadd_meta_boxes_callback() {
            global $post;
            $ch_fitting_data = get_post_meta($post->ID, 'ch_fitting', true) ? get_post_meta($post->ID, 'ch_fitting', true) : '';
            $o_type = array(
                __('Never', 'zoa'),
                __('Yes, fitted by Chiyono', 'zoa'),
                __('Yes, fitted at T.B.Y.B', 'zoa'),
            );
            $str_option = '<option value="">' . __('Please select', 'zoa') . '</option>';
            foreach ($o_type as $value) {
                $selected = '';
                if ($value == $ch_fitting_data) {
                    $selected = 'selected="true"';
                }
                $str_option .= '<option ' . $selected . ' value="' . $value . '">' . $value . '</option>';
            }
            echo '<select name="ch_fitting">' . $str_option . '</select>';
        }
    }

    add_filter('woocommerce_get_order_item_totals', 'ch_fitting_woo_email', 10, 2);
    function ch_fitting_woo_email($total_rows, $order) {
        if ($order->has_status('shipped-gb') || $order->has_status('processing-gb')) {
            $label = __('Do you have experience with fittings by us?', 'zoa');
        } else {
            $label = __('Do you have experience with fittings by us?', 'zoa');
        }
        $value_ch_fitting = get_post_meta($order->id, 'ch_fitting', true);
        $new_total_rows = array();
        foreach ($total_rows as $key => $value) {
            $new_total_rows[$key] = $total_rows[$key];
            if ('order_total' == $key) {
                $new_total_rows['ch_fitting'] = array(
                    'label' => $label,
                    'value' => $value_ch_fitting,
                );
            }
        }

        return sizeof($new_total_rows) > 0 ? $new_total_rows : $total_rows;
    }
    // end

    add_filter('woocommerce_shipping_free_shipping_is_available', 'ch_free_shipping_for_special_dates', 20);
    function ch_free_shipping_for_special_dates($is_available) {
        //allow free shipping for all orders by start date and end date
        $current_time = current_time('timestamp');
        $start_time = date_i18n('2023-05-01 00:00:00');
        $end_time = date_i18n('2023-05-07 23:59:59');
        if ($current_time >= strtotime($start_time) && $current_time <= strtotime($end_time)) {
            return true;//this mean allow free shipping
        }
        // nothing found return the default value
        return $is_available;
    }


    add_action('personal_options_update', 'mailpoet_subscribe_on_edit_account');
    add_action('edit_user_profile_update', 'mailpoet_subscribe_on_edit_account');
    add_action('woocommerce_save_account_details', 'mailpoet_subscribe_on_edit_account');
    function mailpoet_subscribe_on_edit_account($customer_id) {
        require_once(WP_PLUGIN_DIR . '/mailpoet/vendor/autoload.php');
        if (class_exists(\MailPoet\API\API::class)) {
            $mailpoet_api = \MailPoet\API\API::MP('v1');
            try {
                if ( isset($_REQUEST['account_email']) ) {
                    $email = $_REQUEST['account_email'];
                    $get_subscriber = $mailpoet_api->getSubscriber($email);
                    try {
                        $list_ids[] = 6;//配信可能顧客リスト
                        //add to Lists
                        if (isset($get_subscriber['id'])&&$get_subscriber['id']>0){
                            $subscriber_id = $get_subscriber['id'];
                            // remove from Lists
                            if (!isset($_REQUEST['mailpoet_subscribe_on_edit_account'])) {
                                $res = $mailpoet_api->unsubscribeFromLists($subscriber_id, $list_ids);
                            }
                        } else {
                            if (isset($_REQUEST['mailpoet_subscribe_on_edit_account']) && $_REQUEST['mailpoet_subscribe_on_edit_account'] == '1') {
                                $subscriber = array(
                                    'email'=>$email,
                                    'first_name' => $_REQUEST['account_first_name'],
                                    'last_name' => $_REQUEST['account_last_name']
                                );
                                $res = $mailpoet_api->addSubscriber($subscriber);
                                if ( isset($res['id']) && $res['id'] > 0 ) {
                                    $subscriber_id = $res['id'];
                                }
                            }
                        }
                        if (isset($_REQUEST['mailpoet_subscribe_on_edit_account']) && $_REQUEST['mailpoet_subscribe_on_edit_account'] == '1' ) {
                            $res = $mailpoet_api->subscribeToLists($subscriber_id, $list_ids);
                        }
                    } catch (\Exception $e) {
                        $error_message = $e->getMessage();
                        echo $error_message;
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    add_filter('woocommerce_pay_order_product_has_enough_stock', 'ch_make_in_stock_when_paying_for_order', 10, 3);
    add_filter('woocommerce_pay_order_product_in_stock', 'ch_make_in_stock_when_paying_for_order', 10, 3);
    function ch_make_in_stock_when_paying_for_order( $product_stock, $product, $order ) {
        if ($product->stock_status != 'instock' && strpos($_SERVER['REQUEST_URI'], 'order-pay') != false && $_GET['pay_for_order'] == 'true') {
            return true;
        }
        return $product_stock;
    }

    add_action('wp_head', 'custom_og_image',1);
    function custom_og_image(){
        if ( ( function_exists('is_product_category') && is_product_category()) || is_tax('series')) {
            global $wp_query;
            $term_id = $wp_query->get_queried_object()->term_id;
            if ( $term_id > 0 ) {
                $og_image=get_field('og_image',$wp_query->get_queried_object());
                if ( $og_image != '' ) {
                    echo '<meta property="og:image" content="' . $og_image . '">';
                    echo '<meta property="og:image:secure_url" content="' . $og_image . '">';
                    echo '<meta name="twitter:image" content="' . $og_image . '">';
                }
            }
        }
    }

    /**
     * cart include sale product
     * @return boolean
     */
    function is_cart_has_sale_product() {
        $exist = false;
        foreach (WC()->cart->get_cart() as $cart_item) {
            $tags_arr=array();
            $tags     = get_the_terms( $cart_item['product_id'], 'product_tag' );
            if ( $tags && ! is_wp_error( $tags ) ) {
                $tags_arr = wp_list_pluck($tags, 'slug');
            }
            if(!empty($tags_arr)&& in_array('sale', $tags_arr)){
                $exist = true;
                break;
            }
        }
        return $exist;
    }

    /**
     * Show only Stripe if cart include sale product
     * @param type $available_gateways
     * @return type
     */
    add_filter('woocommerce_available_payment_gateways', 'only_stripe_for_sale_product_in_cart', 10, 1);
    function only_stripe_for_sale_product_in_cart($available_gateways) {
        if (!function_exists('WC') || !isset(WC()->cart) || WC()->cart->is_empty() || empty($available_gateways)) {
            return $available_gateways;
        }
        foreach ($available_gateways as $id => $gateway) {
            if (is_cart_has_sale_product() == true && $id != 'stripe') {
                unset($available_gateways[$id]);
            }
        }
        return $available_gateways;
    }

    add_filter('woocommerce_coupon_is_valid', 'not_apply_any_coupon_code_on_cart_if_has_sale_products', 10, 2);
    function not_apply_any_coupon_code_on_cart_if_has_sale_products($true, $instance){
        if(is_cart_has_sale_product() == true){
            return false;
        }
        return $true;
    }

    add_action('wp_footer', 'render_org_price_js');
    function render_org_price_js() {
        wc_enqueue_js(
                "var org_price = 0;"
        );
    }
    