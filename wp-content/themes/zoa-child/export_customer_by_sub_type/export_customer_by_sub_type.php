<?php

function ecbst_st_export_orders_by_sub_type() {
    $target_dir = ABSPATH . "/wp-content/uploads/order_sub_type/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777);
    }
    $order_sub_type = $_REQUEST['order_sub_type'];
    global $wpdb;
    $sql = "SELECT meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key='order_sub_type' GROUP BY meta_value";
    $results = $wpdb->get_results($sql, ARRAY_A);
    ob_start();
    ?>
    <h3><?php _e('Export Customers By Sub Type', 'zoa'); ?>:</h3>
    <hr/>
    <form action="" method="POST">
        <?php _e('Select Order Sub Type:', 'zoa'); ?>
        <?php
        if (!empty($results)) {
            ?>
            <select name="order_sub_type">
                <?php
                foreach ($results as $result) {
                    $vl = $result['meta_value'];
                    if (!empty($vl)) {
                        ?>
                        <option <?php echo $vl == $order_sub_type ? 'selected' : ''; ?> value="<?php echo $vl; ?>"><?php echo $vl; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
            <input type="submit" name="ch_save_ecbst_st_export_orders" id="submit" class="button button-primary" value="<?php _e('Export Customers', 'zoa'); ?>"> <i><?php esc_html_e('Only order status: completed, processing', 'zoa'); ?></i>
            <br/>
            <?php
        } else {
            echo __('No order sub type.', 'zoa');
        }
        ?>
    </form>
    <?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

add_action('admin_menu', 'ecbst_st_menu_mr_setting');

/**
 * Call back function for admin_menu
 */
function ecbst_st_menu_mr_setting() {
    add_submenu_page('woocommerce', __('Export Customers By Sub Type', 'zoa'), __('Export Customers By Sub Type', 'zoa'), 'manage_options', 'ecbst_st_export_orders_by_sub_type', 'ecbst_st_export_orders_by_sub_type', '', 10);
}

add_action('admin_init', 'ecbst_customers_export_csv');

function ecbst_customers_export_csv() {
    if (isset($_POST['ch_save_ecbst_st_export_orders'])) {
        $target_dir = ABSPATH . "/wp-content/uploads/export_customer_by_sub_type/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777);
        }
        $args = [
            'post_type' => 'shop_order',
            'posts_per_page' => '-1',
            'post_status' => ['wc-completed', 'wc-processing']
        ];
        $my_query = new WP_Query($args);
        $orders = $my_query->posts;
        if (!empty($orders)) {
            $file_name = date('YmdHis') . '_customers_sub_type.csv';
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '";');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Email', 'First Name', 'Last Name'));
            $order_sub_type = $_REQUEST['order_sub_type'];
            foreach ($orders as $order => $value) {
                $order_id = $value->ID;
                $order = wc_get_order($order_id);
                $order_sub_type_db = get_post_meta($order_id, 'order_sub_type', true) ? get_post_meta($order_id, 'order_sub_type', true) : '';
                if (!empty($order_sub_type_db) && $order_sub_type_db == $order_sub_type) {
                    $firsname = get_post_meta($order_id, '_billing_first_name', true) ? get_post_meta($order_id, '_billing_first_name', true) : '';
                    $lastname = get_post_meta($order_id, '_billing_last_name', true) ? get_post_meta($order_id, '_billing_last_name', true) : '';
                    $user_id = get_post_meta($order_id, '_customer_user', true);
                    $customer = new WC_Customer($user_id);
                    if ($firsname == '' || $lastname == '') {
                        if ($firsname == '') {
                            $firsname = $customer->get_first_name();
                        }
                        if ($lastname == '') {
                            $lastname = $customer->get_last_name();
                        }
                    }
                    $email = $customer->get_email();
                    if (!isset($email) || empty($email)) {
                        $email = get_post_meta($order_id, '_billing_email', true) ? get_post_meta($order_id, '_billing_email', true) : '';
                    }
                    fputcsv($output, array($email, $firsname, $lastname));
                }
            }
            fclose($output);
            exit();
        }
    }
}
