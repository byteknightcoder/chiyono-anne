<?php
/**
 * This template displays gift products layout in cart page
 *
 * This template can be overridden by copying it to yourtheme/free-gifts-for-woocommerce/gift-products-layout.php
 *
 * To maintain compatibility, Free Gifts for WooCommerce will update the template files and you have to copy the updated files to your theme
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$_columns = array(
    'product_name' => __('Product Name', 'free-gifts-for-woocommerce'),
    'product_image' => __('Product Image', 'free-gifts-for-woocommerce'),
    'add_to_cart' => __('Add to cart', 'free-gifts-for-woocommerce'),
);
?>
<div class="fgf_gift_products_wrapper">
    <?php
    /**
     * This hook is used to display the extra content before gift products content.
     * 
     * @since 1.0
     */
    do_action('fgf_before_gift_products_content');
    $temp_gift_product = array();
    $current_user = wp_get_current_user();
    $rank_and_amount = mr_get_member_rank($current_user->ID);
    $rank=$rank_and_amount['rank'];
    foreach ($gift_products as $gift_product) {
        $rule_id = $gift_product['rule_id'];
        $rule = fgf_get_rule($rule_id);
        if ($rule->get_name() == 'bronze' && $rank == 'bronze') {
            $temp_gift_product[] = $gift_product;
        } elseif ($rule->get_name() == 'silver' && $rank == 'silver') {
            $temp_gift_product[] = $gift_product;
        } elseif ($rule->get_name() == 'gold' && $rank == 'gold') {
            $temp_gift_product[] = $gift_product;
        } elseif ($rule->get_name() == 'royal' && $rank == 'royal') {
            $temp_gift_product[] = $gift_product;
        } else {
            if ($rule->get_name() !== 'bronze' && $rule->get_name() !== 'silver' && $rule->get_name() !== 'gold' && $rule->get_name() !== 'royal') {
                $temp_gift_product[] = $gift_product;
            }
        }
    }
    $gift_products = $temp_gift_product;
    ?>
    <h3><?php echo esc_html(get_option('fgf_settings_free_gift_heading_label')); ?></h3>
    <?php
    foreach ($gift_products as $gift_product) {
        $rule_id = $gift_product['rule_id'];
        $rule = fgf_get_rule($rule_id);
        if (!empty($rule->get_description())) {
            ?>
            <div class="fgf_rule_description">
                <?php echo $rule->get_description(); ?>
            </div>
            <?php
            break;
        }
    }
    ?>
    <div rule="<?php echo $rule_id; ?>" class="fgf-gift-products-content">
        <div class="shop_table shop_table_responsive fgf_gift_products_table">

            <?php
            fgf_get_template(
                    'gift-products.php', array(
                'gift_products' => $gift_products,
                'permalink' => get_permalink(),
                    )
            );
            ?>
        </div>
    </div>
    <?php
    /**
     * This hook is used to display the extra content after gift products content.
     * 
     * @since 1.0
     */
    do_action('fgf_after_gift_products_content');
    ?>
    <input type="hidden" id="fgf_gift_products_type" value='<?php echo esc_attr($mode); ?>'>
</div>
<?php
