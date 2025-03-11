<?php 
if (!session_id())
{
	session_start();
}
$appointment_info = get_appointment_info();
$appointment_info = !$appointment_info ? array() : $appointment_info;

?>
<script type="text/javascript">
var appointment_info = <?php echo json_encode($appointment_info);?>;
jQuery(function($){
	if(appointment_info.location_id)
	{
		$('#birs_appointment_location').val(appointment_info.location_id);
		$('#birs_appointment_location').trigger('change');
		
		setTimeout(function(){
			$('#birs_appointment_service').val(appointment_info.service_id);
			$('#birs_appointment_service').trigger('change');

			setTimeout(function(){
				$('#birs_appointment_staff').val(appointment_info.staff_id);
				$('#birs_appointment_staff').trigger('change');

				setTimeout(function(){
					$('#birs_appointment_datepicker').datepicker("setDate", new Date(appointment_info.year,appointment_info.month-1,appointment_info.day) );
					$('.ui-datepicker-current-day a').trigger('click');

					setTimeout(function(){
						var get_timer = setInterval(function(){
							if ($('#birs_appointment_timeoptions').find('a.birs_option').length)
							{
								$('.birs_option[data-time="'+(parseInt(appointment_info.hour * 60) + parseInt(appointment_info.minute))+'"]').trigger('click');
								clearInterval(get_timer);
								get_timer = null;
							}
						}, 50);
						
					}, 150);
				}, 100);
				
			}, 100);
		}, 100);

		$('#birs_appointment_notes').val(appointment_info.notes);
	}
})
</script>
<?php
$image_url = get_the_post_thumbnail_url(get_the_ID(),'full');
//$image_url = $image_data[0];
?>
<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
	
		<!--<div class="col-lg-6 col-md-5 col-12 form_sideimg" style="background: url(<?php echo $image_url; ?>); background-size:cover;">
			<div class="sideimg__content text-center">
	<?php
			/*the_title( '<h1 class="page-title entry-title">', '</h1>' );
		the_content();*/
	?>
				<a href="<?php //echo home_url('/bespoke'); ?>" class="more_details double_button"><?php //_e('See more details', 'zoa'); ?></a>
				</div>
			</div>-->
		
	<div id="reservationForm" class="form_entry">
		
		<?php 
		global $birchschedule;
		
		echo $birchschedule->view->bookingform->get_shortcode_content(array());
		?>
		
	</div>
	
</div>
