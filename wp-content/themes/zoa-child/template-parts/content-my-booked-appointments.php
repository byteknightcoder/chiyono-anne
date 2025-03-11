<?php
// Start content appointment list
$user_id = get_current_user_id();

$args = array(
    'post_type'      => 'booked_appointments',
    'posts_per_page' => 1000,
    'meta_key'       => '_appointment_timestamp',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
    'meta_query'     => array(
        array(
            'key'     => '_appointment_user',
            'value'   => $user_id,
            'compare' => '='
        )
    )
);

$list = get_posts($args);

if (!empty($list)) : ?>
    <div class="box-list">
        <table id="table_orders" class="table table-responsive table-condensed table-striped">
            <thead>
                <th><strong><?php esc_html_e('Appointment Date/Time', 'booked'); ?></strong></th>
                <th><strong><?php esc_html_e('Phone', 'booked'); ?></strong></th>
                <th><strong><?php esc_html_e('Appointment Information', 'booked'); ?></strong></th>
            </thead>
            <tbody>
                <?php foreach ($list as $appointment) :
                    $timestamp = get_post_meta($appointment->ID, '_appointment_timestamp', true);
                    $date_time = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp);
                    $user_info = get_userdata($user_id);
                    $info = get_post_meta($appointment->ID, '_cf_meta_value', true);
                    $order_id = get_post_meta($appointment->ID, '_ch_wc_order_id', true);
                ?>
                    <tr>
                        <td><?php echo esc_html($date_time); ?></td>
                        <td><?php echo esc_html($user_info->billing_phone); ?></td>
                        <td>
                            <?php
                            if (isset($order_id) && $order_id > 0) {
                                $zoom_metting_url = get_post_meta($order_id, 'zoom_metting_url', true);
                                if (!empty($zoom_metting_url)) {
                                    echo '<strong>' . esc_html__('Url:', 'zoa') . '</strong><br/>';
                                    echo '<a target="_blank" href="' . esc_url($zoom_metting_url) . '">' . esc_html($zoom_metting_url) . '</a><br/>';
                                }
                                echo '<strong>' . esc_html__('This appointment for order ID', 'zoa') . '</strong><br/>';
                                echo '#' . esc_html($order_id);
                            }
                            echo wp_kses_post($info);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="empty-appointments">
        <p><?php echo esc_html__('予約された面談はありません。', 'booked'); ?></p>
    </div>
<?php
endif;
// End content appointment list
?>