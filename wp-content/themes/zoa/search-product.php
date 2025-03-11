<?php
/**
 * Only search product
 *
 * @package zoa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// sidebar.
$sidebar = is_active_sidebar( 'shop-widget' ) ? get_theme_mod( 'shop_sidebar', 'full' ) : 'full';

/*query*/
if ( get_query_var( 'paged' ) ) {
	$paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
	$paged = get_query_var( 'page' );
} else {
	$paged = 1;
}

$ppp = (int) get_theme_mod( 'c_shop_ppp', 12 );
$key = isset( $_GET['s'] ) ? sanitize_key( $_GET['s'] ) : 'product';

$args = array(
	'post_type'           => 'product',
	's'                   => $key,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => 1,
	'paged'               => $paged,
	'posts_per_page'      => $ppp,
);

$search_query = new WP_Query( $args );

get_header(); ?>

<div class="container">
	<div class="row">
		<?php
		switch ( $sidebar ) :
			// sidebar left.
			case 'left':
				?>
			<div class="col-md-3">
				<?php get_sidebar( 'shop' ); ?>
			</div>

			<main id="main" class="col-md-9 col-lg-9">
				<?php
				if ( $search_query->have_posts() ) :
					echo '<ul class="products">';
					while ( $search_query->have_posts() ) :
						$search_query->the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
					echo '</ul>';

					zoa_paging( $search_query );
				else :
					do_action( 'woocommerce_no_products_found' );
				endif;
				?>
			</main>
				<?php
				break;
			// sidebar right.
			case 'right':
				?>
			<main id="main" class="col-md-9 col-lg-9">
				<?php
				if ( $search_query->have_posts() ) :
					echo '<ul class="products">';
					while ( $search_query->have_posts() ) :
						$search_query->the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
					echo '</ul>';

					zoa_paging( $search_query );
				else :
					do_action( 'woocommerce_no_products_found' );
				endif;
				?>
			</main>

			<div class="col-md-3">
				<?php get_sidebar( 'shop' ); ?>
			</div>
				<?php
				break;
			// no sidebar.
			case 'full':
				?>
			<main id="main" class="col-md-12 col-lg-12">
				<?php
				if ( $search_query->have_posts() ) :
					echo '<ul class="products">';
					while ( $search_query->have_posts() ) :
						$search_query->the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
					echo '</ul>';

					zoa_paging( $search_query );
				else :
					do_action( 'woocommerce_no_products_found' );
				endif;

				wp_reset_postdata();
				?>
			</main>
				<?php
				break;
			endswitch;
		?>
	</div>
</div>

<?php
get_footer();
