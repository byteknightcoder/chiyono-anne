<?php

add_filter( 'ph_shipping_pro_change_currency', 'ph_shipping_pro_change_currency' , 10, 1 );

if( ! function_exists('ph_shipping_pro_change_currency') ) {
	function ph_shipping_pro_change_currency( $price ) {

		$wc_store_currency	= get_woocommerce_currency();
		$woocs_currencies  	= get_option('woocs', true);	// WOOCS Currencies table

		if ( isset($woocs_currencies) && is_array($woocs_currencies) ) {
			
			$decimals = isset($woocs_currencies[$wc_store_currency]['decimals']) && !empty($woocs_currencies[$wc_store_currency]['decimals']) ? $woocs_currencies[$wc_store_currency]['decimals'] : 2;

			foreach ( $woocs_currencies as $key => $value ) {

				if ( $value['is_etalon'] ) {
					$default_currency  	= 	$key; // Default currency
					break;
				}
			}

			if ( $default_currency !== $wc_store_currency ) {

				if ( $woocs_currencies[$wc_store_currency] ) {

					$conversion_rate 	  = $woocs_currencies[$wc_store_currency]['rate'];
					$conversion_rate_plus = $woocs_currencies[$wc_store_currency]['rate_plus'];

					if ( !empty($conversion_rate_plus) ) {

						$conversion_rate_plus = $conversion_rate_plus + $conversion_rate;
						$price = $price/$conversion_rate_plus;
					}else{

						$price = $price/$conversion_rate;
					}
				}
			}

			return round($price,$decimals);
		}else{

			return $price;
		}
	}
}