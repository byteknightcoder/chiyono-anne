<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! zoa_is_elementor() ) : ?>
		<header class="entry-header">
			<?php
			if ( is_single() ) {
				the_title( '<h1 class="entry-title blog-title">', '</h1>' );
			} else {
				the_title( '<h2 class="entry-title blog-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			}
			?>
		</header>
	<?php
		endif;
	?>
	
	<?php
	//ACF
	$step01 = get_field('step01');
	$step02 = get_field('step02');
	if( $step01 ):
	$sideImgStep1 = $step01['image'];
	$sideTxtStep1 = $step01['text'];
	$sideTitleStep1 = '<h1 class="page-title entry-title">'.get_the_title().'</h1>';
	$sideTextStep1 = '<p class="rv_item_body">'.$sideTxtStep1.'</p>';
	$showTxtConStep1 = '<div class="sideimg__content text-center">'.$sideTitleStep1.$sideTextStep1.'</div>';
	endif;
	if( $step02 ):
	$sideImgStep2 = $step02['image'];
	$sideTxtStep2 = $step02['text'];
	$sideTitleStep2 = '<h1 class="page-title entry-title">Fill your information</h1>';
	$sideTextStep2 = '<p class="rv_item_body">'.$sideTxtStep2.'</p>';
	$showTxtConStep2 = '<div class="sideimg__content text-center">'.$sideTitleStep2.$sideTextStep2.'</div>';
	endif;
	//WrapElement
	$fieldOpen = '<fieldset class="step">';
	$fieldClose = '</fieldset>';
	$rowOpen = '<div class="row rv_form_row">';
	$rowClose = '</div>';
	?>
	<div id="bookedForm" class="form">
		<?php
		//step01 Calendar
		echo $fieldOpen;
		echo '<div id="calendarForm" class="form_entry">';
		echo $rowOpen;
		echo '<div class="col-lg-6 col-md-5 col-xs-12 form_sideimg" style="background-image: url('.$sideImgStep1.');">'.$showTxtConStep1.'</div>';
		echo '<div class="col-lg-6 col-md-7 col-xs-12 form_sidecon">';
		echo '<legend class="booked__form__section"><h2 class="heading heading--xlarge">'.__( 'Appointment Info', 'zoa' ).'</h2></legend>';
		echo '<div class="full-width-content">';
		the_content();
		echo '</div>';
		echo '</div>';//end sidecon
		echo $rowClose;
		echo '</div>';//end form_entry
		echo $fieldClose;

    ?>
</div>
	<?php
		zoa_wp_link_pages(); /*break page*/
	?>
</div>
