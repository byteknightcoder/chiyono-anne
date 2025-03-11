<?php

namespace FSProVendor\WPDesk\License;

use FSProVendor\WPDesk_Plugin_Info;
/**
 * Replaces WPDesk_Helper_Plugin. Gets info from plugin and sends it to subscription/update integrations
 *
 * @depreacted Check LicenseServer namespace
 * @package WPDesk\License
 */
class OldLicenseRegistrator implements \FSProVendor\WPDesk\License\PluginRegistratorInterface
{
    /** @var WPDesk_Plugin_Info */
    private $plugin_info;
    /**
     * @var PluginLicense
     */
    private $plugin_license;
    public function __construct(\FSProVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $this->plugin_info = $plugin_info;
        $this->plugin_license = new \FSProVendor\WPDesk\License\PluginLicense($plugin_info);
    }
    public function is_active() : bool
    {
        return $this->plugin_license->is_active();
    }
    /**
     * Initializes license manager.
     */
    public function initialize_license_manager()
    {
        $license_manager = new \FSProVendor\WPDesk\License\LicenseManager($this->plugin_info);
        $license_manager->init_activation_form();
        $api_manager = $license_manager->create_api_manager();
        $license_manager->init_wp_upgrader($api_manager->is_activated(), $api_manager->get_myaccount_url());
        $license_manager->hooks();
    }
}
