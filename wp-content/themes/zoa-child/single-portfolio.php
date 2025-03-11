<?php
	$is_ajax = (defined('DOING_AJAX') && DOING_AJAX);
	$serie_index_request = isset($_REQUEST['image_serie_index']) ? $_REQUEST['image_serie_index'] : 0;
	if (!isset($is_ajax)) {
		get_header();
		$sidebar = get_theme_mod('blog_sidebar', 'right');
	}
	if (!isset($is_ajax)) { ?>
		<div class="container">
	<?php
	}
?>
<?php
	$page_reservation = get_page_by_path('reservation-form');
	if (have_posts() || isset($post)) :
		$terms = get_the_terms($post, 'portfolio_category');
		$parent_cat = $terms[0];
		$child_cat = isset($terms[1]) ? $terms[1] : $terms[0];
		$parent_catname = $parent_cat->name;
		$catname = $child_cat->name;
		$thecontent = get_the_content();
		if (!isset($_REQUEST['image_serie_index']) || !$_REQUEST['image_serie_index']) {
		?>
			<div class="flex-justify-between row test">
				<div class="col-lg-6 col-sm-12 col-12 portfolio_sideimg">
					<div class="slick-gallery">
					<?php
						if (has_post_thumbnail()) {
							the_post_thumbnail('full');
						} else {

							echo '<img src="' . get_stylesheet_directory_uri() . '/images/pf_sample_thum.jpg" alt="sample" />';
						}
						$images = get_field('gallery');
						$size = 'full';
						if ($images) :
					?>
							<?php foreach ($images as $image) : ?>
								<?php echo wp_get_attachment_image($image['ID'], $size); ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-lg-6 col-sm-12 col-12 portfolio_sideinfo">
					<div class="head_row display--small-up">
						<h3 class="entry-title"><?php the_title(); ?></h3>
						<span class="pf_cat"><?php echo $catname; ?></span>
					</div>
					<div class="body_row">
						<div class="body_sec">
							<?php if (!empty($thecontent) || !$parent_cat->description || $child_cat->description) : ?>
								<div class="body_title heading heading--small"><span><?php esc_html_e('Fabrics', 'zoa') ?></span></div>
							<?php endif; ?>
							<?php if (!empty($thecontent)) : ?>
								<div class="description"><?php echo $thecontent; ?></div>
							<?php endif; ?>
							<div class="description colleciton-desc"><?php echo $parent_cat->description; ?></div>
							<div class="description series-desc"><?php echo $child_cat->description; ?></div>
						</div>
					</div>
					<div class="foot_row">
						<div class="titile_row display--small-only">
							<h3 class="entry-title"><?php the_title(); ?></h3>
							<span class="pf_cat"><?php echo $catname; ?></span>
						</div>
						<?php
							$galllery = get_post_meta($post->ID, 'gallery', true);
							if ( '' != $galllery ) :
							?>
								<div class="button_row"><a class="btn btn-primary" href="<?php echo get_permalink($page_reservation->ID); ?>?pid=<?php echo $post->ID; ?>"><?php esc_html_e('Order Now', 'zoa') ?><?php echo $page_reservation->ID; ?></a></div>
							<?php else :
								$product = get_post_meta($post->ID, 'product', true);
							?>
								<div class="button_row"><a class="btn btn-primary" href="<?php echo get_permalink($product); ?>"><?php esc_html_e('Buy Now', 'zoa') ?></a></div>	
							<?php endif; ?>
					</div>
				</div>
			</div>
			<?php
			} else {
				$images_series = get_field('images_series', $post->ID);
				$size = 'full';
			?>
			<div class="flex-justify-between row test2">
				<div class="col-lg-6 col-sm-12 col-12 portfolio_sideimg">
					<div class="slick-gallery">
					<?php
						// image series:
						foreach ($images_series as $loop_series_index => $images_serie) :
							if ($serie_index_request == $loop_series_index + 1) :
								$pt_categorized_images = $images_serie['images'];
								foreach ($pt_categorized_images as $pt_categorized_image) : ?>
									<div class="item"><img src="<?php echo $pt_categorized_image['url'] ?>" class="attachment-portfolio size-portfolio wp-post-image" alt=""></div>
									<!--<img width="<?php //echo $pt_categorized_image['sizes']['shop_single-width'] ?>" height="<?php //echo $pt_categorized_image['sizes']['shop_single-height']?>" src="<?php //echo $pt_categorized_image['sizes']['shop_single']?>" class="attachment-portfolio size-portfolio wp-post-image" alt="">-->
								<?php
								endforeach;
								break;
							endif;
						endforeach;
					?>
					</div>
				</div>
				<div class="col-lg-6 col-sm-12 col-12 portfolio_sideinfo">
					<div class="head_row display--small-up">
						<?php
							foreach ($images_series as $loop_series_index => $images_serie) :
								if ($serie_index_request == $loop_series_index + 1) :
									if (!empty($images_serie['info'])) : ?>
										<h3 class="entry-title"><?php echo $images_serie['info']['title']; ?></h3>
									<?php endif;
									break;
								endif;
							endforeach;
						?>
						<span class="pf_cat"><?php the_title(); ?><?php if (!empty($parent_catname)) { ?> from <?php echo $parent_catname; ?><?php } ?></span>
					</div>

					<div class="body_row">
						<?php
							// get description from product
							foreach ($images_series as $loop_series_index => $images_serie) :
								if ($serie_index_request == $loop_series_index + 1) :
									if (!empty($images_serie['product'])) :
										foreach ($images_serie['product'] as $product) :
											$product_desc = $product->post_excerpt;
											if (!empty($product_desc)) : ?>
												<div class="body_sec">
													<div class="body_title heading heading--small"><span><?php esc_html_e('Details', 'zoa') ?></span></div>
													<div class="descripion"><?php echo $product_desc; ?></div>
												</div>
												<?php
											endif;
										endforeach;
									endif;
									break;
								endif;
							endforeach;
						?>
						<?php
							foreach ($images_series as $loop_series_index => $images_serie) :
								if ($serie_index_request == $loop_series_index + 1) :
									if (!empty($images_serie['product'])) {
										$product_fabric = get_field('fabric', $images_serie['product'][0]->ID);
									}

									if (isset($product_fabric)) : ?>
										<div class="body_sec">
											<div class="body_title heading heading--small"><span><?php _e('Fabrics', 'zoa') ?></span></div>
											<div class="fabric_content"><?php echo $product_fabric; ?></div>
										</div>
									<?php elseif (!empty($images_serie['info']) && isset($images_serie['info']['fabric']) && !empty($images_serie['info']['fabric'])) : ?>
										<div class="body_sec">
											<div class="body_title heading heading--small"><span><?php _e('Fabrics', 'zoa') ?></span></div>
												<?php foreach ($images_serie['info']['fabric'] as $fabric) : ?>
													<ul class="fabric_list">
														<?php if ($fabric['info']['parts']) : ?>
															<li class="fab_pos"><?php echo $fabric['info']['parts']; ?></li>
														<?php endif; ?>
														<?php if ($fabric['info']['list']) : ?>
															<?php foreach ($fabric['info']['list'] as $fabric_item) : ?>
																<li><?php echo $fabric_item['name']; ?></li>
															<?php endforeach; ?>
														<?php endif; ?>
													</ul>
												<?php endforeach; ?>
											</div><!--end if fabric-->
									<?php endif;
									break;
								endif;
							endforeach;
						?>
					</div>
					<div class="foot_row">
						<div class="titile_row display--small-only">
							<?php
								foreach ($images_series as $loop_series_index => $images_serie) {
									if ($serie_index_request == $loop_series_index + 1) {
										if (!empty($images_serie['info'])) : ?>
											<h3 class="entry-title"><?php echo $images_serie['info']['title']; ?></h3>
										<?php endif;
										break;
									}
								}
							?>
							<span class="pf_cat"><?php the_title(); ?><?php if (!empty($parent_catname)) { ?> from <?php echo $parent_catname; ?><?php } ?></span>
						</div>
							<?php
								foreach ($images_series as $loop_series_index => $images_serie) :
									if ($serie_index_request == $loop_series_index + 1) :
										if (!empty($images_serie['product'])) : ?>
											<?php foreach ($images_serie['product'] as $product) : ?>
												<div class="button_row"><a class="btn btn-primary" href="<?php echo get_permalink($product); ?>"><?php _e('Buy Now', 'zoa') ?></a></div>
											<?php endforeach; ?>
										<?php else : ?>
											<div class="button_row"><a class="btn btn-primary" href="<?php echo get_permalink($page_reservation->ID); ?>?pid=<?php echo $post->ID; ?>&serie_index=<?php echo $serie_index_request ?>"><?php esc_html_e('Order Now', 'zoa') ?></a></div>
										<?php endif;
										break;
									endif;
								endforeach;
							?>
					</div>
				</div>
			</div>
			<?php
				}
			endif;
			if (!isset($is_ajax)) { ?>
			</div>
			<?php
				}
			?>
			<?php
				if (!isset($is_ajax)) {
					get_footer();
				}
			?>
