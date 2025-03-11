<?php
if (function_exists('hfe_render_header')) :
    hfe_render_footer();
else:
?>
	<footer id="theme-footer">
		<?php zoa_footer(); ?>
	</footer>
<?php
endif;
	// close tag content container `</div>`
	zoa_after_content();
?>
<div class="site-hider"><div class="site-hider__logo"></div></div>
<div class="site-overlay"></div>
<?php
	// quick view markup
	if (class_exists('woocommerce')) {
		zoa_product_action();
	}
	if (get_theme_mod('ajax_search', false)) {
		zoa_ajax_search_form();
	} else {
		zoa_dialog_search_form();
	}
?>
<a href="#" class="scroll-to-top js-to-top">
  <i class="ion-chevron-up"></i>
</a>
</div><!-- #theme-container -->
<?php
	if (true === get_theme_mod('loading', false)) {
		echo '<span class="is-loading-effect"></span>';
	}
?>
<?php wp_footer(); ?>

<div class="remodal" data-remodal-id="portfolio_modal" id="portfolio_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
	<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
	<div class="remodal_wraper"></div>
	<br>
</div>

<div class="remodal" data-remodal-id="news_top_modal" id="news_top_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
	<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
	<div class="remodal_wraper"></div>
	<br>
</div>

<?php
    global $post;
if ( class_exists('productsize_chart_Public') && function_exists('is_product') && is_product() && $post ) {
    global $product;
    $chart_md = new productsize_chart_Public('productsize-chart-for-woocommerce', 123);
    $chart_id = $chart_md->productsize_chart_id($post->ID);
    if ($chart_id && is_object( $product ) && !$product->is_type( 'bundle' )) : ?>
		<div class="remodal" data-remodal-id="remodal_config_size_info" id="remodal_config_size_info" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
			<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
			<div class="remodal_wraper">
				<div class="pop-up tooltip-pop pop-size">
					<div class="pop-head">
						<h2 class="pop-title">
							<i class="oecicon oecicon-alert-circle-que"></i><?php esc_html_e("About Chiyono Anne's Size", 'zoa'); ?>
						</h2>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="prod-detail-content" id="size_chart_content_popup">
								<?php $chart_md->productsize_chart_new_product_tab_content(); ?>
								<?php if (is_product_in_cat($post->ID, 'bras')) : // only for bras categories ?>
									<div class="add-chart-1 show_jis_area">
										<h3 id="modal1Title">
											<a id="show_jis" class="bf_icon link_otbtn" href="javascript:void(0);"><i class="oecicon oecicon-measurement-2"></i><?php _e('JIS規格サイズ詳細はこちら', 'zoa'); ?></a>
										</h3>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div><!--/.row-->
				</div>
			</div>
			<br>
		</div>
		<div class="remodal" data-remodal-id="remodal_jis_area" id="remodal_jis_area" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
			<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
			<div class="remodal_wraper">
				<div class="pop-up tooltip-pop pop-size">
					<div class="pop-head">
						<h2 class="pop-title bf_icon">
							<i class="oecicon oecicon-measurement-2"></i><?php _e("JIS規格サイズ詳細はこちら", 'zoa'); ?>
						</h2>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="prod-detail-content jis_area_content" id="jis_area_content">
								<div class="table-wrap">
									<?php get_template_part('template-parts/static', 'table'); ?>
								</div>
								<div class="hint_table">
									<span class="hint_size size_01"><?php _e('サイズ1', 'zoa'); ?></span>
									<span class="hint_size size_02"><?php _e('サイズ2', 'zoa'); ?></span>
									<span class="hint_size size_03"><?php _e('サイズ3', 'zoa'); ?></span>
									<span class="hint_size size_04"><?php _e('サイズ4', 'zoa'); ?></span>
								</div>
								<div class="link_center">
									<div id="go_back"><span class="cta"><?php esc_html_e('Go back', 'zoa'); ?></span></div>
								</div>
							</div>
						</div>
					</div><!--/.row-->
				</div>
			</div>
			<br>
		</div>
<?php
	endif;

	// For Bundle product
	if(is_object( $product ) && $product->is_type( 'bundle' )) :
		$bundled_items = $product->get_bundled_items();
		if ( sizeof( $bundled_items ) ) :
			foreach ( $bundled_items as $bundled_item_id => $bundled_item ) :
				$bundled_product_id   = $bundled_item->get_product_id();
				$chart_md = new productsize_chart_Public('productsize-chart-for-woocommerce', 123);
				$chart_id = $chart_md->productsize_chart_id($bundled_product_id);
				if ($chart_id) : ?>
					<div class="remodal" size_char_bundle="<?php echo $bundled_product_id; ?>" data-remodal-id="remodal_config_size_info" id="remodal_config_size_info" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
						<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
						<div class="remodal_wraper">
							<div class="pop-up tooltip-pop pop-size">
								<div class="pop-head">
									<h2 class="pop-title">
										<i class="oecicon oecicon-alert-circle-que"></i><?php esc_html_e("About Chiyono Anne's Size", 'zoa'); ?>
									</h2>
								</div>
								<div class="row">
									<div class="col-12">
										<div class="prod-detail-content" id="size_chart_content_popup">
											<?php $chart_md->productsize_chart_new_product_tab_content($bundled_product_id); ?>
											<?php if (is_product_in_cat($bundled_product_id, 'bras')) : // only for bras categories ?>
												<div class="add-chart-1 show_jis_area">
													<h3 id="modal1Title">
														<a id="show_jis" show_jis_size_char_bundle="<?php echo $bundled_product_id; ?>" class="bf_icon link_otbtn" href="javascript:void(0);"><i class="oecicon oecicon-measurement-2"></i><?php _e('JIS規格サイズ詳細はこちら', 'zoa'); ?></a>
													</h3>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div><!--/.row-->
							</div>
						</div>
						<br>
					</div>
					<div class="remodal" show_jis_size_char_bundle="<?php echo $bundled_product_id; ?>" data-remodal-id="remodal_jis_area" id="remodal_jis_area" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
						<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
						<div class="remodal_wraper">
							<div class="pop-up tooltip-pop pop-size">
								<div class="pop-head">
									<h2 class="pop-title bf_icon">
										<i class="oecicon oecicon-measurement-2"></i><?php _e("JIS規格サイズ詳細はこちら", 'zoa'); ?>
									</h2>
								</div>
								<div class="row">
									<div class="col-12">
										<div class="prod-detail-content jis_area_content" id="jis_area_content">
											<div class="table-wrap">
												<?php get_template_part('template-parts/static', 'table'); ?>
											</div>
											<div class="hint_table">
												<span class="hint_size size_01"><?php _e('サイズ1', 'zoa'); ?></span>
												<span class="hint_size size_02"><?php _e('サイズ2', 'zoa'); ?></span>
												<span class="hint_size size_03"><?php _e('サイズ3', 'zoa'); ?></span>
												<span class="hint_size size_04"><?php _e('サイズ4', 'zoa'); ?></span>
											</div>
											<div class="link_center">
												<div id="go_back"><span class="cta"><?php esc_html_e('Go back', 'zoa'); ?></span></div>
											</div>
										</div>
									</div>
								</div><!--/.row-->
							</div>
						</div>
						<br>
					</div>
			<?php
				endif; // $chart_id if
			endforeach;
				endif; // sizeof if
	endif; //

	// <!--Bra Size-->
	$chart_id = 3300;
	$chart_md = new productsize_chart_Public('productsize-chart-for-woocommerce', $chart_id);
	$assets   = $chart_md->productsize_chart_assets( $chart_id );
	?>
		<div class="remodal size_info_modal" data-remodal-id="bra_size_info" id="bra_size_info" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
			<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
			<div class="remodal_wraper">
				<div class="pop-up tooltip-pop pop-size">
					<div class="pop-head">
						<h2 class="pop-title">
							<i class="oecicon oecicon-alert-circle-que"></i><?php esc_html_e("About Chiyono Anne's Size", 'zoa'); ?>
						</h2>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="prod-detail-content">
								<?php
									$title = !empty($title_wrapper) ? $title_wrapper : 'h2';
									$enable_additional_chart = 1;
									// $chart_md->default_assets['productsize-chart-enable-additional-chart'];
									// printf('<%1$s id="modal1Title">%2$s</%3$s>', $title, __($assets['label'],$chart_md->plugin_name), $title);
									$post_data = get_post($chart_id);  
									$pimg = get_post_meta($post_data->ID, 'primary-chart-image', true);
									if ($pimg) {
										$position = get_post_meta($post_data->ID, 'primary-image-position', true);
										$pimg = wp_get_attachment_image_src($pimg,'full'); 
										echo '<div class="chart-1-image image-' . $position . '">
											//<img src="' . $pimg[0] . '" alt="' . __($post->post_title, $chart_md->plugin_name) . '" title="' . ($title) . '" />
										</div>';
									}

									if ($post_data->post_content) {
										$content = apply_filters('the_content', $post_data->post_content); 
										echo $content;   
									}

									if ($assets['chart-table']) {
										$chart_md->productsize_chart_display_table($assets['chart-table']); 
									}

									// chart 1 content goes here 
									if($enable_additional_chart == 1) {
										// $title_additional=$chart_md->default_assets['productsize-chart-additional-title'];
										$title2 = !empty($title_additional) ? $title_additional : 'h3';

										if($assets['chart-1']) {
											$title_c1 = $assets['chart-1'][0]['chart-title'];
											$image_c1 = $assets['chart-1'][0]['chart-image'];
											$content_c1 = $assets['chart-1'][0]['chart-content'];
											$position_c1 = isset($assets['chart-1'][0]['image-position']) && $assets['chart-1'][0]['image-position'] == 'left' 
											? 'image-left' 
											: 'image-right';
											$chart_c1 = $assets['chart-1'][0]['chart-table'];

											echo '<div class="add-chart-1">';
											printf('<%1$s id="modal1Title">%2$s</%3$s>', $title2, $title_c1, $title2);

											if ($image_c1) {
												$img = wp_get_attachment_image_src($image_c1,'full'); 
												echo '<div class="chart-1-image ' . $position_c1 . '"><img src="' . $img[0] . '" alt="' . $title_c1 . '" 
													title="' . $title_c1 . '" />
												</div>';
											}

											if ($content_c1) {
												echo apply_filters('the_content',$content_c1);
											}

											if ($chart_c1) {
												$chart_md->productsize_chart_display_table($chart_c1); 
											}

											echo '</div><div class="clear"></div>';

										}

										if ($assets['chart-2']) {

											$title_c2 = $assets['chart-2'][0]['chart-title-1'];
											$image_c2 = $assets['chart-2'][0]['chart-image-1'];
											$content_c2 = $assets['chart-2'][0]['chart-content-1'];
											$position_c2 = isset($assets['chart-2'][0]['image-position-1']) && $assets['chart-2'][0]['image-position-1'] == 'left' 
												? 'image-left' 
												: 'image-right';

											$chart_c2 = $assets['chart-2'][0]['chart-table-1'];

											echo '<div class="add-chart-2">';
											if ($image_c2) {
												$img = wp_get_attachment_image_src($image_c2,'full'); 
												echo '<div class="chart-2-image ' . $position_c2 . '">
													<img src="' . $img[0] . '" alt="' . $title_c2 . ' ?>" title="' . $title_c2 . '" />
												</div>';
											}

											if ($content_c2) {
												echo apply_filters('the_content',$content_c2); 
											}

											if ($chart_c2) {
												$chart_md->productsize_chart_display_table($chart_c2); 
											}

											echo '</div>';

										}
									} 
								?>
							</div><!-- prod-detail-content -->
						</div><!-- col-12 -->
					</div><!--/.row-->
				</div><!-- pop-up -->
			</div><!-- remodal_wraper -->
			<br>
		</div><!-- size_info_modal -->
<?php } // productsize_chart_Public if ?>
<?php
	if (is_page('reservation-form')) {
		
		if ( isset($_REQUEST['secure']) && 'specialappointment' == $_REQUEST['secure'] ) {
			$term_cat = 'event-reservation-notify';
		} else {
			$term_cat = 'reserve-notify';
		}
		$posts = get_posts(array(
			'numberposts' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => $term_cat
				)
			)
		));
		if (!empty($posts)) {
			require_once dirname(__FILE__) . '/includes/Mobile_Detect.php';
			$detect = new Mobile_Detect;
			$adr = '';
			if ($detect->isAndroidOS() == true && $detect->isMobile() == true) {
				$adr = ' modal_foot_androi';
			}
			$btn_text = function_exists('get_field') ? get_field('button_text',$posts[0]->ID) : '';
			$btn_link = function_exists('get_field') ? get_field('button_link',$posts[0]->ID) : '';
		?>
			<div class="remodal remodal_hbody" data-remodal-id="reserve_notify" id="reserve_notify" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
				<div class="remodal_wraper">
					<?php if (!empty($btn_text) && !empty($btn_link)) : ?>
						<button data-remodal-action="close" class="remodal-close"></button>
					<?php endif; ?>
					<div class="modal_head"><?php echo $posts[0]->post_title; ?></div>
					<div class="modal_body">
						<?php
							echo wpautop($posts[0]->post_content);
							if (empty($btn_text)&&empty($btn_link)) : ?>
								<button data-remodal-action="close" class="remodal-close-button ja" aria-label="Close" style="margin-top:1rem;"><?php _e('確認しました', 'zoa'); ?></button>
							<?php else : ?>
								<button onclick="location.href = '<?php echo $btn_link; ?>';" class="remodal-close-button ja" aria-label="Close" style="margin-top:1rem;"><?php echo $btn_text; ?></button>
							<?php endif; ?>
					</div><!-- modal_body -->
				</div><!-- remodal_wraper -->
			</div><!-- remodal_hbody -->
		<?php
		}
	}
?>
<?php
	$special_service_check = get_option('ch_special_service', '');
	if (isset($post->ID) && is_product_in_cat_fit($post->ID, $special_service_check)) : // Ensure $post is defined before using
?>
		<div class="remodal remodal_hbody" data-remodal-id="read_term_of_use_try_fit_modal" id="read_term_of_use_try_fit_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
			<div class="remodal_wraper">
				<?php 
					$term_page = get_page_by_path( 'term-of-use-try-fit-your-size', OBJECT, 'page' ); // Use a different variable
					if ( $term_page ) {
						$heard = get_the_subtitle($term_page->ID) ? get_the_subtitle($term_page->ID) : $term_page->post_title;
						echo '<div class="modal_head">' . esc_html($heard) . '</div>';
						echo '<div class="modal_body">' . wp_kses_post($term_page->post_content) . '</div>';
						echo '<div class="modal_foot"><button data-remodal-action="close" id="ch_agree_term_try_fit" class="ch_agree_term_try_fit" aria-label="' . esc_attr__('I confirmed and agree','zoa') . '">' . esc_html__('I confirmed and agree','zoa') . '</button></div>';
					}
				?>
			</div><!-- remodal_wraper -->
		</div><!-- remodal_hbody -->
<?php endif; ?>
<?php
	if (is_page('event')) :
		$term_cat='event-modal';
		$posts = get_posts(array(
			'numberposts' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => $term_cat
				)
			)
		));
		if (!empty($posts)) :
	?>
			<div class="remodal remodal_hbody" data-remodal-id="event_modal" id="event_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
				<div class="remodal_wraper">
					<div class="modal_image">
						<img class="img" src="<?php echo wp_get_attachment_url( get_post_thumbnail_id($posts[0]->ID)); ?>" />
					</div>
					<div class="modal_head">
						<?php echo $posts[0]->post_title; ?>
					</div>
					<div class="modal_body">
						<?php echo wpautop($posts[0]->post_content); ?>
						<button data-remodal-action="close" class="remodal-close-button ja" aria-label="Close" style="margin-top:1rem;"><?php esc_html_e('SHOP NOW','zoa') ?></button>
					</div>
				</div><!-- remodal_wraper -->
			</div><!-- remodal_hbody -->
	<?php 
		endif;
	endif;


	$group_name = 'ms_definition';
	$base_page = get_page_by_path( 'about-repair' );
	$base_id = $base_page->ID;
	$def_rank = 'rank';
	$def_benefit = 'list';
	$def_repair = 'repair';
	$def_repairship = 'repair_shipfee';
?>

<?php if( have_rows($group_name, $base_id) ) : ?>
	<div data-remodal-id="repairModal" class="remodal_basic repair_summary">
		<div class="remodal_head">
			<h3 class="remodal_ttl ja"><?php _e('お直しについて', 'zoa'); ?></h3>
			<button data-remodal-action="close" class="remodal-close"></button>
		</div>
		<div class="remodal_body">
			<?php echo do_shortcode('[mrf_member_info_shortcode]'); ?>
		</div>
	</div>
<?php endif;?>

<?php if (is_single() && 'post' == get_post_type()) : ?>
	<div data-remodal-id="modal_img_single_post" class="remodal_basic repair_summary">
		<div class="remodal_body"></div>
	</div>
<?php endif; ?>

<?php include_once dirname(__FILE__) . '/size_modal_extra_option.php'; ?>
</body>
</html>
