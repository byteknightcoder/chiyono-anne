<?php
global $current_user;
get_currentuserinfo();
?>
<?php
/*
  if (! is_user_logged_in()) :
  else:
  $current_user = wp_get_current_user();
  $fname = get_user_meta( $current_user->ID, 'first_name', true );
  $lname = get_user_meta( $current_user->ID, 'last_name', true );
  echo '<p>You are'.$lname.$fname.'</p>';
  endif;
 */
?>
<?php if (!is_user_logged_in()) { ?>
    <ul class="tabs">
        <li class="ch_register_tab"><a href="#login-info"><i class="oecicon oecicon-single-03"></i><?php esc_html_e('Sign up', 'zoa'); ?></a></li>
        <li class="ch_login_tab"><a href="#signin-info"><i class="oecicon oecicon-log-in-2"></i><?php esc_html_e('Login', 'zoa'); ?></a></li>
    </ul>
<?php } ?>
<div id="reservationFormCustomer" class="form_entry<?php if (!is_user_logged_in()) { ?> tab_container<?php } ?>">
    <?php
     //if(isset($_SESSION['is_booked_special']) && !is_user_logged_in()){
         //if it is specialappointment booking and not yet loggin, so don't show custome info fields
     //}else{
    ?>
    <div id="login-info" class="confirm-box ch-step2<?php if (!is_user_logged_in()) { ?> tab_content<?php } ?>">
        <div class="row flex-justify-center pad_row">
            <fieldset class="confirm_info col-md-12 col-xs-12">
                <div class="form-row">
                    <div class="field-wrapper">
                        <div class="flex-row pad_row">
                            <div class="col-md-6 col-xs-12">
                                <label class="form-row__label"><?php esc_html_e('Last Name', 'zoa'); ?><i class="required-asterisk booked-icon booked-icon-required"></i></label>
                                <input type="text" required="required" id="user_lastname" name="user_lastname" value="<?php echo $current_user->user_lastname; ?>"/>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <label class="form-row__label"><?php esc_html_e('First Name', 'zoa'); ?><i class="required-asterisk booked-icon booked-icon-required"></i></label>
                                <input type="text" required="required" id="user_firstname" name="user_firstname" value="<?php echo $current_user->user_firstname; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="field-wrapper">
                        <div class="flex-row pad_row">
                            <div class="col-md-6 col-xs-12">
                                <label class="form-row__label"><?php esc_html_e('Last Name Kana', 'zoa'); ?></label>
                                <input type="text" id="billing_last_name_kana" name="billing_last_name_kana" value="<?php echo $current_user->billing_last_name_kana; ?>"/>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <label class="form-row__label"><?php esc_html_e('First Name Kana', 'zoa'); ?></label>
                                <input type="text" id="billing_first_name_kana" name="billing_first_name_kana" value="<?php echo $current_user->billing_first_name_kana; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="field-wrapper">
                        <div class="flex-row pad_row">
                            <div class="col-md-12 col-xs-12">
                                <label class="form-row__label"><?php esc_html_e('Email', 'zoa'); ?><i class="required-asterisk booked-icon booked-icon-required"></i></label>
                                <input <?php if (is_user_logged_in()) {echo 'readonly="true"'; } ?>type="email" required="required" id="email" name="email" value="<?php echo $current_user->user_email; ?>"/>
                                <div class="status msg_exist_email" style="display: none;"><i class="booked-icon booked-icon-alert" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;<?php _e("アカウント登録済みのメールアドレスです。ログインしてください。",'zoa'); ?></div>
                                <?php
                                if (is_user_logged_in()) {
                                    ?>
                                <p class="notice_email_change"><?php printf(__('*if you want to change email, change from <a href="%s">my page</a>','zoa'), home_url('my-account')); ?></p>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (!is_user_logged_in()) {
                    ?>
                    <div id="password-row" class="form-row">
                        <div class="field-wrapper">
                            <div class="flex-row pad_row">
                                <div class="col-md-12 col-xs-12">
                                    <label class="form-row__label"><?php _e('パスワード', 'zoa'); ?><i class="required-asterisk booked-icon booked-icon-required"></i></label>
                                    <input type="password" required="required" id="b_password" name="b_password" value=""/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="birthday-row" class="form-row">
                        <div class="field-wrapper">
                            <div class="flex-row pad_row">
                                <div class="col-md-12 col-xs-12">
                                    <label class="form-row__label"><?php _e('生年月日', 'zoa'); ?><i class="required-asterisk booked-icon"></i></label>
                                    <input readonly class="form-control" type="text" name="account_birth_data" id="account_birth" placeholder="YYYY-MM-DD">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-row">
                    <div class="field-wrapper">
                        <div class="flex-row pad_row">
                            <div class="col-md-12 col-xs-12">

                                <label class="form-row__label"><?php esc_html_e('Phone', 'zoa'); ?></label>
                                <input type="text" id="phone" name="phone" value="<?php echo $current_user->billing_phone; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (!is_user_logged_in()) {
                    $checked='';
                    $hide='';
                    $detect_class='';
                    if(isset($_SESSION['is_booked_special']) && $_SESSION['is_booked_special']=='yes'){
                        $hide='display: none;';
                        $detect_class='ch_is_specialappointment';
                    }
                    $checked='checked="true" ';
                    ?>
                <div style="<?php echo $hide; ?>" class="form-row <?php echo $detect_class; ?>">
                        <div class="field-wrapper">
                            <label class="form-row__label"><input <?php echo $checked; ?> type="checkbox" id="is_register" name="is_register" id="is_register" value="1"><?php _e('Do you want to register as client?', 'zoa'); ?></label>
                        </div>
                    </div>
                <?php } ?>
            </fieldset>
        </div>
        <div class="status msg2">&nbsp;</div>
    </div>
     <?php //} ?>
    <?php if (!is_user_logged_in()) { ?>
        <div id="signin-info" class="booked-login<?php if (!is_user_logged_in()) { ?> tab_content<?php } ?>">
            <div class="row flex-justify-center pad_row">
                <fieldset class="sign_info col-md-12 col-xs-12">
                    <?php echo do_shortcode('[booked-login]'); ?>
                    <div><a class="ch_to_register" href="javascript:;">まだご登録されてない方はこちら</a></div>
                </fieldset>
            </div>
        </div>
    <?php } ?>
</div>