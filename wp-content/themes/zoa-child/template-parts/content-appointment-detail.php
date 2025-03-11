<?php //start content appointment details
global $birchschedule;
$appointment_id = $_REQUEST['id'];
$appointment_info = $birchschedule->model->mergefields->get_appointment_merge_values( $appointment_id );
$status = get_post_meta($appointment_id, '_birs_appointment_status', true);
$appointment_info['status'] = !$status ? __('Active', 'zoa') : __($status, 'zoa');
$appointment_info['status_code'] = $status;
// Client
$user = get_current_user();

// Get client id
$args = array(
	'meta_key' => '_birs_client_email',
	'meta_value' => $user->user_email,
	'post_status' => 'publish',
	'post_type' => 'birs_client',
	'posts_per_page' => 1
);
$clients = get_posts($args);
$client = $clients[0];
$client_id = $client->ID;
$customer_name = get_post_meta($client_id, 'name_last_name', true) . '&nbsp;' . get_post_meta($client_id, 'name_first_name', true);
$customer_kananame = get_post_meta($client_id, 'name_kana_last_name', true) . '&nbsp;' . get_post_meta($client_id, 'name_kana_first_name', true);
$customer_tel = get_post_meta($client_id, 'tel', true);
$customer_email = get_post_meta($client_id, 'email', true); 


// Get questions
$field_group_fields = acf_get_fields(BOOKING_FORM_ID);
$questions = array();
$add_question = '';
foreach ($field_group_fields as $field_group_field)
{
	if ($field_group_field['name'] == 'questions') {
		$questions[] = $field_group_field;
	}
	
	elseif ($field_group_field['name'] == 'additional_questions') {
		$add_question = get_post_meta($client_id, $field_group_field['name'], true);
	}
}
?>
<div class="appointment--details appointment_item status-<?php echo !$appointment_info['status_code'] ? 'active' : 'cancelled'?>">
	<div class="appointment--details__info details__info_block">
		<h3 class="appointment--details__number heading heading--xsmall">
			<span class="label"><?php echo __('Appointment Number', 'zoa')?>:</span>
			<span class="value">#<?php echo $appointment_info['ID']?></span>
		</h3>
		<div class="appointment--details__info--half">
			<p class="appointment--details__date">
					<span class="label"><?php echo __('Appointment Date/Time', 'zoa')?>:</span>
					<span class="value"><?php echo $appointment_info['_birs_appointment_datetime']?></span>
				</p>
				<div class="booking-status">
					<span class="label"><?php echo __('Appointment Status', 'zoa')?>:</span>
					<span class="value"><?php echo $appointment_info['status']?></span>
				</div>
		</div>
		<?php 
		$is_allow_cancel = zoa_is_allow_cancel_appointment($appointment_id);
		if ($appointment_info['status_code'] != 'cancelled' && $is_allow_cancel) {?>
		<div class="appointment--details__info--half">
			<a class="cancel-appointment-btn button" href="#" data-id="<?php echo $appointment_info['ID']?>" class="button"><?php echo __('Cancel', 'zoa')?></a>
		</div>
		<?php }?>
	</div>
	<div class="appointment--details__col row flex-justify-between details__info_block">
		<div class="appointment--details__items customer-info-list col-lg-6 col-xs-12">
			<div class="appointment__summary__contents item-customer-info">
				<p class="item__summary__row heading m-no_topmargin heading--small">
					<?php echo __('Service Location', 'zoa')?>
				</p>
				<div class="item__summary__row row_small_label">
					<span class="value"><?php echo zoa_get_appointment_location_addrress($appointment_info)?></span>
					<!--if user lang is en, format is {Address2}{Address 1}, {City}, {State}, {Country}<br/>{postcode}-->
				</div>
				<div class="item__summary__row row_small_label">
					<span class="value"><?php echo $appointment_info['_birs_location_phone']?></span>
				</div>
			</div>
			<div class="appointment__summary__contents item-customer-info">
				<p class="item__summary__row heading m-no_topmargin heading--small">
					<?php echo __('Customer Info', 'zoa')?>
				</p>
				<div class="item__summary__row row_small_label">
					<span class="label"><?php echo __('Name', 'woocommerce')?>:</span>
					<span class="value"><?php echo $customer_name?></span>
				</div>
				<!--start to show only for ja lang user-->
				<div class="item__summary__row row_small_label">
					<span class="label"><?php echo __('Name Kana', 'woocommerce')?>:</span>
					<span class="value"><?php echo $customer_kananame?></span>
				</div>
				<!--end to show only for ja lang user-->
				<div class="item__summary__row row_small_label">
					<span class="label"><?php echo __('Email', 'woocommerce')?>:</span>
					<span class="value"><?php echo $customer_email?></span>
				</div>
				<div class="item__summary__row row_small_label">
					<span class="label"><?php echo __('Phone', 'woocommerce')?>:</span>
					<span class="value"><?php echo $customer_tel?></span>
				</div>
			</div>
		</div>
		<div class="appointment--details__items question-list col-lg-6 col-xs-12">
			<div class="appointment__summary__contents item-questions-info item-questions" style="display: none;">
				<p class="item__summary__row heading m-no_topmargin heading--small">
					<?php echo __('Answers for out questions', 'zoa')?>
				</p>
				
				<?php 
				$fields = array();
				$sub_questions = array();
				foreach ($questions as $field)
				{
					loop_to_get_sub_field($field, $fields);
				}
				foreach ($fields as $question)
				{
					$field_value = $question['full_value'] ? $question['full_value'] : get_post_meta($appointment_id, ($question['name_long'] ? $question['name_long'] : $question['name']), true);
					if (!$field_value) continue;
					
					echo '<style>.item-questions{display: block;}</style>';
					if ($question['sub_depth'] == 1)
					{
					?>
						<div class="item__summary__row row_small_label">
							<span class="label"><?php echo __($question['label'], 'zoa')?>:</span>
							<span class="value"><?php echo (is_array($field_value) ? implode(', ', $field_value) : $field_value)?></span>
						</div>
					<?php
					}
					else {
						// get parent
						foreach ($questions[0]['sub_fields'] as $parent_question)
						{
							if ($parent_question['ID'] == $question['parent_id'])
							{
								$sub_questions[$parent_question['ID']]['label_parent'] = __($parent_question['label'], 'zoa');
								break;
							}
						}
						$sub_questions[$parent_question['ID']]['sub'][$question['ID']]['label'] = $question['label'];
						$sub_questions[$parent_question['ID']]['sub'][$question['ID']]['value'] = $field_value;
					}
				}
				// Show sub questions
				foreach ($sub_questions as $sub_question)
				{
				?>
				
				<div class="item__summary__row row_small_label">
					<span class="label"><?php echo __($sub_question['label_parent'], 'zoa')?></span>
					<span class="value">
						<?php 
						foreach ($sub_question['sub'] as $sub_sub_question)
						{
							echo __($sub_sub_question['label'], 'zoa') . ':' . $sub_sub_question['value'] . '<br />';
						}
						?>
					</span>
				</div>
				<?php
				}
				?>
			</div>
			<!--Start to show if value has-->
			<?php if ($add_question) {?>
			<div class="appointment__summary__contents item-questions-info item-add-questions">
				<p class="item__summary__row heading m-no_topmargin heading--small">
					<?php echo __('Other questions', 'zoa')?>
				</p>
				<div class="item__summary__row row_small_label">
					<p class="value"><p><?php echo $add_question;?></p></p>
				</div>
			</div>
			<?php }?>
			<!--End to show if value has-->
		</div>
	</div>
</div>
<?php //end content appointment details
?>