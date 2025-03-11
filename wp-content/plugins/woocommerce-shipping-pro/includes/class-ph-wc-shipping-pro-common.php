<?php

if( ! class_exists( 'Ph_WC_Shipping_Pro_Common' ) ) {

    class Ph_WC_Shipping_Pro_Common {

        /**
         * Active plugins.
         */
        public static $active_plugins;

        /**
         * Active plugins.
         * @return array.
         */
        public static function get_active_plugins() {
            if( empty(self::$active_plugins) ) {
                self::$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );
                // Multisite case
                if ( is_multisite() ) {
                    self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
                }
            }

            return self::$active_plugins;
		}
		
		/**
		 * Check whether specified plugin is active or not.
		 * @param string $plugin (Required) Path to the main plugin file from plugins directory.
		 * @return boolean
		 */
		public static function is_plugin_active( $plugin ){

			if ( ! self::$active_plugins ) self::get_active_plugins();

			if( ! empty($plugin) )
				return in_array( $plugin, self::$active_plugins ) || array_key_exists( $plugin, self::$active_plugins );
			else
				return false;
		}
        
    }
    new Ph_WC_Shipping_Pro_Common();
}