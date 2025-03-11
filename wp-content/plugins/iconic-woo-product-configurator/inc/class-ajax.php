<?php
/**
 * Ajax methods class.
 *
 * @package Iconic_PC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Iconic_PC_Ajax class.
 */
class Iconic_PC_Ajax {
	/**
	 * Init.
	 */
	public static function run() {
		self::add_ajax_actions();
	}

	/**
	 * Add ajax actions.
	 */
	public static function add_ajax_actions() {
		$ajax_events = array(
			'generate_image'                  => true,
			'get_image_layer'                 => true,
			'get_conditional_group'           => false,
			'get_product_attributes_template' => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_iconic_pc_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_iconic_pc_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Generate final image.
	 */
	public static function generate_image() {
		global $jckpc;

		$params = self::ajax_img_get_params();

		if ( ! $params['images']['background']['full'] ) {
			die;
		}

		$size = ! empty( $params['size']['name'] ) ? $params['size']['name'] : 'full';

		// If we have already generated this image, return this
		// image instead of generating a new one.
		$existing_image_url = get_transient( $jckpc::$transient_prefix . $params['imgData']['imgName'] . $size );

		if ( $existing_image_url ) {
			if ( 1 === $params['redirect'] ) {
				wp_safe_redirect( esc_url( $existing_image_url ) );
			} else {
				// IMPORTANT: the php directive `allow_url_fopen` must be
				// enabled on the server, if a CDN is being used to serve
				// generated images, rather than serving images hosted on
				// the server.
				$is_path     = str_contains( $existing_image_url, home_url() );
				$path_or_url = ( $is_path ) ? $params['imgData']['finalImgPath'] : esc_url( $existing_image_url );

				// Validate the URL and abort if the check fails.
				if ( ! $is_path ) {
					$url_exists = wp_remote_head( $path_or_url );

					if ( ! $url_exists || is_wp_error( $url_exists ) ) {
						die();
					}
				}

				$img = imagecreatefrompng( $path_or_url );

				if ( $img && is_a( $img, 'GdImage' ) ) {
					imagesavealpha( $img, true );
					header( 'Content-Type: image/png' );
					imagepng( $img );
					imagedestroy( $img );
				}
			}

			die();
		}

		// Set up image space.
		$canvas_width  = $params['images']['background']['full'][1];
		$canvas_height = $params['images']['background']['full'][2];
		$bg            = imagecreatetruecolor( $canvas_width, $canvas_height );

		imagesavealpha( $bg, true );

		$trans_colour = imagecolorallocatealpha( $bg, 0, 0, 0, 127 );
		imagefill( $bg, 0, 0, $trans_colour );

		if ( is_array( $params['images'] ) ) {
			foreach ( $params['images'] as $index => $image_data ) {
				if ( empty( $image_data['path'] ) ) {
					continue;
				}

				$img = imagecreatefrompng( $image_data['path'] );

				if ( ! $img ) {
					continue;
				}

				imagecopyresized( $bg, $img, 0, 0, 0, 0, $canvas_width, $canvas_height, $canvas_width, $canvas_height );
				imagedestroy( $img );
			}
		}

		$jckpc_img_size = filter_input( INPUT_GET, 'jckpc-img-size', FILTER_SANITIZE_SPECIAL_CHARS );

		if ( 'woocommerce_thumbnail' === $jckpc_img_size ) {
			// Resize the image to the thumbnail size.
			$thumb_width  = $params['size']['width'];
			$thumb_height = $params['size']['height'];

			$thumbnail = imagecreatetruecolor( $thumb_width, $thumb_height );
			imagesavealpha( $thumbnail, true );
			$trans_colour = imagecolorallocatealpha( $thumbnail, 0, 0, 0, 127 );
			imagefill( $thumbnail, 0, 0, $trans_colour );
			imagecopyresampled( $thumbnail, $bg, 0, 0, 0, 0, $thumb_width, $thumb_height, $canvas_width, $canvas_height );

			self::output_image( $params, $thumbnail );
		}

		self::output_image( $params, $bg );
	}

	/**
	 * Save image, save transient, output image and die.
	 *
	 * @param array   $params       Image parameters.
	 * @param GDImage $final_image  Generated image.
	 *
	 * @return void
	 */
	public static function output_image( $params, $final_image ) {
		$size = ! empty( $params['size']['name'] ) ? $params['size']['name'] : 'full';

		if ( 'woocommerce_thumbnail' === $size ) {
			$final_url  = $params['imgData']['thumbnailImgUrl'];
			$final_path = $params['imgData']['thumbnailImgPath'];
		} else {
			$final_url  = $params['imgData']['finalImgUrl'];
			$final_path = $params['imgData']['finalImgPath'];
		}

		if ( 1 === $params['redirect'] ) {
			wp_safe_redirect( esc_url( $final_url ) );
			die;
		}

		global $jckpc;

		header( 'Content-Type: image/png' );
		$image_saved = imagepng( $final_image, $final_path );

		if ( $image_saved ) {
			imagepng( $final_image );

			/**
			 * Hook: after generate image.
			 *
			 * Can be used to take action after image generation
			 * has occured e.g. upload the generated image to a
			 * CDN and then delete the local copy.
			 *
			 * @since 1.22.0
			 *
			 * @param string $image_path Path to the generated image.
			 * @param array  $image_data Associative array of image data required to build URL.
			 * @param array  $size       Image size.
			 */
			do_action( 'iconic_pc_after_generate_image', $final_path, $params['imgData'], $size );

			/**
			 * Filter: modify the pre-generated product image URL.
			 *
			 * Can be used to modify the image URL e.g. if you are
			 * using a CDN to deliver generated images.
			 *
			 * IMPORTANT: the php directive `allow_url_fopen` must be
			 * enabled on the server, if a CDN is being used to serve
			 * generated images, rather than serving images hosted on
			 * the server.
			 *
			 * @since 1.22.0
			 *
			 * @param string $url        Path to the pre-generated image URL.
			 * @param array  $image_data Associative array of image data required to build URL.
			 * @param array  $size       Image size.
			 */
			$final_image_url = apply_filters( 'iconic_pc_product_image_url', $final_url, $params['imgData'], $size );

			set_transient( $jckpc::$transient_prefix . $params['imgData']['imgName'] . $size, $final_image_url, $jckpc->settings['general_cache_duration'] * HOUR_IN_SECONDS );

			imagedestroy( $final_image );
		}

		die;
	}

	/**
	 * Get single image layer.
	 */
	public static function get_image_layer() {
		global $iconic_wpc;

		do_action( 'iconic_pc_before_get_image_layer' );

		$response = array(
			'post'       => $_POST, // phpcs:ignore WordPress.Security.NonceVerification.Missing
			'images'     => array(),
			'request_id' => absint( filter_input( INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT ) ),
		);

		$product_id          = filter_input( INPUT_POST, 'prodid', FILTER_SANITIZE_NUMBER_INT );
		$selected_attributes = filter_input( INPUT_POST, 'selected_attributes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $selected_attributes ) ) {
			wp_send_json_error( $response );
		}

		$response['images'] = $iconic_wpc->get_images_by_attributes( $product_id, $selected_attributes );

		wp_send_json_success( $response );
	}

	/**
	 * Ajax image get params.
	 */
	public static function ajax_img_get_params() {
		global $iconic_wpc;

		$params             = array();
		$product_id         = filter_input( INPUT_GET, 'prodid', FILTER_VALIDATE_INT, FILTER_SANITIZE_NUMBER_INT );
		$attributes         = $iconic_wpc->get_atts_from_querystring();
		$params['redirect'] = filter_input( INPUT_GET, 'redirect', FILTER_VALIDATE_INT, FILTER_SANITIZE_NUMBER_INT );
		$params['images']   = $iconic_wpc->get_images_by_attributes( $product_id, $attributes ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$params['imgData']  = $iconic_wpc->generate_img_paths( $product_id, $attributes ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$size = filter_input( INPUT_GET, 'jckpc-img-size', FILTER_SANITIZE_STRING );
		$size = wc_get_image_size( $size );

		if ( ! empty( $size ) ) {
			$params['size'] = array(
				'width'  => $size['width'],
				'height' => $size['height'],
				'name'   => filter_input( INPUT_GET, 'jckpc-img-size', FILTER_SANITIZE_SPECIAL_CHARS ),
			);
		}

		return $params;
	}

	/**
	 * Get conditional group.
	 */
	public static function get_conditional_group() {
		check_ajax_referer( 'iconic-pc', 'nonce' );

		$data = array(
			'layer_id'     => filter_input( INPUT_POST, 'layer_id' ),
			'product_id'   => absint( filter_input( INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT ) ),
			'condition_id' => absint( filter_input( INPUT_POST, 'condition_id', FILTER_SANITIZE_NUMBER_INT ) ),
		);

		if ( empty( $data['product_id'] ) ) {
			wp_send_json_error( $data );
		}

		$attributes = Iconic_PC_Product::get_attributes( $data['product_id'] );

		ob_start();
		Iconic_PC_Templates::conditional_layer( $data['layer_id'], $attributes, null, $data['condition_id'] );
		$data['html'] = ob_get_clean();

		wp_send_json_success( $data );
	}

	/**
	 * Get Admin template.
	 *
	 * @return void
	 */
	public static function get_product_attributes_template() {
		global $iconic_wpc;

		$product_id = absint( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) );
		$post       = get_post( $product_id );

		if ( empty( $post ) ) {
			wp_send_json_error();
		}

		ob_start();
		$iconic_wpc->product_config_tab_options( $post );
		$html = ob_get_clean();

		wp_send_json_success(
			array(
				'html' => $html,
			)
		);
	}

}
