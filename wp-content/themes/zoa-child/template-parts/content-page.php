<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! zoa_is_elementor() ) : ?>
		<header class="entry-header">
			<?php
			if ( is_single() ) {
				the_title( '<h1 class="entry-title blog-title">', '</h1>' );
			} else {
				the_title( '<h2 class="entry-title blog-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			}
			?>
		</header>
	<?php
		endif;
	if (is_page('reservation-form')) {
		echo '<div id="bookedForm" class="custom-steps form form--stepped">';
		the_content();
		get_template_part( 'template-parts/booked', 'confirm' );
		// echo'<div class="btn-group">
    //         <input type="button" class="btn btn--1 btn--prev js-prev" value="Previous" /> 
    //         <input type="button" class="btn btn--next js-next" value="Next" />
    //         <input type="submit" class="btn btn--2" value="Submit form" />
    //      </div>';
		echo '</div>';
	} else {
		the_content();
	}
	zoa_wp_link_pages(); /*break page*/
	?>
</div>
