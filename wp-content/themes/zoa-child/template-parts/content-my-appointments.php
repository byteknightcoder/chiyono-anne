<?php //start content appointment list
$user = get_current_user();
global $birchschedule;

// Get client id
$args = array(
	'meta_key' => '_birs_client_email',
	'meta_value' => $user->user_email,
	'post_status' => 'publish',
	'post_type' => 'birs_client',
	'posts_per_page' => 1
);
$clients = get_posts($args);
$appointmentInfos = array();
if (!empty($clients))
{
	$client = $clients[0];
	// Get appointments
	$args = array(
		'meta_key' => '_birs_client_id',
		'meta_value' => $client->ID,
		'post_status' => 'publish',
		'post_type' => 'birs_appointment1on1',
		'posts_per_page' => -1
	);
	$appointment1on1s = get_posts($args);
	foreach ($appointment1on1s as $appointment1on1)
	{
		$appointment_id = get_post_meta($appointment1on1->ID, '_birs_appointment_id', true);
		$appointment_info = $birchschedule->model->get( $appointment_id, array(
			'base_keys' => array(),
			'meta_keys' => $birchschedule->model->get_appointment_fields()
		) );
		$service_id = $appointment_info['_birs_appointment_service'];
		$service = $birchschedule->model->get( $service_id, array( 'keys' => array( 'post_title' ) ) );
		
		$appointment = get_post($appointment_id);
		if ($appointment->post_status == 'publish')
		{
			$status = get_post_meta($appointment_id, '_birs_appointment_status', true);
			$appointmentInfos[$appointment_id]['time'] = $appointment_info['_birs_appointment_datetime'];
			$appointmentInfos[$appointment_id]['link'] = site_url('my-account/appointment-detail?id=' . $appointment->ID);
			$appointmentInfos[$appointment_id]['ID'] = $appointment_id;
			$appointmentInfos[$appointment_id]['status'] = !$status ? __('Active', 'zoa') : __($status, 'zoa');
			$appointmentInfos[$appointment_id]['status_code'] = $status;
			$appointmentInfos[$appointment_id]['service'] = $service['post_title'];
		}
	}
}
?>

<?php if (!empty($appointmentInfos)) {?>
	<div class="contact-inquiry-asset p4 sub-text">
		<p><?php echo __('To cancel an appointment, please click "Cancel" button.', 'zoa')?>
		<?php //echo __('To request changing booked date and time, call us on {phone number} or write to us at hello@chiyono-anne.com', 'zoa')?></p>
	</div>
	<div class="box-list">
		<?php echo do_shortcode("[booked-appointments]"); //test ?>
		<?php //start loop
		foreach($appointmentInfos as $appointmentInfo) {
			$is_allow_cancel = zoa_is_allow_cancel_appointment($appointmentInfo['ID']);
		?>
		<div class="box appointment appointment_item status-<?php echo !$appointmentInfo['status_code'] ? 'active' : 'cancelled'?>">
			<div class="box__main">
				<div class="box__main__details">
					<h3 class="appointment__number heading heading--xsmall">
						<span class="label"><?php echo __('Appointment Number', 'zoa')?>:</span>
						<span class="value">#<?php echo $appointmentInfo['ID']?></span>
					</h3>
					<p class="booked__date">
						<span class="label"><?php echo __('Appointment Date/Time', 'zoa')?>:</span>
						<span class="value"><?php echo $appointmentInfo['time']?></span>
					</p>
					<div class="booking-status">
						<span class="label"><?php echo __('Appointment Status', 'zoa')?>:</span>
						<span class="value"><?php echo $appointmentInfo['status']?></span>
					</div>
					<p class="service__type">
						<span class="label"><?php echo __('Service Type', 'zoa')?>:</span>
						<span class="value"><?php echo $appointmentInfo['service']?></span>
					</p>
				</div>
				<div class="box__actions">
					<a href="<?php echo $appointmentInfo['link']?>" class="button"><?php echo __('Appointment Details', 'zoa')?></a>
					<?php if ($appointmentInfo['status_code'] != 'cancelled' && $is_allow_cancel) {?>
						<a data-id="<?php echo $appointmentInfo['ID']?>" href="#" class="cancel-appointment-btn button"><?php echo __('Cancel', 'zoa')?></a>
					<?php }?>
				</div>
			</div>
		</div>
		<?php //End loop
		}
		?>
	</div>
	<?php //End content appointment list
}
?>