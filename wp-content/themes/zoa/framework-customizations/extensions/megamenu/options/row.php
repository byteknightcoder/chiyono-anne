<?php
/**
 * Mega menu row options
 *
 * @package zoa
 */

if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'menu_bg_image' => array(
		'label' => esc_html__( 'Background Image', 'zoa' ),
		'desc'  => esc_html__( 'Choose Image', 'zoa' ),
		'type'  => 'upload',
	),
	'menu_bg_position' => array(
		'label'   => esc_html__( 'Background Position', 'zoa' ),
		'desc'    => esc_html__( 'Choose Image', 'zoa' ),
		'type'    => 'short-select',
		'choices' => array(
			''              => esc_html__( 'Default', 'zoa' ),
			'top-left'      => esc_html__( 'Top Left', 'zoa' ),
			'top-center'    => esc_html__( 'Top Center', 'zoa' ),
			'top-right'     => esc_html__( 'Top Right', 'zoa' ),
			'center-left'   => esc_html__( 'Center Left', 'zoa' ),
			'center-center' => esc_html__( 'Center Center', 'zoa' ),
			'center-right'  => esc_html__( 'Center Right', 'zoa' ),
			'bottom-left'   => esc_html__( 'Bottom Left', 'zoa' ),
			'bottom-center' => esc_html__( 'Bottom Center', 'zoa' ),
			'bottom-right'  => esc_html__( 'Bottom Right', 'zoa' ),
		),
	),
	'menu_bg_repeat' => array(
		'label'   => esc_html__( 'Background Repeat', 'zoa' ),
		'desc'    => esc_html__( 'Choose Image', 'zoa' ),
		'type'    => 'short-select',
		'choices' => array(
			''          => esc_html__( 'Default', 'zoa' ),
			'no-repeat' => esc_html__( 'No Repeat', 'zoa' ),
			'repeat'    => esc_html__( 'Repeat', 'zoa' ),
			'repeat-x'  => esc_html__( 'Repeat X', 'zoa' ),
			'repeat-y'  => esc_html__( 'Repeat Y', 'zoa' ),
		),
	),
	'menu_bg_size' => array(
		'label'   => esc_html__( 'Background Size', 'zoa' ),
		'desc'    => esc_html__( 'Choose Image', 'zoa' ),
		'type'    => 'short-select',
		'choices' => array(
			''        => esc_html__( 'Default', 'zoa' ),
			'auto'    => esc_html__( 'Auto', 'zoa' ),
			'cover'   => esc_html__( 'Cover', 'zoa' ),
			'contain' => esc_html__( 'Contain', 'zoa' ),
		),
	),
);
