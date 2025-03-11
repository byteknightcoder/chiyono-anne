<?php 
if (!session_id())
{
	session_start();
}
?>
<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="form__steps_wrap"><ul class="form__steps"><li class="form__step"><div class="form__step-container"><span class="form__step-nr">1</span><span class="form__step-title"><?php echo __('Appointment Info', 'zoa')?></span></div></li><li class="form__step"><div class="form__step-container"><span class="form__step-nr">2</span><span class="form__step-title"><?php _e( 'Your Info', 'zoa' ); ?></span></div></li><li class="form__step"><div class="form__step-container"><span class="form__step-nr">3</span><span class="form__step-title"><?php _e( 'Your Inquiry', 'zoa' ); ?></span></div></li><li class="form__step is-active"><div class="form__step-container"><span class="form__step-nr">4</span><span class="form__step-title"><?php _e( 'Confirmation', 'zoa' ); ?></span></div></li></ul></div>
	<?php
		the_content();
		zoa_wp_link_pages(); /*break page*/
	?>
	<div id="reservationFormConfirm" class="form_entry">
		<legend class="confirm--booking__form__section"><h2 class="heading heading--xlarge"><?php _e( 'Confirm your appointment', 'zoa' ); ?></h2></legend>
		<form class="form-horizontal" id="confirmed_booking_form">
		<input type="hidden" name="booking_confirm" value="1"/>
		<div class="confirm-box">
			<?php get_booking_confirm_html();?>
		</div>
		
		<?php if ($_SESSION['appointment_id']) {?>
		<div class="order--checkout__footer input-list">
			<button type="submit" class="button button--primary button-submit" id="book_confirmed"><?php echo __('Book', 'zoa')?></button>
			<a type="button" class="cta" id="book_back" href="<?php echo site_url()?>/reservation?pid=<?php echo $post->ID; ?>"><?php echo __('Back', 'zoa')?></a>
			
		</div>
		<?php }?>
		
		</form>
		<?php 
		?>
		
	</div>
</div>
