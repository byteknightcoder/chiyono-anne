<?php

add_action('wp_head','remove_action_fn');
function remove_action_fn() {
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	remove_action( 'woocommerce_before_single_product_summary', 'zoa_product_gallery', 20 );	
}
/*PRODUCT IMAGE*/

add_action( 'woocommerce_before_single_product_summary', 'zoa_product_gallery_custom', 20 );
function zoa_product_gallery_custom() {

	/*PRODUCT ATTRIBUTE*/
	global $product;
	$product_id       = $product->get_id();
	$image_id         = $product->get_image_id();
	$image_alt        = zoa_img_alt( $image_id, esc_attr__( 'Product image', 'zoa' ) );

	if ( $image_id ) {
		$image_small_src    = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		$image_medium_src   = wp_get_attachment_image_src( $image_id, 'woocommerce_single' );
		$image_full_src     = wp_get_attachment_image_src( $image_id, 'full' );
	} else {
		$image_small_src[0] = $image_medium_src[0] = $image_full_src[0] = wc_placeholder_img_src();
	}

	$gallery_id       = $product->get_gallery_image_ids();
	$class_name       = 'pro-single-image';
	$video_url        = '';

	/*LAYOUT*/
	$layout = get_theme_mod( 'shop_gallery_layout', 'vertical' );
	
	$columns = absint( get_option( 'woo_variation_gallery_thumbnails_columns', apply_filters( 'woo_variation_gallery_default_thumbnails_columns', 4 ) ) );
	
	$post_thumbnail_id = $product->get_image_id();
	
	$default_sizes  = wp_get_attachment_image_src( $post_thumbnail_id, 'woocommerce_single' );
	$default_height = $default_sizes[ 2 ];
	$default_width  = $default_sizes[ 1 ];
	
	$attachment_ids = $product->get_gallery_image_ids();
	
	$has_gallery_thumbnail = ( has_post_thumbnail() && ( count( $attachment_ids ) > 0 ) );
	
	$gallery_slider_js_options = apply_filters( 'woo_variation_gallery_slider_js_options', array(
		'slidesToShow'   => 1,
		'slidesToScroll' => 1,
		'arrows'         => false,
		'adaptiveHeight' => true,
		// 'lazyLoad'       => 'progressive',
	) );
	
	$thumbnail_slider_js_options = apply_filters( 'woo_variation_gallery_thumbnail_slider_js_options', array(
		'slidesToShow'   => $columns,
		'slidesToScroll' => $columns,
		'focusOnSelect'  => true,
		'arrows'         => false,
		'asNavFor'       => '.woo-variation-gallery-slider',
		'centerMode'     => true,
		'infinite'       => true,
		'centerPadding'  => '0px'
	) );
	
	$gallery_thumbnail_position = get_option( 'woo_variation_gallery_thumbnail_position', 'bottom' );
	
	// Reset Position
	if ( ! woo_variation_gallery()->is_pro_active() ) {
		$gallery_thumbnail_position = 'bottom';
	}
	
	$gallery_width = absint( get_option( 'woo_variation_gallery_width', apply_filters( 'woo_variation_gallery_default_width', 30 ) ) );
	
	$inline_style = apply_filters( 'woo_variation_product_gallery_inline_style', array(// 'max-width' => esc_attr( $gallery_width ) . '%'
	) );
	
	$wrapper_classes = apply_filters( 'woo_variation_gallery_product_image_classes', array(
		'woo-variation-product-gallery',
		'woo-variation-product-gallery-thumbnail-columns-' . absint( $columns ),
		$has_gallery_thumbnail ? 'woo-variation-gallery-has-product-thumbnail' : ''
	) );

	
	/*PRODUCT OPTION*/
	if ( function_exists( 'FW' ) && function_exists( 'fw_get_db_post_option' ) ) {
		$video_url = fw_get_db_post_option( $product_id, 'video' );
		$p_layout  = fw_get_db_post_option( $product_id, 'layout' );
		if ( isset( $p_layout ) && 'default' != $p_layout ){
			$layout = $p_layout;
		}

		wp_enqueue_style( 'lity-style' );
		wp_enqueue_script( 'lity-script' );
	}

	/*SLIDER FOR `vertical` AND `horizontal` LAYOUT*/
	if ( ! empty( $gallery_id ) && ( 'vertical' == $layout || 'horizontal' == $layout ) ) {

		$class_name = '';
		$mode       = 'vertical';
		$gutter     = 0;
		$fixedWidth = 0;

		if ( 'horizontal' == $layout ) {
			$mode       = 'horizontal';
			$gutter     = 10;
			$fixedWidth = 80;
		}
		
		wp_add_inline_script(
				'tiny-slider',
				"
				jQuery(document).ready(function($){
					/* $('.wvg-gallery-image').zoom({
						touch:false
					}); */
				});
				",
				'after'
		);
		/*
		jQuery('#gallery-image').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: true,
			fade: true,
			asNavFor: '#gallery-thumb',
			dots: true,
			infinite: true,
		});
		jQuery('#gallery-thumb').slick({
			slidesToShow: 5,
			slidesToScroll: 1,
			asNavFor: '#gallery-image',
			dots: false,
			arrows: false,
			infinite: true,
			centerMode: true,
			focusOnSelect: true,
			vertical: true,
			verticalSwiping: true,
			centerMode: true,
			centerPadding: '15px'
		});
		*/
	}

	/*STICKY PRODUCT SUMMARY FOR `list` AND `grid` LAYOUT*/
	if ( ! empty( $gallery_id ) && ( 'list' == $layout || 'grid' == $layout ) ) {
		$class_name = '';

		wp_enqueue_script( 'sticky-sidebar' );
		wp_add_inline_script(
			'sticky-sidebar',
			"document.addEventListener( 'DOMContentLoaded', function() {
				var window_width = window.innerWidth;

				function sticky_summary() {
					if ( window_width < 992 ) {
						jQuery( '.summary.entry-summary' ).trigger( 'sticky_kit:detach' );
					} else {
						jQuery( '.summary.entry-summary' ).stick_in_parent({
							parent: '.shop-content > .product',
							offset_top: 0
						});
					}
				}

				window.addEventListener( 'load', function() {
					sticky_summary();
				});

				window.addEventListener( 'resize', function() {
					sticky_summary();
				});

			} );",
			'after'
		);
	}

	?>
	<div class="single-product-gallery woo-variation-product-gallery woo-variation-gallery-wrapper <?php echo esc_attr( $class_name ); ?>">
		<?php /*MAIN CAROUSEL*/ ?>
		<div class="pro-carousel-image">
				<?php if ( has_post_thumbnail() && ( 'yes' === get_option( 'woo_variation_gallery_lightbox', 'yes' ) ) ) : ?>
					<a href="#" class="woo-variation-gallery-trigger woo-variation-gallery-trigger-position-<?php echo get_option( 'woo_variation_gallery_zoom_position', 'top-right' ) ?>">
						<span class="dashicons dashicons-search"></span>
					</a>
				<?php endif; ?>
			<div id="gallery-image" class="woo-variation-gallery-slider" data-slick='<?php echo htmlspecialchars( wp_json_encode( $gallery_slider_js_options ) ); // WPCS: XSS ok. ?>'>
				<?php
					// Main  Image
					//$default_image_loaded = false;
					if ( has_post_thumbnail() ) {
						echo wvg_get_gallery_image_html( $post_thumbnail_id, true );
					} else {
						echo '<div class="wvg-gallery-image wvg-gallery-image-placeholder">';
						echo sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
						echo '</div>';
					}
					
					// Gallery Image
					if ( $has_gallery_thumbnail ) {
						foreach ( $attachment_ids as $attachment_id ) {
							echo wvg_get_gallery_image_html( $attachment_id, true );
						}
					}
				?>
			</div>
		</div>

		<?php /*THUMB CAROUSEL*/ ?>
		<?php if( ! empty( $gallery_id ) && ( 'vertical' == $layout || 'horizontal' == $layout ) ) : ?>
			<div class="pro-carousel-thumb">
				<div id="gallery-thumb" class="woo-variation-gallery-thumbnail-slider"  data-slick='<?php echo htmlspecialchars( wp_json_encode( $thumbnail_slider_js_options ) ); // WPCS: XSS ok. ?>'>
					
					<?php
						if ( $has_gallery_thumbnail ) {
							// Main Image
							echo wvg_get_gallery_image_html( $post_thumbnail_id );
						}
						// Gallery Image
						foreach ( $attachment_ids as $key => $attachment_id ) {
							echo wvg_get_gallery_image_html( $attachment_id, false, $key );
						}
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php if( ! empty( $video_url ) ) : ?>
			<a class="video-popup-btn zoa-icon-play" data-lity href="<?php echo esc_url( $video_url ); ?>"><?php esc_html_e( 'Video', 'zoa' ); ?></a>
		<?php endif; ?>

		<?php /* PRODUCT LABEL SINGLE PRODUCT */ ?>
		<?php echo zoa_product_label( $product ); ?>
	</div>
<?php
}