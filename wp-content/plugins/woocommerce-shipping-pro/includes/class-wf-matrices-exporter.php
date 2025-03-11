<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WF_Admin_Exporter' ) ) :

class WF_Admin_Exporter {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if (isset($_GET['wf_export_shippingpro_rate_matrix_csv'])) {
			add_action('init', array($this, 'wf_export_shippingpro_rate_matrix_csv'));
		}
	}

	public function wf_export_shippingpro_rate_matrix_csv(){

		// For Zone Export instace id required to get settings
		if (isset($_GET['ph_instance_id'])) {

			$instance_id 			= $_GET['ph_instance_id'];
			$shipping_pro_settings 	= get_option( 'woocommerce_ph_woocommerce_shipping_pro_'.$instance_id.'_settings' );
			$csv_data 				= 'shipping_name,method_group,country_list,state_list,city,postal_code,shipping_class,product_category,min_weight,max_weight,min_item,max_item,min_price,max_price,cost_based_on,fee,cost,weigh_rounding'."\n";

		}else{
			$shipping_pro_settings 	= get_option( 'woocommerce_wf_woocommerce_shipping_pro_settings' );
			$csv_data 				= 'shipping_name,method_group,zone_name,country_list,state_list,city,postal_code,shipping_class,product_category,min_weight,max_weight,min_item,max_item,min_price,max_price,cost_based_on,fee,cost,weigh_rounding'."\n";
		}

		if(!empty($shipping_pro_settings) && is_array($shipping_pro_settings) && isset($shipping_pro_settings['rate_matrix']) && !empty($shipping_pro_settings['rate_matrix']) ){
			foreach($shipping_pro_settings['rate_matrix'] as $matrix_row){
				if(!empty($matrix_row) && is_array($matrix_row)){
					$csv_data .= isset( $matrix_row['shipping_name'] ) ? $matrix_row['shipping_name'] . ',' : ',';
					$csv_data .= isset( $matrix_row['method_group'] ) ? $matrix_row['method_group']. ',' : ',';

					if( !isset($instance_id) && empty($instance_id) ) {
						
						$csv_data .= !empty($matrix_row['zone_list']) ? implode(";", $matrix_row['zone_list']).',' : ',';
					}
					
					$csv_data .= isset($matrix_row['country_list']) && !empty($matrix_row['country_list']) ? implode(";", $matrix_row['country_list']).',' : ',';
					$csv_data .= isset($matrix_row['state_list']) && !empty($matrix_row['state_list']) ? implode(";", $matrix_row['state_list']).',' : ',';
					$csv_data .= isset( $matrix_row['city'] ) ? $matrix_row['city']. ',' : ',';
					$csv_data .= isset( $matrix_row['postal_code'] ) ? $matrix_row['postal_code']. ',' : ',';
					$csv_data .= !empty($matrix_row['shipping_class']) ? implode(";", $matrix_row['shipping_class']).',' : ',';
					$csv_data .= !empty($matrix_row['product_category']) ? implode(";", $matrix_row['product_category']).',' : ',';
					$csv_data .= isset( $matrix_row['min_weight'] ) ? $matrix_row['min_weight']. ',' : ',';
					$csv_data .= isset( $matrix_row['max_weight'] ) ? $matrix_row['max_weight']. ',' : ',';
					$csv_data .= isset( $matrix_row['min_item'] ) ? $matrix_row['min_item']. ',' : ',';
					$csv_data .= isset( $matrix_row['max_item'] ) ? $matrix_row['max_item']. ',' : ',';
					$csv_data .= isset( $matrix_row['min_price'] ) ? $matrix_row['min_price']. ',' : ',';
					$csv_data .= isset( $matrix_row['max_price'] ) ? $matrix_row['max_price']. ',' : ',';
					$csv_data .= isset( $matrix_row['cost_based_on'] ) ? $matrix_row['cost_based_on']. ',' : ',';
					$csv_data .= isset( $matrix_row['fee'] ) ? $matrix_row['fee']. ',' : ',';
					$csv_data .= isset( $matrix_row['cost'] ) ? $matrix_row['cost']. ',' : ',';
					$csv_data .= isset($matrix_row['weigh_rounding']) ? $matrix_row['weigh_rounding']."\n" : "\n";
				}				
			}
		}	
		header('Content-Type: application/csv');
		header('Content-disposition: attachment; filename="ShippingProMatrix-'.date("Y-m-d-H-i-s").'.csv"');
		echo($csv_data); 		
		exit;
	}
}

endif;

return new WF_Admin_Exporter();
