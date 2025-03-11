<?php
/**
 * This template displays the add order gift wrapper.
 *
 * This template can be overridden by copying it to yourtheme/gift-wrapper-for-woocommerce/add-order-gift-wrapper.php
 *
 * To maintain compatibility, Gift Wrapper for WooCommerce will update the template files and you have to copy the updated files to your theme
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (WC()->cart->total > 60000) {
    ?>
    <p class="gtw-add-order-gift-wrapper-content">
        <button type="button" class="button gtw-popup-order-gift-wrapper ch_gtw_show_modal_option">
            <?php echo wp_kses_post(gtw_get_order_gift_wrapper_button_label($product)); ?>
        </button>
    </p>
    <div class="remodal remodal_hbody" data-remodal-id="gift_wrapper" id="gift_wrapper" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
        <div class="remodal_wraper">
            <div class="modal_head">
                <?php echo $product->get_title(); ?>
            </div>
            <div class="modal_body">
                <?php
                $product_id = gtw_get_order_gift_wrapper_product();
                if ($product_id) {
                    $product = wc_get_product($product_id);
                    $product_id_parent = $product->get_parent_id();
                    $product_parent = wc_get_product($product_id_parent);
                    $vari = $product_parent->get_children();
                    if (!empty($vari)) {
                        ?>
                        <select class="gift_wrap_options" name="add-to-cart">
                            <?php
                            foreach ($vari as $value) {
                                $obj = wc_get_product($value);
                                ?>
                                <option value="<?php echo wc_get_cart_url() . '?add-to-cart=' . $obj->get_id(); ?>"><?php echo $obj->get_attributes()['box-size'] . ' (' . $obj->get_description() . ')'; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <div>
                            <button type="button" class="button ch_add_gift_wrap_to_cart">
                                <?php echo __('Add to cart', 'zoa'); ?>
                            </button>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}