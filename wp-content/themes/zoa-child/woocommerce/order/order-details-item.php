<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>
<div class="product-list__item line-item <?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">
	<div class="mini-product--group">
		<?php
                        if($product!=false){
			$is_visible        = $product && $product->is_visible();
			$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

			echo '<a class="mini-product__link" href="'.$product_permalink.'"><img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="" width="" class="mini-product__img" /></a>';
                        }else{
                            ?>
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/no_image.jpg" alt="商品画像" height="" width="" class="mini-product__img">
            <?php
                        }
		?>
	<div class="mini-product__info">
	<p class="mini-product__item mini-product__name p5">
		<?php 
		echo apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, $order); ?>
	</p>
	<?php
	do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

	wc_display_item_meta( $item );

	do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
	?>
	<div class="line-item-quantity mini-product__attribute mini-product__item">
		<span class="label"><?php esc_html_e( 'Quantity', 'zoa' ); ?>: </span>
		<span class="value lato"><?php echo apply_filters( 'woocommerce_order_item_quantity_html', '' . sprintf( '%s', $item->get_quantity() ) . '', $item ); ?></span>
	</div>
	<div class="mini-product__item txt--bold serif mini-product__price">
		<?php echo $order->get_formatted_line_subtotal( $item ); ?>
	</div>
	<?php
		if ($product!=false && !empty($product->get_sku()) ) {
									echo '<div class="mini-product__item mini-product__id light-copy">';
									echo '<span class="label">商品番号: </span><span class="value">' . $product->get_sku() . '</span>';
									echo '</div>';//sku
									}
		?>
	<?php if ( $show_purchase_note && $purchase_note ) : ?>
	<div class="mini-product__item product-purchase-note"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></div>
	<?php endif; ?>
	</div><!--/mini-product__info-->
	</div><!--/mini-product--group-->
</div>

