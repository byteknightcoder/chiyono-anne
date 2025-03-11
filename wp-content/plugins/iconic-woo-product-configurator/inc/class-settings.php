<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_PC_Settings.
 *
 * @class    Iconic_PC_Settings
 * @version  1.0.0
 * @author   Iconic
 */
class Iconic_PC_Settings {
	/**
	 * Run.
	 */
	public static function run() {
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_filter( 'iconic_woo_product_configurator_settings_validate', array( __CLASS__, 'tool_install_db' ), 10, 1 );
		add_filter( 'iconic_woo_product_configurator_settings_validate', array( __CLASS__, 'tool_clear_cache' ), 10, 1 );
	}

	/**
	 * Init.
	 */
	public static function init() {
		global $jckpc;

		if ( empty( $jckpc ) ) {
			return;
		}

		$jckpc->set_settings();
	}

	/**
	 * @param array $settings
	 *
	 * @return array
	 */
	public static function tool_install_db( $settings ) {
		if ( ! isset( $_POST['iconic_pc_install_db'] ) ) {
			return $settings;
		}

		Iconic_PC_Inventory::install_db( true );

		add_settings_error( 'iconic_pc_install_db', esc_attr( 'jckpc-success' ), __( 'Tables successfully installed.', 'jckpc' ), 'updated' );

		return $settings;
	}

	/**
	 * Clear cache.
	 */
	public static function tool_clear_cache( $settings ) {
		global $jckpc;

		if ( ! is_user_logged_in() && ! current_user_can( 'manage_options' ) ) {
			return $settings;
		}

		$action = filter_input( INPUT_POST, 'iconic_pc_clear_cache' );

		if ( 'clear_cache' !== $action ) {
			return $settings;
		}

		$jckpc->delete_cached_images();
		$jckpc->delete_all_transient();

		add_settings_error( 'iconic_pc_clear_cache', esc_attr( 'jckpc-success' ), __( 'Cache cleared successfully.', 'jckpc' ), 'updated' );

		return $settings;
	}

}