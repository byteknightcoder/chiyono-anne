<?php

//Register new status
add_action('init', 'register_ch_gb_my_new_order_statuses');

function register_ch_gb_my_new_order_statuses() {
    register_post_status('wc-shipped-gb', array(
        'label' => __('Shipped foreigner', 'zoa'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Shipped foreigner <span class="count">(%s)</span>', 'Shipped foreigner <span class="count">(%s)</span>', 'zoa')
    ));
    register_post_status('wc-processing-gb', array(
        'label' => __('Processing foreigner', 'zoa'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Processing foreigner <span class="count">(%s)</span>', 'Processing foreigner <span class="count">(%s)</span>', 'zoa')
    ));
}

add_filter('wc_order_statuses', 'ch_gb_my_new_wc_order_statuses');

// Add to list of WC Order statuses
function ch_gb_my_new_wc_order_statuses($order_statuses) {
    $order_statuses['wc-processing-gb'] = __('Processing foreigner', 'zoa');
    $order_statuses['wc-shipped-gb'] = __('Shipped foreigner', 'zoa');
    return $order_statuses;
}

/**
 * Class Custom_WC_Email
 */
class Ch_Customer_Gb_Custom_WC_Email {

    /**
     * Custom_WC_Email constructor.
     */
    public function __construct() {
// Filtering the emails and adding our own email.
        add_filter('woocommerce_email_classes', array($this, 'ch_customer_gb_register_email'), 90, 1);
// Absolute path to the plugin folder.
    }

    /**
     * @param array $emails
     *
     * @return array
     */
    public function ch_customer_gb_register_email($emails) {
        require_once dirname(__FILE__) . '/class-wc-customer-processing-gb-order.php';
        require_once dirname(__FILE__) . '/class-wc-customer-shipped-gb-order.php';

        $emails['WC_Customer_Processing_Gb_Order'] = new WC_Customer_Processing_Gb_Order();
        $emails['WC_Customer_Shipped_Gb_Order'] = new WC_Customer_Shipped_Gb_Order();
        return $emails;
    }

}

new Ch_Customer_Gb_Custom_WC_Email();

//send email when status from sent-sample to completenotrefund
add_action('woocommerce_order_status_pending_to_processing-gb', 'ch_send_email_customer_processing_gb');
add_action('woocommerce_order_status_on-hold_to_processing-gb', 'ch_send_email_customer_processing_gb');
add_action('woocommerce_order_status_processing_to_processing-gb', 'ch_send_email_customer_processing_gb');

function ch_send_email_customer_processing_gb($order_id) {
    $mailer = WC()->mailer();
    $mails = $mailer->get_emails();
    $email_to_send = 'WC_Customer_Processing_Gb_Order';
    if (!empty($mails)) {
        foreach ($mails as $mail) {
            if ($mail->id == $email_to_send) {
                $mail->trigger($order_id);
            }
        }
    }
}

add_action('woocommerce_order_status_processing-gb_to_shipped-gb', 'ch_send_email_customer_shipped_gb');

function ch_send_email_customer_shipped_gb($order_id) {
    $mailer = WC()->mailer();
    $mails = $mailer->get_emails();
    $email_to_send = 'WC_Customer_Shipped_Gb_Order';
    if (!empty($mails)) {
        foreach ($mails as $mail) {
            if ($mail->id == $email_to_send) {
                $mail->trigger($order_id);
            }
        }
    }
}
