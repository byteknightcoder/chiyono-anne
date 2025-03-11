<?php
    define('PAGE_TERM_ID', 2734);
    define('PAGE_PRIVACY_ID', 2732);
    define('PAGE_CANCEL_ID', 2741);
    define('BOOKING_FORM_ID', 2673);

    // Print out order details for order ID "2031237"
    // add_action( 'init', function() {
    //     print_order_details(2031237);
    // });

    function print_order_details($order_id) {
        // Lấy đối tượng đơn hàng từ WooCommerce
        if ( function_exists('wc_get_order') ) {
            $order = wc_get_order( $order_id );
        
            if ( $order ) {
                $order_data = array(
                    'order_id'         => $order->get_id(),
                    'order_date'       => $order->get_date_created(),
                    'total'            => $order->get_total(),
                    'billing_name'     => $order->get_formatted_billing_full_name(),
                    'billing_email'    => $order->get_billing_email(),
                    'billing_address'  => $order->get_billing_address_1(),
                    'shipping_name'    => $order->get_formatted_shipping_full_name(),
                    'shipping_address' => $order->get_shipping_address_1(),
                    'status'           => $order->get_status(),
                    'payment_method'   => $order->get_payment_method_title(),
                    'items'            => array(),
                );
        
                // Lấy danh sách sản phẩm trong đơn hàng
                foreach ( $order->get_items() as $item_id => $item ) {
                    $product = $item->get_product();
                    $order_data['items'][] = array(
                        'name'     => $item->get_name(),
                        'quantity' => $item->get_quantity(),
                        'total'    => $item->get_total(),
                    );
                }
        
                // In etc .
                // error_log( print_r( $order_data, true ) );
            } else {
                error_log( "Order ID $order_id does not exist." );
            }
        }
    }

    include_once dirname(__FILE__) . '/inc/func-scripts-style.php'; // All Custom Enqueue Scripts
    include_once dirname(__FILE__) . '/inc/func-hooks.php'; // All Custom Hooks
    include_once dirname(__FILE__) . '/inc/func-functions.php'; // All Custom Functions
    include_once dirname(__FILE__) . '/notification_refunded_order_to_admin/notification_refunded_order_to_admin.php'; // Notification Refunded Order to Admin
    include_once dirname(__FILE__) . '/inc/func-ajax.php'; // All Ajax Functions
    include_once dirname(__FILE__) . '/inc/func-shortcode.php'; // All Custom Shortcodes
    include_once dirname(__FILE__) . '/inc/func-woocommerce.php'; // All WooCommerce Functions
    include_once dirname(__FILE__) . '/inc/func-kanafield.php'; // All Kana Field Functions
    include_once dirname(__FILE__) . '/custom-free-gifts/custom-free-gifts.php'; // Custom Free Gift Functions

    function log_cart_status($message) {
        // error_log(date('[Y-m-d H:i:s] ') . $message);
    }

    // add_action('wp', 'check_cart_on_page_load');
    function check_cart_on_page_load() {
        if ( function_exists('is_product') && is_product() ) {
            global $post;
            $product = wc_get_product($post->ID);
            log_cart_status("Product page loaded for ID: {$post->ID}, Name: {$product->get_name()}");
            log_cart_status("Cart count before: " . WC()->cart->get_cart_contents_count());
            
            // Check if it's a gift card product
            if (has_term('Gift Card', 'product_cat', $post->ID)) {
                log_cart_status("This is a Gift Card product");
            }
        }
    }

    // add_action('woocommerce_cart_reset', 'log_cart_reset');
    function log_cart_reset() {
        log_cart_status("Cart was reset");
        log_cart_status("Backtrace: " . wp_debug_backtrace_summary());
    }

    // add_action('woocommerce_cart_emptied', 'log_cart_emptied');
    function log_cart_emptied() {
        log_cart_status("Cart was emptied");
        log_cart_status("Backtrace: " . wp_debug_backtrace_summary());
    }

    // add_action('woocommerce_after_calculate_totals', 'log_after_calculate_totals');
    function log_after_calculate_totals($cart) {
        log_cart_status("After calculate totals - Cart count: " . $cart->get_cart_contents_count());
    }

    // Add TypeKit Font Set
    add_action('wp_head', 'chiyono_font_typekit');
    function chiyono_font_typekit() {
        echo '<link rel="stylesheet" href="https://use.typekit.net/jlw3otc.css">';
    }

    // Add Payment Link to Email
    add_action('woocommerce_email_order_details', 'add_payment_link_to_email', 10, 4);
    function add_payment_link_to_email($order, $sent_to_admin, $plain_text, $email) {
        if ( 'cod' != $order->get_payment_method() && $order->needs_payment() ) {
            $payment_url = $order->get_checkout_payment_url();
            
            $payment_button = sprintf(
                '<div style="margin: 20px 0;text-align: center;">
                    <p style="margin-bottom: 15px;"><strong>お支払いが完了していません。下記のリンクからお支払いを完了してください：</strong></p>
                    <a href="%s" style="background-color: #7f54b3;color: #ffffff;padding: 12px 25px;text-decoration: none;border-radius: 3px;display: inline-block;">
                        お支払いを完了する
                    </a>
                </div>',
                esc_url($payment_url)
            );

            if ($plain_text) {
                echo "\n\nこちらからお支払いを完了してください: " . esc_url($payment_url) . "\n\n";
            } else {
                echo wp_kses_post($payment_button);
            }
        }
    }

    add_filter('woocommerce_email_enabled_customer_on_hold_order', '__return_true');
    add_filter('woocommerce_email_enabled_customer_pending_order', '__return_true');

    add_action('woocommerce_admin_order_actions_end', 'add_payment_link_to_admin_order_actions', 10, 1);
    function add_payment_link_to_admin_order_actions($order) {
        if ( 'cod' != $order->get_payment_method() && $order->needs_payment() ) {
            $payment_url = $order->get_checkout_payment_url();
            printf(
                '<a class="button tips payment-link" href="%s" data-tip="%s" target="_blank">%s</a>',
                esc_url($payment_url),
                esc_attr__('支払いリンク', 'woocommerce'),
                esc_html__('支払いリンク', 'woocommerce')
            );
        }
    }

    add_action('admin_head', 'add_payment_link_admin_styles');
    function add_payment_link_admin_styles() {
        echo '<style>
            .payment-link {
                margin-left: 5px !important;
                background: #7f54b3 !important;
                color: #fff !important;
            }
            .payment-link:hover {
                background: #684a94 !important;
                color: #fff !important;
            }
        </style>';
    }

    // Save custom user meta fields
    add_action('wp_ajax_update_custom_user_meta', 'update_custom_user_meta_callback');
    function update_custom_user_meta_callback() {
        check_ajax_referer('update_custom_user_meta_nonce', 'nonce');
        
        if (!current_user_can('edit_user')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'zoa'));
        }
        
        $user_id = get_current_user_id(); // Or however you're determining the user ID
        
        // Loop through the POST data and update user meta
        foreach ($_POST as $key => $value) {
            if ($key !== 'action' && $key !== 'nonce') {
                update_user_meta($user_id, sanitize_text_field($key), sanitize_text_field($value));
            }
        }
        
        wp_send_json_success(__('User meta updated successfully.', 'zoa'));
    }

    // Add a custom weekly cron schedule
    add_filter( 'cron_schedules', 'add_weekly_cron_schedule' );
    function add_weekly_cron_schedule( $schedules ) {
        if ( ! isset( $schedules['once_in_week'] ) ) {
            $schedules['once_in_week'] = array(
                'interval' => 604800, // One week in seconds
                'display'  => __( 'Once a week', 'zoa' ),
            );
        }
        return $schedules;
    }

    // Save post 
    add_action('save_post', 'zoa_update_q_04_from_client', 10000, 3);
    function zoa_update_q_04_from_client( $post_id, $post, $update ) {

        if ( 'birs_client' == $post->post_type && isset( $_POST['birs_client_email'] ) ) {
            $client_email = get_post_meta($post_id, '_birs_client_email', true);
            $user = get_user_by('email', $client_email);
            if ($user) {
                save_birthday_account_details($user->ID);
            }
        }

        if ( isset( $_POST['post_type'] ) && 'birs_appointment' == $_POST['post_type']  ) {
            after_save_client_booking( ( $_POST['post_ID'] ?? 0 ) );
        }
    }

    // Unset Specific Menu Items
    add_filter( 'wp_nav_menu_objects', 'hide_menu_items_based_on_language', 10, 2 );
    function hide_menu_items_based_on_language( $items, $args ) {
        // Check if TranslatePress function exists
        if ( function_exists( 'trp_get_current_language' ) ) {
            $current_language = trp_get_current_language();
            die($current_language);
            // If current language is English
            if ( 'en' == $current_language ) {
                // Loop through menu items and hide based on ID
                foreach ( $items as $key => $item ) {
                    // Replace '123' with your menu item ID that you want to hide
                    if ( $item->ID == 69 || $item->ID == 456 ) {
                        unset( $items[$key] );
                    }
                }
            }
        }
        return $items;
    }

    function get_client_info_html( $client_id, $step = 0 ) {
        // Check If ACF Plugin is Active
        // Check If BOOKING_FORM_ID defined
        if ( class_exists('ACF') && defined( 'BOOKING_FORM_ID' ) ) {
            $field_group_fields = acf_get_fields(BOOKING_FORM_ID);
            $save_fields = array();
            foreach ($field_group_fields as $field) {
                loop_to_get_sub_field($field, $save_fields);
            }
            ob_start();
            acf_form(array(
                'id' => BOOKING_FORM_ID
            ));
            ob_end_clean();
    
            if ( is_user_logged_in() && !is_admin() ) {
                // Fill user info to acf if logged in
                $user_id = get_current_user_id();
                $user_info = array();
                $user_info['billing_last_name'] = get_user_meta($user_id, 'billing_last_name', true);
                $user_info['billing_first_name'] = get_user_meta($user_id, 'billing_first_name', true);
                $user_info['billing_last_name_kana'] = get_user_meta($user_id, 'billing_last_name_kana', true);
                $user_info['billing_first_name_kana'] = get_user_meta($user_id, 'billing_first_name_kana', true);
                $user_info['billing_email'] = get_user_meta($user_id, 'billing_email', true);
                $user_info['billing_phone'] = get_user_meta($user_id, 'billing_phone', true);
    
                $aDefaultFields = array(
                    '2675' => 'billing_last_name',
                    '2676' => 'billing_first_name',
                    '2678' => 'billing_last_name_kana',
                    '2679' => 'billing_first_name_kana',
                    '2680' => 'billing_email',
                    '2681' => 'billing_phone'
                );
                foreach ($field_group_fields as &$field_group_field) {
                    if ($field_group_field['name'] == 'name' || $field_group_field['name'] == 'name_kana') {
                        $field_id1 = $field_group_field['sub_fields'][0]['ID'];
                        $field_id2 = $field_group_field['sub_fields'][1]['ID'];
    
                        $field_group_field['sub_fields'][0]['default_value'] = $user_info[$aDefaultFields[$field_id1]];
                        $field_group_field['sub_fields'][1]['default_value'] = $user_info[$aDefaultFields[$field_id2]];
                    }
    
                    if ($field_group_field['name'] == 'email' || $field_group_field['name'] == 'tel') {
                        $field_group_field['default_value'] = $user_info[$aDefaultFields[$field_group_field['ID']]];
                    }
                    if ($field_group_field['name'] == 'questions') {
                        $user_id = get_current_user_id();
                        if ($user_id && !isset($_SESSION['appointment_id'])) {
                            foreach ($field_group_field['sub_fields'] as $field_key => $question) {
                                if ($question['name'] == 'q_04') {
                                    foreach ($question['sub_fields'] as $question_key => $question_item) {
                                        $field_default_value = get_user_meta($user_id, $question_item['name'], true);
    
                                        if ($field_default_value) {
                                            $field_group_field['sub_fields'][$field_key]['sub_fields'][$question_key]['value'] = (array) $field_default_value;
                                            $field_group_field['sub_fields'][$field_key]['sub_fields'][$question_key]['default_value'] = (array) $field_default_value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
    
            $booking_id = isset($_GET['post']) ? $_GET['post'] : $client_id;
            $field_group_fields = getBookingStepFields($field_group_fields, $step);
    
            acf_render_fields($field_group_fields, $booking_id, 'div', 'label');
            global $birchschedule;
            $fields = $birchschedule->model->get_client_fields();
            foreach ($fields as $field) {
                echo '<input type="hidden" name="birs_client_fields[]" value="' . $field . '" />';
            }
        }
    }

    // Get Booking Step Fields
    function getBookingStepFields($field_group_fields, $step = 0) {
        if ( 0 == $step ) {
            return $field_group_fields;
        }

        $field_group_fields_clone = $field_group_fields;
        if ( 3 == $step ) {
            // Remove fields not in step 3
            foreach ($field_group_fields_clone as $field_index => $field_group_field_clone) {
                if (strpos($field_group_field_clone['wrapper']['class'], 'step3-fields') === false) {
                    unset($field_group_fields[$field_index]);
                }
            }
        } else {
            // Remove Step 3 fields
            foreach ($field_group_fields_clone as $field_index => $field_group_field_clone) {
                if (strpos($field_group_field_clone['wrapper']['class'], 'step3-fields') !== false) {
                    unset($field_group_fields[$field_index]);
                }
            }
        }
        return $field_group_fields;
    }

    add_action('after_save_booking', 'after_save_client_booking', 10, 3);
    function after_save_client_booking( $appointment_id, $client_id = 0, $appointment1on1_id = 0 ) {
        if (!session_id()) {
            session_start();
        }

        // Check If ACF Plugin is Active
        // Check If BOOKING_FORM_ID defined
        if ( class_exists('ACF') && defined( 'BOOKING_FORM_ID' ) ) {

            $field_group_fields = acf_get_fields(BOOKING_FORM_ID);

            $save_fields = array();
            foreach ($field_group_fields as $field) {
                loop_to_get_sub_field($field, $save_fields);
            }

            foreach ($save_fields as $save_field) {
                $field_name = ($save_field['name_long'] ? $save_field['name_long'] : $save_field['name']);
                update_post_meta($appointment_id, '_' . $field_name, $save_field['key']);
                update_post_meta($appointment_id, $field_name, $_POST[$save_field['key']]);

                // Save q_04 value to custom info
                if (is_user_logged_in() && $save_field['parent_name'] == 'q_04' && !is_admin()) {
                    $user_id = get_current_user_id();
                    update_user_meta($user_id, $save_field['name'], $_POST[$save_field['key']]);
                }
            }

            if (!isset($_POST['booking_confirm']) && $_POST['action'] == 'birchschedule_view_bookingform_schedule') {
                $booking_post = array(
                    'ID' => $appointment_id,
                    'post_status' => 'trash',
                );

                // Update the post into the database
                wp_update_post($booking_post);
                $_SESSION['appointment_id'] = $appointment_id;
                $_SESSION['client_id'] = $client_id;
                $_SESSION['appointment1on1_id'] = $appointment1on1_id;

                wp_scheduled_delete();
            }

            if (is_admin() && $_POST['post_ID'] && $client_id) {
                update_meta_info_booking($appointment_id);
            }
        }
    }

    // Get Available Booking Time
    function zoa_get_avaiable_booking_time() {
        global $birchschedule, $birchpress;

        $staff_id = $_POST['birs_appointment_staff'];
        $location_id = $_POST['birs_appointment_location'];
        $service_id = $_POST['birs_appointment_service'];
        $date_text = $_POST['birs_appointment_date'];
        $date = $birchpress->util->get_wp_datetime(
            array(
                'date' => $date_text,
                'time' => 0
            )
        );

        $time_options = $birchschedule->model->schedule->get_staff_avaliable_time($staff_id, $location_id, $service_id, $date);

        return $time_options;
    }

    function update_meta_info_booking($appointment_id = 0) {
        global $birchpress, $birchschedule;
        $appointment_id = $appointment_id ? $appointment_id : $_SESSION['appointment_id'];

        $appointment = $birchschedule->model->get($appointment_id, array(
            'base_keys' => array(),
            'meta_keys' => $birchschedule->model->get_appointment_fields()
        ));

        $timestamp = $birchpress->util->get_wp_datetime($appointment['_birs_appointment_timestamp']);
        $appointment_date = $timestamp->format('Y-m-d');
        $appointment_time = $timestamp->format('H:i');

        update_post_meta($appointment_id, '_birs_appointment_date', $appointment_date);
        update_post_meta($appointment_id, '_birs_appointment_time', $appointment_time);

        // Check and set date fulled room
        $_POST['birs_appointment_location'] = $appointment['_birs_appointment_location'];
        $_POST['birs_appointment_service'] = $appointment['_birs_appointment_service'];
        $_POST['birs_appointment_staff'] = $appointment['_birs_appointment_staff'];
        $_POST['birs_appointment_date'] = $timestamp->format('m/d/Y');
        $time_options = zoa_get_avaiable_booking_time();
        $booked_times = getTimeIsBookedFromDate();
        foreach ($booked_times as $booked_time) {
            foreach ($time_options as $time_key => $time_option) {
                if ($booked_time == date('H:i', strtotime($time_option['text']))) {
                    unset($time_options[$time_key]);
                    break;
                }
            }
        }
        if (empty($time_options)) {
            // Set this date is unavaiable
            $today = date('Y-m-d');
            $unavaiable_dates = get_option('booking_unavaiable_dates');
            $unavaiable_dates = $unavaiable_dates ? $unavaiable_dates : array();
            if (!empty($unavaiable_dates)) {
                $clone_unavaiable_dates = $unavaiable_dates;
                foreach ($clone_unavaiable_dates as $key_date => $clone_unavaiable_date) {
                    if ($clone_unavaiable_date < $today) {
                        unset($unavaiable_dates[$key_date]);
                    }
                }
            }
            $unavaiable_dates[$_POST['birs_appointment_staff']] = $appointment_date;
            update_option('booking_unavaiable_dates', $unavaiable_dates);
        }
    }

    add_action('wp_ajax_bookingform_schedule_confirmed', 'bookingform_schedule_confirmed');
    add_action('wp_ajax_nopriv_bookingform_schedule_confirmed', 'bookingform_schedule_confirmed');
    function bookingform_schedule_confirmed() {
        if (!session_id()) {
            session_start();
        }
        $booking_post = array(
            'ID' => $_SESSION['appointment_id'],
            'post_status' => 'publish',
        );

        wp_update_post($booking_post);

        update_meta_info_booking();

        // Send emails
        $success = send_booking_email();

        echo json_encode(array('success' => 1));
        die;
    }

    function getTimeIsBookedFromDate() {
        // 	$location_id = $_REQUEST['birs_appointment_location'];
        // 	$service_id = $_REQUEST['birs_appointment_service'];
        $staff_id = $_POST['birs_appointment_staff'];
        $selected_date = $_POST['birs_appointment_date'];
        $aDate = explode('/', $selected_date);

        $args = array(
            'post_type' => 'birs_appointment',
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_birs_appointment_staff',
                    'value' => $staff_id,
                    'compare' => '='
                ),
                // this array results in no return for both arrays
                array(
                    'key' => '_birs_appointment_date',
                    'value' => $aDate[2] . '-' . $aDate[0] . '-' . $aDate[1],
                    'compare' => '='
                )
            )
        );
        
        $posts = get_posts($args);
        $aBookedTimes = array();
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $post_id = $post->ID;
                $booked_time = get_post_meta($post_id, '_birs_appointment_time', true);
                if ($booked_time) {
                    $aBookedTimes[] = $booked_time;
                }
            }
        }
        return $aBookedTimes;
    }

    function send_booking_email() {

        $user_email = get_post_meta($_SESSION['client_id'], '_birs_client_email', true);
        $options = get_option('birchschedule_options');
        $admin_email = $options['store_email'] ?? 'hello@chiyono-anne.com';

        $site_title = get_bloginfo('name');

        $headers = 'Content-type: text/html;charset=utf-8; reservation=true' . "\r\n";
        $headers .= 'From: ' . $site_title . ' <info@chiyonoanne.xsrv.jp>' . "\r\n";

        $attachments = array();
        if (!isset($_SESSION['image_id']) || !$_SESSION['image_id']) {
            $galllery = get_post_meta($_SESSION['pid'], 'gallery', true);
            if ($galllery != '') {
                foreach ($galllery as $gallery) {
                    $attachments[] = get_attached_file($gallery);
                }
            }
        } else {
            $attachments[] = get_attached_file($_SESSION['image_id']);
        }
        // Send to user
        $email_content = get_booking_email_template(false, false);
        $subject = __('Thank you for booking', 'zoa') . ' | ' . get_bloginfo('name') . "\r\n";
        $success = wp_mail($user_email, $subject, $email_content, $headers, $attachments);
        if ($success) {
            // Now send to admin
            $email_content = get_booking_email_template(true, false);
            $subject = __('We have a booking via form', 'zoa') . "\r\n";
            $success = wp_mail($admin_email, $subject, $email_content, $headers, $attachments);
        }

        return $success;
    }

    function get_booking_email_template($is_admin, $has_html = true) {
        ob_start();
        get_booking_confirm_html(true);
        $content_html = ob_get_contents();
        ob_end_clean();
        return $content_html;
    }

    function get_booking_confirm_html($is_email = false) {
        if ($is_email) {
            get_template_part('template-parts/content', 'booking-email');
            return '';
        }

        $appointment_info = get_appointment_info();
        if (empty($appointment_info)) {
            return '';
        }
    ?>
        <div class="row flex-justify-center pad_row">
            <fieldset class="confirm_info col-md-4 col-xs-12">
                <h3 class="appointment--confirm__form__title heading heading--small"><?php esc_html_e('Appointment Info', 'zoa'); ?></h3>
                <div class="form-row">
                    <div class="field-wrapper">
                        <label class="form-row__label light-copy"><?php esc_html_e('Date', 'zoa'); ?></label>
                        <div class="text_output">
                            <span class="confirm-text-value"><?php echo $appointment_info['date4picker'] ?? ''; ?></span>
                        </div>
                    </div>
                </div>
                <!--/.form-row-->
                <div class="form-row">
                    <div class="field-wrapper">
                        <label class="form-row__label light-copy"><?php esc_html_e('Time', 'zoa') ?></label>
                        <div class="text_output">
                            <span class="confirm-text-value"><?php echo $appointment_info['time'] ?? ''; ?></span>
                        </div>
                    </div>
                </div>
                <!--/.form-row-->
                <div class="form-row">
                    <div class="field-wrapper">
                        <label class="form-row__label light-copy"><?php esc_html_e('Service', 'zoa'); ?></label>
                        <div class="text_output">
                            <span class="confirm-text-value"><?php echo $appointment_info['service_name'] ?? ''; ?></span>
                        </div>
                    </div>
                </div>
                <!--/.form-row-->
            </fieldset>

        <?php
            $field_group_fields = acf_get_fields(BOOKING_FORM_ID);
            $field_group_fields = getBookingStepFields($field_group_fields, 2);
            $save_fields = array();
            echo '<fieldset class="confirm_info col-md-4 col-xs-12">';
            echo '<h3 class="appointment--confirm__form__title heading heading--small">' . __('Your Info', 'zoa') . '</h3>';
            foreach ($field_group_fields as $field) {
                loop_to_get_sub_field($field, $save_fields);
            }

            $last_name_value = get_post_meta($_SESSION['appointment_id'], ($save_fields['2675']['name_long'] ? $save_fields['2675']['name_long'] : $save_fields['2675']['name']), true);
            $first_name_value = get_post_meta($_SESSION['appointment_id'], ($save_fields['2676']['name_long'] ? $save_fields['2676']['name_long'] : $save_fields['2676']['name']), true);
            $last_name_kana_value = get_post_meta($_SESSION['appointment_id'], ($save_fields['2678']['name_long'] ? $save_fields['2678']['name_long'] : $save_fields['2678']['name']), true);
            $first_name_kana_value = get_post_meta($_SESSION['appointment_id'], ($save_fields['2679']['name_long'] ? $save_fields['2679']['name_long'] : $save_fields['2679']['name']), true);

            $save_fields['2675']['label'] = __('Name', 'zoa');
            $save_fields['2675']['full_value'] = $last_name_value . $first_name_value;

            $save_fields['2678']['label'] = __('Name Kana', 'zoa');
            $save_fields['2678']['full_value'] = $last_name_kana_value . $first_name_kana_value;

            unset($save_fields[2676]);
            unset($save_fields[2679]);

            foreach ($save_fields as $save_field) {
                $field_value = $save_field['full_value'] ? $save_field['full_value'] : get_post_meta($_SESSION['appointment_id'], ($save_field['name_long'] ? $save_field['name_long'] : $save_field['name']), true);

                if (!$field_value) {
                    continue;
                }

                echo '<div class="form-row">
                    <div class="field-wrapper">
                        <label class="form-row__label light-copy">' . $save_field['label'] . '</label>
                        <div class="text_output">
                            <span class="confirm-text-value">' . (is_array($field_value) ? implode(', ', $field_value) : $field_value) . '</span>
                        </div>
                    </div>
                </div>';
            }
            echo '</fieldset>';
            echo '<div class="confirm_info col-md-4 col-xs-12">';
            echo '<fieldset>';

            $field_group_fields = acf_get_fields(BOOKING_FORM_ID);
            $field_group_fields = getBookingStepFields($field_group_fields, 3);
            $save_fields = array();
            foreach ($field_group_fields as $field) {
                loop_to_get_sub_field($field, $save_fields);
            }
        ?>
            <h3 class="appointment--confirm__form__title heading heading--small"><?php esc_html_e('Your Inquiry', 'zoa'); ?></h3>
        <?php
            foreach ($save_fields as $save_field) :
                $field_value = $save_field['full_value'] ? $save_field['full_value'] : get_post_meta($_SESSION['appointment_id'], ($save_field['name_long'] ? $save_field['name_long'] : $save_field['name']), true);
                if (!$field_value) {
                    continue;
                }
            ?>
                <div class="form-row">
                    <div class="field-wrapper">
                        <label class="form-row__label light-copy"><?php echo $save_field['label'] ?? ''; ?></label>
                        <div class="text_output">
                            <span class="confirm-text-value"><?php echo (is_array($field_value) ? implode(', ', $field_value) : $field_value) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </fieldset>

            <?php if (isset($_SESSION['p_image']) && $_SESSION['p_image']) : ?>
                <fieldset>
                    <h3 class="appointment--confirm__form__title heading heading--small"><?php esc_html_e('Inspired Photo', 'zoa'); ?>
                    </h3>
                    <div class="form-row">
                        <div class="field-wrapper">
                            <div class="col-12">
                                <img src="<?php echo $_SESSION['p_image']; ?>" alt="image">
                            </div>
                        </div>
                    </div>
                </fieldset>
            <?php endif; ?>
        </div>
        </div>
        <div class="cancel_term_text">
            <?php
                printf(__('cancel_appointment_confirm_text', 'zoa'), $appointment_info['cancel_before_timestamp']->format('H:i'), $appointment_info['cancel_before_timestamp']->format(get_option('date_format')));
            ?>
        </div>
    <?php
    }

    function get_appointment_info($appointment_id = 0) {
        if (!session_id()) {
            session_start();
        }

        global $birchpress, $birchschedule;
        $appointment_id = $appointment_id ? $appointment_id : $_SESSION['appointment_id'];
        //$_SESSION['pid'] = $_REQUEST['pid'];
        $appointment = $birchschedule->model->get($appointment_id, array(
            'base_keys' => array(),
            'meta_keys' => $birchschedule->model->get_appointment_fields()
        ));

        $appointment_info = array();
        if ($appointment) {
            $notes = get_post_meta($_SESSION['appointment1on1_id'], '_birs_appointment_notes', true);
            $appointment_info['notes'] = $notes;
            $location_id = $appointment['_birs_appointment_location'];
            $location = $birchschedule->model->get($location_id, array('keys' => array('post_title')));
            $appointment_info['location_name'] = $location ? $location['post_title'] : '';
            $appointment_info['location_id'] = $location_id;

            $service_id = $appointment['_birs_appointment_service'];
            $service = $birchschedule->model->get($service_id, array('keys' => array('post_title', '_birs_service_day_cancel_before', '_birs_service_time_cancel_before')));
            $appointment_info['service'] = $service;
            $appointment_info['service_name'] = $service ? $service['post_title'] : '';
            $appointment_info['service_id'] = $service_id;

            $staff_id = $appointment['_birs_appointment_staff'];
            $staff = $birchschedule->model->get($staff_id, array('keys' => array('post_title')));
            $appointment_info['staff_name'] = $staff ? $staff['post_title'] : '';
            $appointment_info['staff_id'] = $staff_id;

            $timestamp = $birchpress->util->get_wp_datetime($appointment['_birs_appointment_timestamp']);
            $appointment_info['timestamp'] = $timestamp;
            $appointment_info['date4picker'] = $timestamp->format(get_option('date_format'));
            $appointment_info['date'] = $timestamp->format('m/d/Y');
            $appointment_info['year'] = $timestamp->format('Y');
            $appointment_info['month'] = $timestamp->format('m');
            $appointment_info['day'] = $timestamp->format('d');
            $appointment_info['time'] = $timestamp->format(get_option('time_format'));
            $appointment_info['hour'] = $timestamp->format('H');
            $appointment_info['minute'] = $timestamp->format('i');

            $hour_cancel_sub = (float) $appointment_info['service']['_birs_service_day_cancel_before'] * 24 + (float) $appointment_info['service']['_birs_service_time_cancel_before'];
            $timestamp_cancel = clone $timestamp;
            $timestamp_cancel->sub(new DateInterval('PT' . $hour_cancel_sub . 'H'));
            $appointment_info['cancel_before_timestamp'] = $timestamp_cancel;
            $appointment_info['cancel_before'] = $timestamp_cancel->format(get_option('date_format')) . ' ' . $timestamp_cancel->format('H:i');
            $appointment_info['default'] = $birchschedule->model->mergefields->get_appointment_merge_values($appointment_id);
        }
        return $appointment_info;
    }

    function zoa_is_allow_cancel_appointment($appointment_id) {
        $now = new DateTime("now");
        $appointment_info = get_appointment_info($appointment_id);
        $is_allow_cancel = $now <= $appointment_info['cancel_before_timestamp'];
        return $is_allow_cancel;
    }

    function zoa_get_appointment_location_addrress($appointment_info) {
        $address = '';
        if (get_locale() == 'ja') {
            $address = $appointment_info['_birs_location_zip'] . '&nbsp' .
                $appointment_info['_birs_location_state'] .
                $appointment_info['_birs_location_city'] .
                $appointment_info['_birs_location_address1'] .
                $appointment_info['_birs_location_address2'];
        } else {
            '<!--if user lang is en, format is {Address2}{Address 1}, {City}, {State}, {Country}<br/>{postcode}-->';
            $address = $appointment_info['_birs_location_address2'] . ', ' .
                $appointment_info['_birs_location_address1'] . ', ' .
                $appointment_info['_birs_location_city'] . ', ' .
                $appointment_info['_birs_location_state'] . ', ' .
                WC()->countries->countries[$appointment_info['_birs_location_country']] . '<br /> ' .
                $appointment_info['_birs_location_zip'];
        }
        return $address;
    }

    /**
     * Woocommerce customizations
    */
    include_once('woocommerce/custom.php');

    // Check if user is has free membership
    function isMemberFreeShip() {
        // If user Logged In
        if ( is_user_logged_in() && function_exists('mr_get_member_rank') ) {
            $rank = mr_get_member_rank( get_current_user_id() );
            if (in_array($rank['rank'], array('royal', 'gold'))) {
                return true;
            }
        }
        return false;
    }

    function isGrantedCouponFreeShipping() {

        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
    
        if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            $applied_coupons = WC()->cart->get_applied_coupons();
            foreach ( $applied_coupons as $coupon_code ){
                $coupon = new WC_Coupon($coupon_code);
                if ($coupon->get_free_shipping()){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check is order family sale by CART data
     * @return boolean
     */
    function is_order_familysale() {
        $exist = false;
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
    
        if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            $current_time = current_time('timestamp');
            $event_page = get_page_by_path('special-event', OBJECT, 'page');
            $closed_datetime = function_exists('get_field') && isset( $event_page->ID ) ? get_field('closed_datetime', $event_page->ID) : 0;
            foreach ( WC()->cart->get_cart() as $cart_item ) {
                if (is_product_in_cat($cart_item['product_id'], 'familysale') && $current_time < strtotime($closed_datetime)) {
                    $exist = true;
                    break;
                }
            }
        }

        return $exist;
    }

    function getShippingPackageByDeliverDate($order = null) {
        $delivery_dates = array();
        $product_gift_wrapper_id = get_gift_box_product_id();
        if ($product_gift_wrapper_id > 0) {
            $gift_wrapper = wc_get_product($product_gift_wrapper_id);
            if (is_object($gift_wrapper)) {
                $gift_wrapper_id = $gift_wrapper->get_parent_id();
            } else {
                $gift_wrapper_id = 0;
            }
        } else {
            $product_gift_wrapper_id = 0;
            $gift_wrapper_id = 0;
        }
        if ($order) {
            $line_items = $order->get_items(apply_filters('woocommerce_admin_order_item_types', 'line_item'));
            foreach ($line_items as $item) {
                $product_id = $item->get_product_id();
                $delivery_date = trim(get_post_meta($product_id, 'deliver_date', true));
                $specific_deliver_date = trim(get_post_meta($product_id, 'specific_deliver_date', true));
                $has_delivery_date = $delivery_date ? $delivery_date : 0;
                $custom_field_type = get_post_meta($product_id, 'from_to', true);
                $from_to = '';
                if (!empty($custom_field_type)&& is_array($custom_field_type)) {
                    $from_to = implode('', $custom_field_type);
                }
                $key = $delivery_date . $specific_deliver_date . $from_to;
                if (empty(trim($key))) {
                    $key = 0;
                }
                if ($product_id != $product_gift_wrapper_id && $product_id != $gift_wrapper_id) {
                    $delivery_dates[$key] = $has_delivery_date;
                }
            }
        } else {
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                $delivery_date = trim(get_post_meta($product_id, 'deliver_date', true));
                $specific_deliver_date = trim(get_post_meta($product_id, 'specific_deliver_date', true));
                $has_delivery_date = $delivery_date ? 1 : 0;
                $custom_field_type = get_post_meta($product_id, 'from_to', true);
                $from_to = '';
                if (!empty($custom_field_type)&& is_array($custom_field_type)) {
                    $from_to = implode('', $custom_field_type);
                }
                $key = $delivery_date . $specific_deliver_date . $from_to;
                if (empty(trim($key))) {
                    $key = 0;
                }
                if ($cart_item['product_id'] != $product_gift_wrapper_id && $cart_item['product_id'] != $gift_wrapper_id) {
                    $delivery_dates[$key] = $has_delivery_date;
                }
            }
        }
        return $delivery_dates;
    }

    function meta_box_deliver_option_markup($post) {
        $rates = array();
        $order = wc_get_order($post->ID);
        $shipping_delivery_option = get_post_meta($order->get_id(), 'shipping_delivery_option', true);

        $selected_option_1 = !$shipping_delivery_option ? 'checked' : ($shipping_delivery_option == 1 ? 'checked' : '');
        $selected_option_2 = $shipping_delivery_option == 2 ? 'checked' : '';

        $num_cart_product = count(getShippingPackageByDeliverDate($order));
        if (!isset($_REQUEST['post'])) {
            if ($num_cart_product <= 1 || isMemberFreeShip()|| isGrantedCouponFreeShipping()) {
                return;
            }
        }

        wp_nonce_field(basename(__FILE__), "meta-box-nonce");
        echo '<div class="deliver_option_wraper">';
        echo '<div><input type="radio" id="shipping_delivery_option_1" name="shipping_delivery_option" value="1" ' . $selected_option_1 . '/><label for="shipping_delivery_option_1" class="label">' . __('Ship together', 'zoa') . '</label></div>
                <div><input id="shipping_delivery_option_2" type="radio" name="shipping_delivery_option" value="2" ' . $selected_option_2 . '/><label for="shipping_delivery_option_2" class="label">' . __('Ship according to completion date', 'zoa') . '(' . sprintf(__('%1s pkg.'), $num_cart_product) . ')</label></div>';
        echo '</div>';
    }

    if (!function_exists('woof_show_btn')) {

        function woof_show_btn($autosubmit = 1, $ajax_redraw = 0) {
        ?>
            <div class="woof_container woof_submit_search_form_container">
                <div class="toggle-wrap">
                    <div class="toggle__link flex-justify-between toggle__link--no-indicator">
                        <h3 class="toggle__name"><?php esc_html_e('Filter by', 'zoa'); ?></h3>
                        <?php
                            global $WOOF;
                            if ($WOOF->is_isset_in_request_data($WOOF->get_swoof_search_slug())) :
                                global $woof_link;
                        ?>

                                <?php
                                    $woof_reset_btn_txt = get_option('woof_reset_btn_txt', '');
                                    if (empty($woof_reset_btn_txt)) {
                                        $woof_reset_btn_txt = __('Reset', 'woocommerce-products-filter');
                                    }
                                    $woof_reset_btn_txt = WOOF_HELPER::wpml_translate(null, $woof_reset_btn_txt);
                                ?>

                                <?php if ($woof_reset_btn_txt != 'none') : ?>
                                    <button class="woof_reset_search_form refinement__clear" data-link="<?php echo $woof_link; ?>"><?php echo $woof_reset_btn_txt; ?></button>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php if (!$autosubmit or $ajax_redraw) : ?>
                            <?php
                                $woof_filter_btn_txt = get_option('woof_filter_btn_txt', '');
                                if (empty($woof_filter_btn_txt)) {
                                    $woof_filter_btn_txt = __('Filter', 'woocommerce-products-filter');
                                }

                                $woof_filter_btn_txt = WOOF_HELPER::wpml_translate(null, $woof_filter_btn_txt);
                            ?>
                            <button style="float: left;" class="button woof_submit_search_form"><?php echo $woof_filter_btn_txt; ?></button>
                        <?php endif; ?>
                    </div>
                    <!--/.toggle__link-->
                </div>
                <!--/.togggle-wrap-->
            </div>
            <button id="closeRefinement" class="display--mid-only button"><?php esc_html_e('Close Filter', 'zoa'); ?></button>
        <?php
        }
    }

    /**
     * Call back function
     */
    function ch_set_html_content_type() {
        return 'text/html';
    }

    function po_settings_review() {
        if (isset($_POST['save_bd_setting'])) {
            update_option('common_notion_subject_email', $_REQUEST['common_notion_subject_email']);
            update_option('common_notion_content_email', $_REQUEST['common_notion_content_email']);
            update_option('wc_get_order_statuses', $_REQUEST['wc_get_order_statuses']);
            update_option('order_date', $_REQUEST['order_date']);
        }
        if (isset($_POST['save_bd_setting_send'])) {
            //get all orders
            if (isset($_REQUEST['wc_get_order_statuses']) && !empty($_REQUEST['wc_get_order_statuses'])) {
                $order_status = str_replace("wc-", '', $_REQUEST['wc_get_order_statuses']);
                //get order by status
                $args = array(
                    'status' => $order_status,
                    'return' => 'ids',
                );
            } else {
                // Get all orders
                $args = array('return' => 'ids');
            }
            if (!empty($_REQUEST['order_date'])) {
                $args['date_created'] = '<=' . $_REQUEST['order_date'];
            }
            $args['posts_per_page'] = -1;

            $orders = wc_get_orders($args);
            if (!empty($orders)) {
                $all_emails = array();
                $id_emails = array();
                foreach ($orders as $order_id) {
                    $_billing_email = get_post_meta($order_id, '_billing_email', true);
                    $_sent_email_review = get_post_meta($order_id, '_sent_email_review', true);
                    if (!empty($_billing_email) && $_sent_email_review != 'yes') {
                        $all_emails[] = $_billing_email;
                        $id_emails[$order_id] = $_billing_email;
                    }
                }
            }
            if (!empty($all_emails)) {
                $site_title = get_bloginfo('name');
                $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
                $attachments = array();
                //Remove duplidate emails
                $all_emails = array_unique($all_emails);
                $subject = trim($_REQUEST['common_notion_subject_email']);
                $message = trim(get_option('common_notion_content_email', false));
                $message = stripslashes($message);
                $check = 0;
                if (!empty($subject) && !empty($message)) {
                    add_filter('wp_mail_content_type', 'ch_set_html_content_type');
                    foreach ($all_emails as $value) {
                        wp_mail($value, $subject, $message, $headers, $attachments);
                        $id_sent = array_keys($id_emails, $value);
                        if (!empty($id_sent)) {
                            foreach ($id_sent as $value_id) {
                                update_post_meta($value_id, '_sent_email_review', 'yes');
                                update_post_meta($value_id, '_sent_email_review_date', date_i18n('Y-m-d'));
                            }
                        }
                    }
                    $check = 1;
                    remove_filter('wp_mail_content_type', 'ch_set_html_content_type');
                }
                if ($check == 1) {
                    $err_send = __('Send emails completed.', 'zoa');
                }
            } else {
                $err_send = __("Don't have old orders to send emails", 'zoa');
            }
            //end emails
        }
        ob_start();
        if (isset($_POST['save_bd_setting'])) :
    ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
            </div>
    <?php endif;
        if (isset($err_send)) :
    ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php echo $err_send; ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
            </div>
    <?php endif; ?>
        <h3><?php esc_html_e('Send email to old orders remind write review:', 'zoa'); ?></h3>
        <i><?php esc_html_e('NOTE: each email send only once for each user.', 'zoa'); ?></i>
        <hr />
        <form action="" method="POST">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Email subject', 'zoa'); ?></label></th>
                        <td>
                            <textarea rows="2" cols="100" name="common_notion_subject_email"><?php echo get_option('common_notion_subject_email'); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Email content', 'zoa'); ?></label></th>
                        <td>
                            <?php
                                $common_notion_content_email = get_option('common_notion_content_email');
                                $common_notion_content_email = stripslashes($common_notion_content_email);
                                wp_editor($common_notion_content_email, 'common_notion_content_email', array('textarea_name' => 'common_notion_content_email', 'media_buttons' => false, 'editor_height' => 250, 'teeny' => true));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Filter by order status', 'zoa'); ?></label></th>
                        <td>
                            <?php
                                $array = wc_get_order_statuses();
                                $current_status = get_option('wc_get_order_statuses', '');
                                if (!empty($array)) :
                            ?>
                                    <select name="wc_get_order_statuses">
                                        <option><?php esc_html_e('All', 'zoa'); ?></option>
                                        <?php foreach ($array as $key => $value) : ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($current_status == $key) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Filter by Order date end', 'zoa'); ?></label></th>
                        <td>
                            <input type="text" id="datepicker" name="order_date" value="<?php echo get_option('order_date'); ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" name="save_bd_setting" id="submit" class="button button-primary" value="<?php esc_attr_e('Save setting', 'zoa'); ?>">
            <input type="submit" onclick="return confirm('Are you sure?')" name="save_bd_setting_send" id="submit" class="button button-primary" value="<?php esc_attr_e('Send email', 'zoa'); ?>">
        </form>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#datepicker').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            });
        </script>
        <?php
            // get to show log
            $args = array(
                'post_type' => 'shop_order',
                "post_status" => array('wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed'),
                'posts_per_page' => '-1',
                'meta_query' => array(
                    array(
                        'key' => '_sent_email_review',
                        'value' => 'yes',
                        'compare' => '=',
                    ),
                ),
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) :
                $group_date = array();
                while ($query->have_posts()) : $query->the_post();
                    $post_id = get_the_ID();
                    $is_sent_email_date = get_post_meta($post_id, '_sent_email_review_date', true);
                    if ($is_sent_email_date == '' || !isset($is_sent_email_date)) {
                        $is_sent_email_date = '2020-10-20';
                    }
                    $group_date[$is_sent_email_date][] = $post_id;
                endwhile;
                wp_reset_postdata();
                if (!empty($group_date)) :
        ?>
                    <strong><?php esc_html_e('Log sent.', 'zoa'); ?></strong>
                    <style>
                        .logs td,
                        .logs th {
                            border: 1px solid #ddd;
                            padding: 0 8px;
                        }
                    </style>
                    <table class="logs">
                        <thead>
                            <tr>
                                <td><?php esc_html_e('Date', 'zoa'); ?></td>
                                <td><?php esc_html_e('Total Orders Sent', 'zoa'); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group_date as $key => $value) : ?>
                                <tr>
                                    <td><?php echo $key; ?></td>
                                    <td><?php echo count($value); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php
                endif;
            endif;
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
    }

    
    /**
     * Call back function for admin_menu
    */
    add_action('admin_menu', 'setup_menu');
    function setup_menu() {
        add_submenu_page('woocommerce', __('Send email to old orders remind write review.', 'zoa'), __('Send email to old orders remind write review.', 'zoa'), 'manage_options', 'po_settings_review', 'po_settings_review', '', 12);
        add_submenu_page('woocommerce', __('Send email imported customers', 'zoa'), __('Send email imported customers', 'zoa'), 'manage_options', 'send_email_imported_customers', 'send_email_imported_customers', '', 15);
        if (in_array('digits/digit.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_submenu_page('woocommerce', __('Send SMS imported mobile customers', 'zoa'), __('Send SMS imported mobile customers', 'zoa'), 'manage_options', 'send_sms_imported_mobile_customers', 'send_sms_imported_mobile_customers', '', 16);
        }
        add_submenu_page('woocommerce', __('Send bulk by importing csv as email list', 'zoa'), __('Send bulk by importing csv as email list', 'zoa'), 'manage_options', 'send_email_bulk_by_csv', 'send_email_bulk_by_csv', '', 15);
        add_submenu_page('woocommerce', __('Send bulk by importing csv and auto generated coupon code', 'zoa'), __('Send bulk by importing csv and auto generated coupon code', 'zoa'), 'manage_options', 'send_email_bulk_by_csv_couponcode', 'send_email_bulk_by_csv_couponcode', '', 15);
    }

    // -----------------------
    // 2. Calculate sales by state

    function ch_yearly_sales_by_order_type() {

        $activey = '';
        $activel = '';
        $activem = '';
        $active7 = '';

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
        $args = [
            'post_type' => 'shop_order',
            'posts_per_page' => '-1',
            'post_status' => ['wc-completed']
        ];
        if (isset($_REQUEST['range'])) {
            $range = $_REQUEST['range'];
            if ($range == 'year') {
                $activey = 'active';
                $args['year'] = date('Y');
            } else if ($range == 'last_month') {
                $args['year'] = date('Y');
                $args['monthnum'] = date("m", strtotime("-1 month"));
                $activel = 'active';
            } else if ($range == 'month') {
                $args['year'] = date('Y');
                $args['monthnum'] = date('m');
                $activem = 'active';
            } else if ($range == '7day') {
                $active7 = 'active';
                $args['date_query'] = array(
                    array(
                        'after' => date("Y-m-d", strtotime("-7 days")),
                        'before' => date('Y-m-d'),
                        'inclusive' => true,
                    )
                );
            } else if ($range == 'custom') {
                $activec = 'active';
                $start_date = $_REQUEST['start_date'];
                $end_date = $_REQUEST['end_date'];
                if (!empty($start_date) && !empty($end_date)) {
                    $args['date_query'] = array(
                        array(
                            'after' => $start_date,
                            'before' => $end_date,
                            'inclusive' => true,
                        )
                    );
                } else if (!empty($start_date) && empty($end_date)) {
                    $args['date_query'] = array(
                        array(
                            'after' => $start_date,
                            'inclusive' => true,
                        )
                    );
                } else if (empty($start_date) && !empty($end_date)) {
                    $args['date_query'] = array(
                        array(
                            'before' => $end_date,
                            'inclusive' => true,
                        )
                    );
                }
            }
        }
        $my_query = new WP_Query($args);
        $orders = $my_query->posts;
        $order_type_total = array();
        foreach ($orders as $order => $value) {
            $order_id = $value->ID;
            $order = wc_get_order($order_id);
            $meta_field_data = get_post_meta($order_id, 'ch_order_type', true) ? get_post_meta($order_id, 'ch_order_type', true) : '';
            if (!empty($meta_field_data)) {
                $order_type_total[$meta_field_data]['total'] += $order->get_total();
                $order_type_total[$meta_field_data]['shipping_total'] += $order->get_shipping_total();
                $order_type_total[$meta_field_data]['orders_placed'] += 1;
                $order_type_total[$meta_field_data]['items_purchased'] += $order->get_item_count();
            }
        }

        $path_filter = admin_url('admin.php?page=wc-reports&tab=orders&report=sales_by_order_type');
        ?>
        <link rel="stylesheet" href="<?php echo site_url('wp-content/themes/zoa-child/css/sales_by_order_type.css'); ?>">
        <h3><?php esc_html_e('Sales by Order type', 'zoa'); ?></h3>
        <div class="stats_range ch_filter">
            <ul>
                <li class="<?php echo $activey; ?>"><a href="<?php echo $path_filter; ?>&amp;range=year"><?php esc_html_e('Year', 'zoa'); ?></a></li>
                <li class="<?php echo $activel; ?>"><a href="<?php echo $path_filter; ?>&amp;range=last_month"><?php esc_html_e('Last month', 'zoa'); ?></a></li>
                <li class="<?php echo $activem; ?>"><a href="<?php echo $path_filter; ?>&amp;range=month"><?php esc_html_e('This month', 'zoa'); ?></a></li>
                <li class="<?php echo $active7; ?>"><a href="<?php echo $path_filter; ?>&amp;range=7day"><?php esc_html_e('Last 7 days', 'zoa'); ?></a></li>
                <li class="custom <?php echo $activec; ?>">
                <?php esc_html_e('Custom:', 'zoa'); ?> <form method="GET">
                        <div>
                            <input type="hidden" name="page" value="wc-reports"><input type="hidden" name="tab" value="orders"><input type="hidden" name="report" value="sales_by_order_type"><input type="hidden" name="range" value="year">
                            <input type="hidden" name="range" value="custom">
                            <input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php if (isset($start_date)) echo $start_date; ?>" name="start_date" autocomplete="off" id="start_date"> <span>–</span>
                            <input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php if (isset($end_date)) echo $end_date; ?>" name="end_date" autocomplete="off" id="end_date"> <button type="submit" class="button" value="Go"><?php esc_html_e('Go', 'zoa'); ?></button>
                        </div>
                    </form>
                    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            $('#start_date').datepicker({
                                dateFormat: 'yy-mm-dd'
                            });
                            $('#end_date').datepicker({
                                dateFormat: 'yy-mm-dd'
                            });
                        });
                    </script>
                </li>
            </ul>
        </div>
        <table class="wp-list-table widefat fixed">
            <thead>
                <tr>
                    <td><?php esc_html_e('Order Type', 'zoa'); ?></td>
                    <td><?php esc_html_e('Total sales', 'zoa'); ?></td>
                    <td><?php esc_html_e('Orders placed', 'zoa'); ?></td>
                    <td><?php esc_html_e('Items purchased', 'zoa'); ?></td>
                    <td><?php esc_html_e('Shipping total', 'zoa'); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($order_type_total)) :
                    foreach ($order_type_total as $key => $value) :
                ?>
                        <tr>
                            <td><?php echo $key; ?></td>
                            <td><?php echo wc_price($value['total']); ?></td>
                            <td><?php echo $value['orders_placed']; ?></td>
                            <td><?php echo $value['items_purchased']; ?></td>
                            <td><?php echo wc_price($value['shipping_total']); ?></td>
                        </tr>
                <?php
                    endforeach;
                    else : ?>
                    <tr><td colspan="5"><?php esc_html_e('No data', 'zoa'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php
    }

    function send_email_imported_customers() {
        if (isset($_POST['save_im_setting'])) {
            update_option('send_im_subject_email', $_REQUEST['send_im_subject_email']);
            update_option('send_im_content_email', $_REQUEST['send_im_content_email']);
        }
        if (isset($_POST['save_im_send_email'])) {
            $users = get_users(['role__in' => ['customer'], 'fields' => array('ID', 'user_email')]);
            if (!empty($users)) {
                $all_emails = array();
                foreach ($users as $user) {
                    $is_user_imported = get_user_meta($user->ID, 'is_user_imported', true);
                    $is_sent_email = get_user_meta($user->ID, 'is_sent_email', true);
                    if (!empty($is_user_imported) && $is_user_imported == 'yes' && $is_sent_email != 'yes') {
                        if (!empty($user->user_email)) {
                            $all_emails[$user->ID] = $user->user_email;
                        }
                    }
                }
            }

            if (!empty($all_emails)) {
                $site_title = get_bloginfo('name');
                $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
                $attachments = array();

                $subject = trim(get_option('send_im_subject_email', false));
                $message = trim(get_option('send_im_content_email', false));
                $message = stripslashes($message);
                $check = 0;
                if (!empty($subject) && !empty($message)) {
                    add_filter('wp_mail_content_type', 'ch_set_html_content_type');
                    foreach ($all_emails as $u_id => $value) {
                        wp_mail($value, $subject, $message, $headers, $attachments);
                        update_user_meta($u_id, 'is_sent_email', 'yes');
                        update_user_meta($u_id, 'is_sent_email_date', date_i18n('Y-m-d'));
                    }
                    $check = 1;
                    remove_filter('wp_mail_content_type', 'ch_set_html_content_type');
                }
                if ($check == 1) {
                    $err_send = __('Send emails completed.', 'zoa');
                }
            } else {
                $err_send = __("Don't have imported customers to send emails", 'zoa');
            }
            //end emails
        }
        ob_start();
    ?>
        <?php if (isset($_POST['save_im_setting'])) : ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
            </div>
        <?php endif; ?>
        <?php if (isset($err_send)) : ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php echo $err_send; ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
            </div>
        <?php endif; ?>
        <h3><?php esc_html_e('Send email imported customers:', 'zoa'); ?></h3>
        <i><?php esc_html_e('NOTE: each email send only once for each user.', 'zoa'); ?></i>
        <hr />
        <form action="" method="POST">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Email subject', 'zoa'); ?></label></th>
                        <td>
                            <textarea rows="2" cols="100" name="send_im_subject_email"><?php echo get_option('send_im_subject_email'); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Email content', 'zoa'); ?></label></th>
                        <td>
                            <?php
                                $common_notion_content_email = get_option('send_im_content_email');
                                $common_notion_content_email = stripslashes($common_notion_content_email);
                                wp_editor($common_notion_content_email, 'send_im_content_email', array('textarea_name' => 'send_im_content_email', 'media_buttons' => false, 'editor_height' => 250, 'teeny' => true));
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" name="save_im_setting" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Setting', 'zoa'); ?>">
            <input onclick="return confirm('Are you sure?')" type="submit" name="save_im_send_email" id="submit" class="button button-primary" value="<?php esc_attr_e('Send email', 'zoa'); ?>">
        </form>
        <?php
            //get to show log
            $us = get_users(['role__in' => ['customer'], 'fields' => array('ID'), 'meta_key' => 'is_sent_email', 'meta_value' => 'yes', 'meta_compare' => '=']);
            if (!empty($us)) :
                $group_date = array();
                foreach ($us as $value) {
                    $is_sent_email_date = get_user_meta($value->ID, 'is_sent_email_date', true);
                    if ($is_sent_email_date == '' || !isset($is_sent_email_date)) {
                        $is_sent_email_date = '2020-10-20';
                    }
                    $group_date[$is_sent_email_date][] = $value->ID;
                }
                if (!empty($group_date)) :
        ?>
                    <strong><?php esc_html_e('Log sent.', 'zoa'); ?></strong>
                    <style>
                        .logs td,
                        .logs th {
                            border: 1px solid #ddd;
                            padding: 0 8px;
                        }
                    </style>
                    <table class="logs">
                        <thead>
                            <tr>
                                <td><?php esc_html_e('Date', 'zoa'); ?></td>
                                <td><?php esc_html_e('Total Sent', 'zoa'); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group_date as $key => $value) : ?>
                                <tr>
                                    <td><?php echo $key; ?></td>
                                    <td><?php echo count($value); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
            <?php
                endif;
            endif;
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
    }

    $digits_twilio_dir = WP_PLUGIN_DIR . '/digits/Twilio/autoload.php';

    if( file_exists( $digits_twilio_dir ) ) {
        require_once $digits_twilio_dir;
    }
    use Twilio\Rest\Client;
    if (in_array('digits/digit.php', apply_filters('active_plugins', get_option('active_plugins')))) {

        function send_sms_imported_mobile_customers() {
            if (isset($_POST['save_sms_setting'])) {
                update_option('send_sms_content', $_REQUEST['send_sms_content']);
            }
            if (isset($_POST['save_sms_send'])) {
                $users = get_users(['role__in' => ['customer'], 'fields' => array('ID', 'user_email')]);
                if (!empty($users)) {
                    $all_phone = array();
                    foreach ($users as $user) {
                        $is_user_imported = get_user_meta($user->ID, 'is_user_imported', true);
                        $is_sent_sms = get_user_meta($user->ID, 'is_sent_sms', true);
                        $digit_phone = get_user_meta($user->ID, 'digits_phone', true);
                        if (!empty($is_user_imported) && $is_user_imported == 'yes' && $is_sent_sms != 'yes') {
                            if (!empty($digit_phone) && empty($user->user_email)) {
                                $all_phone[$user->ID] = $digit_phone;
                            }
                        }
                    }
                }

                if (!empty($all_phone)) {
                    $content = trim(get_option('send_sms_content', false));
                    $check = 0;
                    $total_sent = 0;
                    if (!empty($content)) {
                        foreach ($all_phone as $u_id => $value) {
                            $tiwilioapicred = get_option('digit_twilio_api');
                            $twiliosenderid = $tiwilioapicred['twiliosenderid'];
                            $sid = $tiwilioapicred['twiliosid'];
                            $token = $tiwilioapicred['twiliotoken'];

                            $mobile = trim($value);
                            try {
                                $client = new Client($sid, $token);
                                $result = $client->messages->create(
                                    $mobile,
                                    array(
                                        'From' => $twiliosenderid,
                                        'Body' => $content
                                    )
                                );
                                update_user_meta($u_id, 'is_sent_sms', 'yes');
                                update_user_meta($u_id, 'is_sent_sms_date', date_i18n('Y-m-d'));
                                $check = 1;
                                $total_sent++;
                            } catch (Exception $e) {
                                var_dump('==' . $mobile . '==' . "\n" . $e->getMessage() . "\n");
                            }
                        }
                    }
                    if ($check == 1) {
                        $err_send = __('Send SMS completed: ' . $total_sent . ' users', 'zoa');
                    }
                } else {
                    $err_send = __("Don't have imported mobile customers to send SMS", 'zoa');
                }
            }
            // for test
            if (isset($_POST['save_sms_send_test'])) {
                $all_phone = $_REQUEST['phone_number_test'];
                if (!empty($all_phone)) {
                    $all_phone = explode(",", $all_phone);
                    $content = trim(get_option('send_sms_content', false));
                    $check = 0;
                    $total_sent = 0;
                    if (!empty($content)) {
                        foreach ($all_phone as $u_id => $value) {
                            $tiwilioapicred = get_option('digit_twilio_api');
                            $twiliosenderid = $tiwilioapicred['twiliosenderid'];
                            $sid = $tiwilioapicred['twiliosid'];
                            $token = $tiwilioapicred['twiliotoken'];

                            $mobile = trim($value);
                            try {
                                $client = new Client($sid, $token);
                                $result = $client->messages->create(
                                    $mobile,
                                    array(
                                        'From' => $twiliosenderid,
                                        'Body' => $content
                                    )
                                );
                                $check = 1;
                                $total_sent++;
                            } catch (Exception $e) {
                                var_dump('==' . $mobile . '==' . "\n" . $e->getMessage() . "\n");
                            }
                        }
                    }
                    if ($check == 1) {
                        $err_send = __('Test Send SMS completed: ' . $total_sent . ' users', 'zoa');
                    }
                } else {
                    $err_send = __("Don't have imported mobile customers to send SMS", 'zoa');
                }
            }
            ob_start();
        ?>
            <?php if (isset($_POST['save_sms_setting'])) : ?>
                <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                    <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
                </div>
            <?php endif; ?>
            <?php if (isset($err_send)) : ?>
                <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                    <p><strong><?php echo $err_send; ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
                </div>
            <?php endif; ?>
            <h3><?php esc_html_e('Send bulk sms only for imported mobile user:', 'zoa'); ?></h3>
            <i><?php esc_html_e('NOTE: each phone number send only once for each imported mobile user.', 'zoa'); ?></i>
            <hr />
            <form method="POST">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label><?php esc_html_e('SMS content', 'zoa'); ?></label></th>
                            <td>
                                <textarea rows="15" cols="100" name="send_sms_content"><?php echo get_option('send_sms_content'); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php esc_html_e('Enter phone number to test.', 'zoa'); ?></label></th>
                            <td>
                                <input type="text" style="width: 400px;" name="phone_number_test" /> (<i><?php esc_html_e('If need multiple please use comma. Example: +81091241241421,+81087324234', 'zoa'); ?></i>)
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="submit" name="save_sms_setting" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Setting', 'zoa'); ?>">
                <input type="submit" onclick="return confirm('Are you sure?')" name="save_sms_send" id="submit" class="button button-primary" value="<?php esc_attr_e('Send Bulk SMS', 'zoa'); ?>">
                <input type="submit" onclick="return confirm('Are you sure?')" name="save_sms_send_test" id="submit" class="button button-primary" value="<?php esc_attr_e('Test Send Bulk SMS', 'zoa'); ?>">
            </form>
        <?php
            // get to show log
            $us = get_users(['role__in' => ['customer'], 'fields' => array('ID'), 'meta_key' => 'is_sent_sms', 'meta_value' => 'yes', 'meta_compare' => '=']);
            if (!empty($us)) :
                $group_date = array();
                foreach ($us as $value) {
                    $is_sent_sms_date = get_user_meta($value->ID, 'is_sent_sms_date', true);
                    $group_date[$is_sent_sms_date][] = $value->ID;
                }
                if (!empty($group_date)) :
            ?>
                    <strong><?php esc_html_e('Log sent.', 'zoa'); ?></strong>
                    <style>
                        .logs td,
                        .logs th {
                            border: 1px solid #ddd;
                            padding: 0 8px;
                        }
                    </style>
                    <table class="logs">
                        <thead>
                            <tr>
                                <td><?php esc_html_e('Date', 'zoa'); ?></td>
                                <td><?php esc_html_e('Total Sent', 'zoa'); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group_date as $key => $value) : ?>
                                <tr>
                                    <td><?php echo $key; ?></td>
                                    <td><?php echo count($value); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
            <?php
                endif;
            endif;
            $contents = ob_get_contents();
            ob_end_clean();
            echo $contents;
        }
    }

    function send_email_bulk_by_csv() {
        if (isset($_POST['save_im_setting'])) {
            update_option('send_email_csv_subject_email', $_REQUEST['send_email_csv_subject_email']);
            update_option('send_email_csv_content_email', $_REQUEST['send_email_csv_content_email']);
            update_option('send_email_csv_type', $_REQUEST['send_email_csv_type']);
        }
        $upload_dir = wp_upload_dir();
        $csv_dirname = $upload_dir['basedir'] . '/csv_email_send_schedule/';
        if (!file_exists($csv_dirname)) {
            wp_mkdir_p($csv_dirname);
        }
        $target_file = $csv_dirname . 'email_list.csv';
        if (isset($_POST['delete_csv_schedule'])) {
            unlink($target_file);
        }
        if (isset($_POST['save_im_csv_schedule'])) {
            update_option('send_email_csv_subject_email', $_REQUEST['send_email_csv_subject_email']);
            update_option('send_email_csv_content_email', $_REQUEST['send_email_csv_content_email']);
            update_option('send_email_csv_type', $_REQUEST['send_email_csv_type']);
            if (isset($_FILES["file"]["name"])) {
                if ($_FILES["file"]["error"] > 0) {
                    if ($_FILES["file"]["error"] == 4) {
                        $err_send = __('No file was uploaded  ', 'zoa');
                    }
                } else {
                    $value = explode(".", $_FILES['file']['name']);
                    $ext = strtolower(array_pop($value));
                    if ($ext === 'csv') {
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                            $err_send = __('Save completed.', 'zoa');
                        }
                    }
                }
            }
        }

        if (isset($_POST['send_email_by_csv'])) {
            update_option('send_email_csv_subject_email', $_REQUEST['send_email_csv_subject_email']);
            update_option('send_email_csv_content_email', $_REQUEST['send_email_csv_content_email']);
            update_option('send_email_csv_type', $_REQUEST['send_email_csv_type']);
            $all_emails = array();
            $emails_sent = array();
            if (isset($_FILES["file"]["name"])) {
                if ($_FILES["file"]["error"] > 0) {
                    if ($_FILES["file"]["error"] == 4) {
                        $err_send = __('No file was uploaded  ', 'zoa');
                    }
                } else {
                    $value = explode(".", $_FILES['file']['name']);
                    $ext = strtolower(array_pop($value));
                    if ($ext === 'csv') {
                        $tmpName = $_FILES["file"]["tmp_name"];
                        $send_type = $_REQUEST['send_email_csv_type'];
                        if (($handle = fopen($tmpName, 'r')) !== FALSE) {
                            set_time_limit(0);
                            $row = 0;
                            $head = array();
                            $log_send_emails_csv = get_option('log_send_emails_csv', '');
                            $emails_in_log = array();
                            if (!empty($log_send_emails_csv)) {
                                $log_send_emails_csv_arr = json_decode($log_send_emails_csv, true);
                                foreach ($log_send_emails_csv_arr as $key_log => $value_log) {
                                    $emails_in_log = array_merge($emails_in_log, $value_log);
                                }
                            }
                            while (($data = fgetcsv($handle, 10000, ',')) !== FALSE) { //echo $row;
                                //get key by hear
                                if ($row == 0) {
                                    $head = $data;
                                    $head_key = array_flip($head);
                                }
                                //end
                                if ($row >= 51) {
                                    break;
                                }
                                if ($row <> 0) {
                                    if ($head_key['email'] == '') {
                                        $head_key['email'] = 0;
                                    }
                                    if (!empty($data[$head_key['email']])) {
                                        if ($send_type == 1) {
                                            if (!in_array(trim($data[$head_key['email']]), $emails_in_log)) {
                                                $all_emails[] = trim($data[$head_key['email']]);
                                            }
                                        } else {
                                            $all_emails[] = trim($data[$head_key['email']]);
                                        }
                                    }
                                }
                                $row++;
                            }
                        }
                        if (!empty($all_emails)) {
                            $all_emails = array_unique($all_emails);
                            $site_title = get_bloginfo('name');
                            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
                            $attachments = array();

                            $subject = trim(get_option('send_email_csv_subject_email', false));
                            $message = trim(get_option('send_email_csv_content_email', false));
                            $message = stripslashes($message);
                            $check = 0;
                            if (!empty($subject) && !empty($message)) {
                                add_filter('wp_mail_content_type', 'ch_set_html_content_type');
                                foreach ($all_emails as $u_id => $value) {
                                    $value = trim($value);
                                    $sent_message = wp_mail($value, $subject, $message, $headers, $attachments);
                                    if ($sent_message) {
                                        $emails_sent[] = $value;
                                    }
                                }
                                if (!empty($emails_sent)) {
                                    $ready_log = false;
                                    $log_send_emails_csv = get_option('log_send_emails_csv', '');
                                    $log = array(date_i18n('Y-m-d') => $emails_sent);
                                    if (!empty($log_send_emails_csv)) {
                                        $log_send_emails_csv_arr = json_decode($log_send_emails_csv, true);
                                        foreach ($log_send_emails_csv_arr as $key_log => $value_log) {
                                            if (date_i18n('Y-m-d') == $key_log) {
                                                $value_log = array_merge($value_log, $emails_sent);
                                                $log_send_emails_csv_arr[$key_log] = $value_log;
                                                $ready_log = true;
                                                break;
                                            }
                                        }
                                        if ($ready_log == false) {
                                            $log_send_emails_csv_arr = array_merge($log_send_emails_csv_arr, $log);
                                        }
                                        update_option('log_send_emails_csv', json_encode($log_send_emails_csv_arr));
                                    } else {
                                        update_option('log_send_emails_csv', json_encode($log));
                                    }
                                }
                                $check = 1;
                                remove_filter('wp_mail_content_type', 'ch_set_html_content_type');
                            }
                            if ($check == 1) {
                                $err_send = __('Send emails completed.', 'zoa') . '. Total' . count($emails_sent) . '. Detail: ' . json_encode($emails_sent);
                            }
                        } else {
                            $err_send = __("Don't have emails to send emails OR all emails sent before.", 'zoa');
                        }
                    } else {
                        $err_send = __('File Should be CSV file ', 'zoa');
                    }
                }
            }
            //end emails
        }
        ob_start();
    ?>
        <?php if (isset($_POST['save_im_setting'])) : ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
            </div>
        <?php endif; ?>
        <?php if (isset($err_send)) : ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php echo $err_send; ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
            </div>
        <?php endif; ?>
        <h3><?php esc_html_e('Send bulk by importing csv as email list:', 'zoa'); ?></h3>
        <hr />
        <form action="" method="POST" enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Email subject', 'zoa'); ?></label></th>
                        <td>
                            <textarea rows="2" cols="100" name="send_email_csv_subject_email"><?php echo get_option('send_email_csv_subject_email'); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Email content', 'zoa'); ?></label></th>
                        <td>
                            <?php
                                $common_notion_content_email = get_option('send_email_csv_content_email');
                                $common_notion_content_email = stripslashes($common_notion_content_email);
                                wp_editor($common_notion_content_email, 'send_email_csv_content_email', array('textarea_name' => 'send_email_csv_content_email', 'media_buttons' => false, 'editor_height' => 250, 'teeny' => true));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Send type', 'zoa'); ?></label></th>
                        <td>
                            <select name="send_email_csv_type">
                                <option <?php if (get_option('send_email_csv_type', '') == 1) echo 'selected="true"'; ?> value="1"><?php esc_html_e('Send only once', 'zoa'); ?></option>
                                <option <?php if (get_option('send_email_csv_type', '') == 2) echo 'selected="true"'; ?> value="2"><?php esc_html_e('Send no limit', 'zoa'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label> <?php esc_html_e('Select CSV file to upload:', 'zoa'); ?></label></th>
                        <td>
                            <input type="file" name="file" id="file">
                            <strong style="color:red"><?php esc_html_e('Please use csv file with maximum 50 records to avoid timeout for server.', 'zoa'); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('CSV file schedule to send :', 'zoa'); ?></label></th>
                        <td>
                            <?php if (file_exists($target_file)) : ?>
                                <a target="_blank" href="<?php echo $upload_dir['baseurl'] . '/csv_email_send_schedule/email_list.csv'; ?>"><?php echo $upload_dir['baseurl'] . '/csv_email_send_schedule/email_list.csv'; ?></a>
                                <input type="submit" name="delete_csv_schedule" id="submit" class="button button-primary" value="Delete">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Url to setting Cronjob on server schedule to send :', 'zoa'); ?></label></th>
                        <td>
                            <i><?php echo get_admin_url() . 'admin-ajax.php?action=schedule_send_email_bulk_by_csv'; ?></i>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" name="save_im_setting" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Setting', 'zoa'); ?>">
            <input type="submit" name="save_im_csv_schedule" id="submit" class="button button-primary" value="<?php esc_attr_e('Save CSV to send schedule', 'zoa'); ?>">
            <input onclick="return confirm('Are you sure?')" type="submit" name="send_email_by_csv" id="submit" class="button button-primary" value="<?php esc_attr_e('Send email', 'zoa'); ?>">
        </form>
    <?php
        // get to show log
        $log_send_emails_csv = get_option('log_send_emails_csv', '');
        if (!empty($log_send_emails_csv)) :
            $log_send_emails_csv_arr = json_decode($log_send_emails_csv, true);
            krsort($log_send_emails_csv_arr);
        ?>
            <strong><?php esc_html_e('Log sent.', 'zoa'); ?></strong>
            <style>
                .logs td,
                .logs th {
                    border: 1px solid #ddd;
                    padding: 0 8px;
                }
            </style>
            <table class="logs">
                <thead>
                    <tr>
                        <td><?php esc_html_e('Date', 'zoa'); ?></td>
                        <td><?php esc_html_e('Total Sent', 'zoa'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($log_send_emails_csv_arr as $key => $value) : ?>
                        <tr>
                            <td><?php echo $key; ?></td>
                            <td><?php echo count($value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    <?php
        endif;
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
    }

    add_action('add_meta_boxes', 'ch_product_tag_add_meta_boxes');
    if (!function_exists('ch_product_tag_add_meta_boxes')) {
        function ch_product_tag_add_meta_boxes() {
            add_meta_box('tag_extra_options', __('Tag Settings', 'woocommerce'), 'ch_tag_extra_options', 'product', 'side', 'core');
        }
    }

    if (!function_exists('ch_tag_extra_options')) {

        function ch_tag_extra_options() {
            global $post;
            $show_tag_front = get_post_meta($post->ID, 'show_tag_front', true) ? get_post_meta($post->ID, 'show_tag_front', true) : '';
            $bg_color_tag = get_post_meta($post->ID, 'bg_color_tag', true) ? get_post_meta($post->ID, 'bg_color_tag', true) : '';
            $checked = '';
            if ($show_tag_front == 'yes' || $show_tag_front == 1) {
                $checked = ' checked="true"';
            }
            ?>
            <script>
                jQuery(document).ready(function($) {
                    $('.my-color-field').wpColorPicker();
                });
            </script>
            <?php
            echo '<input type="hidden" name="ch_order_type_mv_other_meta_field_nonce" value="' . wp_create_nonce() . '">';
            echo '<input type="checkbox" name="show_tag_front" ' . $checked . 'value="yes"/> ' . __('Show tag on front', 'zoa');
            echo '<p>Background color: <input type="text" value="' . $bg_color_tag . '" id="bg_color_tag" name="bg_color_tag" class="my-color-field" data-default-color="#effeff" /></p>';
        }
    }

    // end
    // send bulk email and auto generate coupon code
    function send_email_bulk_by_csv_couponcode() {
        if (isset($_POST['save_im_setting_coupon'])) {
            update_option('send_email_csv_subject_email_coupon', $_REQUEST['send_email_csv_subject_email_coupon']);
            update_option('send_email_csv_content_email_coupon', $_REQUEST['send_email_csv_content_email_coupon']);
            update_option('send_email_csv_type_coupon', $_REQUEST['send_email_csv_type_coupon']);
        }

        if (isset($_POST['send_email_by_csv_coupon'])) {
            update_option('send_email_csv_subject_email_coupon', $_REQUEST['send_email_csv_subject_email_coupon']);
            update_option('send_email_csv_content_email_coupon', $_REQUEST['send_email_csv_content_email_coupon']);
            update_option('send_email_csv_type_coupon', $_REQUEST['send_email_csv_type_coupon']);
            $all_emails = array();
            $emails_sent = array();
            if (isset($_FILES["file"]["name"])) {
                if ($_FILES["file"]["error"] > 0) {
                    if ($_FILES["file"]["error"] == 4) {
                        $err_send = 'No file was uploaded  ';
                    }
                } else {
                    $value = explode(".", $_FILES['file']['name']);
                    $ext = strtolower(array_pop($value));
                    if ($ext === 'csv') {
                        $tmpName = $_FILES["file"]["tmp_name"];
                        $send_type = $_REQUEST['send_email_csv_type_coupon'];
                        if (($handle = fopen($tmpName, 'r')) !== FALSE) {
                            set_time_limit(0);
                            $row = 0;
                            $head = array();
                            $log_send_emails_csv = get_option('log_send_emails_csv_coupon', '');
                            $emails_in_log = array();
                            if (!empty($log_send_emails_csv)) {
                                $log_send_emails_csv_arr = json_decode($log_send_emails_csv, true);
                                foreach ($log_send_emails_csv_arr as $key_log => $value_log) {
                                    $emails_in_log = array_merge($emails_in_log, $value_log);
                                }
                            }
                            while (($data = fgetcsv($handle, 10000, ',')) !== FALSE) { // echo $row;
                                //get key by hear
                                if ($row == 0) {
                                    $head = $data;
                                    $head_key = array_flip($head);
                                }
                                //end
                                if ($row >= 51) {
                                    break;
                                }
                                if ($row <> 0) {
                                    if ($head_key['email'] == '') {
                                        $head_key['email'] = 0;
                                    }
                                    if (!empty($data[$head_key['email']])) {
                                        if ($send_type == 1) {
                                            if (!in_array(trim($data[$head_key['email']]), $emails_in_log)) {
                                                $all_emails[] = trim($data[$head_key['email']]);
                                            }
                                        } else {
                                            $all_emails[] = trim($data[$head_key['email']]);
                                        }
                                    }
                                }
                                $row++;
                            }
                        }
                        if (!empty($all_emails)) {
                            $all_emails = array_unique($all_emails);
                            $site_title = get_bloginfo('name');
                            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
                            $attachments = array();

                            $subject = trim(get_option('send_email_csv_subject_email_coupon', false));
                            $message = trim(get_option('send_email_csv_content_email_coupon', false));
                            $message = stripslashes($message);
                            $check = 0;
                            if (!empty($subject) && !empty($message)) {
                                add_filter('wp_mail_content_type', 'ch_set_html_content_type');
                                foreach ($all_emails as $u_id => $value) {
                                    $value = trim($value);
                                    $coupon_code = coupon_code_each_auto();
                                    $message_coupon = str_replace('{coupon_code}', $coupon_code, $message);
                                    $sent_message = wp_mail($value, $subject, $message_coupon, $headers, $attachments);
                                    if ($sent_message) {
                                        $emails_sent[] = $value;
                                    }
                                }
                                if (!empty($emails_sent)) {
                                    $ready_log = false;
                                    $log_send_emails_csv = get_option('log_send_emails_csv_coupon', '');
                                    $log = array(date_i18n('Y-m-d') => $emails_sent);
                                    if (!empty($log_send_emails_csv)) {
                                        $log_send_emails_csv_arr = json_decode($log_send_emails_csv, true);
                                        foreach ($log_send_emails_csv_arr as $key_log => $value_log) {
                                            if (date_i18n('Y-m-d') == $key_log) {
                                                $value_log = array_merge($value_log, $emails_sent);
                                                $log_send_emails_csv_arr[$key_log] = $value_log;
                                                $ready_log = true;
                                                break;
                                            }
                                        }
                                        if ($ready_log == false) {
                                            $log_send_emails_csv_arr = array_merge($log_send_emails_csv_arr, $log);
                                        }
                                        update_option('log_send_emails_csv_coupon', json_encode($log_send_emails_csv_arr));
                                    } else {
                                        update_option('log_send_emails_csv_coupon', json_encode($log));
                                    }
                                }
                                $check = 1;
                                remove_filter('wp_mail_content_type', 'ch_set_html_content_type');
                            }
                            if ($check == 1) {
                                $err_send = __("Send emails completed.", 'zoa') . '. Total' . count($emails_sent) . '. Detail: ' . json_encode($emails_sent);
                            }
                        } else {
                            $err_send = __("Don't have emails to send emails OR all emails sent before.", 'zoa');
                        }
                    } else {
                        $err_send = "File Should be CSV file ";
                    }
                }
            }
            //end emails
        }
        ob_start();
    ?>
        <?php if (isset($_POST['save_im_setting_coupon'])) : ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
            </div>
        <?php endif; ?>
        <?php if (isset($err_send)) : ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                <p><strong><?php echo $err_send; ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
            </div>
        <?php endif; ?>
        <h3><?php esc_html_e('Send bulk by importing csv as email list and auto generated coupon code(unique for each):', 'zoa'); ?></h3>
        <hr />
        <form method="POST" enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Email subject', 'zoa'); ?></label></th>
                        <td>
                            <textarea rows="2" cols="100" name="send_email_csv_subject_email_coupon"><?php echo get_option('send_email_csv_subject_email_coupon'); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Email content', 'zoa'); ?></label></th>
                        <td>
                            <?php
                                $common_notion_content_email = get_option('send_email_csv_content_email_coupon');
                                $common_notion_content_email = stripslashes($common_notion_content_email);
                                wp_editor($common_notion_content_email, 'send_email_csv_content_email_coupon', array('textarea_name' => 'send_email_csv_content_email_coupon', 'media_buttons' => false, 'editor_height' => 250, 'teeny' => true));
                            ?>
                            <strong><?php esc_html_e('Shortcode:', 'zoa'); ?> {coupon_code}</strong>
                            <p><i><?php esc_html_e('This coupon code value will expired in 30days from send day.', 'zoa'); ?></i></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Send type', 'zoa'); ?></label></th>
                        <td>
                            <select name="send_email_csv_type_coupon">
                                <option <?php if (get_option('send_email_csv_type_coupon', '') == 2) echo 'selected="true"'; ?> value="2"><?php esc_html_e('Send no limit', 'zoa'); ?></option>
                                <option <?php if (get_option('send_email_csv_type_coupon', '') == 1) echo 'selected="true"'; ?> value="1"><?php esc_html_e('Send only once', 'zoa'); ?></option>
                            </select>
                            <?php esc_html_e("Send only once: that mean will don't send email to that email if it sent before. Send no limit: that mean you can send email to that email on manytime.", 'zoa'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label> <?php esc_html_e('Select CSV file to upload:', 'zoa'); ?></label></th>
                        <td>
                            <input type="file" name="file" id="file">
                            <i><?php esc_html_e('Csv format is only 1 column with name is: email', 'zoa'); ?></i>
                            <div>
                                <strong style="color:red"><?php esc_html_e('Please use csv file with maximum 50 records to avoid timeout for server.', 'zoa'); ?></strong>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" name="save_im_setting_coupon" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Setting', 'zoa'); ?>">
            <input onclick="return confirm('Are you sure?')" type="submit" name="send_email_by_csv_coupon" id="submit" class="button button-primary" value="<?php esc_attr_e('Send email', 'zoa'); ?>">
        </form>
    <?php
        // get to show log
        $log_send_emails_csv = get_option('log_send_emails_csv_coupon', '');
        if (!empty($log_send_emails_csv)) :
            $log_send_emails_csv_arr = json_decode($log_send_emails_csv, true);
            krsort($log_send_emails_csv_arr);
        ?>
            <strong><?php esc_html_e('Log sent.', 'zoa'); ?></strong>
            <style>
                .logs td,
                .logs th {
                    border: 1px solid #ddd;
                    padding: 0 8px;
                }
            </style>
            <table class="logs">
                <thead>
                    <tr>
                        <td><?php esc_html_e('Date', 'zoa'); ?></td>
                        <td><?php esc_html_e('Total Sent', 'zoa'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($log_send_emails_csv_arr as $key => $value) : ?>
                        <tr>
                            <td><?php echo $key; ?></td>
                            <td><?php echo count($value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    <?php
        endif;
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
    }
    // end

    include_once dirname(__FILE__) . '/wc-try-fit-your-size/wc-try-fit-your-size.php'; // Try Before you buy code
    include_once dirname(__FILE__) . '/order-reception-function/order-reception-function.php'; // Order reception function
    include_once dirname(__FILE__) . '/wc-email-reminder-bacs.php'; // Email reminder for BACS
    include_once dirname(__FILE__) . '/member-rank-functions/member-rank-functions.php'; // Member rank functions
    include_once dirname(__FILE__) . '/report/report_data.php'; // Report data functions
    include_once dirname(__FILE__) . '/family-sale-request/family-sale-request.php'; // Family sale request
    include_once dirname(__FILE__) . '/auto_email_for_review/auto_email_for_review.php'; // Auto email for review
    include_once dirname(__FILE__) . '/report_order_by_sub_type/report_order_by_sub_type.php'; // Report order by sub type
    include_once dirname(__FILE__) . '/export_customer_by_state/export_customer_by_state.php'; // Export customer by state
    include_once dirname(__FILE__) . '/export_customer_by_sub_type/export_customer_by_sub_type.php'; // Export customer by sub type
    include_once dirname(__FILE__) . '/foreigner-status-emails-woocommerce/foreigner-status-emails-woocommerce.php'; // Foreigner status emails woocommerce
    include_once dirname(__FILE__) . '/family-sale-closed-store/family-sale-closed-store.php'; // Family sale closed store

    // Show Product Variation Image from Woo Thumb Plugin
    add_filter('woocommerce_cart_item_thumbnail', 'custom_variation_image_in_cart', 100, 3);
    function custom_variation_image_in_cart($product_image, $cart_item, $cart_item_key) {
        // Check if the cart item is a variation

        if ( in_array('iconic-woothumbs/iconic-woothumbs.php', apply_filters('active_plugins', get_option('active_plugins'))) && isset($cart_item['variation_id']) && $cart_item['variation_id']) {

            $product_id = $cart_item['product_id'];
            $ai_data = get_post_meta( $product_id, '_iconic_woothumbs_ai', true );
            $ai_data = ( $ai_data ) ? $ai_data : array();
            
            // Get the variation product object
            $variation = wc_get_product($cart_item['variation_id']);
            // Get the variation image
            if ($variation) {
                $selected_attributes = $variation->get_variation_attributes();

                // Loop through the attributes to see what the user selected
                foreach ($selected_attributes as $attribute_name => $attribute_value) {
                    $attribute_name = str_replace('attribute_', '', $attribute_name);
                    if ( '' !== $attribute_value && isset($ai_data[$attribute_name]) && isset($ai_data[$attribute_name]['terms']) && !empty($ai_data[$attribute_name]) && !empty($ai_data[$attribute_name]['terms']) ) {
                        foreach ($ai_data[$attribute_name]['terms'] as $term_data) {
                            $term = strtolower(str_replace(' ', '-', $term_data['term']));
                            $term_images = $term_data['images'] ?? '';
                            $attr_value = strtolower(str_replace(' ', '-', $attribute_value));

                            if ( $attr_value === $term && is_array($term_images) && !empty($term_images) ) {
                                $product_image = wp_get_attachment_image($term_images[0]['id'], 'woocommerce_thumbnail');
                                break;
                            }
                        }
                    }
                }
            }

        }

        return $product_image;
    }

    // Footer Script
    add_action( 'wp_footer', function() {
        if ( function_exists('is_checkout') && is_checkout() ) : ?>
            <script>
                jQuery(document).ready(function ($) {

                    // Change Checkout fields order
                    setTimeout(() => {
                        $('#billing_email_field').insertBefore('#shipping_last_name_field');
                        // $('#billing_phone_field').insertAfter('#billing_address_2_field');
                    }, 1200);

                    // Autocomplete address by zipcode using free API
                    $(document).on('focusout', '[name="billing_postcode"], [name="shipping_postcode"]', async function(e) {
                        let $this = $(this),
                            nameAttr = $this.attr('name'),
                            type = nameAttr.split('_')[0],
                            postalCode = $this.val();

                            const url = 'https://zipcloud.ibsnet.co.jp/api/search?zipcode=' + postalCode;
                            const response = await fetch(url);
                            const data = await response.json();
                            if(data['results']) {
                                let APIdata = data['results'][0];
                                $(`[name="${type}_address_1"]`).val(APIdata.address1).trigger('change');
                                $(`[name="${type}_address_2"]`).val(APIdata.address2 + ' ' + APIdata.address3).trigger('change');
                            }
                    });

                });
            </script>
        <?php
        endif;
    }, 999999999999 );

    // Do you have experience with fittings Field Save for user meta
    add_action( 'woocommerce_checkout_update_order_meta', 'chiyono_save_custom_checkout_field' );
    function chiyono_save_custom_checkout_field( $order_id ) {
        update_post_meta( get_current_user_id(), 'ch_fitting', sanitize_text_field( $_POST['ch_fitting'] ) );
    }

    // Add custom class to body
    add_filter('body_class', 'zoa_child_theme_body_class');
    function zoa_child_theme_body_class($classes) {
        $classes[] = 'zoa-child-theme';
        return $classes;
    }
