<?php
	acf_form_head();
	get_header(); 
	
	if (isset($_GET['pid'])) {
		$_SESSION['pid']=$_GET['pid'];	

		if (isset($_REQUEST['serie_index']) && $_REQUEST['serie_index']) {
			$images_series = get_field('images_series', $_GET['pid']);
			foreach ($images_series as $loop_series_index => $images_serie){
				if ($_REQUEST['serie_index'] == $loop_series_index + 1) {
					$pt_categorized_images = $images_serie['images'];
					foreach ($pt_categorized_images as $pt_categorized_image) {
						$image = $pt_categorized_image['sizes']['woocommerce_thumbnail'];
						$_SESSION['image_id'] = $pt_categorized_image['ID'];	
					}
					break;
				}
			}
		} else {
			$image=get_the_post_thumbnail_url($_GET['pid']);
		}
		if ( '' == $image ) {
			$image = get_stylesheet_directory_uri() . '/images/pf_sample_thum.jpg';
		}
		$_SESSION['p_image']=$image;	
	} else {
		unset($_SESSION['pid']);
		unset($_SESSION['p_image']);
		unset($_SESSION['image_id']);
	}
?>

<main id="main" class="page-content page-reservation">
    <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
			?>
				<div class="max-width--site gutter-padding">
					<?php get_template_part( 'template-parts/content', 'reservation' ); ?>
				</div>
			<?php
           endwhile;
        else:
        ?>
            <div class="container">
                <?php get_template_part( 'template-parts/content', 'none' ); ?>
            </div>
        <?php endif; ?>
</main>

<?php get_footer(); ?>
