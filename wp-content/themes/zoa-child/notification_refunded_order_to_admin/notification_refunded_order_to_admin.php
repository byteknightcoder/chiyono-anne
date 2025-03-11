<?php

/**
 * Class Custom_WC_Email
 */
class Ch_Admin_Custom_WC_Email {

    /**
     * Custom_WC_Email constructor.
     */
    public function __construct() {
// Filtering the emails and adding our own email.
        add_filter('woocommerce_email_classes', array($this, 'admin_register_email'), 90, 1);
// Absolute path to the plugin folder.
    }

    /**
     * @param array $emails
     *
     * @return array
     */
    public function admin_register_email($emails) {
        require_once dirname(__FILE__) . '/class-wc-admin-refunded-order.php';

        $emails['WC_Admin_Refunded_Order'] = new WC_Admin_Refunded_Order();
        return $emails;
    }

}

new Ch_Admin_Custom_WC_Email();