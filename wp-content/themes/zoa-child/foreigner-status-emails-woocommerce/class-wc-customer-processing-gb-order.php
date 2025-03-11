<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WC_Email')) {
    return;
}

/**
 * Class WC_Customer_Cancel_Order
 */
class WC_Customer_Processing_Gb_Order extends WC_Email {

    /**
     * Create an instance of the class.
     *
     * @access public
     * @return void
     */
    function __construct() {
        // Email slug we can use to filter other data.
        $this->id = 'WC_Customer_Processing_Gb_Order';
        $this->title = __('Processing foreigner', 'zoa');
        $this->description = __('An email sent to the customer when an order is processing foreigner.', 'zoa');
        // For admin area to let the user know we are sending this email to customers.
        $this->customer_email = true;
        $this->heading = __('Processing foreigner', 'zoa');
        // translators: placeholder is {blogname}, a variable that will be substituted when email is sent out
        $this->subject = sprintf(__('Processing foreigner', 'zoa'), '{blogname}');

        // Template paths.
        $this->template_html = 'emails/customer-processing-foreigner.php';
        $this->template_plain = 'emails/plain/customer-processing-foreigner.php';
        $this->template_base = dirname(dirname(__FILE__)) . '/woocommerce/';

        // Action to which we hook onto to send the email.
//        add_action('woocommerce_order_status_pending_to_cancelled_notification', array($this, 'trigger'));
//        add_action('woocommerce_order_status_on-hold_to_cancelled_notification', array($this, 'trigger'));
//processing foreigner
//shipped foreigner
        parent::__construct();
    }

    function trigger($order_id) {
        $this->object = wc_get_order($order_id);

        if (version_compare('3.0.0', WC()->version, '>')) {
            $order_email = $this->object->billing_email;
        } else {
            $order_email = $this->object->get_billing_email();
        }

        $this->recipient = $order_email;


        if (!$this->is_enabled() || !$this->get_recipient()) {
            return;
        }

        $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

    public function get_content_html() {
        return wc_get_template_html($this->template_html, array(
            'order' => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text' => false,
            'email' => $this
                ), '', $this->template_base);
    }

    /**
     * Get content plain.
     *
     * @return string
     */
    public function get_content_plain() {
        return wc_get_template_html($this->template_plain, array(
            'order' => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text' => true,
            'email' => $this
                ), '', $this->template_base);
    }

}
