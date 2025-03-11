<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>
<p class="price">
    <?php
    if(in_array($product->id, arr_gift_card_products_use_offline())){
        $org_price=$_SESSION['org_price'];
        ?>
    <span class="bf_price"><del><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol"></span><?php echo wc_price($org_price); ?></bdi></span></del><span class="price__discount--details">20% OFF</span></span>
    <?php } ?>
    <?php echo $product->get_price_html(); ?>
</p>
<p class="link_cp"><a id="ch_link_cp" href="#">納期について</a></p>
<?php
$notice=get_field('notice_text_product',$product->id);
if(!empty($notice)){
    echo '<div class="notice_product">'.do_shortcode($notice).'</div>';
}
?>