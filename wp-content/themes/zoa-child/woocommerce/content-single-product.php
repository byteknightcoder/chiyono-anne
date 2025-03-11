<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;
global $product;
/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
<div class="shop--single row">
	<?php $current_time = current_time('timestamp'); $schedule_start=strtotime(get_field("badge_schedule_start"));$schedule_end=strtotime(get_field("badge_schedule_end")); if(get_field( "badge_show" ) && $current_time>=$schedule_start && $current_time<=$schedule_end) : ?>
		<div id="new-badge-product-single" style="display:none; align-items:center; justify-content:center; position:absolute; top:0; left:0; z-index:100; background:<?php $value = get_field( "badge_background_color" ); echo $value ?>; color:<?php $value = get_field( "badge_text_color" ); echo $value ?>; font-family: 'Open Sans', sans-serif;font-size:13px; height:40px; padding:0 10px;">
			<?php $value = get_field( "badge_text" ); echo $value ?>
		</div>
	<?php endif; ?>
	<?php
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>
	<div class="summary entry-summary">
            <?php
            $p_id= get_the_ID();
            $pbdate=get_the_date('Y-m-d H:i:s', $p_id);
                            $date_allow = date_i18n('Y-m-d 00:00:00', strtotime('+7 days', strtotime($pbdate)));
                            $current_time = current_time('timestamp');
                            $bm_meta = get_post_meta( $p_id, '_yith_wcbm_product_meta', true);
                            if(strtotime($date_allow)>$current_time&& (!isset($bm_meta[ 'id_badge' ])||empty($bm_meta[ 'id_badge' ]))&& !fsl_product_in_cat($p_id, 'familysale')){
                               ?>
                            <div class="container-image-and-badge-new ch_bad_single"><div class="yith-wcbm-badge yith-wcbm-badge-37845 yith-wcbm-badge-37157 yith-wcbm-badge--on-product-36718 yith-wcbm-badge--anchor-point-top-left yith-wcbm-badge-custom" data-position="{&quot;top&quot;:0,&quot;bottom&quot;:&quot;auto&quot;,&quot;left&quot;:0,&quot;right&quot;:&quot;auto&quot;}"><div class="yith-wcbm-badge__wrap"><div class="yith-wcbm-badge-text">NEW</div></div></div></div>
            <?php
                            }
            ?>
		<?php
			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 */
			do_action( 'woocommerce_single_product_summary' );
		?>
	</div>
</div>
<hr class="separate-hr">
	<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
