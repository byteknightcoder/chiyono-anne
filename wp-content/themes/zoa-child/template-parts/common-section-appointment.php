<?php #固定ページ reservationページバナー スラッグ reservation を取得。the_fieldで表示
$page_id = get_page_by_path('reservation');
?>
<section id="appintmentCommon" class="section flex-justify-center align_center js-parallax full_wide_section" style="background-image: url(<?php the_field('common_section_banner',$page_id); ?>);">
	<div class="row center vertical_center">
		<div class="col-xs-12 col-md-5 col-lg-4">
			<a href="<?php echo home_url('/reservation'); ?>" class="btn btn--inverse"><?php esc_html_e('Request an appointment', 'zoa'); ?></a>
		</div>
	</div>
</section>