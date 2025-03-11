<?php

if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

echo wp_kses_post( $before_widget );
echo wp_kses_post( $title );

$tax_query[] = array(
	'taxonomy' => 'product_visibility',
	'field'    => 'name',
	'terms'    => 'featured',
	'operator' => 'IN',
);

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'post_status'         => 'publish',
	'posts_per_page'      => $max,
	'orderby'             => $orderby,
	'order'               => $order,
	'tax_query'           => $tax_query,
);

$query = new WP_Query( $args );

if ( ! $query->have_posts() ) {
	return;
}

// Slick script and style.
wp_enqueue_style( 'slick' );
wp_enqueue_script( 'slick' );
?>

<div class="widget-featured-carousel-product" data-slider_per_row="<?php echo esc_attr( $number ); ?>">
	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		global $product;
		$rate  = wc_get_rating_html( $product->get_average_rating() );
		$price = $product->get_price_html();
		?>
		<div class="featured-carousel-product-item">
			<div class="fcp-image">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>" alt="<?php esc_attr_e( 'Product Image', 'zoa' ); ?>">
				</a>
			</div>
			<div class="fcp-content">
				<h2 class="fcp-title">
					<?php if ( function_exists('get_the_title_product_chiyono')) : ?>
						<?php echo esc_html( get_the_title_product_chiyono() ); ?>
					<?php else : ?>
						<?php echo esc_html( get_the_title() ); ?>
					<?php endif; ?>
				</h2>
				<span class="fcp-rate"><?php echo wp_kses_post( $rate ); ?></span>
				<span class="fcp-price price"><?php echo wp_kses_post( $price ); ?></span>
			</div>
		</div>
		<?php
	endwhile;

	wp_reset_postdata();
	?>
</div>

<?php
echo wp_kses_post( $after_widget );
