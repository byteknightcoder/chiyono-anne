<?php 
	/* Template Name: Thank you page */
	get_header(); 
?>
<main id="main" class="page-content page-short_content">
	<?php
		if ( have_posts() ) :
			echo '<div class="flex-justify-center center row"><div class="col-lg-8 col-md-10 col-12">';
			while ( have_posts() ) :
				the_post();
				/*page without Elementor*/
				get_template_part( 'template-parts/content', 'thanks' );

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			echo '</div></div>';
		else : ?>
			<div class="container">
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			</div>
		<?php endif; ?>
</main>

<?php get_footer();
