<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart cartin__row" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<div class="variations pdp__attribute--group">
			
			<?php 
				global $post;
				$current_post = $post;
				$post = get_post($product->get_id());
				foreach ( $attributes as $attribute_name => $options ) : ?>
					<div class="pdp__attribute variations__attribute">
						<label class="pdp__attribute__label variations__attribute__label" for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?>
							<?php if ($attribute_name == 'pa_size' && class_exists('productsize_chart_Public')) {
								$chart = new productsize_chart_Public('productsize-chart-for-woocommerce', 123);
								$chart_id=$chart->productsize_chart_id($product->get_id());
								if ($chart_id) { ?>
									<div class="info_show_wrap about_size_wraper">
										<div class="bodyshape_info size_info">
											<span class="cta about_size pop-up-button" style="display: none;"><?php esc_html_e('About Size', 'zoa')?></span>
											<span class="cta about_size pop-up-button-remodal"><?php esc_html_e('About Size', 'zoa')?></span>
										</div>
										<div class="pop-up tooltip-pop pop-size">
											<div class="pop-head">
												<h2 class="pop-title">
													<i class="oecicon oecicon-alert-circle-que"></i><?php esc_html_e( "About Chiyono Anne's Size", 'zoa' ); ?></h2>
												<button class="pop-up-close" type="button">
													<i class="oecicon oecicon-simple-remove"></i>
												</button>
											</div>
											<div class="row">
												<div class="col-12">
													<div class="prod-detail-content" id="size_chart_content_popup">
														<?php // $chart->productsize_chart_new_product_tab_content(); ?>
													</div>
												</div>
											</div>
											<!--/.row-->
										</div>
										<!--/.pop-up-->
										</div>
								<?php }?>
							<?php }?>
						</label>
						<div class="value variations__attribute__value">
							<?php
							wc_dropdown_variation_attribute_options( array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
							) );
							echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
							?>
						</div>
						<!--show if a product has size attribute AND categorized as plunge-bra OR soft-bra-->
						<?php
							$plunge_bra = get_option('ch_plunge_bra', ''); //'plunge-bra'; //category slug
							$soft_bra = get_option('ch_soft_bra', ''); //'soft-bra'; //category slug
							if ( 'pa_size' == $attribute_name && ( is_product_in_cat_fit($product->get_id(), $plunge_bra) || is_product_in_cat_fit($product->get_id(), $soft_bra) ) ) : ?>
								<p class="p_small">購入前にサイズを確認されたい方は<a href="<?php echo home_url( 'shop/special-service/product-for-try-fit-your-size/' ); ?>" class="underline">こちら</a></p>
							<?php endif; ?>
							<!--/show if a product size attribute AND categorized as plunge-bra OR soft-bra-->
							<?php if ( function_exists('get_field') && 'pa_size' == $attribute_name && get_field('text_bellow_size') !='' ) : ?>
								<p class="p_small"><?php echo get_field('text_bellow_size'); ?></p>
							<?php endif; ?>
					</div>
				<?php endforeach; ?>
				
				<?php $post = $current_post; ?>
		</div>
		<?php do_action( 'woocommerce_after_variations_table' ); ?>
		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
