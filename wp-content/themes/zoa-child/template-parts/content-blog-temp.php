<?php
$c_header = zoa_page_header_slug();
$categories = get_the_category();
$fvimg = get_field('post_fv');
$fv_type= get_field('fv_type');
?>
<div class="blog-article blog_newstyle01 styled_post" <?php zoa_schema_markup( 'blog' ); ?>>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php zoa_schema_markup( 'blog_list' ); ?>>
		<div itemprop="mainEntityOfPage">
			<div class="blog-article-sum">
				<!--load fv template here-->
				<?php 
					if (isset($fv_type)&&$fv_type=='full width'){
						get_template_part( 'template-parts/fv', 'wide' ); 
					} else {
						get_template_part( 'template-parts/fv', 'fixed' ); 
					}
				?>
				<!--load fv template here-->
				<div class="blog__content container">
					<div class="entry-content <?php if (!has_post_thumbnail() ) { ?>align_center<?php } ?>" <?php zoa_schema_markup( 'post_content' ); ?>>
						<?php
							if ( is_single() ) {
								the_content();
							}
						?>
					</div>
					<footer class="entry-footer">
						<!--center title-->
							<h3 class="section-title-centre"><?php esc_html_e('Recent Posts', 'zoa'); ?></h3>
							<!--/center title-->
						<div class="bsec__wrap"><?php echo show_latest_blog_posts(2); ?></div>
					</footer>
				</div><!--/container-->
			</div>
		</div>
	</article>
</div>