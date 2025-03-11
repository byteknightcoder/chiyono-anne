<?php
/**
 * Product Editor related functions.
 *
 * @package Iconic_PC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Automattic\WooCommerce\Admin\BlockTemplates\BlockTemplateInterface;
use Automattic\WooCommerce\Admin\Features\ProductBlockEditor\ProductTemplates\ProductFormTemplateInterface;
use Automattic\WooCommerce\Admin\Features\ProductBlockEditor\BlockRegistry;

/**
 * Iconic_PC_Product_Editor.
 *
 * @class    Iconic_PC_Product_Editor
 * @version  1.0.0
 */
class Iconic_PC_Product_Editor {
	/**
	 * Run
	 */
	public static function run() {
		add_action( 'init', array( __CLASS__, 'on_init' ) );
		add_filter( 'woocommerce_layout_template_after_instantiation', array( __CLASS__, 'add_blocks_to_product_editor' ), 100, 3 );
		add_action( 'woocommerce_rest_insert_product_object', array( __CLASS__, 'update_inventory' ), 10, 3 );
	}

	/**
	 * On init hook
	 */
	public static function on_init() {
		self::register_blocks();
	}

	/**
	 * Register blocks.
	 */
	public static function register_blocks() {
		$page = filter_input( INPUT_GET, 'page' );

		if ( 'wc-admin' !== $page ) {
			return;
		}

		BlockRegistry::get_instance()->register_block_type_from_metadata( ICONIC_PC_PATH . '/blocks/build/product-editor-block/' );
	}

	/**
	 * Add blocks to product editor.
	 *
	 * @param string                 $layout_id Layout ID.
	 * @param string                 $area      Area ID.
	 * @param BlockTemplateInterface $template  Template.
	 *
	 * @return $template
	 */
	public static function add_blocks_to_product_editor( $layout_id, $area, BlockTemplateInterface $template ) {
		if ( ! $template instanceof ProductFormTemplateInterface || 'simple-product' !== $template->get_id() ) {
			return $template;
		}

		$group_id = 'iconic-pc';

		// Create a new group.
		$group = $template->add_group(
			array(
				'id'         => $group_id,
				'order'      => 20,
				'attributes' => array(
					'title' => __( 'Configurator', 'jckpc' ),
				),
			)
		);

		// Add a new section.
		$basic_details = $group->add_section(
			array(
				'id'         => 'iconic-pc-layers',
				'order'      => 10,
				'attributes' => array(
					'title'       => __( 'Product Configurator', 'jckpc' ),
					'description' => __( 'Manage Product Configurator layers.', 'jckpc' ),
				),
			)
		);

		// Add the block.
		$basic_details->add_block(
			array(
				'id'         => 'iconic-pc-product-editor-layers',
				'order'      => 40,
				'blockName'  => 'iconicpc/product-editor',
				'attributes' => array(),
			)
		);

		return $template;
	}

	/**
	 * Update inventory table when product is updated via REST API.
	 *
	 * @param WC_Product $object  Product object.
	 * @param [type]     $request Request.
	 * @param bool       $creating Creating.
	 *
	 * @return void
	 */
	public static function update_inventory( $object, $request, $creating ) {
		$params = $request->get_json_params();

		if ( empty( $params['meta_data'] ) || ! is_array( $params['meta_data'] ) ) {
			return;
		}

		foreach ( $params['meta_data'] as $meta ) {
			if ( 'jckpc_inventory' === $meta['key'] ) {
				Iconic_PC_Inventory::process_product_meta( $object->get_id(), $meta['value'] );
			}
		}
	}
}
