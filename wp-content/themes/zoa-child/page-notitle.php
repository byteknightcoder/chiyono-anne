<?php 
/* Template Name: Hide Title Page */
	get_header(); 
	global $post, $wp;

	// Get the request path
	$request = explode('/', $wp->request);

	// Fetch post data and ancestors
	$post_data = get_post($post->post_parent);
	$post_slug = $post->post_name;
	$ancestors = get_post_ancestors($post->ID);
	$parents_id = end($ancestors);

	// Check if parent post exists before accessing its slug
	$parent_slug = $parents_id ? get_post($parents_id)->post_name : '';

	// Safely check if the request has at least two elements before accessing the second element
	$prev_slug = isset($request[1]) ? $request[1] : '';
	$lastslug = end($request);

	// Determine page class based on the request path
	if (count($request) >= 2) {
		$pageClass = 'child-' . $parent_slug;
	} else {
		$pageClass = 'parent-' . $parent_slug;
	}

	// Check if this is a subpage of "my-account"
	$is_sub_myaccount = count($request) > 1 && $request[0] == 'my-account';
?>
<main id="main"
  class="page-content <?php echo 'page-' . $post_slug . ' ' . $pageClass . ' page_' . $parent_slug . '--' . $prev_slug . ' page_' . $parent_slug . '--' . $prev_slug . '--' . $lastslug; ?>">
  <?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				if ( zoa_is_elementor() && zoa_elementor_page( get_the_ID() ) ) {
					/*page built with Elementor*/
					get_template_part( 'template-parts/content', 'page' );
				} else {
					/*page without Elementor*/
				?>
					<div
						class="<?php if ( is_cart() || is_checkout() ){ ?>max-width--site gutter-padding--full<?php } elseif (is_account_page()) { ?><?php if ($is_sub_myaccount) {?>max-width--site<?php } else { ?>max-width--large myaccount-dashboard<?php } ?> gutter-padding<?php } else { ?>container<?php } ?>">
						<?php get_template_part( 'template-parts/content', 'page' ); ?>
					</div>
				<?php
				} // End without Elementor
			endwhile;
		else:
		?>
  <div class="container">
	<?php get_template_part( 'template-parts/content', 'none' ); ?>
  </div>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
