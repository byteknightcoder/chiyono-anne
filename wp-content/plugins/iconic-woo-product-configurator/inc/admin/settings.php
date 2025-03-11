<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'wpsf_register_settings_iconic_woo_product_configurator', 'iconic_woo_product_configurator_settings' );

/**
 * WooCommerce Product Configurator Settings
 *
 * @param array $wpsf_settings
 *
 * @return array
 */
function iconic_woo_product_configurator_settings( $wpsf_settings ) {
	// Tabs.

	$wpsf_settings['tabs'][] = array(
		'id'    => 'general',
		'title' => __( 'General', 'jckpc' ),
	);

	// Sections.

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'dashboard',
		'section_id'          => 'tools',
		'section_title'       => __( 'Tools', 'jckpc' ),
		'section_description' => '',
		'section_order'       => 20,
		'fields'              => array(
			array(
				'id'       => 'install_db',
				'title'    => __( 'Install Database Tables', 'jckpc' ),
				'subtitle' => __( "If there's an issue with the database tables, you can run this tool to ensure they're all properly installed.", 'jckpc' ),
				'type'     => 'custom',
				'default'  => '<button type="submit" name="iconic_pc_install_db" class="button button-secondary">' . __( 'Install Tables', 'jckpc' ) . '</button>',
			),
			array(
				'id'       => 'clear_cache',
				'title'    => __( 'Clear Cache', 'jckpc' ),
				'subtitle' => __( 'Clean up the temporary images created by Product Configurator.', 'jckpc' ),
				'type'     => 'custom',
				'default'  => '<button type="submit" value="clear_cache" name="iconic_pc_clear_cache" class="button button-secondary">' . __( 'Clear Cache', 'jckpc' ) . '</button>',
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'general',
		'section_id'          => 'cache',
		'section_title'       => __( 'Cache Settings', 'jckpc' ),
		'section_description' => '',
		'section_order'       => 0,
		'fields'              => array(
			array(
				'id'       => 'enable',
				'title'    => __( 'Enable Image Cache', 'jckpc' ),
				'subtitle' => __( "Once added to cart, the customer's final variation image will be cached. Without this, images will be generated dynamically every time, and could slow down your website.", 'jckpc' ),
				'type'     => 'checkbox',
				'default'  => 0,
			),
			array(
				'id'      => 'duration',
				'title'   => __( 'Cache Duration (Hours)', 'jckpc' ),
				'type'    => 'number',
				'default' => 24,
			),
			array(
				'id'       => 'preload_layers',
				'title'    => __( 'Preload Layer Images', 'jckpc' ),
				'subtitle' => __( 'Upon making attribute selections, the layered image will be downloaded and cached in the browser before the image preview is updated, to prevent a white flash while the new image is loading.', 'jckpc' ),
				'type'     => 'checkbox',
				'default'  => 0,
			),
		),
	);

	return $wpsf_settings;
}