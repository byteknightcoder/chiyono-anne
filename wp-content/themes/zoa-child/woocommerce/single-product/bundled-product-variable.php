<?php
/**
 * Variable Bundled Product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-product-variable.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 6.21.0
 * @package WooCommerce Product Bundles
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="cart bundled_item_cart_content" data-title="<?php echo esc_attr( $bundled_item->get_title() ); ?>"
	data-product_title="<?php echo esc_attr( $bundled_item->get_product()->get_title() ); ?>"
	data-visible="<?php echo $bundled_item->is_visible() ? 'yes' : 'no'; ?>"
	data-optional_suffix="<?php echo esc_attr( $bundled_item->get_optional_suffix() ); ?>"
	data-optional="<?php echo $bundled_item->is_optional() ? 'yes' : 'no'; ?>" 
	data-type="<?php echo esc_attr( $bundled_product->get_type() ); ?>"
	data-product_variations="<?php echo wc_esc_json( wp_json_encode( $bundled_product_variations ) ); ?>"
	data-bundled_item_id="<?php echo esc_attr( $bundled_item->get_id() ); ?>"
	data-custom_data="<?php echo wc_esc_json( wp_json_encode( $custom_product_data ) ); ?>"
	data-product_id="<?php echo esc_attr( $bundled_product_id ); ?>"
	data-bundle_id="<?php echo esc_attr( $bundle_id ); ?>" <?php echo $bundled_item->is_optional() && ! $bundled_item->is_optional_checked() ? 'style="display:none;"' : ''; ?>>
	<table class="variations" cellspacing="0">
		<tbody>
		<?php

		foreach ( $bundled_product_attributes as $attribute_name => $options ) {

			$is_attribute_value_configurable = $bundled_item->display_product_variation_attribute_dropdown( $attribute_name );

			?><tr class="attribute_options <?php echo $is_attribute_value_configurable ? 'attribute_value_configurable' : 'attribute_value_static' ; ?>" data-attribute_label="<?php echo esc_attr( wc_attribute_label( $attribute_name ) ); ?>">
				<td class="label">
					<label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ) . '_' . $bundled_item->get_id(); ?>">
					<?php

						echo wc_attribute_label( $attribute_name );

						if ( $is_attribute_value_configurable ) {
							?> <abbr class="required" title="<?php esc_attr_e( 'Required option', 'woocommerce-product-bundles' ); ?>">*</abbr><?php
						}

					?>
					<?php
						if ( 'pa_size' == $attribute_name && class_exists('productsize_chart_Public') ) :
							$chart = new productsize_chart_Public('productsize-chart-for-woocommerce', 123);
							$chart_id=$chart->productsize_chart_id($bundled_product_id);
							if ($chart_id) : ?>
								<div class="info_show_wrap about_size_wraper">
									<div class="bodyshape_info size_info">
										<span class="cta about_size pop-up-button" style="display: none;"><?php esc_html_e('About Size', 'zoa')?></span>
										<span bundle_product_id="<?php echo $bundled_product_id; ?>" class="cta about_size pop-up-button-remodal-bundle"><?php esc_html_e('About Size', 'zoa')?></span>
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
												<div class="prod-detail-content" bundle_product_id="<?php echo $bundled_product_id; ?>" id="size_chart_content_popup">
													<?php // $chart->productsize_chart_new_product_tab_content();?>
												</div>
											</div>
										</div>
										<!--/.row-->
									</div>
									<!--/.pop-up-->
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</label>
				</td>
				<td class="value"><?php

					echo wc_pb_template_bundled_variation_attribute_options( array(
						'options'      => $options,
						'attribute'    => $attribute_name,
						'bundled_item' => $bundled_item
					) );

				?></td>
			</tr><?php
		}

		?>
		</tbody>
	</table>
	<?php

	/**
	 * 'woocommerce_bundled_product_add_to_cart' hook.
	 *
	 * Used to output content normally hooked to 'woocommerce_before_add_to_cart_button'.
	 *
	 * @param  int              $bundled_product_id
	 * @param  WC_Bundled_Item  $bundled_item
	 */
	do_action( 'woocommerce_bundled_product_add_to_cart', $bundled_product_id, $bundled_item );

	?>
	<div class="single_variation_wrap bundled_item_wrap">
		<?php

		/**
		 * 'woocommerce_bundled_single_variation' hook.
		 *
		 * Used to output variation data.
		 *
		 * @since  4.12.0
		 *
		 * @param  int              $bundled_product_id
		 * @param  WC_Bundled_Item  $bundled_item
		 *
		 * @hooked wc_bundles_single_variation          - 10
		 * @hooked wc_bundles_single_variation_template - 20
		 */
		do_action( 'woocommerce_bundled_single_variation', $bundled_product_id, $bundled_item );

	?>
	</div>
</div>
