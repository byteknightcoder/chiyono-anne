<?php

/**
 * Add 404 Page Section
 */
zoa_Kirki::add_section( '404', array(
	'title' => esc_attr__( '404 Page', 'zoa' ),
) );

// Title
zoa_Kirki::add_field( 'zoa', array(
	'type'      => 'text',
	'label'     => esc_html__( 'Title', 'zoa' ),
	'settings'  => 'not_found_title',
	'section'   => '404',
	'default'   => 'Whoops!',
	'transport' => 'auto',
) );

// Subtitle
zoa_Kirki::add_field( 'zoa', array(
	'type'      => 'text',
	'label'     => esc_html__( 'Subtitle', 'zoa' ),
	'settings'  => 'not_found_subtitle',
	'section'   => '404',
	'default'   => 'Your style does not exist!',
	'transport' => 'auto',
) );

// Description
zoa_Kirki::add_field( 'zoa', array(
	'type'      => 'textarea',
	'label'     => esc_html__( 'Description', 'zoa' ),
	'settings'  => 'not_found_desc',
	'section'   => '404',
	'default'   => 'Any question? Please contact us, we’re usually pretty quick. Cowboys to urbanites, professional athletes to ski bums, business suit to fishing guides.',
	'transport' => 'auto',
) );

// Image
zoa_Kirki::add_field( 'zoa', array(
	'type'     => 'image',
	'label'    => esc_html__( 'Image', 'zoa' ),
	'settings' => 'not_found_image',
	'section'  => '404',
	'default'  => get_template_directory_uri() . '/images/404.png',
) );
