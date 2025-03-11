<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_PC_WPML.
 *
 * @class    Iconic_PC_WPML
 * @version  1.0.0
 * @since    1.2.1
 * @author   Iconic
 */
class Iconic_PC_WPML {
	/**
	 * Run.
	 */
	public static function run() {
		if ( ! Iconic_PC_Helpers::is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			return;
		}

		add_filter( 'iconic_pc_set_images', array( __CLASS__, 'set_images' ), 10, 2 );
		add_filter( 'iconic_pc_query_string_attributes', array( __CLASS__, 'query_string_attributes' ), 10 );
		add_filter( 'iconic_pc_localize_script', array( __CLASS__, 'localize_script' ), 10, 1 );
		add_action( 'iconic_pc_before_get_image_layer', array( __CLASS__, 'set_ajax_language' ), 10 );
		add_filter( 'iconic_pc_get_inventory_args', array( __CLASS__, 'modify_get_inventory_args' ), 10 );

		if ( is_admin() && ! wp_doing_ajax() ) {
			add_filter( 'iconic_pc_sanitise_string', array( __CLASS__, 'modify_sanitized_string' ), 10, 3 );
			add_filter( 'iconic_pc_disable_layer_inputs', array( __CLASS__, 'maybe_disable_translated_product_layer_inputs' ), 10 );
		}
	}

	/**
	 * Change set image keys to translated term.
	 *
	 * @param array|bool $images
	 * @param int        $product_id
	 *
	 * @return array|bool
	 */
	public static function set_images( $images, $product_id ) {
		if ( empty( $images ) ) {
			return $images;
		}

		foreach ( $images as $layer => $images_ids ) {
			if ( ! is_array( $images_ids ) ) {
				continue;
			}

			foreach ( $images_ids as $term_slug => $image_id ) {
				$taxonomy = Iconic_PC_Helpers::strip_prefix( $layer );

				if ( ! taxonomy_exists( $taxonomy ) ) {
					continue;
				}

				$term_slug_stripped   = Iconic_PC_Helpers::strip_prefix( $term_slug );
				$term                 = get_term_by( 'slug', $term_slug_stripped, $taxonomy );
				$translated_term_slug = sprintf( 'jckpc-%s', $term->slug );

				$images[ $layer ][ $translated_term_slug ] = $image_id;
			}
		}

		return $images;
	}

	/**
	 * Get original query string translations.
	 *
	 * @param array $attributes
	 *
	 * @return array
	 */
	public static function query_string_attributes( $attributes ) {
		if ( empty( $attributes ) ) {
			return $attributes;
		}

		foreach ( $attributes as $taxonomy => $term_slug ) {
			$stripped_taxonomy  = Iconic_PC_Helpers::strip_prefix( $taxonomy );
			$stripped_term_slug = Iconic_PC_Helpers::strip_prefix( $term_slug );
			$term               = get_term_by( 'slug', $stripped_term_slug, $stripped_taxonomy );
			$original_term      = self::get_term_for_default_lang( $term, $stripped_taxonomy );

			if ( ! $original_term || is_wp_error( $original_term ) ) {
				continue;
			}

			$attributes[ $taxonomy ] = sprintf( 'jckpc-%s', $original_term->slug );
		}

		return $attributes;
	}

	/**
	 * Get term for default language.
	 *
	 * @param int|WP_Term $term     Term ID/object.
	 * @param string      $taxonomy Taxonomy slug.
	 *
	 * @return array|null|WP_Error|WP_Term|false
	 */
	public static function get_term_for_default_lang( $term, $taxonomy ) {
		global $sitepress;
		global $icl_adjust_id_url_filter_off;

		if ( ! $term || is_wp_error( $term ) ) {
			return false;
		}

		$term_id = is_int( $term ) ? $term : $term->term_id;

		$default_term_id = (int) wpml_object_id_filter( $term_id, $taxonomy, true, $sitepress->get_default_language() );

		$orig_flag_value = $icl_adjust_id_url_filter_off;

		$icl_adjust_id_url_filter_off = true;
		$term                         = get_term( $default_term_id, $taxonomy );
		$icl_adjust_id_url_filter_off = $orig_flag_value;

		return $term;
	}

	/**
	 * Modify script args.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	public static function localize_script( $args ) {
		$current_lang = apply_filters( 'wpml_current_language', null );

		if ( $current_lang ) {
			$args['ajaxurl'] = add_query_arg( 'wpml_lang', $current_lang, $args['ajaxurl'] );
		}

		return $args;
	}

	/**
	 * Set language in ajax.
	 */
	public static function set_ajax_language() {
		$lang = filter_input( INPUT_GET, 'wpml_lang', FILTER_SANITIZE_STRING );

		if ( $lang ) {
			do_action( 'wpml_switch_language', $lang );
		}
	}

	/**
	 * Modify the args passed to get_inventory calls.
	 *
	 * @param array $args Arguments: product_id, att_val_id.
	 *
	 * @return array
	 */
	public static function modify_get_inventory_args( $args ) {
		// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		$current_language = apply_filters( 'wpml_default_language', null );

		// We need to ensure that the attribute value ID doesn't contain
		// the language code suffix e.g. "-de".
		$args['att_val_id'] = Iconic_PC_Helpers::strip_wpml_language_code( $args['att_val_id'] );

		// We need to get the inventory a.k.a stock count from the original
		// parent post/translation, rather than the current one.
		if ( $args['product_id'] ) {
			/**
			 * Filter: WPML filter to return object ID of a given translation.
			 *
			 * @filter: wpml_object_id
			 * @since 1.7.1
			 * @param int    $element_id                 Post ID of the object to query against.
			 * @param string $element_type               Type of object to query against.
			 * @param bool   $return_original_if_missing Return the original value if supplied translation is missing.
			 * @param mixed  $ulanguage_code             Current language if missing/NUll, supplied or original if it does not exist.
			 */
			$original_product_id = apply_filters( 'wpml_object_id', $args['product_id'], 'product', true, $current_language );

			if ( $original_product_id ) {
				$args['product_id'] = $original_product_id;
			}
		}

		return $args;
	}

	/**
	 * Modify sanitized string value.
	 *
	 * @param string $string  String to be returned.
	 * @param string $alt_str Alternative string to use.
	 * @param bool   $prefix  Whether or not to add the `jckpc` prefix.
	 *
	 * @return string
	 */
	public static function modify_sanitized_string( $string, $alt_str, $prefix ) {
		return Iconic_PC_Helpers::strip_wpml_language_code( $string );
	}

	/**
	 * Maybe disable translated product layer inputs.
	 *
	 * @param bool $disable True to disable the PC inputs.
	 *
	 * @return bool
	 */
	public static function maybe_disable_translated_product_layer_inputs( $disable ) {
		// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		$default_language = apply_filters( 'wpml_default_language', null );
		// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		$current_language = apply_filters( 'wpml_current_language', null );

		// Disable the inputs if the current language is
		// not the default.
		if ( $current_language !== $default_language ) {
			$disable = true;
		}

		return $disable;
	}
}
