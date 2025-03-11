<?php

class PH_Shipping_Pro_Common_Methods {

	function __construct() {}

	public static function get_settings_page_fields( $settings, $instance_id = '' ) {

		///////code to support old fields value after Updated UI////////
		$mode 		= get_option('calculation_mode');
		$tmp 		= explode('_',$mode);
		$min_max 	= array_pop($tmp);
		$min_max 	= array_pop($tmp);

		if (empty($min_max)) {
			$min_max='max';
		}

		$calc_min_max 	= $min_max.'_cost';
		$tmp 			= implode('_',$tmp);
		$calc_per 		= $tmp;
		$calc_min_max 	= (isset($settings['calc_min_max']) && !empty($settings['calc_min_max'])) ? $settings['calc_min_max'] : $calc_min_max;
		$calc_per 		= (isset($settings['calc_per']) && !empty($settings['calc_per'])) ? $settings['calc_per'] : $calc_per;

		$settings = array(

			'enabled'	=> array(
				'title'   => __( 'Enable/Disable', 'wf_woocommerce_shipping_pro' ),
				'type'	=> 'checkbox',
				'label'   => __( 'Enable this shipping method', 'wf_woocommerce_shipping_pro' ),
				'default' => 'no',
			),	

			'title'	  => array(
				'title' 		=> __( 'Method Title', 'wf_woocommerce_shipping_pro' ),
				'type' 			=> 'text',
				'description' 	=> __( 'This controls the title name users see during checkout', 'wf_woocommerce_shipping_pro' ),
				'default' 		=> __( 'Table Rate Shipping', 'wf_woocommerce_shipping_pro' ),
			),
			'fixed_base_cost' 	=> array(
				'title' 		=> __( 'Fixed Base Cost (optional)'),
				'type' 			=> 'decimal',
				'description'	=> __( 'One-time shipping cost that will be added to the final shipping cost', 'wf_woocommerce_shipping_pro'),
				'default' 		=> '0',
			),	
			'and_logic'	=> array(
				'title' 		=> __( 'Calculation Logic (AND)', 'wf_woocommerce_shipping_pro' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'On enabling this, the calculation logic for "Shipping Class" and "Product Category" fields will follow AND logic. By default the plugin follows OR logic', 'wf_woocommerce_shipping_pro' ),
				'label' 		=>  __( 'Enable', 'wf_woocommerce_shipping_pro' ),
			),
			'strict_and_logic'	=>	array(
				'title'			=>	__( 'Strict Logic (AND)', 'wf_woocommerce_shipping_pro' ),
				'type'			=>	'checkbox',
				'default'		=>	'no',
				'description'	=>	__( 'On enabling this, the plugin will calculate rates only for the "Shipping Class" and "Product Category" mentioned in the rule', 'wf_woocommerce_shipping_pro'),
				'label'			=>	__( 'Enable', 'wf_woocommerce_shipping_pro' ),
			),
			'displayed_columns'	=> array(
				'title'			=> __( 'Table Rate Shipping Columns', 'wf_woocommerce_shipping_pro' ),
				'type'			=> 'multiselect',
				'description'	=> __( 'Select the columns that you want to use in the Shipping Rule Table. Click on "Save changes" to confirm', 'wf_woocommerce_shipping_pro' ),
				'class'	   		=> 'chosen_select ph_settings_tab',
				'css'		 	=> 'width: 450px;',
				'default'	 	=> array(
					'shipping_name',
					'zone_list',
					'weight',
					'fee',
					'cost',
					'weigh_rounding'   
				), //if change the default value here change 'settings/html-rate-marix.php'
				'options'		=> array(
					'shipping_name'		=> __( 'Shipping Method', 'wf_woocommerce_shipping_pro' ),
					'method_group'		=> __( 'Group', 'wf_woocommerce_shipping_pro' ),					
					'zone_list'			=> __( 'Zone', 'wf_woocommerce_shipping_pro' ),					
					'country_list'		=> __( 'Country', 'wf_woocommerce_shipping_pro' ),
					'state_list'		=> __( 'State', 'wf_woocommerce_shipping_pro' ),
					'city'				=> __( 'City', 'wf_woocommerce_shipping_pro' ),
					'postal_code'		=> __( 'Postal/ZIP Code', 'wf_woocommerce_shipping_pro' ),
					'shipping_class'	=> __( 'Shipping Class', 'wf_woocommerce_shipping_pro' ),
					'product_category'	=> __( 'Product Category', 'wf_woocommerce_shipping_pro' ),
					'weight'			=> __( 'Weight', 'wf_woocommerce_shipping_pro' ),
					'item'				=> __( 'Item', 'wf_woocommerce_shipping_pro' ),
					'price'				=> __( 'Price', 'wf_woocommerce_shipping_pro' ),					
					'cost_based_on'		=> __( 'Cost Based on', 'wf_woocommerce_shipping_pro' ),
					'fee'				=> __( 'Base Cost', 'wf_woocommerce_shipping_pro' ),
					'cost'				=> __( 'Cost Per Unit', 'wf_woocommerce_shipping_pro' ),
					'weigh_rounding'	=> __( 'Round off', 'wf_woocommerce_shipping_pro' )					
				),
				'custom_attributes' => array(
					'data-placeholder' 	=> __( 'Choose matrix columns', 'wf_woocommerce_shipping_pro' )
				)
			),
			'calc_min_max' => array(
				'title' 		=> __( 'Minimum or Maximum Cost', 'wf_woocommerce_shipping_pro' ),
				'type'			=> 'select',
				'description'	=> __( 'Define a maximum or minimum cost when multiple rules are defined' , 'wf_woocommerce_shipping_pro' ),
				'default'		=> !empty($calc_min_max)?$calc_min_max:'max_cost',
				'options'		=> array(
					'max_cost' 	=> __( 'Choose the Maximum Cost', 'wf_woocommerce_shipping_pro' ),
					'min_cost' 	=> __( 'Choose the Minimum Cost', 'wf_woocommerce_shipping_pro' ),
				),
				'class'			=> 'ph_settings_tab',
			),			
			'calc_per' => array(
				'title' 		=> __( 'Calculation Mode', 'wf_woocommerce_shipping_pro' ),
				'type'			=> 'select',
				'description' 	=> __( 'Per Order: Shipping calculation will be done on the entire cart together. Total weight of the cart will be used with the selected rule to calculate the shipping.  Rule will be satisfied if one of the items in the cart meets the criteria. Per Item: Shipping calculation will happen on item wise, item weight multiply with the quantity will be used with the selected rule to calculate the shipping cost for the item, all the item cost will be summed to find the final cost.' , 'wf_woocommerce_shipping_pro' ),
				'default'		=> !empty($calc_per)?$calc_per:'per_order',
				'options'	 	=> array(
					'per_line_item'	=> __( 'Calculate Shipping Cost per Line Item', 'wf_woocommerce_shipping_pro' ),
					'per_item'		=> __( 'Calculate Shipping Cost per Item', 'wf_woocommerce_shipping_pro' ),
					'per_order'		=> __( 'Calculate Shipping Cost per Order', 'wf_woocommerce_shipping_pro' ),
					'per_category'	=> __( 'Calculate Shipping Cost per Category', 'wf_woocommerce_shipping_pro' ),
					'per_shipping_class'	=> __( 'Calculate Shipping Cost per Shipping Class', 'wf_woocommerce_shipping_pro' ),
				),
				'class'			=> 'ph_settings_tab',
			),
			'volumetric_weight'	=> array(
				'title'   		=> __( 'Volumetric weight', 'wf_woocommerce_shipping_pro' ),
				'type'			=> 'checkbox',
				'label'   		=> __( 'Enable this option if your products have lesser weight and occupy more space (higher volume). This will use the volumetric weight of the packages to calculate the shipping cost.' ),
				'default' 		=> 'no',
				'class'			=> 'ph_settings_tab_volume'
			),
		);

		// WSP7
		if( !empty($instance_id) ) {

			$settings['shipping_method_instance_id']=array(
				'title'			=>	__( 'Add Instance Id to Shipping Method', 'wf_woocommerce_shipping_pro' ),
				'type'			=>	'checkbox',
				'default'		=>	'no',
				'description'	=>	__( 'Enable this option to add Instance Id to the Shipping Method Id in Cart/Checkout Pages', 'wf_woocommerce_shipping_pro'),
				'label'   		=> __( 'Enable', 'wf_woocommerce_shipping_pro' ),
			);
		}

		$settings['tax_status'] = array(
			'title'			=> __( 'Tax Status', 'wf_woocommerce_shipping_pro' ),
			'type'			=> 'select',
			'description'	=> '',
			'default' 		=> 'none',
			'options' 		=> array(
				'taxable' 	=> __( 'Taxable', 'wf_woocommerce_shipping_pro' ),
				'none'		=> __( 'None', 'wf_woocommerce_shipping_pro' ),
			),
			'class'			=> 'ph_settings_tab',
		);
		$settings['remove_free_text']= array(
			'title'   		=> __( 'Remove Free Text', 'wf_woocommerce_shipping_pro' ),
			'type'			=> 'checkbox',
			'label'   		=> __( 'Remove default (Free) text from shipping label', 'wf_woocommerce_shipping_pro' ),
			'default' 		=> 'no',
		);
		$settings['debug']      = array(
			'title'			=> __( 'Debug', 'wf_woocommerce_shipping_pro' ),
			'type'			=> 'checkbox',
			'label'			=> __( 'Debug this shipping method', 'wf_woocommerce_shipping_pro' ),
			'default'		=> 'no',
		);		
		
		if( !empty($instance_id) ) {

			if( isset($settings['enabled']) ) {
				unset($settings['enabled']);
			}

			$settings['displayed_columns'] 	= array(
				'title'			=> __( 'Table Rate Shipping Columns', 'wf_woocommerce_shipping_pro' ),
				'type'			=> 'multiselect',
				'description'	=> __( 'Select the columns that you want to use in the Shipping Rule Table. Click on "Save changes" to confirm', 'wf_woocommerce_shipping_pro' ),
				'class'	   		=> 'chosen_select ph_settings_tab',
				'css'		 	=> 'width: 450px;',
				'default'	 	=> array(
					'shipping_name',
					'weight',
					'fee',
					'cost',
					'weigh_rounding'   
				), //if change the default value here change 'settings/html-rate-marix.php'
				'options'		=> array(
					'shipping_name'		=> __( 'Shipping Method', 'wf_woocommerce_shipping_pro' ),
					'method_group'		=> __( 'Group', 'wf_woocommerce_shipping_pro' ),
					'country_list'		=> __( 'Country', 'wf_woocommerce_shipping_pro' ),
					'state_list'		=> __( 'State', 'wf_woocommerce_shipping_pro' ),
					'city'				=> __( 'City', 'wf_woocommerce_shipping_pro' ),
					'postal_code'		=> __( 'Postal/ZIP Code', 'wf_woocommerce_shipping_pro' ),
					'shipping_class'	=> __( 'Shipping Class', 'wf_woocommerce_shipping_pro' ),
					'product_category'	=> __( 'Product Category', 'wf_woocommerce_shipping_pro' ),
					'weight'			=> __( 'Weight', 'wf_woocommerce_shipping_pro' ),
					'item'				=> __( 'Item', 'wf_woocommerce_shipping_pro' ),
					'price'				=> __( 'Price', 'wf_woocommerce_shipping_pro' ),
					'cost_based_on'		=> __( 'Cost Based on', 'wf_woocommerce_shipping_pro' ),
					'fee'				=> __( 'Base Cost', 'wf_woocommerce_shipping_pro' ),
					'cost'				=> __( 'Cost Per Unit', 'wf_woocommerce_shipping_pro' ),
					'weigh_rounding'	=> __( 'Round off', 'wf_woocommerce_shipping_pro' )
				),
				'custom_attributes' => array(
					'data-placeholder' 	=> __( 'Choose matrix columns', 'wf_woocommerce_shipping_pro' )
				)
			);
		}

		return $settings;
	}

	public static function ph_find_zone($package){

		$matching_zones 	= array();

		if( class_exists('WC_Shipping_Zones') ) {

			$zones_obj 	= new WC_Shipping_Zones;
			$matches 	= $zones_obj::get_zone_matching_package($package);

			if( method_exists ( $matches, 'get_id' ) ) { //if WC 3.0+
				$zone_id = $matches->get_id();
			}else{
				$zone_id =  $matches->get_zone_id();
			}

			array_push( $matching_zones, $zone_id );
		}
		return $matching_zones;
	}

	public static function ph_filter_rules( $zone, $country, $state, $city, $post_code, $shipping_classes, $product_category, $package, $calc_mode_strategy, $settings ) {
		
		$selected_rules	= array();
		$rate_matrix 	= (isset($settings['rate_matrix']) && !empty($settings['rate_matrix'])) ? $settings['rate_matrix'] : array();

		if(sizeof($rate_matrix) > 0) {
			foreach($rate_matrix as $key => $rule ) {
				$satified_general = false;
				if( self::ph_compare_array_rule_field($rule,'zone_list',$zone,'','',false,$settings)
					&& self::ph_compare_array_rule_field($rule,'country_list',$country,'rest_world','any_country',false,$settings)
					&& self::ph_compare_array_rule_field($rule,'state_list',$country.':'.strtoupper($state),'rest_country','any_state',false,$settings)
					&& self::ph_compare_string_rule_field($rule,'city',$city,'','*',$settings)
					&& self::ph_compare_post_code($rule,'postal_code',$post_code,'','*',$settings) ) {

					$satified_general=true;	
				}

				if($satified_general) {
					foreach ( $calc_mode_strategy->wf_get_grouped_package($package) as $item_id => $values ) {

						$base_price = apply_filters( 'ph_shipping_pro_change_currency', $calc_mode_strategy->wf_get_price($values) ); // Converted price converting back to base price

						if(	self::ph_compare_array_rule_field($rule,'shipping_class',$shipping_classes,'rest_shipping_class','any_shipping_class',$item_id,$settings)
							&& self::ph_compare_array_rule_field($rule,'product_category',$product_category,'rest_product_category','any_product_category',$item_id,$settings)
							&& self::ph_compare_range_field($rule,'weight',self::ph_get_weight($values,$settings))
							&& self::ph_compare_range_field($rule,'item',self::ph_get_item_count($values))
							&& self::ph_compare_range_field($rule,'price', $base_price ) ) {

							if(!isset($rule['item_ids'])) {
								$rule['item_ids'] = array();
							}
							$rule['item_ids'][] = $item_id;
						}												
					}
					if(isset($rule['item_ids'])) $selected_rules[] = $rule;						
				}					
			}					
		}

		return $selected_rules;	 
	}

	public static function ph_calc_cost( $rules ,$package, $calc_mode_strategy, $row_selection_choice, $title ,$settings) {
		
		$cost = array();
		if ( sizeof($rules) > 0) {

			$grouped_package = $calc_mode_strategy->wf_get_grouped_package($package);

			foreach ( $rules as $key => $rule) {

				$method_group 	= isset($rule['method_group']) ? $rule['method_group'] : null;	
				$item_ids 		= isset($rule['item_ids']) ? $rule['item_ids'] : null;

				if(!empty($item_ids)) {

					foreach($item_ids as $item_key => $item_id) {

						if(empty($grouped_package[$item_id])) {
							continue;
						}

						$shipping_cost = self::ph_get_rule_cost($rule,$grouped_package[$item_id],$calc_mode_strategy,$settings);

						if($shipping_cost !== false) {

							if(isset($cost[$method_group]['cost'][$item_id])) {

								if($cost[$method_group]['cost'][$item_id] > $shipping_cost && $row_selection_choice == 'min_cost' || $cost[$method_group]['cost'][$item_id] < $shipping_cost && $row_selection_choice == 'max_cost') {

									$cost[$method_group]['cost'][$item_id] = $shipping_cost;
									$cost[$method_group]['shipping_name'] = !empty($rule['shipping_name']) ? $rule['shipping_name'] : $title;
								}							   
							}else{
								if(!isset($cost[$method_group])) {
									$cost[$method_group] = array();
									$cost[$method_group]['cost'] = array();
								}

								$cost[$method_group]['shipping_name'] = !empty($rule['shipping_name']) ? $rule['shipping_name'] : $title;
								$cost[$method_group]['cost'][$item_id] = $shipping_cost;																								
							}
						}		   
					}
				}						   
			}
		}

		return 	$cost;
	}

	public static function add_fixed_base_cost( $costs, $fixed_base_cost ) {

		if ( sizeof($costs) > 0 && !empty($fixed_base_cost)) {
			foreach ($costs as $method_group => $method_cost) {
				if(isset($method_cost['shipping_name']) && isset($method_cost['cost'])) {
					$keys = array_keys($method_cost['cost']);
					$costs[$method_group]['cost'][$keys[0]] += $fixed_base_cost;
				}
			}
		}
		return $costs;
	}

	public static function ph_compare_array_rule_field( $rule, $field_name, $input_value, $const_rest, $const_any, $item_id=false, $settings ) {
		
		//if rule_value is null then shipping rule will be acceptable for all
		global $rule_value;

		$debug 				= (isset($settings['debug']) && !empty($settings['debug'])) ? $settings['debug'] : 'no';
		$and_logic  		= (isset($settings['and_logic']) && $settings['and_logic'] == 'yes') ? true : false;
		$strict_and_logic 	= (isset($settings['strict_and_logic']) && $settings['strict_and_logic'] == 'yes') ? true : false;
		$rate_matrix 		= (isset($settings['rate_matrix']) && !empty($settings['rate_matrix'])) ? $settings['rate_matrix'] : array();
		$displayed_columns	= (isset($settings['displayed_columns']) && !empty($settings['displayed_columns'])) ? $settings['displayed_columns'] : array();

		if (!empty($rule[$field_name]) && in_array($field_name,$displayed_columns) ){
			$rule_value = $rule[$field_name];
			self::wf_debug( "rule_value : $rule_value[0]", $debug );
		}else{	
			return true;
		}

		if (is_array($rule_value) && count($rule_value) == 1) {

			if($rule_value[0] == $const_rest) {
				return self::ph_partof_rest_of_the($input_value,$field_name,$item_id,$rule,$rate_matrix);
			}elseif($rule_value[0] == $const_any) {
				return true;	
			}
		}

		if (!is_array($input_value)) {
			return in_array($input_value,$rule_value);
		}else{			
			if( $item_id ) {

				if( isset($input_value[$item_id]) && is_array($input_value[$item_id]) ) {

					if( $and_logic && ($field_name == 'product_category' || $field_name == 'shipping_class' ) ) {
						// return $input_value[$item_id] == $rule_value; //If both arrays are equal, for strict AND logic.
						$matched_shipping_class_or_prod_cat_count 	= count(array_intersect($rule_value, $input_value[$item_id]));
						$rule_val_count								= count($rule_value);
						$input_value_count							= count($input_value[$item_id]);
						return $strict_and_logic ? ( $matched_shipping_class_or_prod_cat_count == $rule_val_count && $rule_val_count == $input_value_count ) : ( $matched_shipping_class_or_prod_cat_count == $rule_val_count );
					}else{
						return count( array_intersect($input_value[$item_id],$rule_value) ) > 0;
					}
				}else{
					return false;
				}

			}else{ //case of zone.
				return count(array_intersect($input_value,$rule_value)) > 0;
			}
		}
	}

	public static function ph_compare_post_code( $rule, $field_name, $input_value, $const_rest, $const_any, $settings ) {
		
		$debug 		= (isset($settings['debug']) && !empty($settings['debug'])) ? $settings['debug'] : 'no';
		$displayed_columns	= (isset($settings['displayed_columns']) && !empty($settings['displayed_columns'])) ? $settings['displayed_columns'] : array();

		//if rule_value is null then shipping rule will be acceptable for all
		if (!empty($rule[$field_name]) && in_array($field_name,$displayed_columns) ) {
			$rule_value = $rule[$field_name];
			self::wf_debug( "rule_value : $rule_value", $debug );
		}else{
			return true;
		}

		if($rule_value == $const_any) {
			return true;
		}

		if ( ! empty( $rule_value ) ) {

			$postcodes = explode( ';', $rule_value );
			$postcodes = array_map( 'strtoupper', array_map( 'wc_clean', $postcodes ) );
			$input_value = strtoupper($input_value);

			foreach( $postcodes as $postcode ) {
				if ( strstr( $postcode, '-' ) ) {
					self::wf_debug( "$postcode - $input_value ", $debug );
					$postcode_parts = explode( '-', $postcode );
					if ( is_numeric( $postcode_parts[0] ) && is_numeric( $postcode_parts[1] ) && $postcode_parts[1] > $postcode_parts[0] ) {
						for ( $i = $postcode_parts[0]; $i <= $postcode_parts[1]; $i ++ ) {
							if ( ! $i )
								continue;

							if ( strlen( $i ) < strlen( $postcode_parts[0] ) )
								$i = str_pad( $i, strlen( $postcode_parts[0] ), "0", STR_PAD_LEFT );

							if($input_value == $i)
							{
								self::wf_debug( "$i matched $input_value ", $debug );							
								return true;
							}								
						}
					}
				}
				elseif ( strstr( $postcode, '*' ) )
				{
					self::wf_debug( "$postcode * $input_value ", $debug );
					if(preg_match('/\A'.str_replace('*', '.', $postcode).'/',$input_value))
						return true;
				}
				else {
					self::wf_debug( "$postcode == $input_value ", $debug );
					if ( $postcode == $input_value)
						return true;
				}
			}
		}
		return false;
	}

	public static function ph_compare_range_field( $rule,$field_name, $totalweight) {
		$weight = $totalweight;
		if (!empty($rule['min_'.$field_name]) && $weight <= $rule['min_'.$field_name]) 
			return false;
		if (!empty($rule['max_'.$field_name]) && $weight > $rule['max_'.$field_name]) 
			return false;					
		return true;	
	}

	public static function ph_compare_string_rule_field( $rule, $field_name, $input_value, $const_rest, $const_any, $settings ) {

		$debug 		= (isset($settings['debug']) && !empty($settings['debug'])) ? $settings['debug'] : 'no';
		$displayed_columns	= (isset($settings['displayed_columns']) && !empty($settings['displayed_columns'])) ? $settings['displayed_columns'] : array();

		//if rule_value is null then shipping rule will be acceptable for all
		if (!empty($rule[$field_name]) && in_array($field_name,$displayed_columns) ) {
			$rule_value = $rule[$field_name];
			self::wf_debug( "rule_value : $rule_value", $debug );
		}else{
			return true;
		}

		if($rule_value == $const_any) {
			return true;
		}

		if ( ! empty( $rule_value ) ) {

			$cities 		= explode( ';', $rule_value );
			$cities 		= array_map( 'strtoupper', array_map( 'wc_clean', $cities ) );
			$input_value 	= strtoupper($input_value);

			foreach( $cities as $city ) {
				if ( $city == $input_value) {
					return true;
				}
			}
		}
		return false;
	}
	
	public static function ph_partof_rest_of_the( $input_value,$field_name,$item_id=false ,$current_rule,$rate_matrix) {

		global $combined_rule_value;

		$combined_rule_value = array();

		if ( sizeof( $rate_matrix ) > 0) {
			foreach ( $rate_matrix as $key => $rule ) {

				if(!empty($rule[$field_name]) && isset($rule['method_group']) && isset($current_rule['method_group']) && ($rule['method_group'] == $current_rule['method_group'])) {
					$combined_rule_value = array_merge($rule[$field_name],$combined_rule_value);
				}
			}					
		}
		
		if(!is_array($input_value)) {

			//county not defined as part of any other rule 
			if(!in_array($input_value,$combined_rule_value)) {
				return true;
			}

			return false;
		}else{
			//returns true if at least one product category doesn't exist combined list.
			if($item_id !== false && isset($input_value[$item_id]) && is_array($input_value[$item_id])) {
				//This is a case where product with NO shipping class in the cart. 
				//This will not handle the case where multiple product in the group and some products are not 'NO Shipping Class' 
				//So finally if its NO Shipping Class case we will consider it as matching with Rest of the shipping class. 
				
				if(empty($input_value[$item_id])) {
					return true;
				}

				return count(array_diff($input_value[$item_id],$combined_rule_value)) > 0;
			}

			return false;				
		}						
	}

	public static function ph_get_weight($package_items,$settings) {

		$total_weight 	= 0;
        $volumetric 	= (isset($settings['volumetric_weight']) && !empty($settings['volumetric_weight'])) ? $settings['volumetric_weight'] : 'no';
		
        foreach($package_items as $package_item) {

        	$_product = $package_item['data'];

        	if($volumetric=='yes') {

        		$volumetric_weight 	= 0;
        		$factor 			= 5000;
        		$weight    			= 0;
        		$length   			= $_product->get_length() ? wc_get_dimension(  $_product->get_length(), 'cm') : 1;
        		$height   			= $_product->get_height() ? wc_get_dimension(  $_product->get_height(), 'cm') : 1;
        		$width    			= $_product->get_width() ? wc_get_dimension(  $_product->get_width(), 'cm') : 1;
        		$weight   			= $_product->get_weight() ? $_product->get_weight() : 0;
        		$factor				= apply_filters('ph_shipping_pro_dimension_factor', $factor, $package_items );

        		$volumetric_weight = ( $length * $width * $height ) / $factor; 

        		if (!empty($volumetric_weight)) {

        			$volumetric_weight = wc_get_weight( $volumetric_weight, get_option( 'woocommerce_weight_unit' ), 'kg' );

        			if ($volumetric_weight>$weight ) {

        				$weight = $volumetric_weight;
        			} else {
        				$weight = $weight;
        			}

        			$total_weight += $weight * $package_item['quantity'];

        		} else {

        			$total_weight += $weight * $package_item['quantity'];
        		}

        	} else {

        		$product_weight  = $_product->get_weight() ? $_product->get_weight() : 0;
        		$total_weight 	+= $product_weight * $package_item['quantity'];
        	}

        	$total_weight = apply_filters( 'wf_shipping_pro_item_weight', (float) $total_weight, $package_item, $package_items );

        }
		return apply_filters('ph_shipping_pro_total_weight',$total_weight, $package_items);
	}
	
	
//	public static function ph_get_item_count($package_items) {
//		$total_count = 0;
//		foreach($package_items as $package_item){		
//			$_product = $package_item['data'];
//			$total_count += apply_filters( 'wf_shipping_pro_item_quantity', $package_item['quantity'],$_product->id);			
//		}
//		return $total_count;
//	}

         //custom for total of all item in cart same delivery date. So it it not same $package_item['quantity']
         public static function ph_get_item_count($package_items) {
        $total_count = 0;

        $group_delivery_qty = getDeliveryDateGroupInCart();
        foreach ($package_items as $package_item) {           
            $g_delivery = $group_delivery_qty['delivery'];
            foreach ($g_delivery as $key => $value) {
                if (in_array($package_item['product_id'], $value)) {
                    $total_count = $group_delivery_qty['qty'][$key];
                    break;
                }
            }
        }
        //end
        return $total_count;
    }

	

	public static function ph_get_rule_cost( $rate, $grouped_package, $calc_mode_strategy,$settings ) {

		//variable to get decimal separator used.
		$separator 			= stripslashes( get_option( 'woocommerce_price_decimal_sep' ) );
		$decimal_separator 	= $separator ? $separator : '.';

		$based_on = 'weight';
		if(!empty($rate['cost_based_on'])) $based_on = $rate['cost_based_on'];

		if($based_on == 'price'){
			$totalweight = $calc_mode_strategy->wf_get_price($grouped_package);
		}
		elseif($based_on == 'item'){
			$totalweight = self::ph_get_item_count($grouped_package);
		}
		else{
			$totalweight = self::ph_get_weight($grouped_package,$settings);		
		}


		$weight = floatval($totalweight);

		if( isset($rate['min_'.$based_on]) ){
			$weight = max(0, $weight - floatval($rate['min_'.$based_on]) );
		}

		$weightStep = isset($rate['weigh_rounding']) ? floatval($rate['weigh_rounding']) : 1;

		if (trim($weightStep)) 
			$weight = floatval(ceil($weight / $weightStep) * $weightStep);

		$rate_fee   = isset($rate['fee']) ? floatval(str_replace($decimal_separator, '.', $rate['fee'])) : 0;
		$rate_cost  = isset($rate['cost']) ? floatval(str_replace($decimal_separator, '.', $rate['cost'])) : 0;
		$price = $rate_fee + $weight * $rate_cost;

		if ( $price !== false) return $price;

		return false;		
	}

	public static function wf_debug( $error_message, $debug_mode = 'no' ){
		if( $debug_mode == 'yes' )
			wc_add_notice( $error_message, 'notice' );
	}
}