<?php
/**
 * Register the required plugins for this theme.
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1
 * @author     Thomas Griffin
 * @author     Gary Jones
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/thomasgriffin/TGM-Plugin-Activation
 */

/*Include the TGM_Plugin_Activation class.*/
require_once get_template_directory() . '/tgm-plugin/class-tgm-plugin-activation.php';

/*TGM_Plugin_Activation class constructor.*/
function zoa_register_required_plugins() {
    $plugins = array(
        array(
            'name'   => esc_html__( 'Revolution Slider', 'zoa' ),
            'slug'   => 'revolution-slider',
            'source' => get_template_directory() . '/inc/plugins/rev.zip',
        ),
        array(
            'name'     => esc_html__( 'Unyson', 'zoa' ),
            'slug'     => 'unyson',
            'source'   => get_template_directory() . '/inc/plugins/unyson.zip',
            'required' => true,
        ),
        array(
            'name'     => esc_html__( 'Elementor', 'zoa' ),
            'slug'     => 'elementor',
            'required' => true,
        ),
        array(
            'name' => esc_html__('Envato Market', 'zoa'),
            'slug' => 'envato-market',
            'source' => 'https://envato.github.io/wp-envato-market/dist/envato-market.zip',
            'recommended' => true,
        ),
        array(
            'name'     => esc_html__( 'Unyson', 'zoa' ),
            'slug'     => 'unyson',
            'required' => true,
        ),
        array(
            'name'     => esc_html__( 'Kirki', 'zoa' ),
            'slug'     => 'kirki',
            'required' => true,
        ),
        array(
            'name'     => esc_html__( 'Woocommerce', 'zoa' ),
            'slug'     => 'woocommerce',
            'required' => true,
        ),
        array(
            'name' => esc_html__( 'Variation Swatches for WooCommerce', 'zoa' ),
            'slug' => 'variation-swatches-for-woocommerce',
            'source' => 'https://github.com/duongancol/variation-swatches-for-woocommerce-fixed-/archive/1.0.3.1.zip'
        ),
        array(
            'name' => esc_html__( 'YITH WooCommerce Wishlist', 'zoa' ),
            'slug' => 'yith-woocommerce-wishlist'
        ),
	    array(
		    'name'      => esc_html__('Contact Form 7', 'zoa'),
		    'slug'      => 'contact-form-7'
        ),
        array(
		    'name'      => esc_html__('Instagram Feed', 'zoa'),
		    'slug'      => 'instagram-feed'
	    ),
    );

    $config = array(
        'id'           => 'tgmpa',
        'menu'         => 'tgmpa-install-plugins',
        'capability'   => 'edit_theme_options',
        'is_automatic' => false,
    );

    tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'zoa_register_required_plugins' );