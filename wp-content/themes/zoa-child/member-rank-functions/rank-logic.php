<?php

/**
 * Update rank of user
 * @param type $user_id
 * @param type $from
 * @param type $to
 * @return string
 */
function mr_get_update_rank($user_id, $from, $to)
{
    $settings = mr_get_settings();
    $spent_total = mr_get_total_amount_customer($user_id, $from, $to);
    if ($spent_total >= $settings['royal']) {
        return array('rank' => 'royal', 'spent_total' => '&yen;' . number_format($spent_total), 'spent_total_in_current' => $spent_total);
    } elseif ($spent_total >= $settings['gold_from']) {
        return array('rank' => 'gold', 'spent_total' => '&yen;' . number_format($spent_total), 'spent_total_in_current' => $spent_total);
    } elseif ($spent_total >= $settings['silver_from']) {
        return array('rank' => 'silver', 'spent_total' => '&yen;' . number_format($spent_total), 'spent_total_in_current' => $spent_total);
    } else {
        return array('rank' => 'bronze', 'spent_total' => '&yen;' . number_format($spent_total), 'spent_total_in_current' => $spent_total);
    }
}

/**
 * Get created date of customer
 * @param type $user_id
 * @return type
 */
function mr_get_user_created_date($user_id)
{
    $registered_date = get_the_author_meta('user_registered', $user_id);
    return $registered_date;
}

function update_rank_data_meta($user_id, $start_date_rank, $end_date_rank, $rank)
{
    update_user_meta($user_id, 'start_date_rank', $start_date_rank);
    update_user_meta($user_id, 'end_date_rank', $end_date_rank);
    update_user_meta($user_id, 'current_rank_new_logic', $rank);
}

function update_rank_and_dates($user_id, $new_rank, $total, $rank_limit, $rank_date, $next_rank = null)
{
    $expired_date = $rank_date->modify('+1 year');
    update_user_meta($user_id, 'rank', $new_rank);
    update_user_meta($user_id, 'spent_total', $total);
    update_user_meta($user_id, 'rank_limit', $rank_limit);
    update_user_meta($user_id, 'rank_date', $rank_date->format('Y-m-d H:i:s'));
    update_user_meta($user_id, 'expired_date', $expired_date->format('Y-m-d H:i:s'));
    update_user_meta($user_id, 'last_rank_updated', get_user_meta($user_id, 'rank', true));

    if ($next_rank) {
        update_user_meta($user_id, 'next_rank', $next_rank);
        update_user_meta($user_id, 'next_update', $rank_limit - $total);
    } else {
        delete_user_meta($user_id, 'next_rank');
        delete_user_meta($user_id, 'next_update');
    }
}

function mr_reset_rank_july($user_id)
{
    delete_user_meta($user_id, 'reset_july', true);

    if (!get_user_meta($user_id, 'reset_july', true)) {
        $end_date = '2024-07-01';
        $end_year = date('Y', strtotime($end_date));
        $end_month = date('m', strtotime($end_date));
        $end_day = date('d', strtotime($end_date));

        // Calculate the start date one year ago
        $start_date = date('Y-m-d', strtotime('-1 year', strtotime($end_date)));
        $start_year = date('Y', strtotime($start_date));
        $start_month = date('m', strtotime($start_date));
        $start_day = date('d', strtotime($start_date));

        // The query arguments
        $args = array(
            'post_type' => 'shop_order',
            'post_status' => array('wc-processing', 'wc-completed'),
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'meta_value' => $user_id,
            'date_query' => array(
                array(
                    'after' => array(
                        'year' => $start_year,
                        'month' => $start_month,
                        'day' => $start_day,
                    ),
                    'before' => array(
                        'year' => $end_year,
                        'month' => $end_month,
                        'day' => $end_day,
                        'hour' => 23,
                        'minute' => 59,
                    ),
                    'inclusive' => true,
                ),
            ),
        );

        // Initialize $rank_date and $expired_date
        $rank_date = null;
        $expired_date = null;

        // Get all customer orders
        $customer_orders = get_posts($args);
        $count = 0;
        $total = 0;
        $no_orders_message = __('No orders this year.', 'zoa');

        if (!empty($customer_orders)) {
            foreach ($customer_orders as $customer_order) {
                $order = new WC_Order($customer_order->ID);
                $order_items = $order->get_items();
                $shipping_total = $order->shipping_total;
                $total_tax = $order->total_tax;
                $rank_date = new WC_DateTime('2024-07-01');
                $expired_date = new WC_DateTime('2025-07-01');
                $total += $order->get_total() - (intval($shipping_total) + intval($total_tax));
                $current_rank = get_user_meta($user_id, 'rank', true);

                // Rank update logic based on total spent
                if ($total >= 300000 && $total < 600000) {
                    update_user_meta($user_id, 'spent_total', $total);
                    update_user_meta($user_id, 'next_update', 600000 - $total);
                    if ('silver' !== $current_rank) {
                        update_user_meta($user_id, 'rank', 'silver');
                        update_user_meta($user_id, 'rank_limit', 300000);
                        update_user_meta($user_id, 'next_rank', 'gold');
                        update_user_meta($user_id, 'rank_date', $rank_date);
                        update_user_meta($user_id, 'expired_date', $expired_date);
                        update_user_meta($user_id, 'last_rank_updated', $current_rank);
                    }
                } elseif ($total >= 600000 && $total < 1000000) {
                    update_user_meta($user_id, 'spent_total', $total);
                    update_user_meta($user_id, 'next_update', 1000000 - $total);
                    if ('gold' !== $current_rank) {
                        update_user_meta($user_id, 'rank', 'gold');
                        update_user_meta($user_id, 'rank_limit', 600000);
                        update_user_meta($user_id, 'next_rank', 'royal');
                        update_user_meta($user_id, 'rank_date', $rank_date);
                        update_user_meta($user_id, 'expired_date', $expired_date);
                        update_user_meta($user_id, 'last_rank_updated', $current_rank);
                    }
                } elseif ($total >= 1000000) {
                    update_user_meta($user_id, 'spent_total', $total);
                    delete_user_meta($user_id, 'next_update');
                    if ('royal' !== $current_rank) {
                        update_user_meta($user_id, 'rank', 'royal');
                        update_user_meta($user_id, 'rank_limit', 1000000);
                        delete_user_meta($user_id, 'next_rank');
                        update_user_meta($user_id, 'rank_date', $rank_date);
                        update_user_meta($user_id, 'expired_date', $expired_date);
                        update_user_meta($user_id, 'last_rank_updated', $current_rank);
                    }
                } else {
                    update_user_meta($user_id, 'spent_total', $total);
                    update_user_meta($user_id, 'rank_limit', 0);
                    update_user_meta($user_id, 'next_update', 300000 - $total);
                    update_user_meta($user_id, 'rank', 'bronze');
                    update_user_meta($user_id, 'next_rank', 'silver');
                    delete_user_meta($user_id, 'rank_date');
                    delete_user_meta($user_id, 'expired_date');
                    delete_user_meta($user_id, 'last_rank_updated');
                }

                foreach ($order_items as $order_item) {
                    $count++;
                }
            }
        } else {
            // No orders case
            update_user_meta($user_id, 'spent_total', 0);
            update_user_meta($user_id, 'rank', 'bronze');
            update_user_meta($user_id, 'rank_limit', 0);
            update_user_meta($user_id, 'next_rank', 'silver');
            update_user_meta($user_id, 'next_update', 300000);
            delete_user_meta($user_id, 'rank_date');
            delete_user_meta($user_id, 'expired_date');
        }

        update_user_meta($user_id, 'reset_july', true);
    }
}



function mr_get_rank_1_year_spending($user_id)
{
    // Date calculations to limit the query to the past year
    $today_year = date('Y');
    $today_month = date('m');
    $today_day = date('d');

    // Calculate the start date one year ago
    $start_date = strtotime('-1 year');
    $start_year = date('Y', $start_date);
    $start_month = date('m', $start_date);
    $start_day = date('d', $start_date);

    // Initialize the variables to prevent undefined variable warnings
    $rank_date = null;
    $expired_date = null;

    // The query arguments
    $args = array(
        'post_type' => 'shop_order',
        'post_status' => array('wc-processing', 'wc-completed'),
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'meta_value' => $user_id,
        'date_query' => array(
            array(
                'after' => array(
                    'year' => $start_year,
                    'month' => $start_month,
                    'day' => $start_day,
                ),
                'before' => array(
                    'year' => $today_year,
                    'month' => $today_month,
                    'day' => $today_day,
                    'hour' => 23,
                    'minute' => 59,
                ),
                'inclusive' => true,
            ),
        ),
    );

    // Get all customer orders
    $customer_orders = get_posts($args);
    $count = 0;
    $total = 0;
    $no_orders_message = __('No o8rders this year.', 'zoa');

    if (!empty($customer_orders)) {
        foreach ($customer_orders as $customer_order) {
            $order = new WC_Order($customer_order->ID);
            $order_items = $order->get_items();
            $shipping_total = $order->shipping_total;
            $total_tax = $order->total_tax;
            $rank_date = $order->get_date_paid();
            $expired_date = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($rank_date)));
            $total += $order->get_total() - (intval($shipping_total) + intval($total_tax));
            $current_rank = get_user_meta($user_id, 'rank', true);
        }
        return $total;
    }
    return 0;

}

function mr_get_rank_data_live($user_id)
{
    // Date calculations to limit the query to the past year
    $today_year = date('Y');
    $today_month = date('m');
    $today_day = date('d');

    // Calculate the start date one year ago
    $start_date = strtotime('-1 year');
    $start_year = date('Y', $start_date);
    $start_month = date('m', $start_date);
    $start_day = date('d', $start_date);

    // Initialize the variables to prevent undefined variable warnings
    $rank_date = null;
    $expired_date = null;

    // The query arguments
    $args = array(
        'post_type' => 'shop_order',
        'post_status' => array('wc-processing', 'wc-completed'),
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'meta_value' => $user_id,
        'date_query' => array(
            array(
                'after' => array(
                    'year' => $start_year,
                    'month' => $start_month,
                    'day' => $start_day,
                ),
                'before' => array(
                    'year' => $today_year,
                    'month' => $today_month,
                    'day' => $today_day,
                    'hour' => 23,
                    'minute' => 59,
                ),
                'inclusive' => true,
            ),
        ),
    );

    // Get all customer orders
    $customer_orders = get_posts($args);
    $count = 0;
    $total = 0;
    $no_orders_message = __('No orders this year.', 'zoa');

    if (!empty($customer_orders)) {
        foreach ($customer_orders as $customer_order) {
            $order = new WC_Order($customer_order->ID);
            $order_items = $order->get_items();
            $shipping_total = $order->shipping_total;
            $total_tax = $order->total_tax;
            $rank_date = $order->get_date_paid();
            $expired_date = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($rank_date)));
            $total += $order->get_total() - (intval($shipping_total) + intval($total_tax));
            $current_rank = get_user_meta($user_id, 'rank', true);

            // Rank update logic based on total spent
            if ($total >= 300000 && $total < 600000) {
                update_user_meta($user_id, 'spent_total', $total);
                update_user_meta($user_id, 'next_update', 600000 - $total);
                if ('silver' !== $current_rank) {
                    update_user_meta($user_id, 'rank', 'silver');
                    update_user_meta($user_id, 'rank_limit', 300000);
                    update_user_meta($user_id, 'next_rank', 'gold');
                    update_user_meta($user_id, 'rank_date', $rank_date);
                    update_user_meta($user_id, 'expired_date', $expired_date);
                    update_user_meta($user_id, 'last_rank_updated', $current_rank);
                }
            } else if ($total >= 600000 && $total < 1000000) {
                update_user_meta($user_id, 'spent_total', $total);
                update_user_meta($user_id, 'next_update', 1000000 - $total);
                if ('gold' !== $current_rank) {
                    update_user_meta($user_id, 'rank', 'gold');
                    update_user_meta($user_id, 'rank_limit', 600000);
                    update_user_meta($user_id, 'next_rank', 'royal');
                    update_user_meta($user_id, 'rank_date', $rank_date);
                    update_user_meta($user_id, 'expired_date', $expired_date);
                    update_user_meta($user_id, 'last_rank_updated', $current_rank);
                }
            } else if ($total >= 1000000) {
                update_user_meta($user_id, 'spent_total', $total);
                delete_user_meta($user_id, 'next_update');
                if ('royal' !== $current_rank) {
                    update_user_meta($user_id, 'rank', 'royal');
                    update_user_meta($user_id, 'rank_limit', 1000000);
                    delete_user_meta($user_id, 'next_rank');
                    update_user_meta($user_id, 'rank_date', $rank_date);
                    update_user_meta($user_id, 'expired_date', $expired_date);
                    update_user_meta($user_id, 'last_rank_updated', $current_rank);
                }
            } else {
                update_user_meta($user_id, 'spent_total', $total);
                update_user_meta($user_id, 'rank_limit', 0);
                update_user_meta($user_id, 'next_update', 300000 - $total);
                update_user_meta($user_id, 'rank', 'bronze');
                update_user_meta($user_id, 'next_rank', 'silver');
                delete_user_meta($user_id, 'rank_date', $rank_date);
                delete_user_meta($user_id, 'expired_date', $expired_date);
                delete_user_meta($user_id, 'last_rank_updated');
            }

            foreach ($order_items as $order_item) {
                $count++;
            }
        }
    } else {
        // No orders case
        update_user_meta($user_id, 'spent_total', 0);
        update_user_meta($user_id, 'rank', 'bronze');
        update_user_meta($user_id, 'rank_limit', 0);
        update_user_meta($user_id, 'next_rank', 'silver');
        update_user_meta($user_id, 'next_update', 300000);
        delete_user_meta($user_id, 'rank_date');
        delete_user_meta($user_id, 'expired_date');
    }
}

function mr_get_rank_from_rank_date($user_id)
{
    $rank_date = get_user_meta($user_id, 'rank_date', true);
    $expired_date = get_user_meta($user_id, 'expired_date', true);

    // If no rank_date or expired_date exists, return early
    if (empty($rank_date) || empty($expired_date)) {
        return;
    }
    $current_date = date('Y-m-d');
    $one_year_ago = strtotime('-1 year'); // Get timestamp for 1 year ago
    $rank_timestamp = strtotime($rank_date);
    $expired_timestamp = strtotime($expired_date);

    $total_active = get_user_meta($user_id, 'spent_total', true);

    // If current date is before expiration, recalculate rank based on new orders
    if (strtotime($current_date) < $expired_timestamp) {
        // Get orders after expiration date
        $new_args = array(
            'post_type' => 'shop_order',
            'post_status' => array('wc-processing', 'wc-completed'),
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'meta_value' => $user_id,
            'date_query' => array(
                array(
                    'after' => date('Y-m-d', $one_year_ago),
                    'before' => $current_date,
                    'inclusive' => true,
                ),
            ),
        );

        $new_orders = get_posts($new_args);
        $new_total = 0;

        if (!empty($new_orders)) {
            foreach ($new_orders as $order) {
                $order_obj = new WC_Order($order->ID);
                $shipping_total = $order_obj->shipping_total;
                $total_tax = $order_obj->total_tax;
                $order_total = $order_obj->get_total() - (intval($shipping_total) + intval($total_tax));
                $new_total += $order_total;

                // Update rank date and expired date to the latest qualifying order
                if ($new_total + $total_active >= get_user_meta($user_id, 'rank_limit', true)) {
                    $rank_date = $order_obj->get_date_paid();
                    $expired_date = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($rank_date)));
                }
            }
        }

        // Use combined total to determine new rank
        $total = $new_total;
        $active_rank = get_user_meta($user_id, 'rank', true);

        // Update user rank based on total
        if ($total >= 1000000) {
            update_user_meta($user_id, 'spent_total', $total);
            update_user_meta($user_id, 'rank', 'royal');
            update_user_meta($user_id, 'rank_limit', 1000000);
            delete_user_meta($user_id, 'next_rank');
            delete_user_meta($user_id, 'next_update');
            if (isset($rank_date) && strtotime($rank_date) > strtotime(get_user_meta($user_id, 'rank_date', true))) {
                update_user_meta($user_id, 'rank_date', $rank_date);
                update_user_meta($user_id, 'expired_date', $expired_date);
            }
        } else if ($total >= 600000) {
            if ($active_rank != 'royal') {
                update_user_meta($user_id, 'spent_total', $total);
                update_user_meta($user_id, 'rank', 'gold');
                update_user_meta($user_id, 'rank_limit', 600000);
                update_user_meta($user_id, 'next_rank', 'royal');
                update_user_meta($user_id, 'next_update', 1000000 - $total);
                if (isset($rank_date) && strtotime($rank_date) > strtotime(get_user_meta($user_id, 'rank_date', true))) {
                    update_user_meta($user_id, 'rank_date', $rank_date);
                    update_user_meta($user_id, 'expired_date', $expired_date);
                }
            }
        } else if ($total >= 300000) {
            update_user_meta($user_id, 'spent_total', $total);
            if ($active_rank != 'gold' && $active_rank != 'royal') {
                update_user_meta($user_id, 'rank', 'silver');
                update_user_meta($user_id, 'rank_limit', 300000);
                update_user_meta($user_id, 'next_rank', 'gold');
                update_user_meta($user_id, 'next_update', 600000 - $total);
                if (isset($rank_date) && strtotime($rank_date) > strtotime(get_user_meta($user_id, 'rank_date', true))) {
                    update_user_meta($user_id, 'rank_date', $rank_date);
                    update_user_meta($user_id, 'expired_date', $expired_date);
                }
            }
        } else {
            update_user_meta($user_id, 'spent_total', $total);

            if ($active_rank != 'silver' && $active_rank != 'gold' && $active_rank != 'royal') {
                update_user_meta($user_id, 'rank', 'bronze');
                update_user_meta($user_id, 'rank_limit', 0);
                update_user_meta($user_id, 'next_rank', 'silver');
                update_user_meta($user_id, 'next_update', 300000 - $total);
                delete_user_meta($user_id, 'rank_date');
                delete_user_meta($user_id, 'expired_date');
            }
        }
    } else {
        mr_get_rank_data_live($user_id);
    }
}

function mr_get_member_rank($user_id)
{
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    $is_force_change_rank = get_user_meta($user_id, 'is_force_change_rank', true);
    $force_rank = get_user_meta($user_id, 'force_rank', true);
    if (isset($is_force_change_rank) && $is_force_change_rank == 'yes' && !empty($force_rank)) {
        update_user_meta($user_id, 'rank', $force_rank);

        switch ($force_rank) {
            case 'royal':
                return array(
                    'rank' => 'royal',
                    'spent_total' => 1000000,
                    'spent_total_in_current' => 1000000,
                    'next_update' => 0
                );
            case 'gold':
                return array(
                    'rank' => 'gold',
                    'spent_total' => 600000,
                    'spent_total_in_current' => 600000,
                    'next_update' => 400000
                );
            case 'silver':
                return array(
                    'rank' => 'silver',
                    'spent_total' => 300000,
                    'spent_total_in_current' => 300000,
                    'next_update' => 300000
                );
            default:
                return array(
                    'rank' => 'bronze',
                    'spent_total' => 0,
                    'spent_total_in_current' => 0,
                    'next_update' => 300000
                );
        }
    }


    mr_reset_rank_july($user_id);
    if (get_user_meta($user_id, 'rank_date', true)) {
        mr_get_rank_from_rank_date($user_id);
    } else {
        mr_get_rank_data_live($user_id);
    }


    if (get_user_meta($user_id, 'rank_date', true)) {

        $rank_start = get_user_meta($user_id, 'rank_date', true)->modify('+1 day');
        $rank_start = strtotime($rank_start);

        $rank_expire = strtotime(get_user_meta($user_id, 'expired_date', true));

        // Date calculations to limit the query to the past year
        $today_year = date('Y');
        $today_month = date('m');
        $today_day = date('d');

        // Calculate the start date one year ago
        $start_date = strtotime('-1 year');
        $start_year = date('Y', $start_date);
        $start_month = date('m', $start_date);
        $start_day = date('d', $start_date);

        $argsRetain = array(
            'post_type' => 'shop_order',
            'post_status' => array('wc-processing', 'wc-completed'),
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'meta_value' => $user_id,
            'date_query' => array(
                array(
                    'after' => array(
                        'year' => $start_year,
                        'month' => $start_month,
                        'day' => $start_day,
                    ),
                    'before' => array(
                        'year' => $today_year,
                        'month' => $today_month,
                        'day' => $today_day,
                        'hour' => 23,
                        'minute' => 59,
                    ),
                    'inclusive' => true,
                ),
            ),
        );

        $total = 0;
        $count = 0;
        $retain_orders = get_posts($argsRetain);
        if (!empty($retain_orders)) {
            foreach ($retain_orders as $retain_order) {
                $order = new WC_Order($retain_order->ID);
                $order_items = $order->get_items();
                $shipping_total = $order->shipping_total;
                $total_tax = $order->total_tax;
                $total += $order->get_total() - (intval($shipping_total) + intval($total_tax));
                foreach ($order_items as $order_item) {
                    $count++;
                }
            }
        }
        update_user_meta($user_id, 'spent_retain', $total);
    }



    return array('rank' => get_user_meta($user_id, 'rank', true), 'spent_total' => get_user_meta($user_id, 'spent_total', true), 'spent_total_in_current' => get_user_meta($user_id, 'spent_total', true), 'next_update' => get_user_meta($user_id, 'next_update', true), 'spent_retain' => get_user_meta($user_id, 'spent_retain', true));
}


/**
 * get spent total purchase in current rank
 * @param type $user_id
 * @param type $from
 * @param type $to
 * @return type
 */
function mr_get_spent_total_in_current_rank_new_logic($user_id, $from, $to)
{
    return mr_get_total_amount_customer($user_id, $from, $to);
}

function mr_rest_amount_next_rank($user_id, $spent_total_in_current)
{ // NOTE: this function is using in other files of theme
    $settings = mr_get_settings();
    $current_rank_new_logic = get_user_meta($user_id, 'current_rank_new_logic', true);
    if ($current_rank_new_logic == 'bronze') {
        return $settings['silver_from'] - $spent_total_in_current;
    } elseif ($current_rank_new_logic == 'silver') {
        return $settings['gold_from'] - $spent_total_in_current;
    } else if ($current_rank_new_logic == 'gold') {
        return $settings['royal'] - $spent_total_in_current;
    } else if ($current_rank_new_logic == 'royal') {
        return $settings['royal'] - $spent_total_in_current;
    }
}

/**
 * Get first order from 01-07-2023
 * @param type $user_id
 * @return type
 */
function mr_get_first_order_date_for_init($user_id)
{
    $customer_orders = get_posts(array(
        'numberposts' => 1,
        'meta_key' => '_customer_user',
        'orderby' => 'date',
        'order' => 'ASC',
        'meta_value' => $user_id,
        'post_type' => wc_get_order_types(),
        'post_status' => array_keys(wc_get_order_statuses()),
        'post_status' => array('wc-processing', 'wc-completed'),
        'date_query' => array(
            array(
                'after' => '2023-07-01', // 2023-07-01 to today
                'inclusive' => true,
            ),
        ),
    ));
    $date_order = '';
    if (!empty($customer_orders)) {
        foreach ($customer_orders as $customer_order) {
            $orderq = wc_get_order($customer_order);
            $date_order = $orderq->get_date_created()->date_i18n('Y-m-d H:i:s');
            break;
        }
    }

    return $date_order;
}

function mr_get_last_order_date_for_upgrade_immediately($user_id)
{
    $customer_orders = get_posts(array(
        'numberposts' => 1,
        'meta_key' => '_customer_user',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_value' => $user_id,
        'post_type' => wc_get_order_types(),
        'post_status' => array_keys(wc_get_order_statuses()),
        'post_status' => array('wc-processing', 'wc-completed'),
        'date_query' => array(
            array(
                'after' => '2023-07-01', // 2023-07-01 to today
                'inclusive' => true,
            ),
        ),
    ));
    $date_order = '';
    if (!empty($customer_orders)) {
        foreach ($customer_orders as $customer_order) {
            $orderq = wc_get_order($customer_order);
            $date_order = $orderq->get_date_created()->date_i18n('Y-m-d H:i:s');
            break;
        }
    }

    return $date_order;
}

/**
 * Get spent amount of customer
 * @param type $user_id
 */
function mr_get_total_amount_customer($user_id, $from_date = '', $end_date = '')
{
    $total = 0;
    $customer_orders = get_posts(array(
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_value' => $user_id,
        'post_type' => wc_get_order_types(),
        'post_status' => array_keys(wc_get_order_statuses()),
        'post_status' => array('wc-processing', 'wc-completed'),
    ));
    $current_rank_new_logic = get_user_meta($user_id, 'current_rank_new_logic', true);
    if (!empty($customer_orders)) {
        foreach ($customer_orders as $customer_order) {
            $orderq = wc_get_order($customer_order);
            if (!empty($from_date) && !empty($end_date)) {
                $date_order = $orderq->get_date_created()->date_i18n('Y-m-d H:i:s');
                if ($current_rank_new_logic == 'bronze') {
                    if (strtotime($date_order) >= strtotime($from_date) && strtotime($date_order) <= strtotime($end_date)) {
                        if ($orderq->get_discount_total() != '') {
                            $total += $orderq->get_subtotal() - (int) $orderq->get_discount_total();
                        } else {
                            $total += $orderq->get_subtotal();
                        }
                    }
                } else {
                    if (strtotime($date_order) > strtotime($from_date) && strtotime($date_order) <= strtotime($end_date)) {
                        if ($orderq->get_discount_total() != '') {
                            $total += $orderq->get_subtotal() - (int) $orderq->get_discount_total();
                        } else {
                            $total += $orderq->get_subtotal();
                        }
                    }
                }
            } else {
                if ($orderq->get_discount_total() != '') {
                    $total += $orderq->get_subtotal() - (int) $orderq->get_discount_total();
                } else {
                    $total += $orderq->get_subtotal();
                }
            }
        }
    }
    return $total;
}


// Temp for chekcing total spent by user
/**
 * Get the total amount spent by a customer within a specific date range
 * excluding tax, coupon discounts, and shipping
 *
 * @param int    $user_id    The customer's user ID
 * @param string $start_date Start date in Y-m-d format
 * @param string $end_date   End date in Y-m-d format
 * @return float             Total amount spent
 */
function get_customer_total_spent($user_id, $start_date, $end_date)
{

    // Ensure user exists
    $user = get_user_by('id', $user_id);
    if (!$user) {
        return new WP_Error('invalid_user', 'User does not exist');
    }

    $end_year = date('Y', strtotime($end_date));
    $end_month = date('m', strtotime($end_date));
    $end_day = date('d', strtotime($end_date));

    // Calculate the start date one year ago
    $start_year = date('Y', strtotime($start_date));
    $start_month = date('m', strtotime($start_date));
    $start_day = date('d', strtotime($start_date));

    // The query arguments
    $args = array(
        'post_type' => 'shop_order',
        'post_status' => array('wc-processing', 'wc-completed'),
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'meta_value' => $user_id,
        'date_query' => array(
            array(
                'after' => array(
                    'year' => $start_year,
                    'month' => $start_month,
                    'day' => $start_day,
                ),
                'before' => array(
                    'year' => $end_year,
                    'month' => $end_month,
                    'day' => $end_day,
                ),
            ),
        ),
    );


    // Get all orders for this customer in the time period
    $customer_orders = get_posts($args);
    // // Debugging output
    // var_dump($customer_orders);
    // exit;

    $total_spent = 0;

    if (!empty($customer_orders)) {
        foreach ($customer_orders as $customer_order) {
            $order = new WC_Order($customer_order->ID);

            // Get order total
            $order_total = $order->get_total();

            // Subtract tax amount
            $tax_amount = $order->get_total_tax();
            $order_total -= $tax_amount;

            // Subtract shipping amount
            $shipping_total = $order->get_shipping_total();
            $order_total -= $shipping_total;

            // Subtract coupon discount
            $discount_total = $order->get_discount_total();
            $order_total -= $discount_total;

            // Add to running total
            $total_spent += $order_total;
        }
    }


    return $total_spent;
}

/**
 * Export all users' member rank data to a CSV file
 * 
 * @param string $filename The name of the CSV file to create (default: 'member_ranks.csv')
 * @return bool True on success, false on failure
 */
function mr_export_all_member_ranks_to_csv($filename = 'member_ranks.csv')
{
    // Get all users
    $users = get_users();

    if (empty($users)) {
        return false;
    }

    // Prepare CSV headers
    $headers = array(
        'User ID',
        'Username',
        'Email',
        'Rank',
        'Rank Date',
        'Expired Date',
        'Spent Total',
        'Spent Total in Current',
        'Next Update',
        'Spent Retain'
    );

    // Create a file pointer
    $fp = fopen($filename, 'w');
    if (!$fp) {
        return false;
    }

    // Write headers to CSV file
    fputcsv($fp, $headers);

    // Loop through each user and add their data
    foreach ($users as $user) {
        // Get member rank data for this user
        $rank_data = mr_get_member_rank($user->ID);

        // Get rank date and expired date
        $rank_date = get_user_meta($user->ID, 'rank_date', true);
        $rank_date_formatted = '';
        if ($rank_date && is_object($rank_date)) {
            $rank_date_formatted = $rank_date->format('Y-m-d');
        }

        $expired_date = get_user_meta($user->ID, 'expired_date', true);
        $expired_date_formatted = '';
        if (!empty($expired_date)) {
            $expired_date_formatted = is_object($expired_date) ? $expired_date->format('Y-m-d') : $expired_date;
        }

        // Build the row data
        $row = array(
            $user->ID,
            $user->user_login,
            $user->user_email,
            isset($rank_data['rank']) ? $rank_data['rank'] : '',
            $rank_date_formatted,
            $expired_date_formatted,
            isset($rank_data['spent_total']) ? $rank_data['spent_total'] : 0,
            isset($rank_data['spent_total_in_current']) ? $rank_data['spent_total_in_current'] : 0,
            isset($rank_data['next_update']) ? $rank_data['next_update'] : 0,
            isset($rank_data['spent_retain']) ? $rank_data['spent_retain'] : 0
        );

        // Write the data to the CSV file
        fputcsv($fp, $row);
    }

    // Close the file pointer
    fclose($fp);

    return true;
}

/**
 * Creates a downloadable CSV file with all member rank data
 * This can be hooked to an admin action or AJAX endpoint
 */
function mr_download_all_member_ranks()
{
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Generate filename with date
    $filename = 'member_ranks_' . date('Y-m-d') . '.csv';

    // Set headers for file download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Very important: we need to prevent WordPress from rendering any HTML before our CSV output
    // This needs to be at the very start of the output
    ob_clean(); // Clean (erase) the output buffer
    flush(); // Flush system output buffer

    // Export data directly to output
    $users = get_users();

    // Prepare CSV headers
    $headers = array(
        'User ID',
        'Username',
        'Email',
        'Rank',
        'Rank Date',
        'Expired Date',
        'Spent Total',
        'Spent Total in Current',
        'Next Update',
        'Spent Retain'
    );

    // Create output file pointer
    $output = fopen('php://output', 'w');

    // Write headers to CSV file
    fputcsv($output, $headers);

    // Loop through each user and add their data
    foreach ($users as $user) {
        // Get member rank data for this user
        $rank_data = mr_get_member_rank($user->ID);

        // Get rank date and expired date
        $rank_date = get_user_meta($user->ID, 'rank_date', true);
        $rank_date_formatted = '';
        if ($rank_date && is_object($rank_date)) {
            $rank_date_formatted = $rank_date->format('Y-m-d');
        }

        $expired_date = get_user_meta($user->ID, 'expired_date', true);
        $expired_date_formatted = '';
        if (!empty($expired_date)) {
            $expired_date_formatted = is_object($expired_date) ? $expired_date->format('Y-m-d') : $expired_date;
        }

        // Build the row data
        $row = array(
            $user->ID,
            $user->user_login,
            $user->user_email,
            isset($rank_data['rank']) ? $rank_data['rank'] : '',
            $rank_date_formatted,
            $expired_date_formatted,
            isset($rank_data['spent_total']) ? $rank_data['spent_total'] : 0,
            isset($rank_data['spent_total_in_current']) ? $rank_data['spent_total_in_current'] : 0,
            isset($rank_data['next_update']) ? $rank_data['next_update'] : 0,
            isset($rank_data['spent_retain']) ? $rank_data['spent_retain'] : 0
        );

        // Write the data to the CSV file
        fputcsv($output, $row);
    }

    // Close the file pointer
    fclose($output);
    exit; // Important to prevent any other output
}

/**
 * Example of adding an admin menu page to download the CSV
 */
function mr_add_export_page()
{
    add_submenu_page(
        'woocommerce',
        'Export Member Ranks',
        'Export Member Ranks',
        'manage_options',
        'mr-export-ranks',
        'mr_export_page_callback'
    );
}
add_action('admin_menu', 'mr_add_export_page');

/**
 * Callback function for the export page
 */
function mr_export_page_callback()
{
    // Check if download action is triggered first, before any HTML output
    if (isset($_GET['action']) && $_GET['action'] == 'download') {
        mr_download_all_member_ranks();
        return; // Stop execution after download is complete
    }

    // Only output HTML if we're not downloading
    ?>
    <div class="wrap">
        <h1>Export Member Ranks</h1>
        <p>Click the button below to export all member rank data to a CSV file.</p>
        <a href="<?php echo admin_url('admin.php?page=mr-export-ranks&action=download'); ?>"
            class="button button-primary">Download CSV</a>
    </div>
    <?php
}

if (isset($_GET) && isset($_GET['user_total_spent_amount'])) {

    $start_date = '2023-07-01';
    $end_date = '2024-06-30';
    $user_id = 829;
    // Calculate the total spent by user
    $total_spent = get_customer_total_spent($user_id, $start_date, $end_date);

    // Check for errors
    if (is_wp_error($total_spent)) {
        echo 'Error: ' . $total_spent->get_error_message();
        exit;
    } else {
        echo 'Customer (ID: ' . $user_id . ') total spent from ' . $start_date . ' to ' . $end_date . ': ' . wc_price($total_spent);
        exit;
        // echo 'Raw amount: ' . number_format($total_spent, 2);
    }

}