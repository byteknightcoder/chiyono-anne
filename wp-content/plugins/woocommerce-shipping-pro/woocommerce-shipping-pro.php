<?php
/*
	Plugin Name: WooCommerce Table Rate Shipping Pro
	Plugin URI: https://www.pluginhive.com/product/woocommerce-table-rate-shipping-pro-plugin/
	Description: This plugin helps you set up multiple shipping rules based on Country, State, Post/ZIP code, Product category, Shipping Class, and Weight. If the user's cart matches a rule, the corresponding shipping cost is displayed on the Cart and Checkout pages.
	Version: 3.3.1
	Author: PluginHive
	Author URI: https://www.pluginhive.com/
	Copyright: 2014-2015 PluginHive.
	WC requires at least: 2.6.0
	WC tested up to: 5.9.0

*/

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	// Define PH_TABLE_RATE_SHIPPING_PRO_VERSION
	if ( !defined( 'PH_TABLE_RATE_SHIPPING_PRO_VERSION' ) )
	{
		define( 'PH_TABLE_RATE_SHIPPING_PRO_VERSION', '3.3.1' );
	}

	// Include API Manager
	if ( !class_exists( 'PH_Table_Rate_API_Manager' ) ) {

		include_once( 'ph-api-manager/ph_api_manager_table_rate.php' );
	}

	if ( class_exists( 'PH_Table_Rate_API_Manager' ) ) {

		$product_title 		= 'Table Rate Shipping'; 
		$server_url 		= 'https://www.pluginhive.com/';
		$product_id 		= '';

		$ph_table_rate_api_obj 	= new PH_Table_Rate_API_Manager( __FILE__, $product_id, PH_TABLE_RATE_SHIPPING_PRO_VERSION, 'plugin', $server_url, $product_title );

	}

	function wf_shipping_pro_activatoin_check() {

    	//check if basic version is there
		if ( is_plugin_active('weight-country-woocommerce-shipping/weight-country-woocommerce-shipping.php') ){
			deactivate_plugins( basename( __FILE__ ) );
			wp_die( __("Oops! You tried installing the premium version without deactivating and deleting the basic version. Kindly deactivate and delete WooCommerce Shipping Pro with Table Rate (BASIC) and then try again", "wf_woocommerce_shipping_pro" ), "", array('back_link' => 1 ));
		}
	}
	register_activation_hook( __FILE__, 'wf_shipping_pro_activatoin_check' );

	load_plugin_textdomain( 'wf_woocommerce_shipping_pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once 'includes/class-ph-wc-shipping-pro-common.php';

	$wc_active = Ph_WC_Shipping_Pro_Common::is_plugin_active('woocommerce/woocommerce.php');

	if ( $wc_active ) {

		include( 'wf-shipping-pro-common.php' );

		if (!function_exists('wf_plugin_configuration')) {

			function wf_plugin_configuration(){
				return array(
					'id' => 'wf_woocommerce_shipping_pro',
					'method_title' => __('Table Rate Shipping', 'wf_woocommerce_shipping_pro' ),
					'method_description' => __('WooCommerce Table Rate Shipping Pro by PluginHive', 'wf_woocommerce_shipping_pro' ));		
			}
		}

		if (!function_exists('ph_plugin_configuration')) {

			function ph_plugin_configuration() {

				return array(
					'id' 					=> 'ph_woocommerce_shipping_pro',
					'method_title' 			=> __('Table Rate Shipping', 'wf_woocommerce_shipping_pro' ),
					'method_description' 	=> __('Calculate shipping rates based on combinations of conditions', 'wf_woocommerce_shipping_pro' ));		
			}
		}

	}

	register_activation_hook( __FILE__, 'wf_plugin_activate' );