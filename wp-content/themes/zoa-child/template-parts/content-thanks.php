<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="short_content box_style">
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title blog-title">', '</h1>' ); ?>
		</header>
		<?php
		echo '<div class="content_box">';
		the_content();
		echo '</div>';
		zoa_wp_link_pages(); /*break page*/
	?>
	</div>
	
</div>
