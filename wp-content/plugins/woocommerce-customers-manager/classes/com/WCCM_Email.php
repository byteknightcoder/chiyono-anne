<?php

class WCCM_Email {

    public function __construct() {
        
    }

    public function trigger(/* $order_id */ $recipient, $subject, $content, $email_type = "notification", $user_id = null) {

        global $wccm_configuration_model, $wccm_customer_model;
        $email_configuration = $wccm_configuration_model->get_email_templates_configurations();
        $template_to_used = $email_type == 'guest_to_restered' ? $email_configuration['guest_to_registered_email_template'] : $email_configuration['customer_notification_email_template'];
        $use_footer_and_or_header = $email_type == 'guest_to_restered' ? $email_configuration['guest_to_registered_header_footer_inlcude'] : $email_configuration['customer_notification_header_footer_inlcude'];


        //$mail = new WC_Emails();
        $mail = WC()->mailer();
        $email_heading = get_bloginfo('name');

        ob_start();
        //if($use_footer_and_or_header == 'all' || $use_footer_and_or_header == 'header')
        //	$mail->email_header($email_heading );
        //$message =  ob_get_clean();		

        $content_history = $content;
        $message = str_replace('[message_body]', $content, $template_to_used);
        if (isset($user_id)) {
            $user_data = $wccm_customer_model->get_user_data($user_id);
            $billing_first_name = $wccm_customer_model->get_user_meta($user_id, 'billing_first_name');
            $billing_last_name = $wccm_customer_model->get_user_meta($user_id, 'billing_last_name');
            $shipping_first_name = $wccm_customer_model->get_user_meta($user_id, 'shipping_first_name');
            $shipping_last_name = $wccm_customer_model->get_user_meta($user_id, 'shipping_last_name');

            $message = isset($user_data->first_name) ? str_replace('{first_name}', $user_data->first_name, $message) : str_replace('{first_name}', "", $message);
            $message = isset($user_data->last_name) ? str_replace('{last_name}', $user_data->last_name, $message) : str_replace('{last_name}', "", $message);
            $message = $billing_first_name ? str_replace('{billing_first_name}', $billing_first_name, $message) : str_replace('{billing_first_name}', "", $message);
            $message = $billing_last_name ? str_replace('{billing_last_name}', $billing_last_name, $message) : str_replace('{billing_last_name}', "", $message);
            $message = $shipping_first_name ? str_replace('{shipping_first_name}', $shipping_first_name, $message) : str_replace('{shipping_first_name}', "", $message);
            $message = $shipping_last_name ? str_replace('{shipping_last_name}', $shipping_last_name, $message) : str_replace('{shipping_last_name}', "", $message);
            //for history email
            $content_history = isset($user_data->first_name) ? str_replace('{first_name}', $user_data->first_name, $content_history) : str_replace('{first_name}', "", $content_history);
            $content_history = isset($user_data->last_name) ? str_replace('{last_name}', $user_data->last_name, $content_history) : str_replace('{last_name}', "", $content_history);
            $content_history = $billing_first_name ? str_replace('{billing_first_name}', $billing_first_name, $content_history) : str_replace('{billing_first_name}', "", $content_history);
            $content_history = $billing_last_name ? str_replace('{billing_last_name}', $billing_last_name, $content_history) : str_replace('{billing_last_name}', "", $content_history);
            $content_history = $shipping_first_name ? str_replace('{shipping_first_name}', $shipping_first_name, $content_history) : str_replace('{shipping_first_name}', "", $content_history);
            $content_history = $shipping_last_name ? str_replace('{shipping_last_name}', $shipping_last_name, $content_history) : str_replace('{shipping_last_name}', "", $content_history);
        }
        echo $message;
        //ob_start();
        if ($use_footer_and_or_header == 'all' || $use_footer_and_or_header == 'footer')
            $mail->email_footer();
        //$message .=  ob_get_clean();
        $message = ob_get_contents();
        ob_end_clean();
        add_filter('wp_mail_from_name', array(&$this, 'wp_mail_from_name'), 99, 1);
        add_filter('wp_mail_from', array(&$this, 'wp_mail_from'));
        $res = $mail->send($recipient, stripslashes($subject), stripslashes($message));
        if ($res == true) {
            if ($email_type == 'notification') {//save to history to show in customer detail
                $current_history = get_user_meta($user_id, 'ch_wccm_email_history', true);
                if ($current_history != '') {
                    $arr_history = @json_decode($current_history, true);
                } else {
                    $arr_history = array();
                }
                $who_user_info = get_userdata(get_current_user_id());
                $arr_history[] = array('usersend' => $who_user_info->user_login, 'subject' => $subject, 'content' => str_replace(array("\r\n","\r"), "", strip_tags($content_history,'<p>')), 'createdtime' => date('Y-m-d H:i:s'));
                $json_data = json_encode($arr_history, JSON_UNESCAPED_UNICODE);
                $wccm_customer_model->update_user_meta($user_id, 'ch_wccm_email_history',$json_data);
            }
        }
        remove_filter('wp_mail_from_name', array(&$this, 'wp_mail_from_name'));
        remove_filter('wp_mail_from', array(&$this, 'wp_mail_from'));
    }

    public function wp_mail_from_name($name) {
        $sender_name = WCCM_Options::get_option('email_sender_name', '');
        return $sender_name == '' ? get_bloginfo('name') : $sender_name;
    }

    public function wp_mail_from($content_type) {
        $email_sender_email = WCCM_Options::get_option('email_sender_email', '');
        return $email_sender_email == '' ? WCCM_Email::get_no_reply_address() : $email_sender_email;
    }

    public static function get_no_reply_address() {
        $server_headers = function_exists('apache_request_headers') ? apache_request_headers() : wccm_apache_request_headers();
        $domain = $server_headers['Host'];
        $domain = str_replace("www.", "", $domain);
        return 'noreply@' . $domain;
    }

    public function send_new_user_notification_email($email, $login, $password, $user_id = null) {
        global $wccm_configuration_model;
        //Notification email
        $default_subject = __('New account', 'woocommerce-customers-manager');
        //$subject = __('New account', 'woocommerce-customers-manager');
        //$text = sprintf (__('New account has been created. Login using the following credentials.<br/>User: %s<br/>Password: %s<br/>', 'woocommerce-customers-manager'), $_REQUEST['user_login'],$_REQUEST['pass1']);
        $subject = $wccm_configuration_model->get_options('email_new_user_subject', $default_subject);
        $subject = trim($subject) == "" ? $default_subject : $subject;
        $text = $wccm_configuration_model->get_options('email_new_user_body', __('New account has been created. Login using the following credentials.<br/>User: %login<br/>Password: %password<br/>', 'woocommerce-customers-manager'));
        $text = str_replace("%login", $login, $text);
        $text = str_replace("%password", $password, $text);
        $text = nl2br($text);
        $this->trigger($email, $subject, $text, $email_type = "notification", $user_id);
    }

}
