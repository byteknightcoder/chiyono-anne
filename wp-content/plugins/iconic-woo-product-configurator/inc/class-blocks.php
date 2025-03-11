<?php
/**
 * Blocks class.
 *
 * @package Iconic_PC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Iconic_PC_Blocks class.
 */
class Iconic_PC_Blocks {
	/**
	 * Init.
	 */
	public static function run() {
		add_filter( 'render_block', array( __CLASS__, 'override_block_render' ), 20, 3 );
	}

	/**
	 * Override block render.
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The full block, including name and attributes.
	 * @param WP_Block $instance      The block instance.
	 * 
	 * @return string
	 */
	public static function override_block_render( $block_content, $block, $block_instance ) {
		if ( is_admin() ) {
			return $block_content;
		}

		$blocks_to_override = array(
			'woocommerce/product-image',
		);

		if ( empty( $block['blockName'] ) || ! in_array( $block['blockName'], $blocks_to_override, true ) ) {
			return $block_content;
		}

		if ( empty( $block['attrs']['isDescendentOfSingleProductBlock'] ) ) {
			return $block_content;
		}

		ob_start();
		?>

		<div class="woocommerce-product-gallery woocommerce-product-gallery--with-images images">
			<div class="woocommerce-product-gallery__wrapper">
				<?php echo Iconic_PC_Product::single_product_image_thumbnail_html( $block_content ); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Extract the product ID from the WC single product block.
	 *
	 * @param WP_Post $post WP_Post object instance.
	 * 
	 * @return bool|int
	 */
	public static function get_single_product_block_product_id( $post ) {
		$product_id = false;

		if ( $post && has_block( 'woocommerce/single-product', $post ) ) {
			foreach( parse_blocks( $post->post_content ) as $block ) {
				if ( 
					'woocommerce/single-product' === $block['blockName'] &&
					! empty( $block['attrs']['productId'] )
				) {
					$product_id = absint( $block['attrs']['productId'] );
					break;
				}
			}
		}

		return $product_id;
	}
}
