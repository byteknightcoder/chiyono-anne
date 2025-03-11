<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }


    if (!function_exists('wf_plugin_path')){
        function wf_plugin_path() {
                return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }
    }
    
    if (!function_exists('wf_pre_loaded_data')){
        $data_file = wf_plugin_path() . '/data/woocommerce-shipping-pro-pre-loaded-data.php';
        if (file_exists($data_file))
            include_once $data_file;
    }

    if (!function_exists('wf_get_settings_url')){
        function wf_get_settings_url(){
            return version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
        }
    }
    
    if (!function_exists('wf_plugin_override')){
        add_action( 'plugins_loaded', 'wf_plugin_override' );
        function wf_plugin_override() {
            if (!function_exists('WC')){
                function WC(){
                    return $GLOBALS['woocommerce'];
                }
            }
        }
    }

    if (!function_exists('wf_get_shipping_countries')){
        function wf_get_shipping_countries(){
            $woocommerce = WC();
            $shipping_countries = method_exists($woocommerce->countries, 'get_shipping_countries')
                    ? $woocommerce->countries->get_shipping_countries()
                    : $woocommerce->countries->countries;
                        
                        $shipping_countries['any_country']='Any Country';
                        $shipping_countries['rest_world']='Rest of the world';                        
            return $shipping_countries;
        }
    }
        if (!function_exists('wf_get_category_list')){
            function wf_get_category_list()
            {   
                $category_list=get_terms( 'product_cat', array('fields' => 'id=>name'));
		foreach ( $category_list as $id => $name )
		{
			$category_list[$id] = str_replace('"', '\"', $name);
		}
                return (array('any_product_category'=>'Any Product Category','rest_product_category'=>'Rest of the Product categories') + $category_list);	//don't use array_merge because that will reindex the numeric index so category won't work
            }
        }
    
        if (!function_exists('wf_get_shipping_class_list')){
            function wf_get_shipping_class_list()
            {   
                $shipping_class_list=array('any_shipping_class'=>'Any Shipping Class','rest_shipping_class'=>'Rest of the shipping classes');
                $shipping_classes=WC()->shipping->get_shipping_classes();
                foreach($shipping_classes as $class_obj)
                {
                    $shipping_class_list[$class_obj->slug] = str_replace( '"', '\"', $class_obj->name );
                }
                return $shipping_class_list;
            }
        }
        if (!function_exists('wf_get_state_list')){
            function wf_get_state_list($shipping_countries)
            {   
                $state_list=array('any_state'=>'Any State','rest_country'=>'Rest of the country');
                $list=array();
                foreach ( $shipping_countries as $key=>$value)
                {   $states =  WC()->countries->get_states($key);
                    if(!empty($states))
                    foreach ($states as $state_key=>$state_value)
                    {
                        $index=esc_attr( $key ) . ':'.$state_key;
                        $list[$index]=esc_attr($state_value);                        
                    }
                }
                return array_merge($state_list,$list);
            }
        }
        
         if (!function_exists('wf_state_dropdown_options')){
            function wf_state_dropdown_options( $countries=array(),$selected_states = array(), $escape = false ) {

                $options=array();
                $i =0;
                
                if ( $countries ) foreach ( $countries as $key=>$value) :
                    if ( $states =  WC()->countries->get_states( $key ) ) :
                        foreach ($states as $state_key=>$state_value) :
                            $options[$i]['key']     = esc_attr( $key ) .':'.$state_key;
                            $options[$i]['name']    = esc_attr( $value ) .' - '.($escape ? esc_js($state_value) : $state_value);
                            $i++;
                        endforeach;
                    endif;
                endforeach;

                $options[$i]['key']     = 'any_state';
                $options[$i]['name']    = 'Any State';
                
                $i++;

                $options[$i]['key']     = 'rest_country';
                $options[$i]['name']    = 'Rest of the Country';

                $states = json_encode($options);

                return $states;
            }
        }
        
        if (!function_exists('wf_get_zone_list')){        
            function wf_get_zone_list(){
                    $zone_list = array();
                    if( class_exists('WC_Shipping_Zones') ){
                            $zones_obj = new WC_Shipping_Zones;
                            $zones = $zones_obj::get_zones();
                            $zone_list[0] = 'Rest of the World'; //rest of the zone always have id 0, which is not available in the method get_zone()
                            foreach ($zones as $key => $zone) {
                                    $zone_list[$key] = $zone['zone_name'];
                            }
                    }
                    return $zone_list;
            }
        }
        if (!function_exists('wf_shipping_class_dropdown_options')){
                function wf_shipping_class_dropdown_options( $selected_class = array()) {
                    $shipping_classes=WC()->shipping->get_shipping_classes();
        if ($shipping_classes) 
                        foreach ( $shipping_classes as $class) :
            echo '<option value="' . esc_attr($class->slug) .'"';
            if (!empty($selected_class) && in_array($class->slug,$selected_class)) echo ' selected="selected"';
            echo '>' . esc_js( $class->name ) . '</option>';
                        endforeach;
                }
    }
    
        
    add_action( 'admin_enqueue_scripts', 'wf_scripts' );
    if (!function_exists('wf_scripts')){
        function wf_scripts() {
           wp_enqueue_script( 'jquery' );
            wp_enqueue_script('common');
            wp_enqueue_script('wp-lists');
            wp_enqueue_script('postbox');
           
        }
    }
    
    if (!function_exists('wf_plugin_url')){
        function wf_plugin_url() {
            return untrailingslashit( plugins_url( '/', __FILE__ ) );
        }
    }

    if (!function_exists('wf_plugin_basename')){
        function wf_plugin_basename() {
            return 'woocommerce-shipping-pro/woocommerce-shipping-pro.php';
        }
    }

    if (!function_exists('wf_plugin_activate')){
        function wf_plugin_activate() {
            wf_pre_load_settings();
        }
    }

    if (!function_exists('wf_pre_load_settings')){
        function wf_pre_load_settings(){
            $wf_shipping_pro_config = wf_plugin_configuration();
            if(get_option( 'woocommerce_wf_woocommerce_shipping_pro_settings') == false){
                $matrix_default_value = wf_get_rate_matrix_default();
                if(!empty($matrix_default_value)){
                    $new_settings = array(
                        'enabled' => 'yes',
                        'title' => $wf_shipping_pro_config['method_title'],
                        'rate_matrix' => $matrix_default_value,
                        'displayed_columns' => array(
                            0 => 'shipping_name',
                            1 => 'method_group',
                            2 => 'country_list',
                            3 => 'shipping_class',
                            4 => 'product_category',
                            5 => 'weight',
                            6 => 'item',
                            7 => 'price',
                            8 => 'cost_based_on',
                            9 => 'fee',
                            10 => 'cost',
                            11 => 'weigh_rounding',
                        ) ,
                        'calculation_mode' => 'per_order_max_cost',
                        'tax_status' => 'none',
                        'remove_free_text' => 'no',
                        'debug' => 'no',
                    );
                    update_option( 'woocommerce_wf_woocommerce_shipping_pro_settings', $new_settings);
                }
            }
        }
    }

    if (!function_exists('wf_get_rate_matrix_default')){
        function wf_get_rate_matrix_default(){
            if (function_exists('wf_pre_loaded_data')) 
              return wf_pre_loaded_data();

            return '';
        }
    }
    
    if (!class_exists('wf_woocommerce_shipping_pro_setup')) {
        class wf_woocommerce_shipping_pro_setup {
            public function __construct() {
                if ( is_admin() ) {
                    add_action( 'init', array( $this, 'wf_admin_includes' ) );
                }
                add_filter( 'plugin_action_links_' . wf_plugin_basename(), array( $this, 'plugin_action_links' ) );
                add_action( 'woocommerce_shipping_init', array( $this, 'wf_woocommerce_shipping_pro_init' ) );
                add_filter( 'woocommerce_shipping_methods', array( $this, 'wf_add_woocommerce_shipping_pro_init' ) );
                add_action('wp_ajax_eh_load_shipping_pro_rule',array( $this,"eh_load_shipping_pro_rule"));
                include_once( 'includes/class-wf-matrices-exporter.php' );          
                
				add_action( 'init', array( $this, 'wf_includes' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
            }
            function eh_load_shipping_pro_rule()
            {
                
                wp_die();
            }
            public function wf_includes(){
                if ( ! class_exists( 'wf_order' ) ) {
                    include_once 'includes/class-wf-legacy.php';
                }
            }

            public function wf_admin_includes() {
                if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
                    include( 'includes/class-wf-admin-importers.php' );
                }
            }

            public function wf_woocommerce_shipping_pro_init() {

                if ( ! class_exists( 'wf_woocommerce_shipping_pro_method' ) ) {
                    include_once( 'core/woocommerce-shipping-pro-core.php' );
                }

                if ( ! class_exists( 'PH_WooCommerce_Shipping_Pro' ) ) {
                    include_once( 'core/woocommerce-shipping-pro-zones.php' );
                }

                $this->third_party_plugin_support();
			}
			
			public function admin_scripts() {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'ph-wc-shipping-pro-admin', plugins_url( '/resources/js/admin.js', __FILE__ ), array( 'jquery' ) );
                wp_enqueue_style( 'ph-shipping-pro-common-style', plugins_url( '/resources/css/ph_common_style.css', __FILE__ ) );
			}

            /**
             * To support third party plugins.
             */
            public function third_party_plugin_support(){
                $active_plugins = Ph_WC_Shipping_Pro_Common::get_active_plugins();
                // To support WPML Multiligual plugin
                if( in_array( 'woocommerce-multilingual/wpml-woocommerce.php', $active_plugins) ) {
                    require_once 'includes/third-party-plugin-compatibility/class-ph-wc-shipping-pro-wpml-mulitiligual.php';
                }

                // For WooCommerce Ship to Multiple Addresses
                if( in_array( 'woocommerce-shipping-multiple-addresses/woocommerce-shipping-multiple-address.php', $active_plugins) || in_array( 'woocommerce-shipping-multiple-addresses/woocommerce-shipping-multiple-addresses.php', $active_plugins) ) {
                    require_once 'includes/third-party-plugin-compatibility/class-ph-shipping-pro-woocommerce-shipping-multiple-address.php';
                }

                // For WooCommerce Multicurrency
                if( in_array( 'woocommerce-multicurrency/woocommerce-multicurrency.php', $active_plugins) ) {
                    require_once 'includes/third-party-plugin-compatibility/class-ph-shipping-pro-woocommerce-currency-switcher.php';
                }

                // For WOOCS - WooCommerce Currency Switcher
                if( in_array( 'woocommerce-currency-switcher/index.php', $active_plugins) ) {
                    require_once 'includes/third-party-plugin-compatibility/class-ph-shipping-pro-woocs-currency-switcher.php';
                }
                
            }

            public function wf_add_woocommerce_shipping_pro_init( $methods ){
                
                // This metod will be added under Shipping independent of Zone

                      $methods[] = 'wf_woocommerce_shipping_pro_method';
                      
                // This metod will be added under Zone Shipping Methods

                     $methods['ph_woocommerce_shipping_pro'] = PH_WooCommerce_Shipping_Pro::class;
                
                return $methods;
            }

            public function plugin_action_links( $links ) {
                $plugin_links = array(
                    '<a href="' . admin_url( 'admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_woocommerce_shipping_pro&inner_section=settings' ) . '">' . __( 'Settings', 'wf_woocommerce_shipping_pro' ) . '</a>',
                    '<a href="https://www.pluginhive.com/knowledge-base/category/woocommerce-table-rate-shipping-pro-plugin/" target="_blank">' . __('Documentation', 'wf_woocommerce_shipping_pro') . '</a>',
                    '<a href="https://www.pluginhive.com/support/" target="_blank">' . __('Support', 'wf_woocommerce_shipping_pro') . '</a>'
                );
                return array_merge( $plugin_links, $links );
            }               
        }
        new wf_woocommerce_shipping_pro_setup();
    }