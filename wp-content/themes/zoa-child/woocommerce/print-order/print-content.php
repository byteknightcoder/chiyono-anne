<?php
/**
 * Print order content. Copy this file to your themes
 * directory /woocommerce/print-order to customize it.
 *
 * @package WooCommerce Print Invoice & Delivery Note/Templates
 */
if (!defined('ABSPATH'))
    exit;
$customer_id = $order->customer_id;
$phone = get_user_meta($customer_id, 'billing_phone', true);
?>
<div class="headerinv">
    <div class="printdate"><span>納品書</span><span><?php echo date("Y年m月d日", strtotime($order->order_date)); ?></span></div>
    <div class="order_id">No. <?php echo $order->get_id(); ?></div>
</div>

<div class="order-addresses<?php if (!wcdn_has_shipping_address($order)) : ?> no-shipping-address<?php endif; ?>">
    <div class="billing-address">
        <h3><?php echo get_user_meta($customer_id, 'last_name', true) . ' ' . get_user_meta($customer_id, 'first_name', true); ?></h3>
        <address>
            <?php
            if (!$order->get_formatted_billing_address())
                _e('N/A', 'woocommerce-delivery-notes');
            else
                echo apply_filters('wcdn_address_billing', $order->get_formatted_billing_address(), $order);
            ?>
            <br/>TEL: <?php echo $phone; ?>
            <div class="box"></div>
            <div class="payment_method">
                <?php echo _e('Payment Method','woocommerce-delivery-notes'); ?>: <?php echo $order->get_payment_method_title(); ?>
            </div>
        </address>
    </div>
    <?php do_action('wcdn_after_addresses', $order); ?>
    <div class="order-branding">
        <div class="company-logo">
            <?php if (wcdn_get_company_logo_id()) : ?><?php wcdn_company_logo(); ?><?php endif; ?>
        </div>
        <div class="company-info">
            <?php //if (!wcdn_get_company_logo_id()) : ?><h3 class="company-name"><?php wcdn_company_name(); ?></h><?php //endif; ?>
                <div class="company-address"><?php wcdn_company_info(); ?></div>
        </div>
        <?php do_action('wcdn_after_branding', $order); ?>
    </div><!-- .order-branding -->
</div><!-- .order-addresses -->
<div class="order-thanks">
    <?php wcdn_personal_notes(); ?>
    <?php do_action('wcdn_after_thanks', $order); ?>
</div><!-- .order-thanks -->
<div class="order-items">
    <table>
        <thead>
            <tr>
                <th class="head-name"><span><?php esc_attr_e('Product', 'woocommerce-delivery-notes'); ?></span></th>
                <th class="head-item-price"><span><?php esc_attr_e('Unit Price', 'woocommerce-delivery-notes'); ?></span></th>
                <th class="head-quantity"><span><?php esc_attr_e('Quantity', 'woocommerce-delivery-notes'); ?></span></th>
                <th class="head-price"><span><?php esc_attr_e('Price(JPY)', 'woocommerce-delivery-notes'); ?></span></th>
            </tr>
        </thead>

        <tbody>
            <?php
            if (count($order->get_items()) > 0) :
                ?>
                <?php foreach ($order->get_items() as $item) : ?>

                    <?php
                    $product = apply_filters('wcdn_order_item_product', $order->get_product_from_item($item), $item);

                    if (version_compare(get_option('woocommerce_version'), '3.0.0', '>=')) {
                        $item_meta = new WC_Order_Item_Product($item['item_meta'], $product);
                    } else {
                        $item_meta = new WC_Order_Item_Meta($item['item_meta'], $product);
                    }
                    ?>
                    <tr>
                        <td class="product-name">
                            <?php do_action('wcdn_order_item_before', $product, $order, $item); ?>
                            <span class="name">
                                <?php
                                $addon_name = $item->get_meta('_wc_pao_addon_name', true);
                                $addon_value = $item->get_meta('_wc_pao_addon_value', true);
                                $is_addon = !empty($addon_value);

                                if ($is_addon) { // Displaying options of product addon.
                                    $addon_html = '<div class="wc-pao-order-item-name">' . esc_html($addon_name) . '</div><div class="wc-pao-order-item-value">' . esc_html($addon_value) . '</div></div>';

                                    echo wp_kses_post($addon_html);
                                } else {

                                    $product_id = $item['product_id'];
                                    $prod_name = get_post($product_id);
                                    $product_name = $prod_name->post_title;

                                    echo wp_kses_post(apply_filters('wcdn_order_item_name', $product_name, $item));
                                    ?>
                                </span>

                                <?php
                                if (version_compare(get_option('woocommerce_version'), '3.0.0', '>=')) {
                                    if (isset($item['variation_id']) && 0 !== $item['variation_id']) {
                                        $variation = wc_get_product($item['product_id']);
                                        foreach ($item['item_meta'] as $key => $value) {
                                            if (!( 0 === strpos($key, '_') )) {
                                                if (is_array($value)) {
                                                    continue;
                                                }
                                                $term_wp = get_term_by('slug', $value, $key);
                                                $attribute_name = wc_attribute_label($key, $variation);
                                                if (isset($term_wp->name)) {
                                                    echo '<br>' . wp_kses_post($attribute_name . ':' . $term_wp->name);
                                                } else {
                                                    echo '<br>' . wp_kses_post($attribute_name . ':' . $value);
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($item['item_meta'] as $key => $value) {
                                            if (!( 0 === strpos($key, '_') )) {
                                                if (is_array($value)) {
                                                    continue;
                                                }
                                                echo '<br>' . wp_kses_post($key . ':' . $value);
                                            }
                                        }
                                    }
                                } else {
                                    $item_meta_new = new WC_Order_Item_Meta($item['item_meta'], $product);
                                    $item_meta_new->display();
                                }
                                ?>
                                <br>
                                <dl class="extras">
                                    <?php if ($product && $product->exists() && $product->is_downloadable() && $order->is_download_permitted()) : ?>

                                        <dt><?php esc_attr_e('Download:', 'woocommerce-delivery-notes'); ?></dt>
                                        <dd>
                                            <?php
                                            // translators: files count.
                                            printf(esc_attr_e('%s Files', 'woocommerce-delivery-notes'), count($item->get_item_downloads()));
                                            ?>
                                        </dd>

                                    <?php endif; ?>

                                    <?php
                                    $fields = apply_filters('wcdn_order_item_fields', array(), $product, $order, $item);

                                    foreach ($fields as $field) :
                                        ?>

                                        <dt><?php echo esc_html($field['label']); ?></dt>
                                        <dd><?php echo esc_html($field['value']); ?></dd>

                                    <?php endforeach; ?>
                                </dl>
                            <?php } ?>
                            <?php do_action('wcdn_order_item_after', $product, $order, $item); ?>
                        </td>
                        <td class="product-item-price">
                            <span><?php echo wp_kses_post(wcdn_get_formatted_item_price($order, $item)); ?></span>
                        </td>
                        <td class="product-quantity">
                            <span><?php echo esc_attr(apply_filters('wcdn_order_item_quantity', $item['qty'], $item)); ?></span>
                        </td>
                        <td class="product-price">
                            <span><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>

        <tfoot>
            <?php
            $totals_arr = $order->get_order_item_totals();
            if ($totals_arr) :

                foreach ($totals_arr as $total) :
                    ?>
                    <tr>
                        <td style="border: none; border-left: solid 1px;" colspan="2"></td>
                        <td class="total-quantity"><span><?php echo wp_kses_post($total['label']); ?></span></td>
                        <td class="total-price"><span><?php echo wp_kses_post($total['value']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tfoot>
    </table>

    <?php do_action('wcdn_after_items', $order); ?>
</div><!-- .order-items -->

<div class="order-colophon">
    <div class="colophon-policies">
        <?php wcdn_policies_conditions(); ?>
    </div>
    <div class="colophon-imprint">
        <?php wcdn_imprint(); ?>
    </div>
    <?php do_action('wcdn_after_colophon', $order); ?>
</div><!-- .order-colophon -->