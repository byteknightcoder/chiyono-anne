<?php

add_action('woocommerce_order_actions', 'ch_wc_add_order_meta_box_action');
function ch_wc_add_order_meta_box_action($actions) {
    global $theorder;
    if ($theorder->payment_method !== 'bacs' || $theorder->has_status('completed')) {
        return $actions;
    }
    $actions['wc_custom_order_action'] = __('未入金の催促メール送信', 'zoa');
    return $actions;
}

add_action('woocommerce_order_action_wc_custom_order_action', 'ch_wc_process_order_meta_box_action');
function ch_wc_process_order_meta_box_action($order) {
    // add the order note
    // translators: Placeholders: %s is a user's display name
    $note = __('未入金の催促メール送信', 'zoa');
    $order->add_order_note($note);

    // add the flag
    update_post_meta($order->id, '_wc_order_sent_reminder_money_bacs', 'yes');
    // send email
    $site_title = get_bloginfo('name');
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
    $subject = trim(get_option('common_notion_subject_email_rm', false));
    $_billing_email = get_post_meta($order->id, '_billing_email', true);
    $message = trim(get_option('common_notion_content_email_rm', false));
    $message = stripslashes($message);
    $message = nl2br($message);
    $last_name = get_post_meta($order->id, '_billing_last_name', true);
    $first_name = get_post_meta($order->id, '_billing_first_name', true);
    $message = str_replace('{billing_last_name}', $last_name, $message);
    $message = str_replace('{billing_first_name}', $first_name, $message);
    $current_time = current_time('timestamp');
    $send_date = strtolower(date_i18n('n月d日 (D)', $current_time));
    $message = str_replace('{send_date}', $send_date, $message);
    $current_time = current_time('timestamp');
    $delay = 7;
    $date_exp = strtotime("+" . $delay . " day", $current_time);
    $expire_date = strtolower(date_i18n('n月d日 (D)', $date_exp));
    $message = str_replace('{expire_date}', $expire_date, $message);
    //bacs info
    $bacs_accounts_info = get_option('woocommerce_bacs_accounts');
    if (!empty($bacs_accounts_info)) {
        foreach ($bacs_accounts_info as $account) {
            $account_name = esc_attr(wp_unslash($account['account_name']));
            $bank_name = esc_attr(wp_unslash($account['bank_name']));
            $account_number = esc_attr($account['account_number']);
            $sort_code = esc_attr($account['sort_code']);
            $iban_code = esc_attr($account['iban']);
            $bic_code = esc_attr($account['bic']);
            ob_start();
        ?>
            <ul style="line-height: 0.7 !important;list-style-type: none;padding-left: 0;margin-top: -50px !important;">
                <li style="margin-left: 0;"><?php esc_html_e('Account name', 'woocommerce'); ?>: <strong><?php echo $account_name; ?></strong></li>
                <li style="margin-left: 0;"><?php esc_html_e('Account number', 'woocommerce'); ?>: <strong><?php echo $account_number; ?></strong></li>
                <li style="margin-left: 0;"><?php esc_html_e('Bank name', 'woocommerce'); ?>: <strong><?php echo $bank_name; ?></strong></li>
                <li style="margin-left: 0;"><?php esc_html_e('支店', 'woocommerce'); ?>: <strong><?php echo $sort_code; ?></strong></li>
                <li style="margin-left: 0;"><?php esc_html_e('口座種別', 'woocommerce'); ?>: <strong><?php echo $iban_code; ?></strong></li>
                <?php if (!empty($bic_code)) : ?>
                    <li style="margin-left: 0;"><?php esc_html_e('BIC / Swift', 'woocommerce'); ?>: <strong><?php echo $bic_code; ?></strong></li>
                <?php endif; ?>
            </ul>
        <?php
            $contents = ob_get_contents();
            ob_end_clean();
        } // End foreach
        $message = str_replace('{bacs_info}', $contents, $message);
    } // end if

    // get order_info
    ob_start();
    $text_align = is_rtl() ? 'right' : 'left';

    if ($sent_to_admin) {
        $before = '<a class="link" href="' . esc_url($order->get_edit_order_url()) . '">';
        $after = '</a>';
    } else {
        $before = '';
        $after = '';
    }

    /* translators: %s: Order ID. */
    // echo wp_kses_post($before . sprintf(__('[Order #%s]', 'woocommerce') . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format('c'), wc_format_datetime($order->get_date_created())));
?>
    <!--    </h2>-->
    <div style="white-space: initial !important;">
        <table class="td" cellspacing="0" cellpadding="6" style="width: 100%;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <thead>
                <tr>
                    <th class="td" scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Product', 'woocommerce'); ?></th>
                    <th class="td" scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
                    <th class="td" scope="col" style="width: 30%;text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Price', 'woocommerce'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    echo wc_get_email_order_items($order, array( // WPCS: XSS ok.
                        'show_sku' => $sent_to_admin,
                        'show_image' => false,
                        'image_size' => array(32, 32),
                        'plain_text' => $plain_text,
                        'sent_to_admin' => $sent_to_admin,
                    ));
                ?>
            </tbody>
            <tfoot>
                <?php
                    $totals = $order->get_order_item_totals();

                    if ($totals) {
                        $text_align = 'right';
                        $i = 0;
                        foreach ($totals as $total) {
                            $i++;
                        ?>
                            <tr>
                                <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr($text_align); ?>; <?php echo ( 1 === $i ) ? '' : ''; ?>"><?php echo wp_kses_post($total['label']); ?></th>
                                <td class="td" style="text-align:<?php echo esc_attr($text_align); ?>; <?php echo ( 1 === $i ) ? '' : ''; ?>"><?php echo wp_kses_post($total['value']); ?></td>
                            </tr>
                        <?php
                        }
                    }
                    if ($order->get_customer_note()) : ?>
                        <tr>
                            <th class="td" scope="row" colspan="2" style="text-align: <?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Note:', 'woocommerce'); ?></th>
                            <td class="td" style="text-align: <?php echo esc_attr($text_align); ?>;"><?php echo wp_kses_post(wptexturize($order->get_customer_note())); ?></td>
                        </tr>
                <?php endif; ?>
            </tfoot>
        </table>
    </div>
<?php
    $contents = ob_get_contents();
    ob_end_clean();
    $message = str_replace('{order_table}', $contents, $message);
    // end
    // first name admin
    $current_user = wp_get_current_user();
    $user_firstname = $current_user->user_lastname;
    $message = str_replace('{admin_first_name}', $user_firstname, $message);
    // end
    $attachments = array();
    add_filter('wp_mail_content_type', 'ch_set_html_content_type');
    wp_mail($_billing_email, $subject, $message, $headers, $attachments);
    remove_filter('wp_mail_content_type', 'ch_set_html_content_type');
}

// Call back function for admin_menu
add_action('admin_menu', 'setup_menu_rm');
function setup_menu_rm() {
    add_submenu_page('woocommerce', __('Email content of reminder BACS', 'zoa'), __('Email content of reminder BACS', 'zoa'), 'manage_options', 'po_settings_review_rm', 'po_settings_review_rm', '', 13);
}

function po_settings_review_rm() {
    if (isset($_POST['save_bd_setting'])) {
        update_option('common_notion_subject_email_rm', $_REQUEST['common_notion_subject_email_rm']);
        update_option('common_notion_content_email_rm', $_REQUEST['common_notion_content_email_rm']);
    }
    ob_start();

    if (isset($_POST['save_bd_setting'])) : ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
            <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button></div>
        <?php
    endif;
?>
    <h3><?php esc_html_e('Settings Page:', 'zoa'); ?></h3>
    <hr/>
    <form action="" method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Email subject', 'zoa'); ?></label></th>
                    <td>
                        <textarea rows="2" cols="100" name="common_notion_subject_email_rm"><?php echo get_option('common_notion_subject_email_rm'); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Email content', 'zoa'); ?></label></th>
                    <td>
                        <?php
                            $common_notion_content_email = get_option('common_notion_content_email_rm');
                            $common_notion_content_email = stripslashes($common_notion_content_email);
                            wp_editor($common_notion_content_email, 'common_notion_content_email_rm', array('textarea_name' => 'common_notion_content_email_rm', 'media_buttons' => false, 'editor_height' => 250, 'teeny' => true));
                        ?>
                        <p>
                            Shortcode: {billing_last_name}, {billing_first_name}, {send_date}, {expire_date}, {order_table}, {bacs_info}, {admin_first_name}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="submit" name="save_bd_setting" id="submit" class="button button-primary" value="Save">
    </form>
<?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}
