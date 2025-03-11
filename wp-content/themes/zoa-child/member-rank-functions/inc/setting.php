<?php

/**
 * Call back function for setting page
 */
function ch_mr_settings() {
    if (isset($_POST['ch_save_mr_setting'])) {
        update_option('ch_mr_royal', $_REQUEST['ch_mr_royal']);
        update_option('ch_mr_gold_from', $_REQUEST['ch_mr_gold_from']);
        update_option('ch_mr_gold_to', $_REQUEST['ch_mr_gold_to']);
        update_option('ch_mr_silver_from', $_REQUEST['ch_mr_silver_from']);
        update_option('ch_mr_silver_to', $_REQUEST['ch_mr_silver_to']);
        update_option('ch_mr_bronze', $_REQUEST['ch_mr_bronze']);
        update_option('ch_mr_up_down_rank', $_REQUEST['ch_mr_up_down_rank']);
        // coupon
        if (empty($_REQUEST['ch_mr_coupon_unlimited'])) {
            update_option('ch_mr_coupon_unlimited', mr_coupon_code(5));
        } else {
            update_option('ch_mr_coupon_unlimited', $_REQUEST['ch_mr_coupon_unlimited']);
        }
        if (empty($_REQUEST['ch_mr_coupon_2times'])) {
            update_option('ch_mr_coupon_2times', mr_coupon_code(5, 2));
        } else {
            update_option('ch_mr_coupon_2times', $_REQUEST['ch_mr_coupon_2times']);
        }
        if (empty($_REQUEST['ch_mr_coupon_except_bra_2times'])) {
            $a = mr_get_taxonomy_hierarchy('product_cat', 0, 'bras');
            $b = mr_get_taxonomy_hierarchy('product_cat', 0, 'panty');
            $c = array_merge(array_keys($a), array_keys($b));
            update_option('ch_mr_coupon_except_bra_2times', mr_coupon_code(5, 2, $c));
        } else {
            update_option('ch_mr_coupon_except_bra_2times', $_REQUEST['ch_mr_coupon_except_bra_2times']);
        }
        // end
        if (isset($_REQUEST['mr_test_mode'])) {
            update_option('mr_test_mode', 'yes');
        } else {
            update_option('mr_test_mode', 'no');
        }
    }
    $mr_test_mode_cb = '';
    if ( 'yes' == get_option('mr_test_mode') ) {
        $mr_test_mode_cb = ' checked="true"';
    }
    ob_start();

    if (isset($_POST['ch_save_mr_setting'])) : ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
            <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button></div>
        <?php
    endif;
?>
    <h3><?php esc_html_e('Member Rank Setting Page', 'zoa'); ?>:</h3>
    <hr/>
    <form action="" method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <td colspan="2"><h4><?php esc_html_e('Order amounts per each customer. First Definition', 'zoa'); ?></h4><i><?php // esc_html_e('Collect from January 2021 to Dec 2021 (1 year) order amounts per each customer', 'zoa'); ?></i><hr/></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Royal', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 210px;" type="number" name="ch_mr_royal" value="<?php echo get_option('ch_mr_royal'); ?>"/><i><?php esc_html_e('Above this value, that mean will get Royal rank', 'zoa'); ?></i>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Gold', 'zoa'); ?></label></th>
                    <td>
                        <?php esc_html_e('From', 'zoa'); ?><input type="number" name="ch_mr_gold_from" value="<?php echo get_option('ch_mr_gold_from'); ?>"/> ~ To<input type="number" name="ch_mr_gold_to" value="<?php echo get_option('ch_mr_gold_to'); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Silver', 'zoa'); ?></label></th>
                    <td>
                        <?php esc_html_e('From', 'zoa'); ?><input type="number" name="ch_mr_silver_from" value="<?php echo get_option('ch_mr_silver_from'); ?>"/> ~ To<input type="number" name="ch_mr_silver_to" value="<?php echo get_option('ch_mr_silver_to'); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Bronze', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 210px;" type="number" name="ch_mr_bronze" value="<?php echo get_option('ch_mr_bronze'); ?>"/><i><?php esc_html_e('Under this value, that mean will get Bronze rank', 'zoa'); ?></i>
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="2"><h4><?php // esc_html_e('From 2022 Definition', 'zoa'); ?></h4><i><?php // esc_html_e('If customers keep to purchase HALF OF FIRST DEFINITION AMOUNTS, member rank will be kept. If more than it, rank will be up, if less than it, rank will be down.', 'zoa'); ?></i><hr/></td>
                </tr> -->
                <!-- <tr>
                    <th scope="row"><label><?php // esc_html_e('Months term', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 210px;" type="number" name="ch_mr_up_down_rank" value="<?php //echo get_option('ch_mr_up_down_rank'); ?>"/><i><?php // esc_html_e('ex) If Gold member purchased less than ¥300,000 in 2022 first 6 months ( Jan 2022 ~ Jun 2022), the member will be downgrade to Silver. ', 'zoa'); ?></i>
                    </td>
                </tr> -->
                <tr>
                    <td colspan="2"><h4><?php esc_html_e('Coupons', 'zoa'); ?></h4><hr/></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Royal coupon - 5% OFF discount coupon for all items (unlimited). This coupon will auto apply in checkout.', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 210px;" type="text" name="ch_mr_coupon_unlimited" value="<?php echo get_option('ch_mr_coupon_unlimited'); ?>"/><i><?php esc_html_e('If it is empty, will auto create new coupon after you click "Save Changes" button bellow'); ?></i>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Gold coupon - 5% OFF discount coupon for all items (2 times). This coupon will display on checkout.', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 210px;" type="text" name="ch_mr_coupon_2times" value="<?php echo get_option('ch_mr_coupon_2times'); ?>"/><i><?php esc_html_e('If it is empty, will auto create new coupon after you click "Save Changes" button bellow'); ?></i>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Silver coupon - 5% OFF discount coupon for all items EXCEPT lingeries (2 times). This coupon will display on checkout.', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 210px;" type="text" name="ch_mr_coupon_except_bra_2times" value="<?php echo get_option('ch_mr_coupon_except_bra_2times'); ?>"/><i><?php esc_html_e('If it is empty, will auto create new coupon after you click "Save Changes" button bellow'); ?></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="submit" name="ch_save_mr_setting" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'zoa'); ?>">
    </form>
    <?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

function ch_mr_export() {
    $target_dir = ABSPATH . '/wp-content/uploads/member-rank-export/';
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777);
    }
    $rank = $_REQUEST['member_rank'];
    ob_start();
?>
    <h3><?php esc_html_e('Export Customer Email By Member Rank', 'zoa'); ?>:</h3>
    <hr/>
    <form action="" method="POST">
        <?php esc_html_e('Select Member Rank:', 'zoa'); ?>
        <select name="member_rank">
            <option <?php echo ( 'royal' == $rank ) ? 'selected' : ''; ?> value="royal"><?php esc_html_e('royal', 'zoa'); ?></option>
            <option <?php echo ( 'gold' == $rank ) ? 'selected' : ''; ?> value="gold"><?php esc_html_e('gold', 'zoa'); ?></option>
            <option <?php echo ( 'silver' == $rank ) ? 'selected' : ''; ?> value="silver"><?php esc_html_e('silver', 'zoa'); ?></option>
            <option <?php echo ( 'bronze' == $rank ) ? 'selected' : ''; ?> value="bronze"><?php esc_html_e('bronze', 'zoa'); ?></option>
        </select>
        <input type="submit" name="ch_save_mr_export" id="submit" class="button button-primary" value="<?php esc_attr_e('Export', 'zoa'); ?>">
        <br/>
        <i>
            <?php esc_html_e('You can use this csv to import to MailPoet to send email', 'zoa'); ?>
        </i>
    </form>
<?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

function ch_mr_export_orders_by_rank() {
    $target_dir = ABSPATH . '/wp-content/uploads/member-rank-export/';
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777);
    }
    $rank = $_REQUEST['member_rank'];
    ob_start();
?>
    <h3><?php esc_html_e('Export Orders By Member Rank', 'zoa'); ?>:</h3>
    <hr/>
    <form action="" method="POST">
        <?php esc_html_e('Select Member Rank:', 'zoa'); ?>
        <select name="member_rank">
            <option <?php echo ( 'royal' == $rank ) ? 'selected' : ''; ?> value="royal"><?php esc_html_e('royal', 'zoa'); ?></option>
            <option <?php echo ( 'gold' == $rank ) ? 'selected' : ''; ?> value="gold"><?php esc_html_e('gold', 'zoa'); ?></option>
            <option <?php echo ( 'silver' == $rank ) ? 'selected' : ''; ?> value="silver"><?php esc_html_e('silver', 'zoa'); ?></option>
            <option <?php echo ( 'bronze' == $rank ) ? 'selected' : ''; ?> value="bronze"><?php esc_html_e('bronze', 'zoa'); ?></option>
        </select>
        <?php esc_html_e('From:', 'zoa'); ?><input type="text" value="2020-07-01" name="from"/>
        <?php esc_html_e('To:', 'zoa'); ?><input type="text" value="2021-06-31" name="to"/>
        <input type="submit" name="ch_save_mr_export_orders" id="submit" class="button button-primary" value="<?php esc_attr_e('Export Orders', 'zoa'); ?>">
        <br/>
    </form>
<?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

/**
 * Call back function for admin_menu
 */
add_action('admin_menu', 'setup_menu_mr_setting');
function setup_menu_mr_setting() {
    add_menu_page(__('Member Rank', 'zoa'), __('Member Rank', 'zoa'), 'manage_options', 'ch_mr_settings', '', 'dashicons-editor-table');
    add_submenu_page('ch_mr_settings', __('Settings', 'zoa'), __('Settings', 'zoa'), 'manage_options', 'ch_mr_settings', 'ch_mr_settings', '', 5);
    add_submenu_page('ch_mr_settings', __('Export Emails', 'zoa'), __('Export Emails', 'zoa'), 'manage_options', 'ch_mr_export', 'ch_mr_export', '', 6);
    add_submenu_page('ch_mr_settings', __('Export Orders By Rank', 'zoa'), __('Export Orders By Rank', 'zoa'), 'manage_options', 'ch_mr_export_orders_by_rank', 'ch_mr_export_orders_by_rank', '', 7);
}

add_action('admin_init', 'mr_export_csv');
function mr_export_csv() {
    if (isset($_POST['ch_save_mr_export'])) {
        $rank = $_REQUEST['member_rank'];
        $blogusers = get_users(array('fields' => array('ID', 'user_email')));
        if (!empty($blogusers)) {
            $file_name = date('YmdHis') . '_' . $rank . '_rank.csv';
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '";');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Email', 'First Name', 'Last Name'));
            foreach ($blogusers as $user) {
                $user_rank = mr_get_member_rank($user->ID, true);
                if ($user_rank['rank'] == $rank && !empty($user->user_email)) {
                    $customer = new WC_Customer($user->ID);
                    fputcsv($output, array($user->user_email, $customer->get_first_name(), $customer->get_last_name()));
                }
            }
            fclose($output);
            exit();
        }
    }
}

add_action('wp_ajax_ch_delete_user_no_phone_no_email_asfhASFj435HKFdh32laSKAS043823WEhweheEEE', 'ch_delete_user_no_phone_no_email');
add_action('wp_ajax_nopriv_ch_delete_user_no_phone_no_email_asfhASFj435HKFdh32laSKAS043823WEhweheEEE', 'ch_delete_user_no_phone_no_email');
function ch_delete_user_no_phone_no_email() {
    $blogusers = get_users(array('fields' => array('ID', 'user_email')));
    $k = 1;
    foreach ($blogusers as $user) {
        $customer_extra_info = get_user_meta($user->ID);
        $billing_phone = isset($customer_extra_info['billing_phone']) ? $customer_extra_info['billing_phone'][0] : '';
        if (empty($user->user_email) && empty($billing_phone)) {
            wp_delete_user($user->ID);
            $k++;
        }
    }

    echo 'Delete total:' . $k;
    exit();
}

add_action('admin_init', 'mr_export_orders_csv');
add_action('wp_ajax_mr_export_orders_csv', 'mr_export_orders_csv');
add_action('wp_ajax_nopriv_mr_export_orders_csv', 'mr_export_orders_csv');
function mr_export_orders_csv() {
    if (isset($_POST['ch_save_mr_export_orders'])) {
        $rank = $_REQUEST['member_rank'];
        $from = $_REQUEST['from'];
        $to = $_REQUEST['to'];
        $blogusers = get_users(array('fields' => array('ID', 'user_email')));
        $rank_text = $rank;
        if (empty($rank)) {
            $rank_text = 'all';
        }
        if (!empty($blogusers)) {
            $file_name = date('YmdHis') . '_' . $rank_text . '_rank_orders.csv';
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '";');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('User ID','Order ID', 'Shipping Fee', 'Tax Fee', 'Total Amount'));
            if (!empty($rank)) {
                foreach ($blogusers as $user) {
                    $user_rank = mr_get_member_rank($user->ID, true);
                    if ($user_rank['rank'] == $rank) {//each rank
                        $orders = mr_get_orders_by_date($user->ID, $from, $to);
                        if (!empty($orders)) {
                            foreach ($orders as $value_order) {
                                $order = wc_get_order($value_order);
                                $shipping = $order->get_shipping_total();
                                if (empty($shipping) || $shipping == null) {
                                    $shipping = 0;
                                }
                                $tax = $order->get_total_tax();
                                if (empty($tax) || $tax == null) {
                                    $tax = 0;
                                }
                                fputcsv($output, array($user->ID,$order->get_id(), $shipping, $tax, $order->get_total()));
                            }
                        }
                    }
                }
            } else {
                foreach ($blogusers as $user) {
                    $orders = mr_get_orders_by_date($user->ID, $from, $to);
                    if (!empty($orders)) {
                        foreach ($orders as $value_order) {
                            $order = wc_get_order($value_order);
                            $shipping = $order->get_shipping_total();
                            if (empty($shipping) || $shipping == null) {
                                $shipping = 0;
                            }
                            $tax = $order->get_total_tax();
                            if (empty($tax) || $tax == null) {
                                $tax = 0;
                            }
                            fputcsv($output, array($user->ID,$order->get_id(), $shipping, $tax, $order->get_total()));
                        }
                    }
                }
            }
            fclose($output);
            exit();
        }
    }
}

function mr_get_orders_by_date($user_id, $from, $to) {
    $agrs = array(
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'orderby' => 'date',
        'order' => 'ASC',
        'meta_value' => $user_id,
        'post_type' => wc_get_order_types(),
        'post_status' => array_keys(wc_get_order_statuses()), 'post_status' => array('wc-processing', 'wc-completed')
    );
    if (!empty($from) && !empty($to)) {
        $agrs['date_query'] = array(
            array(
                'after' => $from,
                'before' => $to,
                'inclusive' => true,
            ),
        );
    }
    $customer_orders = get_posts($agrs);
    if (!empty($customer_orders)) {
        return $customer_orders;
    } else {
        return array();
    }
}
