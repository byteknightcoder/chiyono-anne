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
		the_content();
		zoa_wp_link_pages(); /*break page*/
	?>
</div>
