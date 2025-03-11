<?php

$appt_id = esc_html( $_POST['appt_id'] );
$appt = get_post( $appt_id );
$calendar_id = wp_get_post_terms( $appt_id, 'booked_custom_calendars' );
$appt_author = $appt->post_author;

$timeslot = get_post_meta( $appt_id,'_appointment_timeslot',true);
$timestamp = get_post_meta( $appt_id,'_appointment_timestamp',true);
$timeslots = explode('-',$timeslot);
$timestamp_start = strtotime(date_i18n('Y-m-d',$timestamp).' '.$timeslots[0]);
$current_timestamp = current_time('timestamp');

if (get_current_user_id() == $appt_author):

	if ( $timestamp_start >= $current_timestamp ):

		// Send an email to the user?
            $order_id=get_post_meta($appt_id, '_ch_wc_order_id', true);
                    $type=get_post_meta($appt_id, '_ch_booked_type', true);
                    if(isset($order_id)&&$order_id>0){
                        $email_content = get_option('booked_cancellation_email_content_try_fit_your_size');
                    }else{
                        if($type=='special'){
                            $email_content = get_option('booked_cancellation_email_content_special');
                        }elseif(booked_in_calendar($appt_id, 'bspkmonth-offline')){
                            $email_content = get_option('booked_bspkmonth_offline_cancellation', false);
                        }elseif(booked_in_calendar($appt_id, 'bspkmonth-online')){
                            $email_content = get_option('booked_bspkmonth_online_cancellation', false);
                        }else{
                            $email_content = get_option('booked_cancellation_email_content');
                        }
                    }
		$email_subject = get_option('booked_cancellation_email_subject');

		if ($email_content && $email_subject):

			$token_replacements = booked_get_appointment_tokens( $appt_id );
			$email_content = booked_token_replacement( $email_content,$token_replacements );
			$email_subject = booked_token_replacement( $email_subject,$token_replacements );

			do_action( 'booked_cancellation_email', $token_replacements['email'], $email_subject, $email_content );

		endif;

		// Send an email to the Admin?
		$email_content = get_option('booked_admin_cancellation_email_content');
		$email_subject = get_option('booked_admin_cancellation_email_subject');
		if ($email_content && $email_subject):

			$admin_email = booked_which_admin_to_send_email( $calendar_id );
			$token_replacements = booked_get_appointment_tokens( $appt_id );

			$email_content = booked_token_replacement( $email_content,$token_replacements );
			$email_subject = booked_token_replacement( $email_subject,$token_replacements );

			do_action( 'booked_admin_cancellation_email', $admin_email, $email_subject, $email_content, $token_replacements['email'], $token_replacements['name'] );

		endif;

	endif;

	do_action('booked_appointment_cancelled',$appt_id);
	wp_delete_post($appt_id,true);

endif;
