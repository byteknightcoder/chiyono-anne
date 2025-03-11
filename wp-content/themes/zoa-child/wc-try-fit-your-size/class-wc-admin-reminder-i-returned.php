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
 * Class WC_Admin_Reminder_I_Returned
 */
class WC_Admin_Reminder_I_Returned extends WC_Email {

    /**
     * Create an instance of the class.
     *
     * @access public
     * @return void
     */
    function __construct() {
        // Email slug we can use to filter other data.
        $this->id = 'WC_Admin_Reminder_I_Returned';
        $this->title = __('Reminder I Returned To Admin', 'zoa');
        $this->description = __('An email sent to the admin Reminder I returned', 'zoa');
        // For admin area to let the user know we are sending this email to customers.
        $this->heading = __('Reminder I Returned To Admin', 'zoa');
        // translators: placeholder is {blogname}, a variable that will be substituted when email is sent out
        $this->subject = sprintf(__('Reminder I Returned To Admin', 'zoa'), '{blogname}');

        // Template paths.
        $this->template_html = 'emails/admin-reminder-i-returned.php';
        $this->template_plain = 'emails/plain/admin-reminder-i-returned.php';
        $this->template_base = dirname(dirname(__FILE__)) . '/woocommerce/';

        // Action to which we hook onto to send the email.
//        add_action('woocommerce_order_status_pending_to_cancelled_notification', array($this, 'trigger'));
//        add_action('woocommerce_order_status_on-hold_to_cancelled_notification', array($this, 'trigger'));

        parent::__construct();
        $this->recipient = $this->get_option('recipient', get_option('admin_email'));
    }

    function trigger($order_id, $order = false) {
        $this->setup_locale();

        if ($order_id && !is_a($order, 'WC_Order')) {
            $order = wc_get_order($order_id);
        }

        if (is_a($order, 'WC_Order')) {
            $this->object = $order;
            $this->placeholders['{order_date}'] = wc_format_datetime($this->object->get_date_created());
            $this->placeholders['{order_number}'] = $this->object->get_order_number();
        }

        if ($this->is_enabled() && $this->get_recipient()) {
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
        $this->restore_locale();
    }

    public function get_content_html() {
        return wc_get_template_html($this->template_html, array(
            'order' => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => true,
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
            'sent_to_admin' => true,
            'plain_text' => true,
            'email' => $this
                ), '', $this->template_base);
    }

    public function init_form_fields() {
        /* translators: %s: list of placeholders */
        $placeholder_text = sprintf(__('Available placeholders: %s', 'woocommerce'), '<code>' . implode('</code>, <code>', array_keys($this->placeholders)) . '</code>');
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable this email notification', 'woocommerce'),
                'default' => 'yes',
            ),
            'recipient' => array(
                'title' => __('Recipient(s)', 'woocommerce'),
                'type' => 'text',
                /* translators: %s: WP admin email */
                'description' => sprintf(__('Enter recipients (comma separated) for this email. Defaults to %s.', 'woocommerce'), '<code>' . esc_attr(get_option('admin_email')) . '</code>'),
                'placeholder' => '',
                'default' => '',
                'desc_tip' => true,
            ),
            'subject' => array(
                'title' => __('Subject', 'woocommerce'),
                'type' => 'text',
                'desc_tip' => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_subject(),
                'default' => '',
            ),
            'heading' => array(
                'title' => __('Email heading', 'woocommerce'),
                'type' => 'text',
                'desc_tip' => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_heading(),
                'default' => '',
            ),
            'additional_content' => array(
                'title' => __('Additional content', 'woocommerce'),
                'description' => __('Text to appear below the main email content.', 'woocommerce') . ' ' . $placeholder_text,
                'css' => 'width:400px; height: 75px;',
                'placeholder' => __('N/A', 'woocommerce'),
                'type' => 'textarea',
                'default' => $this->get_default_additional_content(),
                'desc_tip' => true,
            ),
            'email_type' => array(
                'title' => __('Email type', 'woocommerce'),
                'type' => 'select',
                'description' => __('Choose which format of email to send.', 'woocommerce'),
                'default' => 'html',
                'class' => 'email_type wc-enhanced-select',
                'options' => $this->get_email_type_options(),
                'desc_tip' => true,
            ),
        );
    }

}
