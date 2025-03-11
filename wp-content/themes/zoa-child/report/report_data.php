<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Report by Series
// -----------------------
// 1. Create extra tab under Reports / Orders

add_filter('woocommerce_admin_reports', 'ch_bbloomer_admin_add_report_orders_tab');

function ch_bbloomer_admin_add_report_orders_tab($reports) {

    $array = array(
        'ch_yearly_sales_by_series' => array(
            'title' => 'Sales by Series',
            'description' => '',
            'hide_title' => 1,
            'callback' => 'ch_yearly_sales_by_series'
        )
    );

    $reports['orders']['reports'] = array_merge($reports['orders']['reports'], $array);

    return $reports;
}

// -----------------------
// 2. Calculate sales by state

function ch_check_series_in_order($order_id, $series_slug) {

    if (!$order_id) {
        return;
    }
    $order = wc_get_order($order_id);
    $category_in_order = false;
    $items = $order->get_items();
    foreach ($items as $item) {
        $product_id = $item['product_id'];
        if ($product_id > 0 && has_term($series_slug, 'series', $product_id)) {
            $category_in_order = true;
            break;
        }
    }
    return $category_in_order;
}

function ch_yearly_sales_by_series() {

    $activey = '';
    $activel = '';
    $activem = '';
    $active7 = '';
    $args = [
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => ['wc-completed','wc-processing']
    ];
    if (isset($_REQUEST['range'])) {
        $range = $_REQUEST['range'];
        if ($range == 'year') {
            $activey = 'active';
            $args['year'] = date('Y');
        } elseif ($range == 'last_month') {
            $args['year'] = date('Y');
            $args['monthnum'] = date("m", strtotime("-1 month"));
            $activel = 'active';
        } elseif ($range == 'month') {
            $args['year'] = date('Y');
            $args['monthnum'] = date('m');
            $activem = 'active';
        } elseif ($range == '7day') {
            $active7 = 'active';
            $args['date_query'] = array(
                array(
                    'after' => date("Y-m-d", strtotime("-7 days")),
                    'before' => date('Y-m-d'),
                    'inclusive' => true,
                )
            );
        } elseif ($range == 'custom') {
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
            } elseif (!empty($start_date) && empty($end_date)) {
                $args['date_query'] = array(
                    array(
                        'after' => $start_date,
                        'inclusive' => true,
                    )
                );
            } elseif (empty($start_date) && !empty($end_date)) {
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
    $terms = get_terms(array(
        'taxonomy' => 'series',
        'hide_empty' => false,
    ));
    $no_series = 'No series';
    foreach ($orders as $order => $value) {
        $detect_no_series = false;
        $order_id = $value->ID;
        $order = wc_get_order($order_id);
        foreach ($terms as $sies) {
            $is_in_serie = ch_check_series_in_order($order_id, $sies->slug);
            if ($is_in_serie) {
                $detect_no_series = true;
                $order_type_total[$sies->name]['total'] += $order->get_total();
                $order_type_total[$sies->name]['shipping_total'] += $order->get_shipping_total();
                $order_type_total[$sies->name]['orders_placed'] += 1;
                $order_type_total[$sies->name]['items_purchased'] += $order->get_item_count();
            }
        }
        if ($detect_no_series == false) {
            $order_type_total[$no_series]['total'] += $order->get_total();
            $order_type_total[$no_series]['shipping_total'] += $order->get_shipping_total();
            $order_type_total[$no_series]['orders_placed'] += 1;
            $order_type_total[$no_series]['items_purchased'] += $order->get_item_count();
        }
    }
    $_SESSION['sales_report_series'] = $order_type_total;

    $path_filter = admin_url('admin.php?page=wc-reports&tab=orders&report=ch_yearly_sales_by_series');
    ?>
    <link rel="stylesheet" href="<?php echo site_url('wp-content/themes/zoa-child/css/sales_by_order_type.css'); ?>">
    <h3>Sales by Series</h3>
    <div class="stats_range ch_filter">
        <ul>
            <li class="<?php echo $activey; ?>"><a href="<?php echo $path_filter; ?>&amp;range=year">Year</a></li><li class="<?php echo $activel; ?>"><a href="<?php echo $path_filter; ?>&amp;range=last_month">Last month</a></li><li class="<?php echo $activem; ?>"><a href="<?php echo $path_filter; ?>&amp;range=month">This month</a></li><li class="<?php echo $active7; ?>"><a href="<?php echo $path_filter; ?>&amp;range=7day">Last 7 days</a></li>				<li class="custom <?php echo $activec; ?>">
                Custom:					<form method="GET">
                    <div>
                        <input type="hidden" name="page" value="wc-reports"><input type="hidden" name="tab" value="orders"><input type="hidden" name="report" value="ch_yearly_sales_by_series"><input type="hidden" name="range" value="year">							<input type="hidden" name="range" value="custom">
                        <input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php if (isset($start_date)) echo $start_date; ?>" name="start_date" class="" autocomplete="off" id="start_date">							<span>–</span>
                        <input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php if (isset($end_date)) echo $end_date; ?>" name="end_date" class="" autocomplete="off" id="end_date">							<button type="submit" class="button" value="Go">Go</button>
                    </div>
                </form>
                <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        $('#start_date').datepicker({dateFormat: 'yy-mm-dd'});
                        $('#end_date').datepicker({dateFormat: 'yy-mm-dd'});
                    });
                </script>
            </li>
        </ul>
    </div>
    <?php
    if (!empty($order_type_total)) {
        ?>
        <div>
            <a class="button" target="_blank" href="<?php echo admin_url('?export=ch_yearly_sales_by_series', 'https'); ?>"/>Export CSV</a>
        </div>
        <?php
    }
    ?>
    <table class="wp-list-table widefat fixed">
        <thead>
            <tr>
                <td>Series</td>
                <td>Total sales</td>
                <td>Orders placed</td>
                <td>Items purchased</td>
                <td>Shipping total</td>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($order_type_total)) {
                foreach ($order_type_total as $key => $value) {
                    ?>
                    <tr>
                        <td><?php echo $key; ?></td>
                        <td><?php echo wc_price($value['total']); ?></td>
                        <td><?php echo $value['orders_placed']; ?></td>
                        <td><?php echo $value['items_purchased']; ?></td>
                        <td><?php echo wc_price($value['shipping_total']); ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo 'No data';
            }
            ?>
        </tbody>
    </table>
    <?php
}

add_action('admin_init', 'rp_export_sales_series_csv');

function rp_export_sales_series_csv() {
    if (isset($_REQUEST['export']) && $_REQUEST['export'] == 'ch_yearly_sales_by_series') {
        $data = $_SESSION['sales_report_series'];
        if (!empty($data)) {
            $file_name = date('YmdHis') . '_sales_by_series.csv';
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '";');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Series', 'Total sales', 'Orders placed', 'Items purchased', 'Shipping total'));
            foreach ($data as $key => $value) {
                $total = number_format($value['total']);
                $total_ship = number_format($value['shipping_total']);
                fputcsv($output, array($key, $total, $value['orders_placed'], $value['items_purchased'], $total_ship));
            }
            fclose($output);
            exit();
        }
    }
}

///Report by Size

add_filter('woocommerce_admin_reports', 'ch_bbloomer_admin_add_report_orders_size');

function ch_bbloomer_admin_add_report_orders_size($reports) {

    $array = array(
        'ch_yearly_sales_by_size' => array(
            'title' => 'Sales by Size',
            'description' => '',
            'hide_title' => 1,
            'callback' => 'ch_yearly_sales_by_size'
        )
    );

    $reports['orders']['reports'] = array_merge($reports['orders']['reports'], $array);

    return $reports;
}

// -----------------------
// 2. Calculate sales by size

function ch_check_size_in_order($order_id, $size_name) {

    if (!$order_id) {
        return;
    }
    $order = wc_get_order($order_id);
    $category_in_order = false;
    $items = $order->get_items();
    foreach ($items as $item) {
        $product_id = $item['product_id'];
        if ($product_id > 0) {
            $product = wc_get_product($product_id);
            $attributes = $product->get_attributes('bikini-top-size');
            $size_bikini_top = $attributes['bikini-top-size']['options'];
            $size_bottom_size = $attributes['bottom-size']['options'];
            if (in_array($size_name, $size_bikini_top) || in_array($size_name, $size_bottom_size)) {
                $category_in_order = true;
                break;
            }
        } else {
            if ($item['size'] == $size_name) {
                $category_in_order = true;
                break;
            }
        }
    }
    return $category_in_order;
}

function ch_yearly_sales_by_size() {

    $activey = '';
    $activel = '';
    $activem = '';
    $active7 = '';
    $args = [
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => ['wc-completed','wc-processing']
    ];
    if (isset($_REQUEST['range'])) {
        $range = $_REQUEST['range'];
        if ($range == 'year') {
            $activey = 'active';
            $args['year'] = date('Y');
        } elseif ($range == 'last_month') {
            $args['year'] = date('Y');
            $args['monthnum'] = date("m", strtotime("-1 month"));
            $activel = 'active';
        } elseif ($range == 'month') {
            $args['year'] = date('Y');
            $args['monthnum'] = date('m');
            $activem = 'active';
        } elseif ($range == '7day') {
            $active7 = 'active';
            $args['date_query'] = array(
                array(
                    'after' => date("Y-m-d", strtotime("-7 days")),
                    'before' => date('Y-m-d'),
                    'inclusive' => true,
                )
            );
        } elseif ($range == 'custom') {
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
            } elseif (!empty($start_date) && empty($end_date)) {
                $args['date_query'] = array(
                    array(
                        'after' => $start_date,
                        'inclusive' => true,
                    )
                );
            } elseif (empty($start_date) && !empty($end_date)) {
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
    $terms = get_terms(array(
        'taxonomy' => 'pa_size',
        'hide_empty' => false,
    ));

    foreach ($orders as $order => $value) {
        $order_id = $value->ID;
        $order = wc_get_order($order_id);
        foreach ($terms as $sies) {
            $is_in_serie = ch_check_size_in_order($order_id, $sies->name);
            if ($is_in_serie) {
                $order_type_total[$sies->name]['total'] += $order->get_total();
                $order_type_total[$sies->name]['shipping_total'] += $order->get_shipping_total();
                $order_type_total[$sies->name]['orders_placed'] += 1;
                $order_type_total[$sies->name]['items_purchased'] += $order->get_item_count();
            }
        }
    }
    $_SESSION['sales_report_size'] = $order_type_total;

    $path_filter = admin_url('admin.php?page=wc-reports&tab=orders&report=ch_yearly_sales_by_size');
    ?>
    <link rel="stylesheet" href="<?php echo site_url('wp-content/themes/zoa-child/css/sales_by_order_type.css'); ?>">
    <h3>Sales by Series</h3>
    <div class="stats_range ch_filter">
        <ul>
            <li class="<?php echo $activey; ?>"><a href="<?php echo $path_filter; ?>&amp;range=year">Year</a></li><li class="<?php echo $activel; ?>"><a href="<?php echo $path_filter; ?>&amp;range=last_month">Last month</a></li><li class="<?php echo $activem; ?>"><a href="<?php echo $path_filter; ?>&amp;range=month">This month</a></li><li class="<?php echo $active7; ?>"><a href="<?php echo $path_filter; ?>&amp;range=7day">Last 7 days</a></li>				<li class="custom <?php echo $activec; ?>">
                Custom:					<form method="GET">
                    <div>
                        <input type="hidden" name="page" value="wc-reports"><input type="hidden" name="tab" value="orders"><input type="hidden" name="report" value="ch_yearly_sales_by_size"><input type="hidden" name="range" value="year">							<input type="hidden" name="range" value="custom">
                        <input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php if (isset($start_date)) echo $start_date; ?>" name="start_date" class="" autocomplete="off" id="start_date">							<span>–</span>
                        <input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php if (isset($end_date)) echo $end_date; ?>" name="end_date" class="" autocomplete="off" id="end_date">							<button type="submit" class="button" value="Go">Go</button>
                    </div>
                </form>
                <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        $('#start_date').datepicker({dateFormat: 'yy-mm-dd'});
                        $('#end_date').datepicker({dateFormat: 'yy-mm-dd'});
                    });
                </script>
            </li>
        </ul>
    </div>
    <?php
    if (!empty($order_type_total)) {
        ?>
        <div>
            <a class="button" target="_blank" href="<?php echo admin_url('?export=rp_export_sales_series_csv_size', 'https'); ?>"/>Export CSV</a>
        </div>
        <?php
    }
    ?>
    <table class="wp-list-table widefat fixed">
        <thead>
            <tr>
                <td>Size</td>
                <td>Total sales</td>
                <td>Orders placed</td>
                <td>Items purchased</td>
                <td>Shipping total</td>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($order_type_total)) {
                foreach ($order_type_total as $key => $value) {
                    ?>
                    <tr>
                        <td><?php echo $key; ?></td>
                        <td><?php echo wc_price($value['total']); ?></td>
                        <td><?php echo $value['orders_placed']; ?></td>
                        <td><?php echo $value['items_purchased']; ?></td>
                        <td><?php echo wc_price($value['shipping_total']); ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo 'No data';
            }
            ?>
        </tbody>
    </table>
    <?php
}

add_action('admin_init', 'rp_export_sales_series_csv_size');

function rp_export_sales_series_csv_size() {
    if (isset($_REQUEST['export']) && $_REQUEST['export'] == 'rp_export_sales_series_csv_size') {
        $data = $_SESSION['sales_report_size'];
        if (!empty($data)) {
            $file_name = date('YmdHis') . '_sales_by_size.csv';
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $file_name . '";');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Size', 'Total sales', 'Orders placed', 'Items purchased', 'Shipping total'));
            foreach ($data as $key => $value) {
                $total = number_format($value['total']);
                $total_ship = number_format($value['shipping_total']);
                fputcsv($output, array($key, $total, $value['orders_placed'], $value['items_purchased'], $total_ship));
            }
            fclose($output);
            exit();
        }
    }
}
