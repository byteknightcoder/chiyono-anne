<?php
	$client_id = $_SESSION['client_id'];
	$appointment_info = get_appointment_info();
	$customer_name = get_post_meta($client_id, 'name_last_name', true) . '&nbsp;' . get_post_meta($client_id, 'name_first_name', true); 
	$customer_kananame = get_post_meta($client_id, 'name_kana_last_name', true) . '&nbsp;' . get_post_meta($client_id, 'name_kana_first_name', true); 
	$customer_tel = get_post_meta($client_id, 'tel', true); 
	$customer_email = get_post_meta($client_id, 'email', true); 

	$field_group_fields = array();
	$questions = array();
	$add_questions = array();
	
	if ( class_exists('ACF') && defined( 'BOOKING_FORM_ID' ) ) { // Check if ACF plugin is active and BOOKING_FORM_ID is define to prevent errors.
		$field_group_fields = acf_get_fields(BOOKING_FORM_ID);
	}

	foreach ($field_group_fields as $field_group_field) {
		if (  'questions' == $field_group_field['name']) {
			$questions[] = $field_group_field;
		} elseif ( 'additional_questions' == $field_group_field['name'] ) {
			$add_questions = $field_group_field;
		}
	}
?>
<style type="text/css">
	@font-face {
		font-family: 'Noto Sans JP';
		font-style: normal;
		font-weight: 400;
		src: local('Noto Sans Japanese Regular'),
			local('NotoSansJapanese-Regular'),
			url(https://fonts.gstatic.com/s/notosansjp/v16/-F62fjtqLzI2JPCgQBnw7HFowwII2lcnoeA3frgQrvWXpdFgzKXkFY85dZISEzd2Lgk_okkQpbQPZNoSzU1r.0.woff2)
			format('woff2');
	}
	@font-face {
		font-family: 'Montserrat';
		font-style: normal;
		font-weight: 300;
		src: local('Montserrat Light'), local('Montserrat-Light'),
			url(https://fonts.gstatic.com/s/montserrat/v12/JTURjIg1_i6t8kCHKm45_cJD3gTD_vx3rCubqg.woff2)
			format('woff2');
	}
	@font-face {
		font-family: 'Inconsolata';
		font-style: normal;
		font-weight: 400;
		src: local('Inconsolata Regular'), local('Inconsolata-Regular'),
			url(https://fonts.gstatic.com/s/inconsolata/v16/QldKNThLqRwH-OJ1UHjlKGlW5qhExfHwNJU.woff2)
			format('woff2');
		unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
	}
	html {
		width: 100%;
	}
	::-moz-selection {
		background: #fefac7;
		color: #4a4a4a;
	}
	::selection {
		background: #fefac7;
		color: #4a4a4a;
	}
	body {
		margin: 0;
		width: 100%;
		padding: 0;
	}
	.ReadMsgBody {
		width: 100%;
		background-color: #f4f4f4;
	}
	.ExternalClass {
		width: 100%;
		background-color: #f1f1f1;
	}
	a {
		color: #000000;
		text-decoration: underline;
		font-weight: normal;
		font-style: normal;
	}
	p, div, span {
		margin: 0 !important;
	}
	table {
		border-collapse: collapse;
	}

	@media only screen and (max-width: 599px) {
		body {
			width: auto !important;
		}
		table table {
			width: 100% !important;
		}
		td.paddingOuter {
			width: 100% !important;
			padding-right: 20px !important;
			padding-left: 20px !important;
		}
		td.fullWidth {
			width: 100% !important;
			display: block !important;
			float: left;
			margin-bottom: 30px !important;
		}
		td.fullWidthNM {
			width: 100% !important;
			display: block !important;
			float: left;
			margin-bottom: 0px !important;
		}
		td.center {
			text-align: center !important;
		}
		td.right {
			text-align: right !important;
		}
		td.spacer {
			display: none !important;
		}
		img.scaleImg {
			width: 100% !important;
			height: auto;
		}
	}
</style>
</head>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f4f4f4" style="padding: 0; margin: 0;">
	<tr>
		<td align="center" width="700" valign="top" style="padding: 0px;">
			<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="padding: 0; margin: 0;">
				<tr>
					<td class="paddingOuter" valign="top" align="center" style="padding: 0px;">
						<table class="tableWrap" width="560" border="0" align="center" cellpadding="0" cellspacing="0" style="">
							<tr>
								<td style="padding: 0px;">
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-top: 20px; padding-bottom: 10px; font-size: 20px; font-weight: normal; color: #000000; font-family: 'Inconsolata', monospace; line-height: 34px; mso-line-height-rule: exactly;">
															<span>
																<a href="#" style="text-decoration: none; font-style: normal; font-weight: normal; color: #000000;"> <?php esc_html_e('Thanks for your appointment.', 'zoa'); ?> </a>
															</span>
														</td>
													</tr>
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 0; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<p style="padding-bottom: 20px;">
																<?php printf(__('thank_you_for_your_reservation_email_text', 'zoa'), get_bloginfo('name'), $appointment_info['default']['_birs_appointment_datetime']); ?>
															</p>
															<p style="padding-bottom: 10px;"><?php echo $appointment_info['location_name']?></p>
															<p style="padding-bottom: 10px;">
																〒<?php echo $appointment_info['default']['_birs_location_zip']; ?>
																<br>
																<?php 
																	echo $appointment_info['default']['_birs_location_state'] . 
																	$appointment_info['default']['_birs_location_city'].
																	$appointment_info['default']['_birs_location_address1'].
																	$appointment_info['default']['_birs_location_address2'];
																?>
															</p>
															<p>
																<?php 
																	$address =
																	$appointment_info['default']['_birs_location_zip'] . ',' .$appointment_info['default']['_birs_location_state'] . ',' .
																	$appointment_info['default']['_birs_location_city'] . ',' .$appointment_info['default']['_birs_location_city'] . ',' .
																	$appointment_info['default']['_birs_location_address1'] . ',' .$appointment_info['_birs_location_address2'];
																?>
																<a href="http://maps.google.com/?q=<?php echo $address; ?>"><?php esc_html_e('Open Google Map for our store', 'zoa'); ?></a>
															</p>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<!--Spacer-->
							<tr>
								<td height="30" style="padding: 0; line-height: 0;">&nbsp;</td>
							</tr>
							<!-- Spacer end-->
						</table>
					</td>
				</tr>
			</table><!--
			--><table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="padding: 0; margin: 0;">
				<tr><td height="30" style="padding: 0; line-height: 0;">&nbsp;</td></tr><!--
			--><tr><!--
			--><td class="paddingOuter" valign="top" align="center" style="padding: 0px;"><!--
			--><table class="tableWrap" width="560" border="0" align="center" cellpadding="0" cellspacing="0" style="">
							<tr>
								<td style="padding: 25px 0px; border-top: solid 1px #eeeeee;">
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; text-transform: uppercase; font-size: 15px; font-weight: 300; color: #3a3a3a; font-family: 'Montserrat', Lato, Helvetica Neue, Helvetica, Arial, sans-serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Appointment Info', 'zoa'); ?></span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span>ご予約日時</span>
														</td>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php echo $appointment_info['default']['_birs_appointment_datetime']; ?></span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<!-- Spacer -->
							<tr>
								<td height="20" style="padding: 0; line-height: 0;">&nbsp;</td>
							</tr>
							<!-- Spacer End -->
						</table><!--
			--></td><!--
			--></tr><!--
			--><tr><!--
			--><td class="paddingOuter" valign="top" align="center" style="padding: 0px;"><!--
			--><table class="tableWrap" width="560" border="0" align="center" cellpadding="0" cellspacing="0" style="">
							<tr>
								<td style="padding: 25px 0px; border-top: solid 1px #eeeeee;">
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; text-transform: uppercase; font-size: 15px; font-weight: 300; color: #3a3a3a; font-family: 'Montserrat', Lato, Helvetica Neue, Helvetica, Arial, sans-serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Your Info', 'zoa')?></span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Name', 'zoa'); ?></span>
														</td>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php echo $customer_name; ?></span>
														</td>
													</tr>
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Name Kana', 'zoa'); ?></span>
														</td>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php echo $customer_kananame; ?></span>
														</td>
													</tr>
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Email Address', 'zoa'); ?></span>
														</td>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php echo $customer_email; ?></span>
														</td>
													</tr>
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Phone Number', 'woocommerce'); ?></span>
														</td>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php echo $customer_tel; ?></span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<!-- Spacer -->
							<tr>
								<td height="20" style="padding: 0; line-height: 0;">&nbsp;</td>
							</tr>
							<!-- Spacer End -->
						</table><!--
			--></td><!--
			--></tr><!--
			--><tr><!--
			--><td class="paddingOuter" valign="top" align="center" style="padding: 0px;"><!--
						--><table class="tableWrap" width="560" border="0" align="center" cellpadding="0" cellspacing="0" style=""><!--
							--><tr><!--
								--><td style="padding: 25px 0px; border-top: solid 1px #eeeeee;"><!--
									--><table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0"><!--
										--><tr><!--
											--><td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; text-transform: uppercase; font-size: 15px; font-weight: 300; color: #3a3a3a; font-family: 'Montserrat', Lato, Helvetica Neue, Helvetica, Arial, sans-serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Your Inquiry', 'zoa'); ?></span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr><!--
			--><tr><!--
								--><td><!--
									--><table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<?php 
														$fields = array();
														$sub_questions = array();
														foreach ($questions as $field) {
															loop_to_get_sub_field($field, $fields);
														}
														foreach ($fields as $question) {
															$field_value = $question['full_value'] ? $question['full_value'] : get_post_meta($_SESSION['appointment_id'], ($question['name_long'] ? $question['name_long'] : $question['name']), true);
															if (!$field_value) {
																continue;
															}
															
															if ($question['sub_depth'] == 1) {
															?>
																<tr>
																	<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; padding-right: 10px; font-size: 12px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
																		<span><?php esc_html_e($question['label'], 'zoa'); ?></span>
																	</td>
																	<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
																		<span><?php echo (is_array($field_value) ? implode(', ', $field_value) : $field_value); ?></span>
																	</td>
																</tr>
															<?php
															}
															else {
																// get parent
																foreach ($questions[0]['sub_fields'] as $parent_question) {
																	if ($parent_question['ID'] == $question['parent_id']) {
																		$sub_questions[$parent_question['ID']]['label_parent'] = __($parent_question['label'], 'zoa');
																		break;
																	}
																}
																$sub_questions[$parent_question['ID']]['sub'][$question['ID']]['label'] = $question['label'];
																$sub_questions[$parent_question['ID']]['sub'][$question['ID']]['value'] = $field_value;
															}
														}
														// Show sub questions
														foreach ($sub_questions as $sub_question) {
													?>
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; padding-right: 10px; font-size: 12px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e($sub_question['label_parent'], 'zoa'); ?></span>
														</td>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span>
																<?php 
																	foreach ($sub_question['sub'] as $sub_sub_question) {
																		esc_html_e($sub_sub_question['label'], 'zoa') . ':' . $sub_sub_question['value'] . '<br />';
																	}
																?>
															</span>
														</td>
													</tr>
													<?php
														} // endforeach
													?>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<!-- Spacer -->
							<tr>
								<td height="20" style="padding: 0; line-height: 0;">&nbsp;</td>
							</tr>
							<!-- Spacer End -->
						</table>
					</td>
				</tr>
				<tr>
					<td class="paddingOuter" valign="top" align="center" style="padding: 0px;">
						<table class="tableWrap" width="560" border="0" align="center" cellpadding="0" cellspacing="0" style="">
							<tr>
								<td style="padding: 25px 0px; border-top: solid 1px #eeeeee;">
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; text-transform: uppercase; font-size: 15px; font-weight: 300; color: #3a3a3a; font-family: 'Montserrat', Lato, Helvetica Neue, Helvetica, Arial, sans-serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Additional Question', 'zoa'); ?></span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<div><?php echo get_post_meta($_SESSION['appointment_id'], $add_questions['name'], true); ?></div>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<!-- Spacer -->
							<tr>
								<td height="20" style="padding: 0; line-height: 0;">&nbsp;</td>
							</tr>
							<!-- Spacer End -->
						</table>
					</td>
				</tr>
				
				<?php if (isset($_SESSION['p_image']) && $_SESSION['p_image']) { ?>
				<tr>
					<td class="paddingOuter" valign="top" align="center" style="padding: 0px;">
						<table class="tableWrap" width="560" border="0" align="center" cellpadding="0" cellspacing="0" style="">
							<tr>
								<td style="padding: 25px 0px; border-top: solid 1px #eeeeee;">
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; text-transform: uppercase; font-size: 15px; font-weight: 300; color: #3a3a3a; font-family: 'Montserrat', Lato, Helvetica Neue, Helvetica, Arial, sans-serif; line-height: 24px; mso-line-height-rule: exactly;">
															<span><?php esc_html_e('Inspired Photo', 'zoa')?></span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px;">
															<img src="<?php echo $_SESSION['p_image']; ?>" alt="" width="240" height="240" />
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<!-- Spacer -->
							<tr>
								<td height="20" style="padding: 0; line-height: 0;">&nbsp;</td>
							</tr>
							<!-- Spacer End -->
						</table>
					</td>
				</tr>
				<?php }?>
				<tr>
					<td class="paddingOuter" valign="top" align="center" style="padding: 0px;">
						<table class="tableWrap" width="560" border="0" align="center" cellpadding="0" cellspacing="0" style="">
							<tr>
								<td style="padding: 25px 0px; border-top: solid 1px #eeeeee;">
									<table class="tableInner" width="560" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td class="fullWidthNM" width="560" align="left" valign="top" style="padding-bottom: 0px;">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" valign="top" style="margin: 0; padding-bottom: 10px; font-size: 14px; font-weight: normal; color: #000000; font-family: 'Noto Sans JP', sans-serif; , serif; line-height: 24px; mso-line-height-rule: exactly;">
															<p style="padding-bottom: 10px">
															<?php 
																$options = get_option( 'birchschedule_options' );
																$contact_form_url = site_url() . '/contact/';
															
																if (is_user_logged_in()) {
																	echo sprintf(__('cancel_appointment_email_text', 'zoa'), 
																		'href="'. site_url('dashboard/my-account/appointment/') .'"', 
																		'href="tel:'. str_replace('-', '', $appointment_info['default']['_birs_location_phone']) .'"', 
																		$appointment_info['default']['_birs_location_phone'], 
																		'href="mailto:' . $options['store_email'] . '"',
																		$appointment_info['cancel_before']);
																}
																else {
																	echo sprintf(__('guest_cancel_appointment_email_text', 'zoa'),
																		'href="tel:'. str_replace('-', '', $appointment_info['default']['_birs_location_phone']) .'"',
																		$appointment_info['default']['_birs_location_phone'],
																		'href="' . $contact_form_url . '"',
																		$appointment_info['cancel_before']);
																}
															?>
															</p>
															<p style="padding-bottom: 10px;"><?php esc_html_e("We can't wait to see you.", 'zoa')?></p>
															<p><?php esc_html_e('The team at Chiyono Anne.', 'zoa'); ?></p>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<!-- Spacer -->
							<tr>
								<td height="20" style="padding: 0; line-height: 0;">&nbsp;</td>
							</tr>
							<!-- Spacer End -->
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
