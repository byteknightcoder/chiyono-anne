<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_PC_Helpers.
 *
 * @class    Iconic_PC_Helpers
 * @version  1.0.1
 * @since    1.2.0
 * @author   Iconic
 */
class Iconic_PC_Helpers {
	/**
	 * Check whether the plugin is inactive.
	 *
	 * Reverse of is_plugin_active(). Used as a callback.
	 *
	 * @since 3.1.0
	 * @see   is_plugin_active()
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 *
	 * @return bool True if inactive. False if active.
	 */
	public static function is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || self::is_plugin_active_for_network( $plugin );
	}

	/**
	 * Check whether the plugin is active for the entire network.
	 *
	 * Only plugins installed in the plugins/ folder can be active.
	 *
	 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
	 * return false for those plugins.
	 *
	 * @since 3.0.0
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 *
	 * @return bool True, if active for the network, otherwise false.
	 */
	public static function is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}
		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get ajax URL.
	 *
	 * @return str
	 */
	public static function get_ajax_url() {
		return WC()->ajax_url();
	}

	/**
	 * Strip prefix.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function strip_prefix( $string ) {
		return str_replace( 'jckpc-', '', $string );
	}

	/**
	 * Sanitise string
	 *
	 * @param string $str           String to be returned.
	 * @param string $alt_str       Alternative string to use.
	 * @param bool   $prefix        Whether or not to add the `jckpc` prefix.
	 * @param bool   $skip_sanitize Whether or not to skip slug sanitization.
	 *
	 * @return string
	 */
	public static function sanitise_str( $str, $alt_str = '', $prefix = true, $skip_sanitize = false ) {
		if ( empty( $str ) && empty( $alt_str ) ) {
			return '';
		}

		if ( $skip_sanitize ) {
			$new_str = str_replace( array( 'attribute_', '%' ), '', $str );
		} else {
			if ( function_exists( 'ctl_sanitize_title' ) ) {
				$alt_str = ! empty( $alt_str ) ? $alt_str : $str;
				$new_str = sanitize_title( $alt_str );
			} else {
				$new_str = str_replace( array( 'attribute_', '%' ), '', sanitize_title( $str ) );
			}
		}

		$has_prefix = 'jckpc-' === substr( $new_str, 0, 6 );

		if ( $prefix && ! $has_prefix ) {
			$new_str = 'jckpc-' . $new_str;
		} elseif ( ! $prefix && $has_prefix ) {
			$new_str = str_replace( 'jckpc-', '', $new_str );
		}

		/**
		 * Filter: modify sanitized string value.
		 *
		 * @filter: iconic_pc_sanitise_string
		 * @since 1.7.1
		 * @param string $new_str String to be returned.
		 * @param string $alt_str Alternative string to use.
		 * @param bool   $prefix  Whether or not to add the `jckpc` prefix.
		 */
		$new_str = apply_filters( 'iconic_pc_sanitise_string', $new_str, $alt_str, $prefix );

		if ( $skip_sanitize ) {
			return $new_str;
		} else {
			return strtolower( $new_str );
		}
	}

	/**
	 * Strip the WPML language code from PC strings.
	 *
	 * @param string $string Sanitized string.
	 *
	 * @return string
	 */
	public static function strip_wpml_language_code( $string ) {
		$current_language = apply_filters( 'wpml_current_language', null );

		if ( null !== $current_language ) {
			$string = preg_replace( '/-' . $current_language . '$/m', '', $string );
		}

		return $string;
	}

	/**
	 * Determine if PC layer inputs should be disabled.
	 *
	 * @return bool
	 */
	public static function maybe_disable_layer_inputs() {
		/**
		 * Filter: disable PC layer inputs.
		 *
		 * @filter iconic_pc_disable_layer_inputs
		 * @since 1.7.1
		 * @param bool $disable True to disable layer inputs.
		 */
		return apply_filters( 'iconic_pc_disable_layer_inputs', false );
	}

	/**
	 * Remove array item by value.
	 *
	 * @param array $array Array.
	 * @param mixed $value Value.
	 *
	 * @return array
	 */
	public static function remove_item_by_value( $array, $value ) {
		if ( ( $key = array_search( $value, $array ) ) !== false ) {
			unset( $array[ $key ] );
		}

		return $array;
	}
}