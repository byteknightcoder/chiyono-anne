<section id="appintmentHome" class="home_section grid_gals">
	<div class="container button_container fade-down fade-anitop">
		<div class="button_over center"><a href="<?php echo home_url('/reservation-form'); ?>" class="btn btn--inverse">Request an appointment</a></div>
	</div>
	<div class="row flex_wrap">
		<div class="col-sm-7 grid_gal fade-left fade-anitop">
			<div class="gal gal_01"><?php if (get_field('media01')) : ?><img src="<?php the_field('media01'); ?>" /><?php endif; ?></div>
		</div>
		<div class="col-sm-5 grid_gal fade-right fade-anitop">
			<div class="gal gal_02"><?php if (get_field('media02')) : ?><img src="<?php the_field('media02'); ?>" /><?php endif; ?></div>
			<div class="gal gal_03 fade-up fade-ani"><?php if (get_field('media03')) : ?><img src="<?php the_field('media03'); ?>" /><?php endif; ?></div>
		</div>
	</div>
</section>