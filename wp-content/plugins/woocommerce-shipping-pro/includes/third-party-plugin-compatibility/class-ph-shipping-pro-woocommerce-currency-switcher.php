<?php

use WOOMC\API;
use WOOMC\Rate\Storage;

if( ! function_exists('ph_shipping_pro_get_converted_rate') ) {
	function ph_shipping_pro_get_converted_rate($cost) {
		
		$rate_storage 			= new Storage;
        $wc_store_currency		= get_woocommerce_currency();
		$default_currency 		= API::default_currency();

		if( $default_currency != $wc_store_currency && isset( $wc_store_currency ) && !empty( $wc_store_currency ) && is_object( $rate_storage ) )
		{
			$conversion_rate = $rate_storage->get_rate($wc_store_currency,$default_currency);
			$key  = key($cost);
			$cost = current($cost);
			$converted_cost[$key] = round( ((float) $cost * $conversion_rate) ,2);
			return $converted_cost;
		}else{
			return $cost;
		}
	}

}

add_filter( 'ph_shipping_pro_currency_converted_rate', 'ph_shipping_pro_get_converted_rate' ,10,1);