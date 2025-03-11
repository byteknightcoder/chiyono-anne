<?php

class xa_sp_rules_table extends WP_List_Table {
	var $data		= array();
	var $displayed_columns	= array();
	var $counter 		= 0;
	var $country_list 	= array();
	var $product_category 	= array();
	var $shipping_class 	= array();
	var $state_list 	= array();
	var $zone_list 		= array();
	var $cost_based_on 	= array();

	/**
	 * Rules per page to be displayed.
	 */
	public static $rules_per_page = 25;

	function __construct( $args = array() ) {
		parent::__construct($args);
		$this->data 			= $args['data'];
		$this->displayed_columns	= $args['displayed_columns'];
		$this->country_list 		= wf_get_shipping_countries();
		$this->state_list 		= wf_get_state_list($this->country_list);
		$this->zone_list 		= wf_get_zone_list();
		$this->product_category 	= wf_get_category_list();
		$this->shipping_class 		= wf_get_shipping_class_list();
		$this->cost_based_on		= array('weight'=>'Weight','item'=>'Item','price'=>'Price');
	}

	function get_columns(){
		$all = array(
			'cb'			=> 'cb',
			'shipping_name' 	=> 'Shipping Method <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Would you like this shipping rule to have its own shipping service name? If so, please choose a name. Leaving it blank will use Method Title as shipping service name.</span></span>',
			'method_group'		=> 'Group <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Useful if multiple shipping method needs to be returned. All the shipping rule can be grouped to air and ground by providing Method Group appropriate. And different shipping rates for air and ground will be provided to the users. Leaving it blank will only return one shipping rate.</span></span>',
			'zone_list'		=> 'Zone <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">You can choose the zones here once you configured.</span></span>',
			'country_list'		=> 'Country <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Select list of countries which this rule will be applicable.  Leave it blank to apply this rule for all the countries.</span></span>',
			'state_list'		=> 'State <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Select list of states which this rule will be applicable. Leave it blank to apply this rule for all the states.</span></span>',
			'city'			=> 'City <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Enter name of the city for which this rule should be applicable. Leave it blank to apply this rule for all the cities. Use Semi-colon ( ; ) to separate multiple cities.</span></span>',
			'postal_code'		=> 'Postal/ZIP Code <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Enter Post/Zip code. Use Semi-colon ( ; ) to separate multiple Post/ZIP Code. To use range of post/Zip code provide like 12345-12350 (It will take from 12345 to 12350) or use Wildcards ( * ) for Post/ZIP Code Ranges (e.g. 1234* will take all ZIP Codes from 12340 to 12349). Leave it blank to apply to all areas. </span></span>',
			'shipping_class' 	=> 'Shipping Class <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Select list of shipping class which this rule will be applicable.</span></span>',
			'product_category'	=> 'Product Category <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Select list of product category which this rule will be applicable. Only the product category directly assigned to the products will be considered.</span></span>',
			'weight'		=> 'Weight <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">If the min value entered is .25 and the order weight is .25 then this rule will be ignored. if the min value entered is .25 and the order weight is .26 then this rule will be be applicable for calculating shipping cost. if the max value entered is .25 and the order weight is .26 then this rule will be ignored. if the max value entered is .25 and the order weight is .25 or .24 then this rule will be be applicable for calculating shipping cost.</span></span>',
			'item'			=> 'Item <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">If the min value entered is 25 and the item count is 25 then this rule will be ignored. if the min value entered is 25 and the item count is 26 then this rule will be be applicable for calculating shipping cost. if the max value entered is 25 and the item count is 26 then this rule will be ignored. if the max value entered is 25 and the item count is 25 or 24 then this rule will be be applicable for calculating shipping cost.</span></span>',
			'price'			=> 'Price <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">If the min value entered is 25 and the price is 25 then this rule will be ignored. if the min value entered is 25 and the price is 26 then this rule will be be applicable for calculating shipping cost. if the max value entered is 25 and the price is 26 then this rule will be ignored. if the max value entered is 25 and the price is 25 or 24 then this rule will be be applicable for calculating shipping cost.</span></span>',
			'cost_based_on'		=> 'Cost Based On <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Shipping rate calculation based on Weight/Item/Price.</span></span>',
			'fee'			=> 'Base Cost <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Base/Fixed cost of the shipping irrespective of the weight/item count/price.</span></span>',
			'cost'			=> 'Cost Per Unit <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">Per weight/item count/price unit cost. This cost will be added on above the base cost.If select Based on as weight, Total shipping Cost = Base cost + (order weight - minimum weight) * cost per unit.</span></span>',
			'weigh_rounding' 	=> 'Rounding off <span class="xa-tooltip"><img src="'.site_url('/wp-content/plugins/woocommerce/assets/images/help.png').'" height="16" width="16" /><span class="xa-tooltiptext">How would you like to round weight/item count/price? Lets take an example with weight. if the value entered is 0.5 and the order weight is 4.4kg then shipping cost will be calculated for 4.5kg, if the value entered is 1 and the order weight is 4.4kg then shipping cost will be calculated for 5kg, if the value entered is 0 and the order weight is 4.4kg then shipping cost will be calculated for 4.4 kg.</span></span>'
		);
		$columns = array( 'cb' => 'cb' );
		foreach($all as $key=>$val){
			if( in_array($key,$this->displayed_columns) ){
				$columns[$key]=$val;
			}
		}
		return $columns;
	}

	function prepare_items() {

		$rules_per_page 		= self::$rules_per_page;		// Items / rules to be displayed per page
		$columns 				= $this->get_columns();
		$hidden 				= array();
		$sortable 				= array();
		$current_page 			= ! empty($_GET['paged']) ? $_GET['paged'] : 1;
		$this->_column_headers 	= array( $columns, $hidden, $sortable );
		$total_items 			= count($this->data);				// Total number of items / rules
		// For pagination
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $rules_per_page
			  )
		);
		$this->items 			= array_slice( $this->data, ( ( $current_page - 1 ) * $rules_per_page ), $rules_per_page );	// Current page rules only
	}

	/**
	 * Get the current Page number for navigation.
	 * @return int
	 */
	public function get_pagenum(){
		return ! empty($_GET['paged']) ? $_GET['paged'] : 1;
	}
	
	function column_default( $item, $column_name ) {
		
		$rule_no = $item['ID'];

		switch( $column_name ) { 
			case 'shipping_name':
				$rule_text = isset($this->data[$rule_no]) ? $this->wf_rule_to_text($rule_no ,$this->data[$rule_no]) : null;

				$html 	 = "<textarea rows='2'  wrap='soft' readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=$column_name name=rate_matrix[$rule_no][$column_name] value=".$item[ $column_name ]." >".$item[ $column_name ]."</textarea>";
				$html 	.= "<div style='height:20px;margin-bottom:5px;width:max-content;'><a class='button button-primary edit' style='display:none;margin-top: 10px;maring-left:5px;' >Edit</a>";
				$html 	.= "<a class='button-primary delete' style='display:none;margin-top: 10px;maring-left:5px;' >Delete</a>";
				$html 	.= "<a class='button-primary  duplicate_row' style='display:none;margin-top: 20px;maring-left:5px;' >Duplicate</a>";
				$html 	.= "<a class='button-primary edit_mode revert_changes' style='display:none;margin-top: 20px;maring-left:5px;' >Revert changes</a></div>"; 
				$html	.= ! empty($rule_text) ? "<p class='rule_desc' style =' display : none; width : 1050px; padding: 10px; margin-left : 5px; margin-top : 10px;'>$rule_text</p>" : null;
				return $html;
				break;

			case 'method_group':
			case 'postal_code':
			case 'weigh_rounding':
			case 'city':
				$tmpval = !empty($item[ $column_name ]) ? $item[ $column_name ] : '';
				return "<textarea rows='2'  wrap='soft' readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=$column_name name=rate_matrix[$rule_no][$column_name] value=".$tmpval." >".$tmpval."</textarea>";
				break;

			case 'weight':
				$max_val = !empty($item['max_weight']) ? $item['max_weight'] : '';
				$min_val = !empty($item['min_weight']) ? $item['min_weight'] : (!empty($max_val) ? 0 : '');
				return "<input autocomplete=off readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=min_weight name=rate_matrix[$rule_no][min_weight] value='".$min_val."' style='width: 45%;text-align: right;'/>-<input autocomplete=off readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=max_weight name=rate_matrix[$rule_no][max_weight] value='".$max_val."' style='width: 45%;'/>";
				break;

			case 'item':
				$max_val = !empty($item['max_item']) ? $item['max_item'] : '';
				$min_val = !empty($item['min_item']) ? $item['min_item'] : (!empty($max_val) ? 0 : '');
				return "<input autocomplete=off readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=min_item name=rate_matrix[$rule_no][min_item] value='".$min_val."' style='width: 45%;text-align: right;'/>-<input autocomplete=off readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=max_item name=rate_matrix[$rule_no][max_item] value='".$max_val."' style='width: 45%;'/>";
				break;

			case 'price':
				$max_val = !empty($item['max_price']) ? $item['max_price'] : '';
				$min_val = !empty($item['min_price']) ? $item['min_price'] : (!empty($max_val) ? 0 : '');
				return "<input autocomplete=off readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=min_price name=rate_matrix[$rule_no][min_price] value='".$min_val."' style='width: 45%;text-align: right;'/>-<input autocomplete=off readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=max_price name=rate_matrix[$rule_no][max_price] value='".$max_val."' style='width: 45%;'/>";
				break;

			case  'fee'	:
			case  'cost':
				$tmpval=!empty($item[ $column_name ])?$item[ $column_name ]:'';
				return "<input autocomplete=off readonly class='xa_sp_label typetext' type=text rule_no=$rule_no rule_col_name=$column_name name=rate_matrix[$rule_no][$column_name] value='".$tmpval."' />";
				break;
			
			case  'cost_based_on':
				return $this->xa_load_textarea_colomn( $rule_no, 'cost_based_on', $item, $this->cost_based_on );
				break;

			case  'zone_list':
				return $this->xa_load_textarea_colomn( $rule_no, 'zone_list', $item, $this->zone_list );
				break;

			case  'country_list':
				return $this->xa_load_textarea_colomn( $rule_no, 'country_list', $item, $this->country_list );
				break;

			case  'state_list':
				return $this->xa_load_textarea_colomn( $rule_no, 'state_list', $item, $this->state_list );
				break;

			case  'shipping_class':
				return $this->xa_load_textarea_colomn( $rule_no, 'shipping_class', $item, $this->shipping_class );
				break;

			case  'product_category':
				return $this->xa_load_textarea_colomn( $rule_no, 'product_category', $item, $this->product_category );
				break;

			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	function xa_load_textarea_colomn( $rule_no, $column_name, $item, $value='' ){
		$output='';
		if(!empty($item[$column_name]) && is_array($item[$column_name])){
			foreach($item[$column_name] as $key=>$val){
				$name = ( !empty($value) && isset( $value[$val] ) ) ? $value[$val] : $val;
				$output = $output. "<textarea readonly class='xa_sp_label typecombo' type=text rule_no=$rule_no rule_col_name=$column_name index_val=$val >$name</textarea>"
					. "<input hidden readonly  type=text rule_no=$rule_no rule_col_name=$column_name name=rate_matrix[$rule_no][$column_name][$key] value='$val' />";
			}
		}elseif(!empty($item[$column_name])){
			$tmpval=!empty($item[ $column_name ])?$item[ $column_name ]:'1';
			return "<textarea readonly class='xa_sp_label typecombo' type=text rule_no=$rule_no rule_col_name=$column_name index_val=$item[$column_name] >".$tmpval."</textarea>"
				. "<input hidden readonly  type=text rule_no=$rule_no rule_col_name=$column_name name=rate_matrix[$rule_no][$column_name] value='".$tmpval."' />";
		}else{
			return "<textarea readonly class='xa_sp_label typecombo' type=text rule_no=$rule_no rule_col_name=$column_name index_val='' >	  ...</textarea>";
		}
		return $output;
	}

	function get_bulk_actions() {
		return array('delete'=>'Delete','duplicate'=>'Duplicate');
	}

	function column_cb( $item ) {
		return  sprintf('<input id="cb-select-%s" type="checkbox" name="sp_selected_rules[]" value="%s">',$item['ID'],$item['ID']);
	}

	function extra_tablenav( $which ){
		$last_index=!empty($this->data)?max(array_keys($this->data)):0;
		echo '<input type="submit" style="margin: 2px;" class="button bulk_action_btn" value="Apply">';
		echo '<input type="submit" style="margin: 2px;margin-left:10px;" class="button addnewbtn" value="Add New">';
		echo "<img style='margin: 7px 3px;' src='".wf_plugin_url()."\includes\img\colorcode.png' />";
		echo '<input type="hidden" id="last_row_index" value="'.$last_index.'" />';
	}
	
	//Funtion to display the rule text
	function wf_rule_to_text($key ,$box){

		$weight_unit 	= strtolower( get_option('woocommerce_weight_unit') );
		$currency_symbol = get_woocommerce_currency_symbol();
		$text = "";
		//TODO country_list state_list shipping_class postal_code  
		if(!empty($box['min_weight']) && in_array( 'weight',$this->displayed_columns ) )  $text .= " If the order weight is more than ".$box['min_weight']."$weight_unit";	
		if(!empty($box['max_weight']) && in_array( 'weight',$this->displayed_columns ) ) $text .= (empty($box['min_weight']) ? "If the order weight is" : " and") . " less than or equal to ".$box['max_weight']."$weight_unit";
		if(!empty($box['min_item']) && in_array( 'item',$this->displayed_columns ) )  $text .= (!empty($text) ?  " and" : " If") . " the order item count is more than ".$box['min_item'];	
		if(!empty($box['max_item']) && in_array( 'item',$this->displayed_columns ) ) $text .= (empty($box['min_item']) ? "If the order item count is" : " and") . " less than or equal to ".$box['max_item'];
		if(!empty($box['min_price']) && in_array( 'price',$this->displayed_columns ) )  $text .= (!empty($text) ?  " and" : " If") . " the price is more than ".$box['min_price']."$currency_symbol";	
		if(!empty($box['max_price']) && in_array( 'price',$this->displayed_columns ) ) $text .= (empty($box['min_price']) ? "If the price  is" : " and") . " less than or equal to ".$box['max_price']."$currency_symbol";
		if(!empty($box['fee']) && in_array( 'fee',$this->displayed_columns ) ) $text .= (!empty($text) ?  " then" : "") . " shipping cost is $currency_symbol".$box['fee'];					
		if(!empty($box['cost']) && in_array( 'cost',$this->displayed_columns ) ){	
			if(!empty($box['cost_based_on']) && $box['cost_based_on'] == "item"){
				$text .= (!empty($box['fee']) ?  " +" : " shipping cost is") . " per item  $currency_symbol".$box['cost'];
				$text .= empty($box['min_item']) ? "." : " for the remaining item count above ".$box['min_item'];					
			}
			elseif(!empty($box['cost_based_on']) && $box['cost_based_on'] == "price"){
				$text .= (!empty($box['fee']) ?  " +" : " shipping cost is") . " per $currency_symbol $currency_symbol".$box['cost'];
				$text .= empty($box['min_price']) ? "." : " for the remaining price above ".$box['min_price']."$currency_symbol.";					
			}else{
				$text .= (!empty($box['fee']) ?  " +" : " shipping cost is") . " per $weight_unit  $currency_symbol".$box['cost'];
				$text .= empty($box['min_weight']) ? "." : " for the remaining weight above ".$box['min_weight']."$weight_unit.";					
			}			
		}
		if(!empty($box['weigh_rounding']) && in_array( 'weigh_rounding', $this->displayed_columns ) ){
			if(!empty($box['cost_based_on']) && $box['cost_based_on'] == "item"){
				$text .= "(Item count is rounded up to the nearest ".$box['weigh_rounding'].").";
			}
			elseif(!empty($box['cost_based_on']) && $box['cost_based_on'] == "price"){
				$text .= "(Price is rounded up to the nearest ".$box['weigh_rounding']."$currency_symbol).";
			}else{
				$text .= "(Weight is rounded up to the nearest ".$box['weigh_rounding']."$weight_unit).";
			}			
			
		}			
		return $text;
	}
}