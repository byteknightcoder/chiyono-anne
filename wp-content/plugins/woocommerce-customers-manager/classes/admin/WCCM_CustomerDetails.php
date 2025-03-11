<?php
require_once(WCCM_PLUGIN_ABS_PATH . 'classes/lang/Countries.php');

class WCCM_CustomerDetails
{

    var $user_info;
    var $current_customer_id;
    var $customer_info;
    var $customer_extra_info;
    var $starting_date;
    var $ending_date;
    var $date_range_error;
    var $show_date_range_alert;
    var $view_period_range;
    var $date_format;
    var $error;
    var $mail_message_outcome;
    var $is_guest_customer;

    function __construct()
    {
        global $status, $page;


        $this->messages = array();
        $this->show_date_range_alert = false;
        $this->view_period_range = isset($_POST['view_period_range']) ? $_POST['view_period_range'] : 'monthly';
        $this->starting_date = null;
        $this->ending_date = null;

        if ((isset($_POST['end_date_submit']) && isset($_POST['start_date_submit'])) && ($_POST['end_date_submit'] != '' || $_POST['end_date_submit'] != '') && strtotime($_POST['end_date_submit']) >= strtotime($_POST['start_date_submit'])) {
            $this->starting_date = $_POST['start_date_submit'];
            $this->ending_date = $_POST['end_date_submit'];
            $this->date_range_error = false;
        } else {
            $this->date_range_error = true;
            $this->show_date_range_alert = true;
        }
        if (!isset($_POST['end_date_submit']) && !isset($_POST['start_date_submit']) || ($_POST['end_date_submit'] == '' && $_POST['end_date_submit'] == '')) {
            $this->date_range_error = false;
            //$this->starting_date = date('Y/01/01');
            $this->starting_date = date('Y/m/d', strtotime('-5 year'));
            $this->ending_date = date('Y/m/d');
            $this->show_date_range_alert = false;
        }

        add_action('wp_footer', array(&$this, 'include_footer_scripts'));
    }

    function include_footer_scripts()
    {
        ?>
        <!--
        <script  type="module" src="<?php //echo WCCM_PLUGIN_PATH; ?>/js/maps/leaflet-geosearch-master/src/providers/index.js"></script>
        <script  type="module" src="<?php //echo WCCM_PLUGIN_PATH; ?>/js/admin-map-module.js"></script>-->
        <?php
    }

    function get_user_info($user_id = null)
    {
        $this->is_guest_customer = false;
        if (isset($_GET['customer'])) {
            $this->current_customer_id = $user_id == null ? $_GET['customer'] : $user_id;
            $this->customer_info = get_userdata($this->current_customer_id);
            $this->customer_extra_info = get_user_meta($this->current_customer_id);
        } elseif ($_GET['customer_email']) {
            global $wccm_order_model;
            $this->is_guest_customer = true;
            $this->current_customer_id = $_GET['customer_email'];
            $all_data = $wccm_order_model->get_guest_user_data_from_last_order($_GET['customer_email']);
            //wp_die();
            $this->customer_info = $all_data['customer_info'];
            $this->customer_extra_info = $all_data['customer_extra_info'];
        }
    }

    private function send_notification_mail()
    {
        /* var_dump($_POST); */
        $mail = new WCCM_Email();
        $user = get_user_by('email', $_POST['customer_mail']);
        $mail->trigger($_POST['customer_mail'], $_POST['mail_subject'], $_POST['mail_text'], "notification", $user->data->ID);
        $this->mail_message_outcome = __('Your message has been successfully sent.', 'woocommerce-customers-manager');
    }

    private function update_user()
    {
        update_user_meta($_POST['customer_id'], 'wccm_customer_notes', $_POST['customer_notes']);
    }

    function render_page()
    {
        global $wccm_wpml_helper, $wccm_customer_model, $wccm_html_model, $wp_roles, $wpuef_htmlHelper, $wccm_product_model, $wcmca_wpml_helper, $wccm_order_model;
        if (isset($_POST['save_bth'])) {
            if (!empty($_REQUEST['birthday'])) {
                $user_id = $_REQUEST['customer_id'];
                $arr_brth = explode("-", $_REQUEST['birthday']);
                $bod = array('year' => $arr_brth[0], 'month' => $arr_brth[1] . "月", 'day' => $arr_brth[2]);
                update_user_meta($user_id, 'account_birth', $bod);
            }
        }

        if (isset($_POST['save_force_rank'])) {
            $user_id = $_REQUEST['customer_id'];
            if (isset($_POST['force_change_rank'])) {
                $force_change_rank = 'yes';
            } else {
                $force_change_rank = 'no';
            }
            $rank_f = $_REQUEST['force_rank'];
            update_user_meta($user_id, 'is_force_change_rank', $force_change_rank);
            update_user_meta($user_id, 'force_rank', $rank_f);
        }

        if (isset($_POST['send_mail'])) {
            $this->send_notification_mail($_POST);
        } else if (isset($_POST['customer_id'])) {
            $this->update_user();
        } else if (isset($_POST['wccm_order_to_assign'])) { //Assign order to user save process
            $order_ids = $_POST['wccm_order_to_assign'];
            $additional_params = array('overwrite_billing_data' => isset($_POST['wccm_order_to_assign_overwrite_billing_data']), 'overwrite_shipping_data' => isset($_POST['wccm_order_to_assign_overwrite_shipping_data']));

            $wccm_order_model->assign_users($order_ids, $_GET['customer'], $additional_params);
        }
        $user_info = get_userdata($_REQUEST['customer']);
        $locale = get_user_locale($_REQUEST['customer']);
        $this->get_user_info();
        try {
            if (isset($this->customer_extra_info['billing_country'][0]) && file_exists(plugin_dir_path(__FILE__) . 'i18n/' . $this->customer_extra_info['billing_country'][0] . '.php'))
                include(plugin_dir_path(__FILE__) . 'i18n/' . $this->customer_extra_info['billing_country'][0] . '.php');
        } catch (Exception $e) {

        }
        try {
            if (isset($this->customer_extra_info['shipping_country'][0]) && file_exists(plugin_dir_path(__FILE__) . 'i18n/' . $this->customer_extra_info['shipping_country'][0] . '.php'))
                include(plugin_dir_path(__FILE__) . 'i18n/' . $this->customer_extra_info['shipping_country'][0] . '.php');
        } catch (Exception $e) {

        }

        $js_src = includes_url('js/tinymce/') . 'tinymce.min.js';
        $css_src = includes_url('css/') . 'editor.css';
        echo '<script src="' . $js_src . '" type="text/javascript"></script>';
        wp_register_style('tinymce_css', $css_src);
        wp_enqueue_style('tinymce_css');

        $maps_keys = array(
            'AIzaSyAZ1WISwXCzQ7DiGDf2hlMXl8Utn4ha2tE',
            'AIzaSyDPzrSpu0pgXQdJbBi3OlL3kxMY3uTwiB4',
            'AIzaSyDC8X2Vx9cy_oGOEBJfsDp_pbk1FcpTB38',
            'AIzaSyCf1aktDukUnBpIyKie9RKfUMwPqIfHQvM',
            'AIzaSyC5abm67txvc84dcpeecJNGblfg6o2qWXA',
            'AIzaSyBSxo-fQfldIdkJy1jPH5zCG2egrPRCLak'
        );
        ?> -
        <!--<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=false&key=<?php //echo $maps_keys[rand(0, count($maps_keys) - 1)]; ?>"></script> -->
        <?php
        wp_enqueue_style('datepicker-classic', WCCM_PLUGIN_PATH . '/css/datepicker/classic.css');
        wp_enqueue_style('datepicker-date-classic', WCCM_PLUGIN_PATH . '/css/datepicker/classic.date.css');
        wp_enqueue_style('datepicker-time-classic', WCCM_PLUGIN_PATH . '/css/datepicker/classic.time.css');
        wp_enqueue_style('wccm-common', WCCM_PLUGIN_PATH . '/css/common.css');
        wp_enqueue_style('custom-details-view', WCCM_PLUGIN_PATH . '/css/custom-details.css');
        wp_enqueue_style('wccm-leaflet', WCCM_PLUGIN_PATH . '/css/maps/leaflet.css');

        /* wp_deregister_script('jquery-ui-core'); */
        //wp_enqueue_script('ui-google-map-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyArOnqf0FHb0EAAxpNt5OmqnNS-FldclHk&callback=initMap');
        wp_enqueue_script('ui-chart', WCCM_PLUGIN_PATH . '/js/Chart.min.js');
        //wp_enqueue_script('ui-chart-stackedbar', WCCM_PLUGIN_PATH.'/js/Chart.StackedBar.js');
        wp_enqueue_script('ui-picker', WCCM_PLUGIN_PATH . '/js/picker.js');
        wp_enqueue_script('ui-datepicker', WCCM_PLUGIN_PATH . '/js/picker.date.js');
        wp_enqueue_script('ui-timepicker', WCCM_PLUGIN_PATH . '/js/picker.time.js');
        wp_enqueue_script('wccm-simple-pagination', WCCM_PLUGIN_PATH . '/js/paging.js', array('jquery'));
        wp_enqueue_script('wccm-order-table-paginanion', WCCM_PLUGIN_PATH . '/js/admin-customer-details-orders-table-pagination.js', array('jquery'));
        wp_enqueue_script('wccm-leaflet', WCCM_PLUGIN_PATH . '/js/maps/leaflet.js', array('jquery'));
        wp_register_script('wccm-map', WCCM_PLUGIN_PATH . '/js/admin-customer-details-map.js', array('jquery'));
        //datepicker localization get_locale() 
        if (wccm_url_exists(WCCM_PLUGIN_PATH . '/js/time/translations/' . $wccm_wpml_helper->get_current_language() . '.js'))
            wp_enqueue_script('ui-timepicker-localization', WCCM_PLUGIN_PATH . '/js/time/translations/' . $wccm_wpml_helper->get_current_language() . '.js');
        ?>

        <?php if (isset($this->error) && is_wp_error($this->error)): ?>
            <div class="error">
                <ul>
                    <?php
                    foreach ($this->error->get_error_messages() as $err)
                        echo "<li>$err</li>\n";
                    ?>
                </ul>
            </div>
            <?php
        endif;
        if (!empty($this->mail_message_outcome)) {
            echo '<div id="message" class="updated"><ul>';
            //foreach ( $this->mail_message_outcome as $msg )
            echo '<li>' . $this->mail_message_outcome . '</li>';
            echo '</ul></div>';
        }
        ?>


        <h2><?php _e('Customers Details', 'woocommerce-customers-manager'); ?></h2>
        <div id="cdetails_postbox" class="postbox">
            <?php
            $orders = $this->get_all_user_orders($this->current_customer_id, $this->starting_date, $this->ending_date);
            //for ($i=0; $i<50; $i++)
            $products_to_quantities_purchased = array();
            $orders_amount_per_order = array();
            ?>
            <div id="user-general-details">
                <h3><?php _e('General Details', 'woocommerce-customers-manager'); ?></h3>
                <div class="detail_box user-general-details_box">
                    <div class="profile_image">
                        <label><?php _e('Profile Image', 'woocommerce-customers-manager'); ?></label>
                        <?php echo get_avatar($this->current_customer_id, 96, "", false, array('class' => 'wccm_avatar_img')); ?>
                    </div>
                    <?php
                    if ($locale == 'ja' || $locale == 'site-default') {
                        ?>
                        <div class="form_row"><label><?php _e('Name', 'woocommerce-customers-manager'); ?></label><span
                                class="value"><?php if (isset($this->customer_extra_info['last_name']))
                                    echo $this->customer_extra_info['last_name'][0]//$this->customer_extra_info['last_name'][0]                                             ?></span>&nbsp;<span
                                    class="value"><?php if (isset($this->customer_extra_info['first_name']))
                                    echo $this->customer_extra_info['first_name'][0]; //$this->customer_extra_info['first_name'][0]                                                ?></span>
                        </div>

                        <?php
                    } else {
                        ?>
                        <div class="form_row"><label><?php _e('Name', 'woocommerce-customers-manager'); ?></label><span
                                class="value"><?php if (isset($this->customer_extra_info['first_name']))
                                    echo $this->customer_extra_info['first_name'][0]; //$this->customer_extra_info['first_name'][0]                                                ?></span>&nbsp;<span
                                class="value"><?php if (isset($this->customer_extra_info['last_name']))
                                    echo $this->customer_extra_info['last_name'][0]//$this->customer_extra_info['last_name'][0]                                             ?></span>
                            </div>
                    <?php } ?>
                    <div class="form_row"><label><?php _e('Email Address', 'woocommerce-customers-manager'); ?></label><span
                            class="value"><?php if (isset($this->customer_info->user_email))
                                echo $this->customer_info->user_email ?></span>
                        </div>
                        <?php
                            if (isset($this->customer_extra_info['account_birth'][0]) && !empty($this->customer_extra_info['account_birth'][0])) {
                                $account_birth = unserialize($this->customer_extra_info['account_birth'][0]);
                                $account_birth['month'] = str_replace("月", "", $account_birth['month']);
                                $account_birth = $account_birth['year'] . '-' . $account_birth['month'] . '-' . $account_birth['day'];
                            } else {
                                $account_birth = '';
                            }
                            ?>
                    <div class="form_row">
                        <label><?php _e('Birthday', 'woocommerce-customers-manager'); ?></label><span
                            class="value"><?php //echo $account_birth; ?></span>
                        <form action="" method="post">
                            <input type="hidden" value="<?php echo $_REQUEST['customer']; ?>" name="customer_id">
                            <input style="width: 30%" type="text" value="<?php echo $account_birth; ?>" name="birthday"
                                class="date_picker" />
                            <input type="submit" name="save_bth" class="button-primary"
                                value="<?php _e('Save Birthday', 'woocommerce-customers-manager'); ?>" />
                        </form>
                        <script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                $('.date_picker').datepicker({
                                    dateFormat: 'yy-mm-dd'
                                });
                            });
                        </script>
                    </div>
                    <?php
                    //}
                    ?>
                    <div class="form_row"><label><?php _e('Registration Date', 'woocommerce-customers-manager'); ?>
                        </label><span class="value"><?php if (isset($this->customer_info->user_registered))
                            echo $this->customer_info->user_registered ?></span>
                        </div>
                        <div class="form_row"><label><?php _e('Roles', 'woocommerce-customers-manager'); ?> </label>
                        <span class="value">
                            <?php
                            $user = new WP_User($this->current_customer_id);
                            if (!empty($user->roles) && is_array($user->roles)) {
                                foreach ($user->roles as $role_code)
                                    echo $wp_roles->roles[$role_code]["name"] . " (" . __('Role code:', 'woocommerce-customers-manager') . " <i>" . $role_code . "</i>)<br/>";
                            }
                            ?></span>
                    </div>
                    <div class="form_row"><label><?php _e('Total Spent', 'woocommerce-customers-manager'); ?> </label><span
                            class="value"><b><?php echo WCCM_CustomerDetails::get_user_total_spent(null, null, $this->get_all_user_orders($this->current_customer_id)/* $orders */); ?></b></span>
                    </div>
                    <div class="form_row"><label>Total Spent in 1 Year </label><span class="value"><b><?php mr_get_member_rank($this->current_customer_id);
                    echo get_user_meta($this->current_customer_id, 'spent_total', true); ?></b></span>
                    </div>
                    <div class="form_row"><label>Rank </label><span class="value"><b><?php mr_get_member_rank($this->current_customer_id);
                    echo get_user_meta($this->current_customer_id, 'rank', true); ?></b></span>
                    </div>
                    <?php if ((int) get_user_meta($this->current_customer_id, 'rank_limit', true) - (int) get_user_meta($this->current_customer_id, 'spent_retain', true) > 0) { ?>
                        <div class="form_row"><label>To Retain Current Rank </label><span
                                class="value"><b><?php mr_get_member_rank($this->current_customer_id);
                                echo (int) get_user_meta($this->current_customer_id, 'rank_limit', true) - (int) get_user_meta($this->current_customer_id, 'spent_retain', true); ?></b></span>
                        </div>
                    <?php } ?>
                    
                            <?php if (get_user_meta($this->current_customer_id, 'expired_date', true)) { ?>
                                <div class="form_row"><label>Next Update </label><span class="value"><b><?php mr_get_member_rank($this->current_customer_id);
                                echo get_user_meta($this->current_customer_id, 'expired_date', true) ?></b></span></div>
                            <?php } ?>
                            <div class="form_row">
                                <?php
                                $get_is_force_change_rank = get_user_meta($_REQUEST['customer'], 'is_force_change_rank', true);
                                $get_force_rank = get_user_meta($_REQUEST['customer'], 'force_rank', true);
                                ?>
                                <form action="" method="post">
                                    <label>
                                        <input <?php if ($get_is_force_change_rank == 'yes')
                                            echo 'checked="true"'; ?> type="checkbox" name="force_change_rank"/>
                                        <?php echo _e('Force to change rank?', 'woocommerce-customers-manager'); ?>
                                    </label>
                                    <?php
                                    $ranks = array('royal', 'gold', 'silver', 'bronze');
                                    ?>
                                    <select name="force_rank">
                                        <?php
                                        foreach ($ranks as $value_r) {
                                            $seleted = '';
                                            if ($get_force_rank == $value_r) {
                                                $seleted = ' selected="true"';
                                            }
                                            ?>
                                            <option <?php echo $seleted; ?> value="<?php echo $value_r ?>"><?php echo $value_r; ?></option>
                                                <?php
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" value="<?php echo $_REQUEST['customer']; ?>" name="customer_id">
                                    <input type="submit" name="save_force_rank" class="button-primary" value="<?php _e('Save Force Rank', 'woocommerce-customers-manager'); ?>"/>
                                </form>
                            </div>
                            <?php
                            $rank_and_amount = mr_get_member_rank($this->current_customer_id);
                            $rank = $rank_and_amount['rank'];
                            $settings = mr_get_settings();
                            if ($rank == 'gold') {
                                $coupon = new WC_Coupon($settings['ch_mr_coupon_2times']);
                                $usage_limit_per_user = $coupon->get_usage_limit_per_user();
                                $used = mr_get_coupon_used_by_user($this->current_customer_id, $coupon->id);
                                $remain = $usage_limit_per_user - $used;

                                ?>
                                <div class="form_row"><label><?php _e('Membership coupon available', 'woocommerce-customers-manager'); ?> </label><span class="value"><b><?php echo $coupon->coupon_amount . '%OFF × ' . $remain; ?></b></span></div>
                                <?php
                            } elseif ($rank == 'silver') {
                                $coupon = new WC_Coupon($settings['ch_mr_coupon_except_bra_2times']);
                                $usage_limit_per_user = $coupon->get_usage_limit_per_user();
                                $used = mr_get_coupon_used_by_user($this->current_customer_id, $coupon->id);
                                $remain = $usage_limit_per_user - $used;
                                ?>
                                <div class="form_row"><label><?php _e('Membership coupon available', 'woocommerce-customers-manager'); ?> </label><span class="value"><b><?php echo $coupon->coupon_amount . '%OFF × ' . $remain; ?></b></span></div>
                                <?php
                            }
                            ?>
                        </div>

                        <!--- Non Guest User extras -->
                        <?php if (!$this->is_guest_customer): ?>
                                <p>
                                    <a class="button-primary" href="<?php echo "?page=" . $_REQUEST['page'] . "&action=wccm-customer-add&customer=" . $this->current_customer_id . "&edit=1&back=details"; ?>"> <?php _e('Edit', 'woocommerce-customers-manager'); ?> </a>
                                    <a class="button-primary" href="<?php echo "?page=" . $_REQUEST['page'] . "&action=wccm-customer-metadata&customer=" . $this->current_customer_id . "&back=details"; ?>"> <?php _e('View / Edit meta data', 'woocommerce-customers-manager'); ?> </a>
                                </p>
                        <?php endif; ?>

                        <?php
                        if (!$this->is_guest_customer && isset($wpuef_htmlHelper) && method_exists($wpuef_htmlHelper, 'woocommerce_render_extra_fields_wccm')):
                            ?>
                                <h3 class="wccm_title_with_distance"><?php _e('Extra fields', 'woocommerce-customers-manager'); ?></h3>
                                <?php
                                //$wpuef_htmlHelper->woocommerce_render_edit_form_extra_fields($this->current_customer_id,true);
                                $wpuef_htmlHelper->woocommerce_render_extra_fields_wccm($this->current_customer_id);
                        endif;
                        ?>

                        <?php if (!$this->is_guest_customer): ?>
                                <div id="notes-box">
                                    <h3 class="wccm_title_with_distance"><?php _e('Notes', 'woocommerce-customers-manager'); ?></h3>
                                    <p>
                                    <form name="customer_notes" method="post" action="">
                                        <input type="hidden" value="<?php echo $this->current_customer_id ?>" name="customer_id">
                                        <!--<label><?php _e('Notes', 'woocommerce-customers-manager'); ?></label> -->
                                        <textarea id="customer_notes"  cols="40" rows="8" name="customer_notes"><?php if (isset($this->customer_extra_info['wccm_customer_notes']))
                                            echo $this->customer_extra_info['wccm_customer_notes'][0]; ?></textarea>
                                        <p class="submit">
                                            <input class="button-primary" type="submit" value=" <?php _e('Save notes', 'woocommerce-customers-manager'); ?>"> </input>
                                        </p>
                                    </form>

                                    </p>
                                </div>
                        <?php endif; ?>
                        <!--- END Non Guest User extras -->

                        <div id="orders-list">
                            <h3><?php _e('Orders', 'woocommerce-customers-manager'); ?> </h3>

                            <?php if (isset($_GET['customer'])): ?>
                                    <h4 id="wccm_order_assigner_title"><?php _e('Assigner', 'woocommerce-customers-manager'); ?></h4>
                                    <p id="wccm_order_description"><?php _e('Select which orders have to be assigned to this user', 'woocommerce-customers-manager'); ?></p>

                                    <form method="post">
                                        <div id="wccm_order_assigner_container">
                                            <?php $wccm_html_model->assign_orders_to_user_selector(); ?>
                                        </div>
                                        <p class="submit">
                                            <input class="button-primary" type="submit" value=" <?php _e('Assign', 'woocommerce-customers-manager'); ?>"> </input>
                                        </p>
                                    </form>
                            <?php endif; ?>

                            <h4 id="wccm_order_list_title"><?php _e('List for', 'woocommerce-customers-manager'); ?>         <?php
                                        _e('period: ', 'woocommerce-customers-manager');
                                        echo '<span class="wccm_woo_highlight">' . $this->starting_date . '</span> - <span class="wccm_woo_highlight">' . $this->ending_date . "</span>";
                                        ?></h4>
                            <p id="wccm_order_description"><?php _e('To display orders for a <strong>different period</strong>, select a different <strong>date range in the Stat section</strong> (it is located after the map).', 'woocommerce-customers-manager'); ?></p>

                            <?php
                            if (!$this->date_range_error)
                                $orders_amounts_per_date = $this->createDateRange($this->starting_date, $this->ending_date, $this->view_period_range);
                            $today = date('Y/m/d');

                            /* if($date_range_error)
                              $this-> */
                            $seq_order_obj = class_exists('WC_Seq_Order_Number') ? wc_sequential_order_numbers() : null;
                            if (isset($orders) && is_array($orders))
                                foreach ($orders as $order):
                                    //print_r($order); echo "<br/><br/>";	
                                    ?>
                                            <div class="order-block">
                                                <?php
                                                $temp_order_id = isset($seq_order_obj) ? get_post_meta($order->ID, '_order_number', true) : $order->ID;
                                                //echo '<h4> '._e('Order', 'woocommerce-customers-manager').' #'.$order->ID.' on '.$order->post_date.'</h4>';
                                                echo '<h4> ' . sprintf(__('Order #%s on %s', 'woocommerce-customers-manager'), $temp_order_id, $order->post_date) . '</h4>';
                                                //var_dump($order);
                                                //$products = $this->get_order_details_by_id($order->ID);
                                                $wc_order = new WC_Order($order->ID);
                                                $products = $wc_order->get_items();

                                                $order_total = 0;
                                                $taxes_total = 0;
                                                if (count($products) > 0):
                                                    ?>


                                                        <table class="wp-list-table widefat striped wccm-customer-details-table" >
                                                            <thead>
                                                                <tr>
                                                                    <th><?php _e('Product SKU', 'woocommerce-customers-manager'); ?></th>
                                                                    <th><?php _e('Product name', 'woocommerce-customers-manager'); ?></th>
                                                                    <th><?php _e('Quantity', 'woocommerce-customers-manager'); ?></th>
                                                                    <th><?php _e('Sub total', 'woocommerce-customers-manager'); ?></th>
                                                                    <th><?php _e('Taxes', 'woocommerce-customers-manager'); ?></th>
                                                                    <th><?php _e('Discount', 'woocommerce-customers-manager'); ?></th>
                                                                    <th><?php _e('Total', 'woocommerce-customers-manager'); ?></th>
                                                                    <th><?php _e('Actions', 'woocommerce-customers-manager'); ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                                <?php
                                                                foreach ($products as $product):

                                                                    //$wc_product =new WC_Product( $product['item_meta']['_product_id'][0] );
                                                                    //wccm_var_dump($product->get_product_id());
                                                                    //wccm_var_dump($product->get_variation_id());
                                                                    //var_dump($wc_product);
                                                                    $order_item_id = version_compare(WC_VERSION, '2.7', '<') ? $product['item_meta']['_product_id'][0] : $product->get_product_id();
                                                                    $product_m = wc_get_product($order_item_id);
                                                                    $order_item_variation_id = version_compare(WC_VERSION, '2.7', '<') ? $product['item_meta']['_variation_id'][0] : $product->get_variation_id();
                                                                    $order_item_quantity = version_compare(WC_VERSION, '2.7', '<') ? $product['item_meta']['_qty'][0] : $product->get_quantity();
                                                                    $discount = ($product['subtotal'] + $product['subtotal_tax']) - ($product['line_total'] + $product['line_tax']);
                                                                    $discount = $discount > 0 ? $discount : 0;
                                                                    ?>
                                                                        <tr>
                                                                            <td> <?php
                                                                            if ($product_m instanceof WC_Product) {
                                                                                echo $product_m->get_sku();
                                                                            }
                                                                            if ($order_item_variation_id != 0) {
                                                                                $product_v = wc_get_product($order_item_variation_id);
                                                                                if ($product_v instanceof WC_Product) {
                                                                                    echo " (" . __('Var: ', 'woocommerce-customers-manager') . $product_v->get_sku() . ")";
                                                                                }
                                                                            }
                                                                            ?> </td>
                                                                            <td class="wccm_product_name_column"> <a href="<?php echo get_permalink($order_item_id); ?>">
                                                                                    <?php
                                                                                    if ($order_item_variation_id != 0):
                                                                                        echo $wccm_product_model->get_variation_complete_name($order_item_variation_id);
                                                                                    else:
                                                                                        echo $product['name'];
                                                                                    endif;
                                                                                    ?>
                                                                                </a></td>
                                                                            <td> <?php echo $order_item_quantity; ?> </td>
                                                                            <td> <?php echo get_woocommerce_currency_symbol() . number_format(ceil($product['subtotal'])); ?> </td>
                                                                            <td> <?php echo get_woocommerce_currency_symbol() . number_format(ceil($product['subtotal_tax'])); ?> </td>
                                                                            <td> <?php echo get_woocommerce_currency_symbol() . number_format(ceil($discount)); ?> </td>
                                                                            <td> <?php echo get_woocommerce_currency_symbol() . number_format(ceil($product['line_total'] + $product['line_tax']))//$wc_product->get_price_html();                                               ?> </td>
                                                                            <td> <a class="button-primary" target="_blank" href="<?php echo get_edit_post_link($order_item_id); ?>">  <?php _e('Edit', 'woocommerce-customers-manager'); ?> </a> </td>
                                                                        </tr>


                                                                        <?php
                                                                        if ($wc_order->get_status() != "cancelled" && $wc_order->get_status() != "refunded") {
                                                                            if (isset($products_to_quantities_purchased[$product['name']]))
                                                                                $products_to_quantities_purchased[$product['name']]['total_purchased'] += $order_item_quantity;
                                                                            else
                                                                                $products_to_quantities_purchased[$product['name']] = array("total_purchased" => $order_item_quantity, "product" => $product);
                                                                        }

                                                                        $order_total += $product['subtotal']; //$product['line_total']; //No, because of coupon usage, this is the already discouted price
                                                                        $taxes_total += $product['subtotal_tax']; //$product['line_tax']; //No, because of coupon usage, this is the already discouted price
                                                                endforeach; //end PRODUCT foreach
                                                                //Order stats
                                                                if ($wc_order->get_status() != "cancelled" && $wc_order->get_status() != "refunded") {
                                                                    if (!array_key_exists($order->post_date, $orders_amount_per_order))
                                                                        //$orders_amount_per_order[date("Y/m/d", strtotime( $order->post_date))] = $order_total+$taxes_total;
                                                                        $orders_amount_per_order[$order->ID] = $order_total + $taxes_total;
                                                                    else
                                                                        //$orders_amount_per_order[date("Y/m/d", strtotime( $order->post_date))] += $order_total+$taxes_total; 
                                                                        $orders_amount_per_order[$order->ID] += $order_total + $taxes_total;

                                                                    if (!$this->date_range_error) {
                                                                        $orders_amounts_per_date[date($this->date_format, strtotime($order->post_date))] += $order_total + $taxes_total;
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                        <span class="stats">
                                                            <span class="stats_title">Status:</span>
                                                            <span class="stats_content">
                                                                <?php
                                                                $order_status = strtoupper($wc_order->get_status());
                                                                if ($order_status == 'COMPLETED')
                                                                    echo '<span class="order_status order_completed">' . __($order_status, 'woocommerce-customers-manager') . '</span>';
                                                                else if ($order_status == 'PROCESSING' || $order_status == 'ON-HOLD')
                                                                    echo '<span class="order_status order_processing_onhold">' . __($order_status, 'woocommerce-customers-manager') . '</span>';
                                                                else
                                                                    echo '<span class="order_status order_not_completed">' . __($order_status, 'woocommerce-customers-manager') . '</span>';
                                                                //$order->post_status; 
                                                                $refounded = $wc_order->get_total_refunded();
                                                                $refounded = isset($refounded) ? floatval($refounded) : 0;
                                                                if (method_exists($wc_order, 'get_total_shipping')) {
                                                                    $total_shipping_numeric = $wc_order->get_shipping_total() != "" ? $wc_order->get_shipping_total() : 0;
                                                                    $total_shipping = get_woocommerce_currency_symbol() . number_format($total_shipping_numeric);
                                                                    $total_shipping_tax = get_woocommerce_currency_symbol() . number_format(floatval($wc_order->get_shipping_tax()));
                                                                    $total_order = get_woocommerce_currency_symbol() . number_format((ceil($order_total + $taxes_total + $total_shipping_numeric + floatval($wc_order->get_shipping_tax()) - floatval($wc_order->get_total_discount()) - $refounded)));
                                                                } else {
                                                                    $total_shipping = "N/A";
                                                                    $total_shipping_tax = "N/A";
                                                                    $total_order = get_woocommerce_currency_symbol() . number_format((ceil($order_total + $taxes_total - $wc_order->get_total_discount())));
                                                                }
                                                                ?></span></span>
                                                        <span class="stats"><span class="stats_title"><?php _e('Sub total:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo get_woocommerce_currency_symbol() . number_format(ceil($order_total)); ?></span></span>
                                                        <span class="stats"><span class="stats_title"><?php _e('Taxes:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo get_woocommerce_currency_symbol() . number_format(ceil($taxes_total)); ?></span></span>
                                                        <span class="stats"><span class="stats_title"><?php _e('Shipping:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo $total_shipping; ?></span></span>
                                                        <span class="stats"><span class="stats_title"><?php _e('Shipping Taxes:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo $total_shipping_tax; ?></span></span>
                                                        <span class="stats"><span class="stats_title"><?php _e('Discount:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo get_woocommerce_currency_symbol() . number_format(ceil($wc_order->get_total_discount())); ?></span></span>
                                                        <span class="stats"><span class="stats_title"><?php _e('Total refounded:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo get_woocommerce_currency_symbol() . ($refounded); ?></span></span>
                                                        <span class="stats"><span class="stats_title"><?php _e('Total:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo ($total_order); ?></span></span>
                                                        <span class="stats"><span class="stats_title"><?php _e('Payment method:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo $wc_order->get_payment_method_title(); ?></span></span>
                                                        <?php
                                                        //WCST support
                                                        $data = get_post_custom($order->ID);
                                                        if (isset($data['_wcst_order_trackname'][0]) && $data['_wcst_order_trackname'][0] != '') {
                                                            ?>
                                                                <span class="stats"><span class="stats_title"><?php _e('Shipping company:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo $data['_wcst_order_trackname'][0]; ?></span></span>
                                                                <span class="stats"><span class="stats_title"><?php _e('Tracking Number:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><a href="<?php
                                                                   if (isset($data['_wcst_order_track_http_url'][0]))
                                                                       echo $data['_wcst_order_track_http_url'][0];
                                                                   else
                                                                       echo '#'
                                                                           ?>"><?php echo $data['_wcst_order_trackno'][0]; ?></a></span></span>
                                                                    <?php
                                                        }
                                                        $index_additiona_companies = 0;
                                                        if (isset($data['_wcst_additional_companies'])) {
                                                            $additiona_companies = unserialize(array_shift($data['_wcst_additional_companies']));
                                                            foreach ($additiona_companies as $company) {
                                                                ?>
                                                                        <span class="stats"><span class="stats_title"><?php _e('Shipping company:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><?php echo $company['_wcst_order_trackname']; ?></span></span>
                                                                        <span class="stats"><span class="stats_title"><?php _e('Tracking Number:', 'woocommerce-customers-manager'); ?></span> <span class="stats_content"><a href="<?php
                                                                           if (isset($company['_wcst_order_track_http_url']))
                                                                               echo $company['_wcst_order_track_http_url'];
                                                                           else
                                                                               echo '#'
                                                                                   ?>"><?php echo $company['_wcst_order_trackno']; ?></a></span></span>
                                                                            <?php
                                                            }
                                                        }
                                                        ?>

                                                        <a class="button-primary" style="margin-top:10px;" href="<?php echo get_edit_post_link($order->ID); ?>"> <?php _e('Order details', 'woocommerce-customers-manager'); ?> </a>
                                                <?php endif; //end if(products>0)     ?>

                                            </div><!-- order block -->
                                    <?php endforeach; //print_r($orders_amounts_per_date);    ?>
                        </div><!-- orders-list -->
                        <div id="order-list-paging"></div>
                        <div class="clear"></div>
                        <div style="position: relative;" class="appointments_list">
                            <link href="<?php echo WP_PLUGIN_URL; ?>/booked/assets/datatables/media/css/jquery.dataTables.css" rel="stylesheet" type="text/css">
                            <script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/booked/assets/datatables/media/js/jquery.dataTables.js"></script>
                            <style>
                                table.dataTable{
                                    border-collapse: collapse;
                                }
                                .loading-gif {
                                    position: fixed;
                                    top: 40%;
                                    left: 40%;
                                }
                                .dataTables_filter{
                                    display: none;
                                }
                                table.dataTable tbody th, table.dataTable tbody td{
                                    border: solid 1px grey;
                                }
                            </style>

                            <h3><?php _e('Appointments List', 'booked'); ?></h3>
                            <table id="table_orders" class="table table-responsive table-condensed table-striped">
                                <thead>
                                <th><strong><?php esc_html_e('Appointment Date/Time', 'booked'); ?></strong></th>
                                <th><strong><?php esc_html_e('Appointment Information', 'booked'); ?></strong></th>
                                </thead>
                            </table>
                            <input type="hidden" value="<?php echo $_REQUEST['customer']; ?>" name="customer" id="customer"/>
                            <input type="hidden" value="<?php echo $this->customer_extra_info['billing_email'][0]; ?>" name="customer_email" id="customer_email"/>
                            <script type="text/javascript">
                                jQuery(document).ready(function () {
                                    datatable = jQuery('#table_orders').DataTable({
                                        language: {
                                            processing: "<img class='loading-gif' width='120px' src='<?php echo WP_PLUGIN_URL; ?>/booked/templates/images/tenor.gif'>",
                                        },
                                        stateSave: true,
                                        "aoColumns": [
                                            {"bSortable": false},
                                            {"bSortable": false}
                                        ],
                                        "processing": true,
                                        "serverSide": true,
                                        "ajax": {
                                            "url": ajaxurl + '?action=get_app_list_ajax_customer',
                                            "data": function (d) {
                                                d.customer = jQuery("#customer").val();
                                                d.customer_email = jQuery("#customer_email").val();
                                            }, complete: function () {
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>	
                    <div id="user-geo-details-and-stats">

                        <div id="billing-details">
                            <h3> <?php _e('Billing Details', 'woocommerce-customers-manager'); ?></h3>

                            <?php
                            /* billing_country, billing_first_name, billing_last_name, billing_company, billing_address_1, billing_address_2, billing_city
                              billing_state, billing_postcode, billing_email, billing_phone */

                            /* shipping_country, shipping_first_name, shipping_last_name, shipping_company, shipping_address_1, shipping_address_2, shipping_city
                              shipping_state, shipping_postcode, shipping_country */
                            ?>
                            <p>
                                <label> <?php _e('Address', 'woocommerce-customers-manager'); ?></label>
                                <?php
                                $countries_obj = new WC_Countries();
                                $billing_states_list = $shipping_states_list = false;

                                $billing_address_1 = isset($this->customer_extra_info['billing_address_1']) ? $this->customer_extra_info['billing_address_1'][0] : "";
                                $billing_postcode = isset($this->customer_extra_info['billing_postcode']) ? $this->customer_extra_info['billing_postcode'][0] : "";
                                $billing_city = isset($this->customer_extra_info['billing_city']) ? $this->customer_extra_info['billing_city'][0] : "";
                                $billing_state_code = isset($this->customer_extra_info['billing_state']) ? $this->customer_extra_info['billing_state'][0] : "";
                                $billing_country_code = isset($this->customer_extra_info['billing_country']) ? $this->customer_extra_info['billing_country'][0] : "";

                                $shipping_state_code = isset($this->customer_extra_info['shipping_state']) ? $this->customer_extra_info['shipping_state'][0] : "";
                                $shipping_country_code = isset($this->customer_extra_info['shipping_country']) ? $this->customer_extra_info['shipping_country'][0] : "";
                                ?>
                                <?php
                                if ($locale == 'ja' || $locale == 'site-default') {
                                    ?>
                                        <?php if (isset($this->customer_extra_info['billing_last_name']))
                                            echo $this->customer_extra_info['billing_last_name'][0]; ?>             <?php if (isset($this->customer_extra_info['billing_first_name']))
                                                               echo $this->customer_extra_info['billing_first_name'][0]; ?>
                                        <?php
                                        echo '<br/>' . $user_info->billing_last_name_kana . ' ' . $user_info->billing_first_name_kana;
                                } else {
                                    ?>
                                        <?php if (isset($this->customer_extra_info['billing_first_name']))
                                            echo $this->customer_extra_info['billing_first_name'][0]; ?>             <?php if (isset($this->customer_extra_info['billing_last_name']))
                                                               echo $this->customer_extra_info['billing_last_name'][0]; ?>
                                <?php } ?>
                                <?php if (isset($this->customer_extra_info['billing_company']) && $this->customer_extra_info['billing_company'][0] != null): ?>
                                        <br/>
                                        <?php
                                        echo $this->customer_extra_info['billing_company'][0];
                                endif;
                                ?>
                                <br/>
                                <?php
                                if ($locale == 'ja' || $locale == 'site-default') {
                                    if ($billing_postcode != "")
                                        echo $billing_postcode;
                                    if (isset($billing_state_code) && $billing_state_code != null) {
                                        if ($billing_country_code != '') {
                                            $billing_states_list = $countries_obj->get_states($billing_country_code);
                                            if ($billing_states_list && isset($billing_states_list[$billing_state_code]))
                                                echo '<br/>' . $billing_states_list[$billing_state_code];
                                            else
                                                echo '<br/>' . $billing_state_code;
                                        } else
                                            echo '<br/>' . $billing_state_code;
                                    }
                                    echo ' ' . $billing_city . ' ' . $billing_address_1 . '<br/>';
                                    echo $this->customer_extra_info['billing_address_2'][0] . '<br/>';
                                    if ($billing_country_code != '')
                                        echo country_code_to_country($billing_country_code);
                                } else {
                                    ?>
                                        <?php echo $billing_address_1; ?>
                                        <?php if (isset($this->customer_extra_info['billing_address_2'][0]) && $this->customer_extra_info['billing_address_2'][0] != null): ?>
                                                <br/>
                                                <?php
                                                echo $this->customer_extra_info['billing_address_2'][0];
                                        endif;
                                        ?>
                                        <br/>
                                        <?php if ($billing_postcode != "")
                                            echo $billing_postcode . ","; ?>             <?php echo $billing_city; ?>
                                        <?php
                                        if (isset($billing_state_code) && $billing_state_code != null) {
                                            if ($billing_country_code != '') {
                                                $billing_states_list = $countries_obj->get_states($billing_country_code);
                                                if ($billing_states_list && isset($billing_states_list[$billing_state_code]))
                                                    echo '<br/>' . $billing_states_list[$billing_state_code];
                                                else
                                                    echo '<br/>' . $billing_state_code;
                                            } else
                                                echo '<br/>' . $billing_state_code;
                                        }
                                        ?>
                                        <br/>
                                        <?php
                                        if ($billing_country_code != '')
                                            echo country_code_to_country($billing_country_code);
                                }
                                if (!$this->is_guest_customer && $vat_number = $wccm_customer_model->get_vat_number($this->current_customer_id)):
                                    ?>
                                        <br/><br/>
                                        <label> <?php _e('Vat Number', 'woocommerce-customers-manager'); ?></label>
                                        <?php echo $vat_number; ?>
                                <?php endif; ?>
                                <br/><br/>
                                <label> <?php _e('Email', 'woocommerce-customers-manager'); ?></label>
                                <?php if (isset($this->customer_extra_info['billing_email']))
                                    echo $this->customer_extra_info['billing_email'][0] ?>
                                    <br/><br/>
                                    <label> <?php _e('Telephone', 'woocommerce-customers-manager'); ?></label>
                                <?php if (isset($this->customer_extra_info['billing_phone']))
                                    echo $this->customer_extra_info['billing_phone'][0] ?>
            <br/><br/>
                                    <label> <?php _e('It is imported', 'woocommerce-customers-manager'); ?></label>
                                <?php if (isset($this->customer_extra_info['is_user_imported']) && $this->customer_extra_info['is_user_imported'][0] == 'yes') {
                                    echo 'Yes';
                                } else {
                                    echo 'No';
                                } ?>
                                <?php
                                //WCMCA support
                                if (isset($wcmca_wpml_helper)):
                                    ?>
                                    <h3> <?php _e('Additional Addresses', 'woocommerce-customers-manager'); ?></h3>
                                    <a class="button button-primary" target="_blank" href="<?php echo get_admin_url(); ?>admin.php?page=woocommerce-multiple-customer-addresses-edit-user&user_id=<?php echo $user->ID; ?>"><?php _e('Details', 'woocommerce-multiple-customer-addresses'); ?></a>
                            <?php endif; ?>

                            </p>
                        </div>
                        <div id="shipping-details">
                            <h3> <?php _e('Shipping Details', 'woocommerce-customers-manager'); ?></h3>
                            <p>
                                <label> <?php _e('Address', 'woocommerce-customers-manager'); ?></label>
                                <?php
                                if ($locale == 'ja' || $locale == 'site-default') {
                                    ?>
                                        <?php if (isset($this->customer_extra_info['shipping_last_name']))
                                            echo $this->customer_extra_info['shipping_last_name'][0] ?>             <?php if (isset($this->customer_extra_info['shipping_first_name']))
                                            echo $this->customer_extra_info['shipping_first_name'][0] ?>
                                            <?php
                                        echo '<br/>' . $user_info->shipping_last_name_kana . ' ' . $user_info->shipping_first_name_kana;
                                } else {
                                    ?>
                                        <?php if (isset($this->customer_extra_info['shipping_first_name']))
                                            echo $this->customer_extra_info['shipping_first_name'][0] ?>             <?php if (isset($this->customer_extra_info['shipping_last_name']))
                                            echo $this->customer_extra_info['shipping_last_name'][0] ?>
                                <?php } ?>
                                <?php if (isset($this->customer_extra_info['shipping_company']) && $this->customer_extra_info['shipping_company'][0] != ''): ?>

                                        <?php
                                        echo '<br/>' . $this->customer_extra_info['shipping_company'][0];
                                endif;
                                ?>
                                <?php
                                if ($locale == 'ja' || $locale == 'site-default') {
                                    if (isset($this->customer_extra_info['shipping_postcode']) && !empty($this->customer_extra_info['shipping_postcode'][0]) && $this->customer_extra_info['shipping_postcode'][0] != " ")
                                        echo "<br/>" . $this->customer_extra_info['shipping_postcode'][0];
                                    if (isset($shipping_state_code) && $shipping_state_code != null) {
                                        if ($shipping_country_code != '') {
                                            $shipping_states_list = $countries_obj->get_states($shipping_country_code);
                                            if ($shipping_states_list && isset($shipping_states_list[$shipping_state_code]))
                                                echo '<br/>' . $shipping_states_list[$shipping_state_code];
                                            else
                                                echo '<br/>' . $shipping_state_code;
                                        } else
                                            echo '<br/>' . $shipping_state_code;
                                    }
                                    if (isset($this->customer_extra_info['shipping_city']))
                                        echo ' ' . $this->customer_extra_info['shipping_city'][0];
                                    if (isset($this->customer_extra_info['shipping_address_1']))
                                        echo ' ' . $this->customer_extra_info['shipping_address_1'][0];
                                    if (isset($this->customer_extra_info['shipping_address_2']) && $this->customer_extra_info['billing_address_2'][0] != null)
                                        echo '<br/>' . $this->customer_extra_info['shipping_address_2'][0];
                                    if ($shipping_country_code != '')
                                        echo '<br/>' . country_code_to_country($shipping_country_code);
                                } else {
                                    ?>
                                        <?php if (isset($this->customer_extra_info['shipping_address_1']))
                                            echo "<br/>" . $this->customer_extra_info['shipping_address_1'][0] ?>
                                        <?php if (isset($this->customer_extra_info['shipping_address_2']) && $this->customer_extra_info['billing_address_2'][0] != null): ?>
                                                <br/>
                                                <?php
                                                echo $this->customer_extra_info['shipping_address_2'][0];
                                        endif;
                                        ?>

                                        <?php
                                        if (isset($this->customer_extra_info['shipping_postcode']) && !empty($this->customer_extra_info['shipping_postcode'][0]) && $this->customer_extra_info['shipping_postcode'][0] != " ")
                                            echo "<br/>" . $this->customer_extra_info['shipping_postcode'][0] . ","
                                                ?>
                                            <?php
                                        if (isset($this->customer_extra_info['shipping_city']))
                                            echo $this->customer_extra_info['shipping_city'][0];
                                        ?>
                                        <?php
                                        /* if(isset($shipping_state) && isset($this->customer_extra_info['shipping_state']) && isset($shipping_state[$this->customer_extra_info['shipping_state'][0]])) : ?>

                                          <?php echo "<br/>".$shipping_state[$this->customer_extra_info['shipping_state'][0]];
                                          elseif(isset($this->customer_extra_info['shipping_state'])):
                                          echo '<br/>'.$this->customer_extra_info['shipping_state'][0];
                                          endif; */
                                        ?>

                                        <?php
                                        if (isset($shipping_state_code) && $shipping_state_code != null) {
                                            if ($shipping_country_code != '') {
                                                $shipping_states_list = $countries_obj->get_states($shipping_country_code);
                                                if ($shipping_states_list && isset($shipping_states_list[$shipping_state_code]))
                                                    echo '<br/>' . $shipping_states_list[$shipping_state_code];
                                                else
                                                    echo '<br/>' . $shipping_state_code;
                                            } else
                                                echo '<br/>' . $shipping_state_code;
                                        }
                                        ?>
                                        <br/>
                                        <?php
                                        if ($shipping_country_code != '')
                                            echo country_code_to_country($shipping_country_code);
                                        ?>
                                <?php } ?>
                                <br/><br/>
                                <label> <?php _e('Telephone', 'woocommerce-customers-manager'); ?></label>
                                <?php if (isset($this->customer_extra_info['shipping_phone']))
                                    echo $this->customer_extra_info['shipping_phone'][0]; ?>

                            </p>
                        </div>

                        <?php if (isset($this->customer_extra_info['billing_email'])): ?>
                                <div id="send-mail-box">
                                    <script>
                                        var wccm_disable_visual_editor = <?php
                                        if (WCCM_Options::get_option('disable_email_visual_editor') === 'true')
                                            echo 'true';
                                        else
                                            echo 'false';
                                        ?>;
                                        jQuery(document).ready(function ()
                                        {
                                            if (!wccm_disable_visual_editor)
                                                setTimeout(function () {
                                                    tinymce.init({selector: '#mail_text'});
                                                }, 1500);
                                        });
                                    </script>
                                    <h3> <?php _e('Send an email', 'woocommerce-customers-manager'); ?></h3>
                                    <p>
                                    <form id="wccm-send-email-form" name="send-mail-form" method="post" action="">
                                        <input type="hidden" value="true" name="send_mail"></input>
                                        <input type="hidden" value="<?php echo $this->current_customer_id ?>" name="customer_id"></input>
                                        <input type="hidden" value="<?php echo $this->customer_info->user_email; /*  $this->customer_extra_info['billing_email'][0] */ ?>" name="customer_mail"></input>
                                        <label><?php _e('Subject', 'woocommerce-customers-manager'); ?></label> 
                                        <input type="text" name="mail_subject"></input>
                                        <label><?php _e('Text (HTML code is allowed only if the visual editor has been disable in the Options menu)', 'woocommerce-customers-manager'); ?></label> 
                                        <p><?php _e('use <strong>{first_name}</strong>, <strong>{last_name}</strong>, <strong>{billing_first_name}</strong>, <strong>{billing_last_name}</strong>, <strong>{shipping_first_name}</strong>, <strong>{shipping_last_name}</strong> placeholders to render user first and last names in the email body.', 'woocommerce-customers-manager'); ?></p>
                                        <textarea style="clear:both" cols="40" rows="8" name="mail_text" id="mail_text"></textarea>
                                        <p class="submit">
                                            <input class="button-primary" type="submit" value=" <?php _e('Send email', 'woocommerce-customers-manager'); ?>"> </input>
                                        </p>
                                    </form>
                                    </p>
                                    <?php
                                    $current_history = get_user_meta($_REQUEST['customer'], 'ch_wccm_email_history', true);
                                    $arr_current_history = @json_decode($current_history, true);
                                    if (@is_array($arr_current_history)) {
                                        if (@count($arr_current_history) > 0) {

                                            function cb($a, $b)
                                            {
                                                return strtotime($b['createdtime']) - strtotime($a['createdtime']);
                                            }

                                            usort($arr_current_history, 'cb');
                                            ?>
                                                    <h3> <?php _e('Email history', 'woocommerce-customers-manager'); ?></h3>
                                                    <div>
                                                        <table class="table">
                                                            <thead>
                                                            <th><strong><?php esc_html_e('User send', 'woocommerce-customers-manager'); ?></strong></th>
                                                            <th><strong><?php esc_html_e('Date', 'woocommerce-customers-manager'); ?></strong></th>
                                                            <th><strong><?php esc_html_e('Subject', 'woocommerce-customers-manager'); ?></strong></th>
                                                            <th><strong><?php esc_html_e('Content', 'woocommerce-customers-manager'); ?></strong></th>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($arr_current_history as $value_h) {
                                                                    ?>
                                                                        <tr>
                                                                            <td><?php echo $value_h['usersend']; ?></td>
                                                                            <td><?php echo $value_h['createdtime']; ?></td>
                                                                            <td><?php echo $value_h['subject']; ?></td>
                                                                            <td><?php echo $value_h['content']; ?></td>
                                                                        </tr>
                                                                        <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                        <?php endif; ?>



                        <?php
                        if (WCCM_Options::get_option('hide_map_on_customer_detail_page') !== 'true'):
                            $billing_state = WC()->countries->get_states($billing_country_code);
                            $google_address = $billing_address_1 . " " . $billing_postcode . " " . $billing_city . " " . trim($billing_state[$billing_state_code]) . " " . WC()->countries->countries[$billing_country_code];

                            $google_address = str_replace("\\", ' ', $google_address);
                            $google_address = str_replace('/', ' ', $google_address);
                            if (strlen(preg_replace('/\s+/u', '', $google_address)) != 0)
                                $google_address = str_replace('"', '\"', $google_address);

                            $js_map_options = array(
                                'address' => $google_address,
                                'zoom_level' => WCCM_Options::get_option('map_default_zoom_level', 8)
                            );
                            wp_localize_script('wccm-map', 'wccm_map', $js_map_options);
                            wp_enqueue_script('wccm-map');
                            $google_address = urlencode($google_address);
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $google_address . '&sensor=false&key=AIzaSyArOnqf0FHb0EAAxpNt5OmqnNS-FldclHk');
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($ch, CURLOPT_TIMEOUT, -1); # optional: -1 = unlimited, 3600 = 1 hour
                            $map_data = curl_exec($ch);
                            curl_close($ch);
                            $map_data = json_decode($map_data, true);
                            if (isset($map_data['results'][0]['geometry']['location'])) {
                                if (isset($map_data['results'][0]['geometry']['location']['lat'])) {
                                    $lat = $map_data['results'][0]['geometry']['location']['lat'];
                                } else {
                                    $lat = 36.204824;
                                }
                                if (isset($map_data['results'][0]['geometry']['location']['lng'])) {
                                    $lng = $map_data['results'][0]['geometry']['location']['lng'];
                                } else {
                                    $lng = 138.252924;
                                }
                            } else {
                                $lat = 36.204824;
                                $lng = 138.252924;
                            }
                            //echo $map_data;
                            ?>
                                <div style="display:block; clear:both; height:50px; width:100%;"></div>	
                                <h3> <?php _e('Location', 'woocommerce-customers-manager'); ?></h3>
                                    <style>
                   /* Set the size of the div element that contains the map */
                  #map-container {
                    height: 400px;  /* The height is 400 pixels */
                    width: 100%;  /* The width is the width of the web page */
                   }
                </style>
                                <div id="map-container"> </div>
                                    <script>
            // Initialize and add the map
            function initMap() {
              // The location of Uluru
              var uluru = {lat: <?php echo $lat; ?>, lng: <?php echo $lng; ?>};
              // The map, centered at Uluru
              var map = new google.maps.Map(
                  document.getElementById('map-container'), {zoom: 8, center: uluru});
              // The marker, positioned at Uluru
              var marker = new google.maps.Marker({position: uluru, map: map});
            }
                </script>
                        <?php endif; ?>

                        <div style="display:block; clear:both; height:50px; width:100%;"></div>
                        <h3> <?php _e('Stats', 'woocommerce-customers-manager'); ?></h3>
                        <form method="post">
                            <input class="range_datepicker" type="text" id="picker_start_date" name="start_date" value="" placeholder="<?php _e('Starting date', 'woocommerce-customers-manager'); ?>" />
                            <input class="range_datepicker" type="text" id="picker_end_date" name="end_date" value="" placeholder="<?php _e('Ending date', 'woocommerce-customers-manager'); ?>" />
                            <select name='view_period_range'>
                                <option value="daily" <?php if ($this->view_period_range === 'daily')
                                    echo 'selected="selected"' ?>><?php _e('Daily View', 'woocommerce-customers-manager'); ?></option>
                                <option value="monthly" <?php if ($this->view_period_range === 'monthly')
                                    echo 'selected="selected"' ?>><?php _e('Monthly View', 'woocommerce-customers-manager'); ?></option>
                                <option value="yearly" <?php if ($this->view_period_range === 'yearly')
                                    echo 'selected="selected"' ?>><?php _e('Yearly View', 'woocommerce-customers-manager'); ?></option>
                            </select>
                            <input class="button-primary" type="submit" value="<?php _e('Filter', 'woocommerce-customers-manager'); ?>" >  </input>
                        </form>
                        <div style="display:block; clear:both; height:50px; width:100%;"></div>

                        <script>
                            jQuery(document).ready(function ()
                            {
                <?php if ($this->show_date_range_alert)
                    echo 'alert("' . __("Starting date cannot be before ending date", 'woocommerce-customers-manager') . '");'; ?>
                                var $picker_start_date = jQuery("#picker_start_date").pickadate({formatSubmit: 'yyyy/mm/dd', selectYears: true, selectMonths: true});
                                var $picker_end_date = jQuery("#picker_end_date").pickadate({formatSubmit: 'yyyy/mm/dd', selectYears: true, selectMonths: true});

                                var picker_start_date = $picker_start_date.pickadate('picker');
                                var picker_end_date = $picker_end_date.pickadate('picker');

                <?php if (isset($_POST['start_date_submit'])): ?>
                                        picker_start_date.set('select', '<?php echo $_POST['start_date_submit']; ?>', {format: 'yyyy/mm/dd'});
                <?php else: ?>
                                        picker_start_date.set('select', '<?php echo $this->starting_date; ?>', {format: 'yyyy/mm/dd'});
                    <?php
                endif;
                if (isset($_POST['end_date_submit'])):
                    ?>
                                        picker_end_date.set('select', '<?php echo $_POST['end_date_submit']; ?>', {format: 'yyyy/mm/dd'});
                <?php else: ?>
                                        picker_end_date.set('select', '<?php echo $today; ?>', {format: 'yyyy/mm/dd'});
                <?php endif; ?>

                                var radar_chart_data = {
                                    type: 'radar',
                                    data: {
                                        labels: [
                <?php
                $counter = 0;
                $totals = "";
                foreach ($products_to_quantities_purchased as $product_name => $product_object):
                    if ($counter > 0) {
                        echo ",";
                        $totals .= ",";
                    }
                    echo '"' . str_replace('"', '\"', $product_name) . '"';
                    $totals .= $product_object['total_purchased'];
                    $counter++;
                endforeach;
                ?>
                                        ],
                                        datasets: [
                                            {
                                                label: '<?php _e('Number of products', 'woocommerce-customers-manager'); ?>',
                                                backgroundColor: "rgba(164, 100, 151, 0.2)",
                                                borderColor: "rgba(164, 100, 151, 1)",
                                                /* fillColor: "rgba(164, 100, 151, 0.2)",
                                                 strokeColor: "rgba(164, 100, 151, 1)",
                                                 pointColor: "rgba(164, 100, 151, 1)",
                                                 pointStrokeColor: "#a46497",
                                                 pointHighlightFill: "#a46497",
                                                 pointHighlightStroke: "rgba(164, 100, 151, 1)", */
                                                data: [<?php echo $totals; ?>]
                                            }
                                        ]
                                    },
                                    options:
                                            {
                                                responsive: true
                                            }
                                };
                                //var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
                                var ctx = jQuery("#myChart").get(0).getContext("2d");
                                var radarChart = new Chart(ctx, radar_chart_data);

                                var bar_chart_data =
                                        {
                                            type: 'bar',
                                            data: {
                                                labels: [<?php
                                                $counter = 0;
                                                $totals = '';
                                                ksort($orders_amount_per_order);
                                                foreach ($orders_amount_per_order as $order_date => $total_spent):
                                                    if ($counter > 0) {
                                                        echo ",";
                                                        $totals .= ",";
                                                    }
                                                    echo '"ID:' . str_replace('"', '\"', $order_date) . '"';
                                                    $totals .= $total_spent;
                                                    $counter++;
                                                endforeach;
                                                ?>],
                                                datasets: [
                                                    {
                                                        label: '<?php _e('Amount', 'woocommerce-customers-manager'); ?>',
                                                        backgroundColor: "rgba(164, 100, 151, 0.2)",
                                                        borderColor: "rgba(164, 100, 151, 1)",
                                                        /* fillColor: "rgba(164, 100, 151, 0.2)",
                                                         strokeColor: "rgba(164, 100, 151, 1)",
                                                         highlightFill: "rgba(164, 100, 151, 0.75)",
                                                         highlightStroke: "rgba(164, 100, 151, 1)", */
                                                        data: [<?php echo $totals; ?>]
                                                    }
                                                ]
                                            },
                                            options:
                                                    {
                                                        responsive: true,
                                                        scales: {
                                                            xAxes: [{
                                                                    stacked: true
                                                                }],
                                                            yAxes: [{
                                                                    stacked: true
                                                                }]
                                                        }
                                                    }
                                        };
                                ctx = jQuery("#myChart2").get(0).getContext("2d");
                                var barChart = new Chart(ctx, bar_chart_data);


                                //line
                                var line_chart_data = {
                                    type: 'line',
                                    data: {
                                        labels: [<?php
                                        $counter = 0;
                                        $totals = '';
                                        foreach ($orders_amounts_per_date as $order_date => $amount):
                                            if ($counter > 0) {
                                                echo ",";
                                                $totals .= ",";
                                            }
                                            echo '"' . $order_date . '"'; //date($date_format ,strtotime($order_date))
                                            $totals .= $amount;
                                            $counter++;
                                        endforeach;
                                        ?>],
                                        datasets: [
                                            {
                                                label: '<?php _e('Amount', 'woocommerce-customers-manager'); ?>',
                                                backgroundColor: "rgba(164, 100, 151, 0.2)",
                                                borderColor: "rgba(164, 100, 151, 1)",
                                                /* fillColor: "rgba(164, 100, 151, 0.2)",
                                                 strokeColor: "rgba(164, 100, 151, 1)",
                                                 highlightFill: "rgba(164, 100, 151, 0.75)",
                                                 highlightStroke: "rgba(164, 100, 151, 1)", */
                                                data: [<?php echo $totals; ?>]
                                            }
                                        ]
                                    },
                                    options:
                                            {
                                                responsive: true
                                            }
                                };

                                ctx = jQuery("#myChart3").get(0).getContext("2d");
                                var myLineChart = new Chart(ctx, line_chart_data);

                                //EXPORT
                                var data = [];
                                jQuery('#export-all-orders-button').click(exportAllOrders);
                                jQuery('.export-single-order-button').click(exportSingleOrder);

                                function exportAllOrders(event)
                                {

                                }
                                function exportSingleOrder(event)
                                {
                                    //var data = [["<?php _e('Order ID', 'woocommerce-customers-manager'); ?>", "<?php _e('Order date', 'woocommerce-customers-manager'); ?> ","<?php _e('Product name', 'woocommerce-customers-manager'); ?>", "<?php _e('Quantity', 'woocommerce-customers-manager'); ?>","<?php _e('Sub total', 'woocommerce-customers-manager'); ?>","\"<?php _e('Taxes', 'woocommerce-customers-manager'); ?>\"","\"<?php _e('Total', 'woocommerce-customers-manager'); ?>\"","\"<?php _e('# Orders', 'woocommerce-customers-manager'); ?>\""]

                                    var block_order = jQuery(event.target).parent();
                                    var order_id_and_timestamp = jQuery(event.target).parent().find('h4').html();
                                    order_id_and_timestamp = order_id_and_timestamp.split(" ");
                                    var order_id = order_id_and_timestamp[2];
                                    var order_timestamp = order_id_and_timestamp[4] + " " + order_id_and_timestamp[5];



                                    //console.log(jQuery(event.target).parent().find('tr'));
                                    return false;
                                }

                                function startExporting()
                                {
                                    var ids_to_export = [];
                                    var csvRows = [];

                                    jQuery('th.check-column input').each(function (index)
                                    {
                                        if (!isNaN(jQuery(this).attr('value')) && jQuery(this).prop("checked") == true)
                                        {
                                            ids_to_export.push(jQuery(this).attr('value'));
                                        }
                                    });


                                    for (var i = 0, l = data.length; i < l; ++i)
                                    {
                                        if (ids_to_export.length == 0)
                                            csvRows.push(data[i].join(','));
                                        else
                                            for (var j = 0; j < ids_to_export.length; j++)
                                            {
                                                if (ids_to_export[j] == data[i][0])
                                                    csvRows.push(data[i].join(','));
                                            }
                                    }


                                    var csvString = csvRows.join("\r\n");
                                    var a = document.createElement('a');
                                    a.href = 'data:attachment/csv,' + encodeURIComponent(csvString);
                                    a.target = '_blank';
                                    a.download = 'WCCM-complete_customers_list.csv';

                                    document.body.appendChild(a);
                                    a.click();
                                }
                            });

                        </script>
                        <div class="chart">
                            <h2 class="stat-title"><?php _e('Amounts per date', 'woocommerce-customers-manager'); ?></h2>
                            <canvas id="myChart3" ></canvas>
                        </div>
                        <div class="chart">
                            <h2 class="stat-title"><?php _e('Products purchased', 'woocommerce-customers-manager'); ?></h2>
                            <canvas id="myChart" ></canvas>
                        </div>
                        <div class="chart">
                            <h2 class="stat-title"><?php _e('Amount per order', 'woocommerce-customers-manager'); ?></h2>
                            <canvas id="myChart2" ></canvas>
                        </div>
                    </div>	
                    <div style="clear:both;"> </div>	
                </div>	
                <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyArOnqf0FHb0EAAxpNt5OmqnNS-FldclHk&callback=initMap"></script>
                <?php
    }

    public static function get_last_order_date($user_id)
    {
        global $wccm_order_model;
        return $wccm_order_model->get_last_order_date($user_id);
    }

    public static function get_first_order_date($user_id)
    {
        global $wccm_order_model;
        return $wccm_order_model->get_first_order_date($user_id);
    }

    public static function get_orders_num($user_id, $starting_date = null, $ending_date = null)
    {
        global $wccm_order_model;
        return $wccm_order_model->get_orders_num($user_id, $starting_date, $ending_date);
    }

    public static function get_guest_orders_num()
    {
        global $wccm_order_model;
        return $wccm_order_model->get_guest_orders_num();
    }

    public static function get_all_guest_customers($current_page = null, $per_page = null, $get_last_order_id = false)
    {
        global $wccm_customer_model;
        return $wccm_customer_model->get_all_guest_customers($current_page, $per_page, $get_last_order_id);
    }

    public static function get_all_orders_filtered_by_date_and_or_product($product_id = null, $starting_date = null, $ending_date = null, $get_count = false, $per_page = null, $offset = null)
    {
        global $wccm_order_model;
        return $wccm_order_model->get_all_orders_filtered_by_date_and_or_product($product_id, $starting_date, $ending_date, $get_count, $per_page, $offset);
    }

    public static function get_user_orders_ids($user_id, $starting_date = null, $ending_date = null, $filter_by_product_id = null, $all_types = false)
    {
        global $wccm_order_model;
        return $wccm_order_model->get_user_orders_ids($user_id, $starting_date, $ending_date, $filter_by_product_id, $all_types);
    }

    private function get_all_user_orders($user_id, $starting_date = null, $ending_date = null, $filter_by_product_id = null)
    {
        global $wccm_order_model;
        return $wccm_order_model->get_all_user_orders($user_id, $starting_date, $ending_date, $filter_by_product_id);
    }

    public static function get_user_total_spent($starting_date = null, $ending_date = null, $orders = array(), $currency_symbol = true)
    {
        global $wccm_customer_model;
        return $wccm_customer_model->get_user_total_spent($starting_date, $ending_date, $orders, $currency_symbol);
    }

    static function get_customers_num($ids = null)
    {
        global $wccm_customer_model;
        return $wccm_customer_model->get_customers_num($ids);
    }

    //Customer list -> Normal flow
    static function get_all_users_with_orders_ids($current_page, $per_page, $customers_ids_to_use_as_filter = null, $get_total = false, $ids = null, $starting_date = null, $ending_date = null, $filter_by_product_id = null, $is_for_export = false)
    {
        global $wccm_customer_model;
        return $wccm_customer_model->get_all_users_with_orders_ids($current_page, $per_page, $customers_ids_to_use_as_filter, $get_total, $ids, $starting_date, $ending_date, $filter_by_product_id, $is_for_export);
    }

    function get_order_details_by_id($order_id)
    {
        global $wccm_order_model;
        $wccm_order_model->get_order_details_by_id($order_id);
    }

    function createDateRange($strDateFrom, $strDateTo, $type)
    {
        // takes two dates formatted as YYYY/MM/DD and creates an inclusive array of the dates between the from and to dates.

        $aryRange = array();
        //MM                         //DD                //YYYY
        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        $this->date_format = 'd/M/y'; //'Y/m/d'
        switch ($type) {
            case 'monthly':
                $this->date_format = 'M/Y'; //Y/m
                break;
            case 'yearly':
                $this->date_format = 'Y';
                break;
        }

        if ($iDateTo >= $iDateFrom) {
            //array_push($aryRange,date($this->date_format,$iDateFrom)); // first entry
            $aryRange[date($this->date_format, $iDateFrom)] = 0;
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                if ($type = 'daily')
                    //array_push($aryRange,date($format,$iDateFrom));
                    $aryRange[date($this->date_format, $iDateFrom)] = 0;
            }
        }
        return $aryRange;
    }

}
?>