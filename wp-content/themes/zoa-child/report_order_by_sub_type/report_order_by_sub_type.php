<?php

add_action('admin_menu', 'rp_st_menu_mr_setting');
function rp_st_menu_mr_setting() {
    add_submenu_page('woocommerce', __('Export Orders By Sub Type', 'zoa'), __('Export Orders By Sub Type', 'zoa'), 'manage_options', 'rp_st_export_orders_by_sub_type', 'rp_st_export_orders_by_sub_type', '', 10);
}

function rp_st_export_orders_by_sub_type() {
    $target_dir = ABSPATH . 'wp-content/uploads/order_sub_type/';
    if (!file_exists($target_dir)) {
        wp_mkdir_p($target_dir);
    }
    $order_sub_type = $_REQUEST['order_sub_type'];
    global $wpdb;
    $sql = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = 'order_sub_type' GROUP BY meta_value";
    $results = $wpdb->get_results($sql, ARRAY_A);
    ob_start();
?>
    <h3><?php esc_html_e('Export Orders By Sub Type', 'zoa'); ?>:</h3>
    <hr/>
    <form action="" method="POST">
        <?php esc_html_e('Select Order Sub Type:', 'zoa'); ?>
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
                <input type="submit" name="ch_save_rp_st_export_orders" id="submit" class="button button-primary" value="<?php _e('Export Orders Total', 'zoa'); ?>"> <i><?php esc_html_e('Only order status: completed, processing', 'zoa'); ?></i>
                <br/>
                <?php
            } else {
                echo __('No order sub type.', 'zoa');
            }
        ?>
    </form>
    <?php
    if (isset($_POST['ch_save_rp_st_export_orders'])) {
        $args = [
            'post_type' => 'shop_order',
            'posts_per_page' => '-1',
            'post_status' => ['wc-completed', 'wc-processing']
        ];
        $my_query = new WP_Query($args);
        $orders = $my_query->posts;
        $order_total = 0;
        $order_sub_type = $_REQUEST['order_sub_type'];
        $number_order = 0;
        $items_purchased = 0;
        foreach ($orders as $order => $value) {
            $order_id = $value->ID;
            $order = wc_get_order($order_id);
            $order_sub_type_db = get_post_meta($order_id, 'order_sub_type', true) ? get_post_meta($order_id, 'order_sub_type', true) : '';
            if (!empty($order_sub_type_db) && $order_sub_type_db == $order_sub_type) {
                $order_total += $order->get_total() - $order->get_total_tax() - $order->get_total_shipping() - $order->get_shipping_tax();
                $items_purchased += $order->get_item_count();
                $number_order++;
            }
        }
        ?>
        <h2><?php esc_html_e('Total sales:', 'zoa'); ?> &yen;<?php echo number_format($order_total); ?></h2>
        <h2><?php esc_html_e('Numbers of order:', 'zoa'); ?> <?php echo $number_order; ?></h2>
        <h2><?php esc_html_e('Products numbers of sales:', 'zoa'); ?> <?php echo $items_purchased; ?></h2>
        <?php
    }
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

