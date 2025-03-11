<div class="conatiner">
	<div class="blog-article normal_post">
	<!--<a href="<?php echo get_permalink($post->ID); ?>" class="" data-id="<?php echo $post->ID; ?>'"></a>-->
	<div class="blog-article-sum">
	<div class="row flex-justify-center">
	<?php if (has_post_thumbnail($post) ) {
	                 echo '<div class="col-md-6 col-xs-12 vertical--align-center">';
	                 echo '<div class="media-content-center-wide">';
	                 echo get_the_post_thumbnail($post, 'full');
	                 echo '</div>';
					 echo '</div>';
     } ?>
		<div class="<?php if (has_post_thumbnail() ) { ?>col-md-6<?php } ?> col-xs-12">
			<div class="blog-article-header <?php if (!has_post_thumbnail($post) ) { ?>align_center<?php } ?>">
				<span class="entry-meta meta-cat"><?php the_category('','', $post->ID); ?></span>
				<header class="post-entry-header"><h1 class="entry-title blog-title"><?php echo $post->post_title; ?></h1></header>
				<div class="post-date-header"><?php echo get_post_time('M d, Y'); ?></div>
			</div>
			<div class="entry-content <?php if (!has_post_thumbnail($post) ) { ?>align_center<?php } ?>">
			<p><?php echo apply_filters( 'the_content', $post->post_content ); ?></p>
			</div>	
	    </div>
	</div>
	</div>
	</div>
</div>