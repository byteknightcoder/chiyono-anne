<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = esc_html__( 'Social Share', 'zoa' );
$manifest['description'] = esc_html__( 'This extension adds social share buttons to your WooCommerce single product page.', 'zoa' );
$manifest['version']     = '1.0';
$manifest['thumbnail']   = 'fa fa-share-alt';
$manifest['display']     = true;
$manifest['standalone']  = true;
