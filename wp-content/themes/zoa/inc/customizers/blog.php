<?php

/* ADD BLOG SECTION
***************************************************/
zoa_Kirki::add_section( 'blog', array(
	'title'    => esc_attr__( 'Blog', 'zoa' ),
	'priority' => 1,
) );


/*BLOG TITLE*/
zoa_Kirki::add_field( 'zoa', array(
	'type'      => 'text',
	'label'     => esc_html__( 'Blog title', 'zoa' ),
	'settings'  => 'blog_title',
	'section'   => 'blog',
	'default'   => 'Blog',
	'transport' => 'postMessage',
	'js_vars'   => array(
		array(
			'element'  => '.group-blog .page-title',
			'function' => 'html',
		),
	),
) );


/*SIDEBAR*/
zoa_Kirki::add_field( 'zoa', array(
	'type'     => 'radio-image',
	'label'    => esc_html__( 'Sidebar position', 'zoa' ),
	'settings' => 'blog_sidebar',
	'section'  => 'blog',
	'default'  => 'full',
	'choices'  => array(
		'left'  => get_template_directory_uri() . '/images/sidebar/left.png',
		'full'  => get_template_directory_uri() . '/images/sidebar/full.png',
		'right' => get_template_directory_uri() . '/images/sidebar/right.png',
	),
) );
