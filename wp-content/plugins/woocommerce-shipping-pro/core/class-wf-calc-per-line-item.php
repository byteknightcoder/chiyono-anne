<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WF_Calc_Per_Line_Item extends WF_Calc_Strategy {
	public function wf_find_shipping_classes( $package ) {

		// WPML Global Object
		global $sitepress;

		$found_shipping_classes = array();

		// Find shipping classes for products in the cart
		if ( sizeof( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$values['data'] = $this->wf_load_product( $values['data']  );
				if ( $values['data']->needs_shipping() ) {

					$found_class = '';

					// Check for WPML Plugin
					if( $sitepress && ICL_LANGUAGE_CODE != null ) {

						$default_product 	= $this->ph_get_default_product_data( $values['data']->get_id(), $sitepress->get_default_language() );
						
						if( $default_product instanceof WC_Product ) {

							$found_class 		= $default_product->get_shipping_class();
						}

					}else{
						$found_class = $values['data']->get_shipping_class();
					}
					
					if(!isset($found_shipping_classes[$item_id]))
						$found_shipping_classes[$item_id] = array();
					if (!empty($found_class) && !in_array($found_class,$found_shipping_classes[$item_id])) 
						$found_shipping_classes[$item_id][] = $found_class;										
				}
			}
		}
		return $found_shipping_classes;
	}

	public function wf_find_product_category( $package ) {

		// WPML Global Object
		global $sitepress;
		
		$found_product_category = array();

		// Find shipping classes for products in the cart
		if ( sizeof( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$values['data'] = $this->wf_load_product( $values['data']  );
				if ( $values['data']->needs_shipping() ) {
					if( (WC()->version > '2.7.0') ){
						$par_id = $values['data']->get_parent_id($values['data']->id);
						$product_id = !empty( $par_id ) ? $par_id : $values['data']->id;
					}else{
						$product_id = $values['data']->id;
					}

					// Check for WPML Plugin
					if( $sitepress && ICL_LANGUAGE_CODE != null ) {

						$default_product_id 	= $this->ph_get_default_product_data( $product_id, $sitepress->get_default_language(), true );

						$found_product_category[$item_id] = wp_get_post_terms( $default_product_id, 'product_cat', array( "fields" => "ids" ) );

					}else{
						$found_product_category[$item_id] = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );
					}
				}
			}
		}
		return $found_product_category;
	}

	public function wf_get_grouped_package($package){
		$rule = array();
		foreach ( $package['contents'] as $item_id => $values ) {
			if ( $values['data']->needs_shipping() ) {
				$values['data'] = $this->wf_load_product( $values['data']  );
				if(!isset($rule[$item_id])) $rule[$item_id] = array(); 
				$rule[$item_id][] = $values;																								
			}
		}
		return $rule;		
	}
	
	public function wf_calc_tax(){
		return 'per_item';
	}
		
	private function wf_load_product( $product ){
		if( !$product ){
			return false;
		}
		return ( WC()->version < '2.7.0' ) ? $product : new wf_product( $product );
	}

	private function ph_get_default_product_data( $current_productid, $default_langauge, $return_productid = false ) {
		
		$default_product_id = apply_filters( 'wpml_object_id', $current_productid, 'post', true, $default_langauge ); 	// Get the Default Product Id

		if( $return_productid ) {
			return $default_product_id;
		}

		$default_product 	= wc_get_product($default_product_id); 	// Get the Default Product

		return $default_product;
	}
}
?>