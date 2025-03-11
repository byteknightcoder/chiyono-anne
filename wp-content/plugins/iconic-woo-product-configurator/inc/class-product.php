<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Iconic_PC_Product.
 *
 * @class    Iconic_PC_Product
 * @version  1.0.0
 * @since    1.2.0
 * @author   Iconic
 */
class Iconic_PC_Product {
	/**
	 * Run.
	 */
	public static function run() {
		add_filter( 'woocommerce_single_product_image_thumbnail_html', array( __CLASS__, 'single_product_image_thumbnail_html' ), 10, 2 );
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( __CLASS__, 'dropdown_variation_attribute_options_html' ), 10, 2 );
	}

	/**
	 * Replace single product image.
	 *
	 * @param string $html
	 * @param int    $post_thumbnail_id
	 *
	 * @return false|string
	 */
	public static function single_product_image_thumbnail_html( $html, $post_thumbnail_id = false ) {
		global $product, $post, $jckpc;

		// Helps to prevent the re-use of the featured image
		// from generating additional PC markup in the gallery.
		static $pc_html_added = false;

		if ( $pc_html_added ) {
			return $html;
		}

		if ( ! $product ) {
			$single_product_block_product_id = Iconic_PC_Blocks::get_single_product_block_product_id( $post );
			$product                         = wc_get_product( $single_product_block_product_id );
		}

		if ( empty( $product ) ) {
			return $html;
		}

		$configurator_enabled = self::is_configurator_enabled( $product->get_id() );

		if ( ! $configurator_enabled ) {
			return $html;
		}

		$featured_image_id = $product->get_image_id();

		if ( $post_thumbnail_id && (int) $featured_image_id !== (int) $post_thumbnail_id ) {
			return $html;
		}

		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
		$thumbnail_src     = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
		$thumbnail_url     = ( ! empty( $thumbnail_src[0] ) ) ? $thumbnail_src[0] : '';
		$alt_text          = trim( wp_strip_all_tags( get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true ) ) );
		$pc_html_added     = true;

		ob_start();
		?>
		<div class="woocommerce-product-gallery__image woocommerce-product-gallery__image--jckpc" data-thumb="<?php echo esc_url( $thumbnail_url ); ?>" data-thumb-alt="<?php echo esc_attr( $alt_text ); ?>">
			<?php $jckpc->display_product_image( $product->get_id() ); ?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Add sanitised attribute names to variable dropdowns.
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return string|string[]
	 */
	public static function dropdown_variation_attribute_options_html( $html, $args ) {
		$html = str_replace( 'select id', 'select data-iconic_pc_layer_id="' . Iconic_PC_Helpers::sanitise_str( $args['attribute'], '', false ) . '" id', $html );
		return $html;
	}

	/**
	 * Get image size name.
	 *
	 * @param $size
	 *
	 * @return string
	 */
	public static function get_image_size( $size ) {
		$is_woo_greater_than_3 = version_compare( WC_VERSION, '3.0.0', '>=' );

		switch ( $size ) {
			case 'single':
				return $is_woo_greater_than_3 ? 'woocommerce_single' : 'shop_single';
			case 'thumbnail':
				return $is_woo_greater_than_3 ? 'woocommerce_thumbnail' : 'shop_thumbnail';
			case 'catalog':
				return $is_woo_greater_than_3 ? 'woocommerce_thumbnail' : 'shop_catalog';
			default:
				return $size;
		}
	}

	/**
	 * Get gallery image ids.
	 *
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	public static function get_gallery_image_ids( $product ) {
		if ( method_exists( $product, 'get_gallery_image_ids' ) ) {
			return $product->get_gallery_image_ids();
		} else {
			return $product->get_gallery_attachment_ids();
		}
	}

	/**
	 * Get set images for product.
	 *
	 * @param int $product_id
	 *
	 * @return array|bool
	 */
	public static function get_set_images( $product_id ) {
		static $images = array();

		if ( ! isset( $images[ $product_id ] ) ) {
			$images[ $product_id ] = get_post_meta( $product_id, 'jckpc_images', true );
		}

		return apply_filters( 'iconic_pc_set_images', $images[ $product_id ], $product_id );
	}

	/**
	 * Get product attributes in a unified format.         * Get static layers.
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	public static function get_static_layers( $product_id ) {
		$images     = array();
		$set_images = self::get_set_images( $product_id );

		if ( ! is_array( $set_images ) ) {
			return $images;
		}

		foreach ( $set_images as $layer_id => $image_id ) {
			if ( strpos( $layer_id, 'jckpc-static-' ) === false ) {
				continue;
			}

			$images[ $layer_id ] = absint( $image_id );
		}

		return $images;
	}

	/**
	 * Get background layer.
	 *
	 * @param $product_id
	 *
	 * @return mixed
	 */
	public static function get_background_layer( $product_id ) {
		static $background_layers = array();

		if ( ! isset( $background_layers[ $product_id ] ) ) {
			$set_images                       = self::get_set_images( $product_id );
			$background_layers[ $product_id ] = isset( $set_images['background'] ) ? absint( $set_images['background'] ) : null;
		}

		return apply_filters( 'iconic_pc_background_layer', $background_layers[ $product_id ], $product_id );
	}

	/**
	 * Get default attributes.
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	public static function get_default_attributes( $product_id ) {
		static $attributes = array();

		if ( ! isset( $attributes[ $product_id ] ) ) {
			$attributes[ $product_id ] = array();
			$product                   = wc_get_product( $product_id );
			$variation_defaults        = $product->get_default_attributes();
			$defaults                  = get_post_meta( $product_id, 'jckpc_defaults', true );

			if ( $defaults ) {
				foreach ( $defaults as $attribute => $value ) {
					$attribute = str_replace( 'jckpc-', '', $attribute );
					$value     = str_replace( 'jckpc-', '', $value );

					$attributes[ $product_id ][ $attribute ] = $value;
				}
			}

			$attributes[ $product_id ] = array_filter( $attributes[ $product_id ] );

			$attributes[ $product_id ] = wp_parse_args( $attributes[ $product_id ], $variation_defaults );
		}

		return apply_filters( 'iconic_pc_default_attributes', $attributes[ $product_id ], $product_id );
	}

	/**
	 * Get conditionals for product.
	 *         *
	 *
	 * @param int $product_id * @param int $product_id
	 *                        *
	 *
	 * @return array|bool
	 */
	public static function get_conditionals( $product_id ) {
		static $conditionals = array();

		if ( ! isset( $conditionals[ $product_id ] ) ) {
			$conditionals[ $product_id ] = get_post_meta( $product_id, 'jckpc_conditionals', true );
		}

		return apply_filters( 'iconic_pc_conditionals', $conditionals[ $product_id ], $product_id );
	}

	/**
	 * Get product attributes in a unified format.
	 *
	 * @param int  $product_id
	 * @param bool $prefixed
	 *
	 * @return array
	 */
  public static function get_attributes( $product_id, $prefixed = false ) {
		if ( empty( $product_id ) ) {
			return array();
		}
  
		static $attribute_values = array();

		$type = $prefixed ? 'prefix' : 'noprefix';

		if ( ! empty( $attribute_values[ $product_id ][ $type ] ) ) {
			return $attribute_values[ $product_id ][ $type ];
		}

		$product                                  = wc_get_product( $product_id );
		$attributes                               = $product->get_attributes();
		$attribute_values[ $product_id ][ $type ] = array();

		if ( empty( $attributes ) ) {
			return $attribute_values[ $product_id ][ $type ];
		}

		foreach ( $attributes as $attribute_data ) {
			if ( ! $attribute_data['is_variation'] ) {
				continue;
			}

			$attribute_name = Iconic_PC_Helpers::sanitise_str( $attribute_data['name'], '', $prefixed );

			$attribute_values[ $product_id ][ $type ][ $attribute_name ] = array();

			$attribute_value_i = 0;

			if ( $attribute_data['is_taxonomy'] ) {
				$tax   = get_taxonomy( $attribute_data['name'] );
				$terms = get_terms( $attribute_data['name'], array( 'hide_empty' => false ) );

				$attribute_values[ $product_id ][ $type ][ $attribute_name ]['name'] = $tax->labels->name;

				if ( is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						if ( has_term( $term->term_id, $attribute_data['name'], $product_id ) ) {
							$attribute_values[ $product_id ][ $type ][ $attribute_name ]['values'][ $attribute_value_i ]['att_val_name'] = $term->name;
							$attribute_values[ $product_id ][ $type ][ $attribute_name ]['values'][ $attribute_value_i ]['att_val_slug'] = $term->slug;
							$attribute_values[ $product_id ][ $type ][ $attribute_name ]['values'][ $attribute_value_i ]['att_val_id']   = $term->term_id;
							$attribute_values[ $product_id ][ $type ][ $attribute_name ]['values'][ $attribute_value_i ]['att_val_json'] = $term->slug;
							$attribute_value_i ++;
						}
					}
				}
			} else {
				$terms = explode( ' | ', $attribute_data['value'] );

				$attribute_values[ $product_id ][ $type ][ $attribute_name ]['name'] = $attribute_data['name'];

				if ( is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						$attribute_values[ $product_id ][ $type ][ $attribute_name ]['values'][ $attribute_value_i ]['att_val_name'] = $term;
						$attribute_values[ $product_id ][ $type ][ $attribute_name ]['values'][ $attribute_value_i ]['att_val_slug'] = sanitize_title( $term );
						$attribute_values[ $product_id ][ $type ][ $attribute_name ]['values'][ $attribute_value_i ]['att_val_json'] = $term;
						$attribute_value_i ++;
					}
				}
			}
		}

		return $attribute_values[ $product_id ][ $type ];
	}

	/**
	 * Get Attribute Value ID
	 *
	 * Get the att val ID for use when checking or altering
	 * stock levels for individual attribute values.
	 *
	 * @param int    $product_id
	 * @param string $chosen_attribute_slug
	 * @param string $chosen_attribute_value
	 *
	 * @return string|bool
	 */
	public static function get_attribute_value_id( $product_id, $chosen_attribute_slug, $chosen_attribute_value ) {
		static $attribute_value_ids = array();

		$chosen_attribute_slug            = Iconic_PC_Helpers::sanitise_str( $chosen_attribute_slug );
		$sanitised_chosen_attribute_value = Iconic_PC_Helpers::sanitise_str( $chosen_attribute_value );
		$key                              = sprintf( '%d_%s_%s', $product_id, $chosen_attribute_slug, $sanitised_chosen_attribute_value );

		if ( isset( $attribute_value_ids[ $key ] ) ) {
			return $attribute_value_ids[ $key ];
		}

		$attribute_value_ids[ $key ] = false;
		$available_attributes        = self::get_attributes( $product_id );

		if ( empty( $available_attributes ) ) {
			return false;
		}

		foreach ( $available_attributes as $attribute_slug => $attribute_data ) {
			$attribute_slug = Iconic_PC_Helpers::sanitise_str( $attribute_slug );

			if ( $attribute_slug !== $chosen_attribute_slug ) {
				continue;
			}

			foreach ( $attribute_data['values'] as $attribute_value ) {
				if ( ! in_array( $chosen_attribute_value, array( $attribute_value['att_val_name'], $attribute_value['att_val_slug'] ) ) ) {
					continue;
				}

				$attribute_value_slug        = Iconic_PC_Helpers::sanitise_str( $attribute_value['att_val_slug'] );
				$attribute_value_ids[ $key ] = sprintf( '%s_%s', $attribute_slug, $attribute_value_slug );
				break;
			}
		}

		return $attribute_value_ids[ $key ];
	}

	/**
	 * Get JSON data for the given product id.
	 *
	 * @param int|bool $product_id Product ID.
	 *
	 * @return array
	 */
	public static function get_json_data( $product_id ) {
		$json_data          = array();
		$product_attributes = self::get_attributes( $product_id );

		foreach ( $product_attributes as $key => $value ) {
			$array_key               = Iconic_PC_Helpers::sanitise_str( $key );
			$json_data[ $array_key ] = array();

			foreach ( $value['values'] as $attr_key => $attr_value ) {
				$value_key                             = Iconic_PC_Helpers::sanitise_str( $attr_value['att_val_json'], '', false, true );
				$json_data[ $array_key ][ $value_key ] = self::get_image_layer_json( $product_id, $attr_value['att_val_slug'], $array_key );
			}
		}

		return apply_filters( 'jckpc_get_json_data', $json_data, $product_id );
	}

	/**
	 * Get conditional data for the given product id.
	 *
	 * @param int|bool $product_id Product ID.
	 *
	 * @return array
	 */
	public static function get_conditional_json_data( $product_id ) {
		$json_data         = array();
		$conditionals      = self::get_conditionals( $product_id );
		$mapped_attributes = self::get_mapped_attributes( $product_id );

		if ( ! empty( $conditionals ) ) {
			foreach ( $conditionals as $attribute => $conditional ) {
				if ( empty( $conditional ) ) {
					continue;
				}

				foreach ( $conditional as $index => $data ) {
					if ( empty( $data['values'] ) ) {
						continue;
					}

					$json_data[ $attribute ][ $index ]['rules'] = $conditionals[ $attribute ][ $index ]['rules'];

					foreach ( $data['values'] as $attr_term => $image_id ) {
						$image_html = '';

						if ( ! empty( $image_id ) ) {
							$image_html = wp_get_attachment_image(
								$image_id,
								self::get_image_size( 'single' ),
								false
							);
						}

						$json_data[ $attribute ][ $index ]['value'][] = array(
							'attribute' => isset( $mapped_attributes[ $attribute ][ $attr_term ] ) ? $mapped_attributes[ $attribute ][ $attr_term ] : $attr_term,
							'value'     => array(
								'image_id'   => $image_id,
								'image_html' => $image_html,
							),
						);
					}
				}
			}
		}

		return apply_filters( 'jckpc_get_conditional_json_data', $json_data, $product_id );
	}

	/**
	 * Map sanitised attributes to original value.
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	public static function get_mapped_attributes( $product_id ) {
		static $mapped_attributes = array();

		if ( isset( $mapped_attributes[ $product_id ] ) ) {
			return $mapped_attributes[ $product_id ];
		}

		$mapped_attributes[ $product_id ] = array();

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return $mapped_attributes[ $product_id ];
		}

		$attributes = $product->get_attributes();

		foreach ( $attributes as $attributes_key => $attribute ) {
			$slugs = $attribute->get_slugs();

			if ( empty( $slugs ) ) {
				continue;
			}

			$attributes_key = Iconic_PC_Helpers::sanitise_str( $attributes_key, $attribute->get_name() );

			$mapped_attributes[ $product_id ][ $attributes_key ] = array();

			foreach ( $slugs as $slug ) {
				$sanitised_slug = Iconic_PC_Helpers::sanitise_str( $slug );

				$mapped_attributes[ $product_id ][ $attributes_key ][ $sanitised_slug ] = $slug;
			}
		}

		return $mapped_attributes[ $product_id ];
	}

	/**
	 * Get mapped attribute.
	 *
	 * @param int    $product_id
	 * @param string $attribute_value
	 * @param string $attribute_key
	 * @param bool   $sanitized
	 *
	 * @return int|mixed|string
	 */
	public static function get_mapped_attribute( $product_id, $attribute_value, $attribute_key, $sanitized = true ) {
		$mapped_attributes = self::get_mapped_attributes( $product_id );

		if ( empty( $mapped_attributes ) || ! isset( $mapped_attributes[ $attribute_key ] ) ) {
			return $attribute_value;
		}

		$mapped_attributes = $mapped_attributes[ $attribute_key ];

		if ( $sanitized ) {
			foreach ( $mapped_attributes as $mapped_attribute_value => $mapped_attribute_value_sanitized ) {
				if ( $attribute_value !== $mapped_attribute_value_sanitized ) {
					continue;
				}

				return $mapped_attribute_value;
			}
		}

		return isset( $mapped_attributes[ $attribute_value ] ) ? $mapped_attributes[ $attribute_value ] : $attribute_value;
	}

	/**
	 * Is configurator enabled?
	 *
	 * @param $product_id
	 *
	 * @return bool
	 */
	public static function is_configurator_enabled( $product_id = false ) {
		if ( ! $product_id ) {
			global $product;

			if ( ! $product || !method_exists($product, 'get_id') ) {
				return false;
			}

			$product_id = $product->get_id();
		}

		static $enabled = array();

		if ( isset( $enabled[ $product_id ] ) ) {
			return $enabled[ $product_id ];
		}

		$product = wc_get_product( $product_id );

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			$enabled[ $product_id ] = false;

			return $enabled[ $product_id ];
		}

		$enabled[ $product_id ] = $product->get_meta( 'jckpc_enabled', true ) === 'yes';

		return $enabled[ $product_id ];
	}

	/**
	 * Get single image layer for use in JSON.
	 * Get single image layer for use in JSON.
	 *
	 * @param int    $product_id   Product ID.
	 * @param string $selected_val Selected attribute value.
	 * @param string $selected_att Selected attribute name.
	 *
	 * @return array
	 */
	public static function get_image_layer_json( $product_id, $selected_val, $selected_att ) {
		global $jckpc;

		$selected_val = Iconic_PC_Helpers::sanitise_str( $selected_val );

		$mapped_attribute      = self::get_mapped_attribute( $product_id, $selected_val, $selected_att );
		$selected_att_stripped = Iconic_PC_Helpers::strip_prefix( $selected_att );
		$image_size            = self::get_image_size( 'single' );
		$image_html            = '';
		$set_images            = self::get_set_images( $product_id );
		$the_term              = get_term_by( 'slug', $mapped_attribute, $selected_att_stripped );
		$default_image_id      = $the_term ? $jckpc::get_default_image( $the_term->term_id ) : '';

		if ( empty( $selected_val ) ) {
			$defaults     = get_post_meta( $product_id, 'jckpc_defaults', true );
			$selected_val = isset( $defaults[ $selected_att ] ) ? $defaults[ $selected_att ] : false;
		}

		if ( ! empty( $selected_val ) ) {
			$image_id = ! empty( $set_images[ $selected_att ][ $selected_val ] ) ? $set_images[ $selected_att ][ $selected_val ] : $default_image_id;

			if ( ! empty( $image_id ) ) {
				$image_html = wp_get_attachment_image(
					$image_id,
					$image_size,
					false,
					array(
						'class' => sprintf( 'iconic-pc-image-%s', esc_attr( $selected_att ) ),
					)
				);
			}
		}

		return array(
			'image_id'   => $image_id,
			'image_html' => $image_html,
		);
	}
}
