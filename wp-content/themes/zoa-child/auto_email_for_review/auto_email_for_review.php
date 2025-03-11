<?php

/**
 * Call back function
 */
function aefr_set_html_content_type() {
    return 'text/html';
}

/**
 * Call back function for admin_menu
 */
add_action('admin_menu', 'aefr_setup_menu');
function aefr_setup_menu() {
    add_submenu_page('woocommerce', __('Auto email for review', 'zoa'), __('Auto email for review', 'zoa'), 'manage_options', 'aefr_settings_auto_email_review', 'aefr_settings_auto_email_review', '', 7);
}

function aefr_settings_auto_email_review() {
    if (isset($_POST['aefr_setting_send'])) {
        update_option('aefr_common_notion_subject_email', $_REQUEST['aefr_common_notion_subject_email']);
        update_option('aefr_common_notion_content_email', $_REQUEST['aefr_common_notion_content_email']);
        //for English
        update_option('aefr_common_notion_subject_email_en', $_REQUEST['aefr_common_notion_subject_email_en']);
        update_option('aefr_common_notion_content_email_en', $_REQUEST['aefr_common_notion_content_email_en']);
    }
    if (isset($_POST['aefr_setting_send_test'])) {
        $email = $_REQUEST['email'];
        $all_emails[] = $email;
        if (!empty($all_emails)) {
            update_option('aefr_common_notion_subject_email', $_REQUEST['aefr_common_notion_subject_email']);
            update_option('aefr_common_notion_content_email', $_REQUEST['aefr_common_notion_content_email']);
            //for english
            update_option('aefr_common_notion_subject_email_en', $_REQUEST['aefr_common_notion_subject_email_en']);
            update_option('aefr_common_notion_content_email_en', $_REQUEST['aefr_common_notion_content_email_en']);
            $site_title = get_bloginfo('name');
            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
            $attachments = array();
            $subject = trim($_REQUEST['aefr_common_notion_subject_email']);
            $message = trim(get_option('aefr_common_notion_content_email', false));
            $message = stripslashes($message);
            //for english
            $subject_en = trim($_REQUEST['aefr_common_notion_subject_email_en']);
            $message_en = trim(get_option('aefr_common_notion_content_email_en', false));
            $message_en = stripslashes($message);
            $check = 0;
            if (!empty($subject) && !empty($message)) {
                add_filter('wp_mail_content_type', 'aefr_set_html_content_type');
                foreach ($all_emails as $value) {
                    wp_mail($value, $subject, $message, $headers, $attachments);
                    //for english
                    wp_mail($value, $subject_en, $message_en, $headers, $attachments);
                }
                $check = 1;
                remove_filter('wp_mail_content_type', 'aefr_set_html_content_type');
            }
            if ($check == 1) {
                $err_send = __("Send test email completed.", 'zoa');
            }
        } else {
            $err_send = __("Don't have old orders to send emails", 'zoa');
        }
        //end emails
    }
    ob_start();
    ?>
<?php if (isset($_POST['aefr_setting_send'])) : ?>
    <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
        <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
    </div>
<?php endif;
    if (isset($err_send)) : ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong><?php echo $err_send; ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button>
        </div>
<?php endif; ?>
    <h3><?php esc_html_e('Cron job setting on server:', 'zoa'); ?> <i><?php echo admin_url('admin-ajax.php?action=auto_email_for_review_hlASflhReh345Ksdg5H683weMEK4098822FMasur'); ?></i></h3>
    <i><?php esc_html_e('NOTE: each email send only once for each user.', 'zoa'); ?></i>
    <hr />
    <form action="" method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Email subject', 'zoa'); ?></label></th>
                    <td>
                        <textarea rows="2" cols="100" name="aefr_common_notion_subject_email"><?php echo get_option('aefr_common_notion_subject_email'); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Email subject English', 'zoa'); ?></label></th>
                    <td>
                        <textarea rows="2" cols="100" name="aefr_common_notion_subject_email_en"><?php echo get_option('aefr_common_notion_subject_email_en'); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Email content', 'zoa'); ?></label></th>
                    <td>
                        <?php
                        $content_email = get_option('aefr_common_notion_content_email');
                        $content_email = stripslashes($content_email);
                        wp_editor($content_email, 'aefr_common_notion_content_email', array('textarea_name' => 'aefr_common_notion_content_email', 'media_buttons' => false, 'editor_height' => 250, 'teeny' => true));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Email content English', 'zoa'); ?></label></th>
                    <td>
                        <?php
                            $content_email = get_option('aefr_common_notion_content_email_en');
                            $content_email = stripslashes($content_email);
                            wp_editor($content_email, 'aefr_common_notion_content_email_en', array('textarea_name' => 'aefr_common_notion_content_email_en', 'media_buttons' => false, 'editor_height' => 250, 'teeny' => true));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Email to test', 'zoa'); ?></label></th>
                    <td>
                        <input type="text" name="email" />
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="submit" name="aefr_setting_send" id="submit" class="button button-primary" value="Save setting">
        <input type="submit" onclick="return confirm('Are you sure?')" name="aefr_setting_send_test" id="submit" class="button button-primary" value="Send test email">
    </form>
    <?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

add_action('wp_ajax_auto_email_for_review_hlASflhReh345Ksdg5H683weMEK4098822FMasur', 'auto_email_for_review_hlASflhReh345Ksdg5H683weMEK4098822FMasur');
add_action('wp_ajax_nopriv_auto_email_for_review_hlASflhReh345Ksdg5H683weMEK4098822FMasur', 'auto_email_for_review_hlASflhReh345Ksdg5H683weMEK4098822FMasur');

function auto_email_for_review_hlASflhReh345Ksdg5H683weMEK4098822FMasur() {
    $args = array(
        'status' => array( 'wc-completed','wc-shipped-gb' ),
        'return' => 'ids',
        'posts_per_page' => -1
    );
    $current_time = current_time('timestamp');
    $args['date_query'] = array(
        array('column' => 'post_modified',
            'after' => '2022-04-02'
        )
    );

    $orders = wc_get_orders($args);
    $current_time = current_time('timestamp');
    if (!empty($orders)) {
        $all_emails = array();
        $id_emails = array();

        foreach ($orders as $order_id) {
            $modify_date = get_the_modified_date('Y-m-d H:i:s', $order_id);
            $after14days = date_i18n('Y-m-d H:i:s', strtotime('+14 days', strtotime($modify_date)));
            $_order_total = get_post_meta($order_id, '_order_total', true);
            if ($_order_total != '') {
                $_order_total = (float) $_order_total;
            }
            if (strtotime($after14days) <= $current_time && $_order_total > 30000) {
                $_billing_email = get_post_meta($order_id, '_billing_email', true);
                $_sent_email_review = get_post_meta($order_id, '_sent_email_review', true);
                if (!empty($_billing_email) && $_sent_email_review != 'yes') {
                    $all_emails[$order_id] = $_billing_email;
                    $id_emails[$order_id] = $_billing_email;
                }
            }
        }
//        var_dump($id_emails);
//        exit();
        if (!empty($all_emails)) {
            wp_mail('chien.lexuan@gmail.com', 'Auto email for review.', json_encode($id_emails));
            $site_title = get_bloginfo('name');
            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
            $attachments = array();
            //Remove duplidate emails
            $all_emails = array_unique($all_emails);
            $subject = trim(get_option('aefr_common_notion_subject_email', false));
            $message = trim(get_option('aefr_common_notion_content_email', false));
            $message = stripslashes($message);
            $check = 0;
            if (!empty($subject) && !empty($message)) {
                add_filter('wp_mail_content_type', 'aefr_set_html_content_type');
                foreach ($all_emails as $or_id => $value) {
                    $order = wc_get_order($or_id);
                    if ($order->has_status('shipped-gb')) {
                        $subject = trim(get_option('aefr_common_notion_subject_email_en', false));
                        $message = trim(get_option('aefr_common_notion_content_email_en', false));
                        $message = stripslashes($message);
                    } else {
                        $subject = trim(get_option('aefr_common_notion_subject_email', false));
                        $message = trim(get_option('aefr_common_notion_content_email', false));
                        $message = stripslashes($message);
                    }
                    wp_mail($value, $subject, $message, $headers, $attachments);
                    $id_sent = array_keys($id_emails, $value);
                    if (!empty($id_sent)) {
                        foreach ($id_sent as $value_id) {
                            update_post_meta($value_id, '_sent_email_review', 'yes');
                            update_post_meta($value_id, '_sent_email_review_date', date_i18n('Y-m-d'));
                        }
                    }
                }
                $check = 1;
                remove_filter('wp_mail_content_type', 'aefr_set_html_content_type');
            }
            if ($check == 1) {
                $err_send = __('Send emails completed.', 'zoa');
            }
        } else {
            $err_send = __("Don't have old orders to send emails", 'zoa');
        }
    } else {
        $err_send = __("Don't have old orders to send emails", 'zoa');
    }
    echo $err_send;
    // end emails
    exit();
}
