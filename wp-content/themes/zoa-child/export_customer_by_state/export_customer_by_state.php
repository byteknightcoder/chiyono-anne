<?php

/**
 * Call back function for admin_menu
 */
add_action('admin_menu', 'rp_st_menu_mr_setting_customers');
function rp_st_menu_mr_setting_customers() {
    add_submenu_page('woocommerce', __('Export Customers By State', 'zoa'), __('Export Customers By State', 'zoa'), 'manage_options', 'rp_st_export_customers_by_state', 'rp_st_export_customers_by_state', '', 10);
}

function rp_st_export_customers_by_state() {
    ob_start();
?>
    <h3><?php esc_html_e('Export Customers', 'zoa'); ?>:</h3>
    <hr/>
    <form action="" method="POST">
        <input type="submit" name="ch_export_customers_states" id="submit" class="button button-primary" value="<?php esc_attr_e('Export Customers', 'zoa'); ?>"> <i>Only customers who billing state is: 大阪府(Osaka), 京都府(Kyoto), 兵庫県(Hyogo), 奈良県(Nara), 滋賀県(Shiga) or 和歌山県(Wakayama)</i>
        <br/>
    </form>
<?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

add_action('admin_init', 'customers_export_csv');
function customers_export_csv() {
    if (isset($_POST['ch_export_customers_states'])) {
        $target_dir = ABSPATH . "/wp-content/uploads/export_customer_by_state/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777);
        }
        $blogusers = get_users(array('fields' => array('ID', 'user_email')));
        $states = array('JP27' => '大阪府(Osaka)', 'JP26' => '京都府(Kyoto)', 'JP28' => '兵庫県(Hyogo)', 'JP29' => '奈良県(Nara)', 'JP25' => '滋賀県(Shiga)', 'JP30' => '和歌山県(Wakayama)');
        $states_key = array_keys($states);
        if (!empty($blogusers)) {
            $file_name = date('YmdHis') . '_customers.csv';
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '";');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Email', 'First Name', 'Last Name', 'State'));
            foreach ($blogusers as $user) {
                if (!empty($user->user_email)) {
                    $customer = new WC_Customer($user->ID);
                    $cus_state = $customer->get_billing_state();
                    if (!empty($cus_state) && in_array($cus_state, $states_key)) {
                        fputcsv($output, array($user->user_email, $customer->get_first_name(), $customer->get_last_name(), $states[$customer->get_billing_state()]));
                    }
                }
            }
            fclose($output);
            exit();
        }
    }
}
