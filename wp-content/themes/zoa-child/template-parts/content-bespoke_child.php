<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
	<!--<section id="bespokeIntro" class="section section_style02 align_center">
		<div class="row flex-justify-center">
			<div class="col-lg-6 col-md-6 col-sm-8 col-xs-12">
				<div class="heading-title">
					<?php //if( get_field('summary_title') ): ?><p class="sub_heading">About</p><h3 class="heading"><?php //the_field('summary_title'); ?></h3><?php //endif; ?>
				</div>
				<?php //if( get_field('summary_content') ): ?>
				<div class="intro_text">
					<?php //the_field('summary_content'); ?>
				</div>
				<?php //endif; ?>
			</div>
		</div>
	</section>-->
	<section id="bespokeStep" class="section full_wide_section">
		<div class="heading-title align_center">
			<p class="sub_heading">The</p><h3 class="heading">Process</h3>
		</div>
		<?php
		if( have_rows('flow_steps') ):
		$counter = 0;
		while( have_rows('flow_steps') ): the_row(); $counter++;
		?>
		<div id="step01" class="step-landing__group row flex-justify-between max-width--large gutter-padding <?php if( $counter % 2 == 0 ) { ?>even<?php } else { ?>odd<?php } ?>">
			<div class="step__group col-xs-12 col-md-5">
				<div class="step-landing__group__desc">
					<div class="heading-title">
						<h2 class="heading heading--main notera"><span>Step<?php echo $counter; ?></span></h2>
						<p class="heading_subtitle"><?php the_sub_field('step_title'); ?></p>
					</div>
					<p class="p2"><?php the_sub_field('step_text'); ?></p>
				</div>
				<?php if( have_rows('do_list') ): ?>
				<div class="step-listing">
					<?php while( have_rows('do_list') ): the_row(); ?>
					<div class="step-item">
						<h4 class="heading heading--small"><?php the_sub_field('title'); ?></h4>
						<p class="p6"><?php the_sub_field('content'); ?></p>
					</div>
					<?php endwhile; ?>
				</div>
				<?php endif; //if( get_sub_field('do_list') ): ?>
			</div>
			<div class="col-xs-12 col-md-6 col-step-img">
				<div class="step-landing__featured-img">
					<img class="featured-img" src="<?php the_sub_field('step_image'); ?>" alt="">
				</div>
			</div>
		</div>
		<?php endwhile; endif; ?>
	</section>
	<?php get_template_part( 'template-parts/common-section', 'appointment' ); ?>
</div>
