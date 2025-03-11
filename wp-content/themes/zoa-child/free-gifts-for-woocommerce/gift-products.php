<?php
/**
 * This template displays contents inside gift products table
 *
 * This template can be overridden by copying it to yourtheme/free-gifts-for-woocommerce/gift-products.php
 *
 * To maintain compatibility, Free Gifts for WooCommerce will update the template files and you have to copy the updated files to your theme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

foreach ( $gift_products as $gift_product ) :

	$link_classes = array( 'fgf_add_to_cart_link' ) ;
	if ( $gift_product[ 'hide_add_to_cart' ] ) {
		$link_classes[] = 'fgf_disable_links' ;
	}
	$buy_product_id = ! empty( $gift_product[ 'buy_product_id' ] ) ? $gift_product[ 'buy_product_id' ] : null ;
	?>
<div class="product-list__item freegift-row">
  <?php $_product       = wc_get_product( $gift_product[ 'parent_id' ] ) ; ?>
  <div class="mini-product--group">
    <div class="mini-product__link freegift__thum" data-title="<?php esc_attr_e( 'Product Image', 'free-gifts-for-woocommerce' ) ; ?>">
      <?php
			/**
			 * This hook is used to alter the gift product image.
			 * 
			 * @since 1.0
			 */
			echo wp_kses_post( apply_filters( 'fgf_table_gift_product_image', fgf_render_product_image( $_product, 'woocommerce_thumbnail', false ), $_product, $gift_product ) ) ;
			?>
    </div>
    <div class="mini-product__info freegift_info">
      <div class="mini-product__info_name freegift_name" data-title="<?php esc_attr_e( 'Product Name', 'free-gifts-for-woocommerce' ) ; ?>">
        <?php
				/**
				 * This hook is used to alter the gift product name.
				 * 
				 * @since 1.0
				 */
				echo wp_kses_post( apply_filters( 'fgf_table_gift_product_name', fgf_render_product_name( $_product, false ), $_product, $gift_product ) ) ;
				?>
      </div>
        <?php
        if(!empty($_product->get_short_description())){
            ?>
        <div class="mini-product__info_name freegift_name fgf_short_description">
            <?php echo $_product->get_short_description(); ?>
        </div>
        <?php
        }
        ?>
      <div class="mini-product--action" data-title="<?php esc_attr_e( 'Add to cart', 'free-gifts-for-woocommerce' ) ; ?>">
        <?php
			/**
			 * This hook is used to display the extra content before gift product add to cart link.
			 * 
			 * @since 1.0
			 */
			do_action( 'fgf_table_gift_product_before_add_cart_link', $_product, $gift_product ) ;
			?>
        <span class="<?php echo esc_attr( implode( ' ', $link_classes ) ) ; ?>">

          <?php if ( fgf_check_is_array( $gift_product[ 'variation_ids' ] ) ) : ?>
          <select class="fgf-product-variations" data-rule_id="<?php echo esc_attr( $gift_product[ 'rule_id' ] ) ; ?>" data-buy_product_id="<?php echo esc_attr( $buy_product_id ) ; ?>">
            <?php
						foreach ( $gift_product[ 'variation_ids' ] as $variation_id ) :
							$_variation = wc_get_product( $variation_id ) ;
							?>
            <option value="<?php echo esc_attr( $_variation->get_id() ) ; ?>"><?php echo esc_html( $_variation->get_name() ) ; ?></option>
            <?php endforeach ; ?>
          </select>
          <?php endif ; ?>

          <a class="<?php echo esc_attr( implode( ' ', fgf_get_gift_product_add_to_cart_classes() ) ) ; ?>" data-product_id="<?php echo esc_attr( $gift_product[ 'product_id' ] ) ; ?>" data-rule_id="<?php echo esc_attr( $gift_product[ 'rule_id' ] ) ; ?>" data-buy_product_id="<?php echo esc_attr( $buy_product_id ) ; ?>" href="<?php echo esc_url( fgf_get_gift_product_add_to_cart_url( $gift_product, $permalink ) ) ; ?>">
            <?php echo esc_html( get_option( 'fgf_settings_free_gift_add_to_cart_button_label' ) ) ; ?>
          </a>
        </span>
        <?php
			/**
			 * This hook is used to display the extra content after gift product add to cart link.
			 * 
			 * @since 1.0
			 */
			do_action( 'fgf_table_gift_product_after_add_cart_link', $_product, $gift_product ) ;
			?>
      </div>
    </div>
  </div>
  <!--/mini-product--group-->


</div>
<?php
endforeach ;