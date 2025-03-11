<?php 
	get_header(); 
	global $post, $wp;

	// Explode the request URL into parts
	$request = explode('/', $wp->request);
	$post_data = get_post($post->post_parent);
	$post_slug = $post->post_name;
	$ancestors = get_post_ancestors($post->ID);
	$parents_id = end($ancestors);
	$parent_slug = get_post($parents_id)->post_name;

	// Check if the request has at least 2 elements
	$prev_slug = isset($request[1]) ? $request[1] : '';
	$lastslug = end($request);

	// Determine the page class based on the number of elements in the request
	if (count($request) >= 2) {
		$pageClass = 'child-' . $parent_slug;
	} else {
		$pageClass = 'parent-' . $parent_slug;
	}
	$is_sub_myaccount = count($request) > 1 && $request[0] == 'my-account';
?>
<main id="main" class="page-content <?php echo 'page-'.$post_slug.' '.$pageClass.' page_'.$parent_slug.'--'.$prev_slug.' page_'.$parent_slug.'--'.$prev_slug.'--'.$lastslug; ?>">
	<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
					if (is_page('bespoke')) {
						get_template_part( 'template-parts/content', 'bespoke' );
					} elseif ($post_data->post_name == 'bespoke') {
						get_template_part( 'template-parts/content', 'bespoke_child' );
					} elseif (is_page('reservation-form')) {
						if (empty($post_data->post_password)&& post_password_required() ){
							$post   = get_post( $post );
							$label  = 'pwbox-' . ( empty( get_the_ID() ) ? rand() : get_the_ID() );
							$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
								<p>' . __( '現在は先行予約者様のみの予約期間となります。一般ご予約は6月1日(月)からとさせていただきます。', 'zoa' ) . '</p>
								<p><label for="' . $label . '">' . __( 'Password:', 'zoa' ) . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form', 'zoa' ) . '" /></p></form>
								';
							echo $output;
						} else {
							get_template_part( 'template-parts/content', 'booked' );
						}
					} else {
						if ( zoa_is_elementor() && zoa_elementor_page( get_the_ID() ) ){
							/*page build with Elementor*/
							get_template_part( 'template-parts/content', 'page' );
						} else {
						/*page without Elementor*/
					?>
						<div class="<?php if( is_cart() || is_checkout() ){ ?>max-width--site gutter-padding--full<?php } elseif(is_account_page()) { ?><?php if($is_sub_myaccount) {?>max-width--site<?php }else{ ?>max-width--large myaccount-dashboard<?php } ?> gutter-padding<?php }else{ ?>container<?php } ?>">
						<?php
							get_template_part( 'template-parts/content', 'page' );
							if ( comments_open() || get_comments_number() ) {
								comments_template();
							}
						?>
					</div>
				<?php
					} // end bespoke else
				}
		   endwhile;
		else:
		?>
			<div class="container">
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			</div>
		<?php endif; ?>
</main>

<?php get_footer();
