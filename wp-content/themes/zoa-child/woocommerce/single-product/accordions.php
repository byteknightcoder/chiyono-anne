<?php

/**
 * Single product Accordion
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

global $post, $product;
$short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);
$deliver_date = get_post_meta($post->ID, 'deliver_date', true);
$deliver_date_new = get_post_meta($post->ID, 'from_to', true);
$specific_deliver_date = get_post_meta($post->ID, 'specific_deliver_date', true);
/*$categ = $product->get_categories();
$term = get_term_by ( 'name' , strip_tags($categ), 'product_cat' );
$catdesc = $term->description;*/
$model_wearsizes = get_field('model_wearsize', $post->ID);
$saved_model_size = get_field('saved_model_size', $post->ID);
$args = array('taxonomy' => 'series',);
$terms = wp_get_post_terms($post->ID, 'series', $args);
$count = count($terms);
/*if ( ! $short_description ) {
	return;
}*/
$term_cats = get_the_terms($post->ID, 'product_cat');
$term_cats_arr = array();

if ($term_cats && !is_wp_error($term_cats)) {
	$term_cats_arr = wp_list_pluck($term_cats, 'slug');
}
$tags_arr=array();
$tags     = get_the_terms( $post->ID, 'product_tag' );
if ( $tags && ! is_wp_error( $tags ) ) {
    $tags_arr = wp_list_pluck($tags, 'slug');
                
}
?>
<ul class="accordion product-details acc-parent">
	<?php if ($short_description || $count > 0) { ?>
		<li class="acc-item acc_desc">
			<div class="acc-toggle"><span class="prod-info-heading"><?php _e('Item Info', 'zoa'); ?></span><span class="acc-icon"></span></div>
			<div class="acc-inner prod-detail-content">
				<?php if (get_field('fabric')) : ?>
					<p class="label_fabric"><?php _e('Fabric', 'zoa'); ?></p>
					<p><?php the_field('fabric'); ?></p>
				<?php endif; ?>
				<?php if (empty($saved_model_size)) : ?>
					<?php if (have_rows('model_size') || have_rows('models_size')) : ?>
						<p class="label_details has_icon has_icon_left"><i class="oecicon oecicon-measurement-2"></i><?php _e('Model size info', 'zoa'); ?></p>
					<?php endif; ?>
					<?php if (have_rows('model_size') || have_rows('models_size')) : ?>
						<ul class="size_model">
							<?php echo ('<li class="list_label">' . __('Measurement', 'zoa') . '</li>'); ?>
							<?php
							while (have_rows('model_size')) : the_row();
								echo ('<li>');
								echo ('<span class="label">' . the_sub_field('size_title') . '</span>');
								echo ('<span class="value">' . the_sub_field('size_value') . 'cm</span>');
								echo ('</li>');
							endwhile;
							while (have_rows('models_size')) : the_row();
								echo ('<li class="multi_model_chart">');
								echo ('<span class="model_name">');
								the_sub_field('model_name');
								echo ('</span>');
								echo ('<span class="lv_list">');
								while (have_rows('model_chart')) : the_row();
									echo ('<span class="lv_list_inner">');
									echo ('<span class="label">');
									the_sub_field('size_title');
									echo ('</span>');
									echo ('<span class="value">');
									the_sub_field('size_value');
									echo ('cm</span>');
									echo ('</span>');
								endwhile;
								echo ('</span>');
								echo ('</li>');
							endwhile;
							?>
						</ul>
					<?php endif; ?>
				<?php endif; ?>
				<?php
				if (empty($saved_model_size)) :
					if ($model_wearsizes && !empty($model_wearsizes)) : ?>
						<ul class="size_model size_model_wear">
							<?php echo ('<li class="list_label">' . __('Wearing Size', 'zoa') . '</li>'); ?>
							<?php
							foreach ($model_wearsizes as $model_wearsize) {

								if (isset($model_wearsize['item']) && !empty($model_wearsize['item'])) {
									$wearWho = $model_wearsize['item']['wear_who'];
									$wearCat = get_term($model_wearsize['item']['wear_item_cat']);
									$wearSize = get_term($model_wearsize['item']['wear_item_size']);
									if (!empty($wearWho)) {
										echo ('<li class="multi_models_wear">');
									} else {
										echo ('<li>');
									}

									if (!empty($wearWho)) {
										echo '<span class="model_wear">';
										echo ('<span class="model_name">' . $wearWho . ' </span>');
									}
									echo ('<span class="label">' . $wearCat->name . ' </span>');
									echo ('<span class="value">' . $wearSize->name . __(' size', 'zoa') . '</span>');
									if (!empty($wearWho)) {
										echo '</span>';
									}
									echo ('</li>');
								}
							}
							?>
						</ul>
				<?php endif; //model_wearsize 
				endif;
				?>

				<?php if (have_rows('feature_item')) : //Feature ACF 
				?>
					<p class="label_details has_icon has_icon_left"><i class="oecicon oecicon-star-rate"></i><?php _e('Feature', 'zoa'); ?></p>
					<ul class="detail_additional detail_feature">
						<?php while (have_rows('feature_item')) : the_row(); ?>
							<li><?php echo (the_sub_field('item')); ?></li>
						<?php endwhile; ?>
					</ul>
				<?php endif; //feature_item 
				?>

				<?php if (have_rows('recommend_who')) : //Recommend who ACF 
				?>
					<p class="label_details has_icon has_icon_left"><i class="oecicon oecicon-heart-2-3"></i><?php _e('Recommend to who', 'zoa'); ?></p>
					<ul class="detail_additional detail_feature">
						<?php while (have_rows('recommend_who')) : the_row(); ?>
							<li><?php echo (the_sub_field('item')); ?></li>
						<?php endwhile; ?>
					</ul>
				<?php endif; //recommend_who 
				?>

				<?php if (have_rows('no_recommend_who')) : //Not Recommend Point ACF 
				?>
					<p class="label_details has_icon has_icon_left"><i class="oecicon oecicon-heart-remove"></i><?php _e('難しい体型', 'zoa'); ?></p>
					<ul class="detail_additional detail_feature">
						<?php while (have_rows('no_recommend_who')) : the_row(); ?>
							<li><?php echo (the_sub_field('item')); ?></li>
						<?php endwhile; ?>
					</ul>
				<?php endif; //Not Recommend Point 
				?>

				<?php if ($short_description || $count > 0) {
					echo '<p class="label_details has_icon has_icon_left"><i class="oecicon oecicon-alert-circle-i"></i>' . __('Item description', 'zoa') . '</i></p>';
				} ?>
				<?php if ($short_description) {
					echo $short_description;
				} ?>
				<?php if ($count > 0) {

					foreach ($terms as $term) {
						echo '<p>';
						echo wpautop($term->description);
						echo '</p>';
					}
				} ?>

			</div>
		</li>
	<?php } ?>
	<?php
	if (class_exists('productsize_chart_Public')) {
		$chart = new productsize_chart_Public('productsize-chart-for-woocommerce', 123);
		global $post;
		$chart_id = $chart->productsize_chart_id($post->ID);
	}
	if ($chart_id) {
	?>
		<li class="acc-item acc_size">
			<div class="acc-toggle"><span class="prod-info-heading"><?php _e('Size Info', 'zoa'); ?></span><span class="acc-icon"></span></div>
			<div class="acc-inner prod-detail-content" id="size_chart_content">
				<?php
				if ($model_wearsizes && !empty($model_wearsizes)) : ?>
					<p class="label_details has_icon has_icon_left"><i class="oecicon oecicon-measurement-2"></i><?php _e('Model Wear Size Info', 'zoa') ?></p>
					<ul class="size_model size_model_wear ch_ch" style="margin-bottom: 15px;">
						<?php echo ('<li class="list_label">' . __('Wearing Size', 'zoa') . '</li>'); ?>
						<?php
						foreach ($model_wearsizes as $model_wearsize) {
							$modelsize_row = $model_wearsize['size_model_wear'];
							if (isset($model_wearsize['size_model_wear']) && !empty($model_wearsize['size_model_wear'])) {

								foreach ($modelsize_row as $modelsize_row_item) {
									$name = $modelsize_row_item['name'];
									$manual_name = $modelsize_row_item['manual_name'];
						?>
									<li class="multi_models_wear">
										<span class="model_wear">
											<span class="model_name"><?php echo $name->post_title; ?> <?php echo $manual_name; ?></span>
											<?php
											$cat_and_size = $modelsize_row_item['cat_and_size'];
											foreach ($cat_and_size as $cat_and_size_item) {
											?>
												<span class="label"><?php echo $cat_and_size_item['cat']; ?></span>
												<span class="value"><?php echo $cat_and_size_item['size']; ?></span>
											<?php
											}
											?>
										</span>
									</li>
						<?php
								}
							}
						}
						?>
					</ul>
				<?php endif; //model_wearsize 
				?>
				<?php

				if (!empty($saved_model_size)) {
				?>
					<ul class="size_model" style="margin-bottom: 15px;">
						<li class="list_label"><?php _e('Model Measurement', 'zoa'); ?></li>
						<?php
						$i = 0;
						foreach ($saved_model_size as $post_s) {
							$model_measure = get_field('model_measure', $post_s->ID);
							if (!empty($model_measure)) {
								$model_name = $post_s->post_title;
						?>
								<li class="multi_model_chart">
									<?php if ($model_name && count($saved_model_size) > 1) {
										echo '<span class="model_name">' . $post_s->post_title . ' </span>';
									} ?>
									<span class="lv_list">
										<span class="lv_list_inner">
											<span class="label"><?php _e('Bust:', 'zoa') ?></span><span class="value"><?php echo $model_measure['bust']; ?>cm</span>
										</span>
										<span class="lv_list_inner"><span class="label"><?php _e('Under:', 'zoa') ?></span><span class="value"><?php echo $model_measure['under']; ?>cm</span></span>
										<span class="lv_list_inner">
											<span class="label"><?php _e('Waist:', 'zoa') ?></span><span class="value"><?php echo $model_measure['waist']; ?>cm</span>
										</span>
										<span class="lv_list_inner"><span class="label"><?php _e('Hip:', 'zoa') ?></span><span class="value"><?php echo $model_measure['hip']; ?>cm</span></span>
										<span class="lv_list_inner"><span class="label"><?php _e('Heights:', 'zoa') ?></span><span class="value"><?php echo $model_measure['heights']; ?>cm</span></span>
									</span>
								</li>

						<?php
							}
							$i++;
						}
						?>
					</ul>
				<?php
				}
				?>

				<?php $chart->productsize_chart_new_product_tab_content(); ?>
			</div>
		</li>
	<?php } ?>
	<?php
	$extra_tab = get_field('accordion_prd', $post->ID);
	if (!empty($extra_tab)) {
		foreach ($extra_tab as $k => $value) {
	?>
			<li class="acc-item">
				<div class="acc-toggle"><span class="prod-info-heading"><?php echo $value['title']; ?></span><span class="acc-icon"></span></div>
				<div class="acc-inner prod-detail-content"><?php echo $value['text']; ?></div>
			</li>
	<?php
		}
	}
	?>
	<li class="acc-item acc_size" id="ch_about_delivery">
		<div class="acc-toggle"><span class="prod-info-heading"><?php _e('About Delivery', 'zoa'); ?></span><span class="acc-icon"></span></div>
		<div class="acc-inner prod-detail-content">
			<!--if product cat is mask-->
			<?php
			if (in_array('mask', $term_cats_arr)) {
			?>
				<p><?php _e('お客様よりご注文いただきました商品は、レターパック（一律370円）にてお届けいたします。', 'zoa'); ?><br /><?php _e('その他商品とまとめて配送の場合は、ゆうパックにてお届けいたします。', 'zoa'); ?><br /><?php _e('詳しくは', 'zoa'); ?><a id="shipping_info_link" class="link_underline"><?php _e('配送について', 'zoa'); ?></a><?php _e('をご覧ください。', 'zoa'); ?></p>
			<?php } elseif (in_array('digitalcard', $tags_arr)) { ?>
				<p><?php _e('こちらの商品は、デジタル商品となり配送はありません。メールにて「受取人」様へコードが送信されます。', 'zoa'); ?></p>
			<?php } else { ?>
				<p><?php _e('お客様よりご注文いただきました商品は、ゆうパックにてお届けいたします。', 'zoa'); ?><br /><?php _e('詳しくは', 'zoa'); ?><a id="shipping_info_link" class="link_underline"><?php _e('配送について', 'zoa'); ?></a><?php _e('をご覧ください。', 'zoa'); ?></p>
			<?php } ?>
			<?php if ((!empty($deliver_date_new) && (!empty($deliver_date_new[0]) || !empty($deliver_date_new[2]))) || !empty($specific_deliver_date)) {
				$text_dwm = '';
				if (!empty($specific_deliver_date)) {
					$text_date = $specific_deliver_date;
				} else {
					if (!empty($deliver_date_new[0]) && empty($deliver_date_new[2])) {
						if ($deliver_date_new[1] == 'months') {
							$text_dwm = 'ヶ月';
						} elseif ($deliver_date_new[1] == 'weeks') {
							$text_dwm = '週間';
						} else {
							$text_dwm = '日';
						}
						$text_date = '注文確定後、約 ' . $deliver_date_new[0] . ' ' . $text_dwm . ' で発送';
					} elseif (!empty($deliver_date_new[2]) && empty($deliver_date_new[0])) {
						if ($deliver_date_new[3] == 'months') {
							$text_dwm = 'ヶ月';
						} elseif ($deliver_date_new[3] == 'weeks') {
							$text_dwm = '週間';
						} else {
							$text_dwm = '日';
						}
						$text_date = '注文確定後、約 ' . $deliver_date_new[2] . ' ' . $text_dwm . ' 以内に発送';
					} elseif (!empty($deliver_date_new[0]) && !empty($deliver_date_new[2])) {
						if ($deliver_date_new[1] == 'months') {
							$text_dwm = 'ヶ月';
						} elseif ($deliver_date_new[1] == 'weeks') {
							$text_dwm = '週間';
						} else {
							$text_dwm = '日';
						}
						$text_dwm_to = '';
						if ($deliver_date_new[3] == 'months') {
							$text_dwm_to = 'ヶ月';
						} elseif ($deliver_date_new[3] == 'weeks') {
							$text_dwm_to = '週間';
						} else {
							$text_dwm_to = '日';
						}
						if ($deliver_date_new[1] == $deliver_date_new[3] && $deliver_date_new[1] == 'days') {
							$text_date = '受注から約 ' . $deliver_date_new[0] . '〜' . $deliver_date_new[2] . ' 営業日以内に発送';
						} else {
							$text_date = '注文確定後、約 ' . $deliver_date_new[0] . ' ' . $text_dwm . '〜' . $deliver_date_new[2] . ' ' . $text_dwm_to . ' 以内に発送';
						}
					}
				}
				$tags = get_the_terms($id, 'product_tag');
				$tag_arr = array();

				if ($tags && !is_wp_error($tags)) {
					$tag_arr = wp_list_pluck($tags, 'name');
				}

				if (in_array('Express Shipping', $tag_arr) && !empty($deliver_date)) {
					$text_date = '注文確定後、' . $deliver_date;
				}
			?>
				<p><?php _e('納期', 'zoa'); ?>：<?php echo $text_date; ?>
					<?php if (empty($specific_deliver_date)) { ?>
						<br>*納期はあくまでも目安となり、前後する可能性がございます
					<?php } ?>
				</p>
			<?php

			} else { ?>
				<p><?php _e('納期', 'zoa'); ?>：<?php _e('受注から約3ヶ月', 'zoa'); ?><br>*納期はあくまでも目安となり、前後する可能性がございます</p>
			<?php } ?>
		</div>
	</li>
</ul>
<div class="pd__extras light-copy">
	<?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>
		<span class="pd__extras__productid offset">
			<?php esc_html_e('Product ID #', 'woocommerce'); ?> <?php echo ($sku = $product->get_sku()) ? $sku : esc_html__('N/A', 'woocommerce'); ?>
		</span>
	<?php endif; ?>
	<p class="pd__extras__moreinfo"><a href="<?php echo home_url('/returns-exchanges'); ?>" class="link_underline"><?php _e('返品・交換', 'zoa'); ?></a>に関して</p>
</div>
<div class="pd__extras light-copy">
<p class="pd__extras__moreinfo"><a href="<?php echo home_url('/howtocare'); ?>" class="link_underline"><?php _e('How to care', 'zoa'); ?></a></p>
</div>

<div class="remodal" data-remodal-id="shipping_info_modal" id="shipping_info_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
	<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
	<div class="remodal_wraper">
		<?php
		if ($post = get_page_by_path('shipping-info', OBJECT, 'page')) {
			echo $post->post_content;
		}
		?>
	</div>
	<br>
</div>