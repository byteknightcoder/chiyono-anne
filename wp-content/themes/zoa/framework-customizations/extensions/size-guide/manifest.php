<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = esc_html__( 'Size Guide', 'zoa' );
$manifest['description'] = esc_html__( 'This extension adds size guide to your WooCommerce single product page.', 'zoa' );
$manifest['version']     = '1.0';
$manifest['thumbnail']   = 'fa fa-book';
$manifest['display']     = true;
$manifest['standalone']  = true;
