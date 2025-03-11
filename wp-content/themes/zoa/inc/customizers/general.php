<?php
// @codingStandardsIgnoreStart
/* ADD GENERAL SECTION
***************************************************/
zoa_Kirki::add_section(
	'c_general', array(
		'title'    => esc_attr__( 'General', 'zoa' ),
		'priority' => 0,
	)
);

/*MENU LAYOUT*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'select',
		'settings'    => 'menu_layout',
		'label'       => esc_attr__( 'Menu layout', 'zoa' ),
		'section'     => 'c_general',
		'default'     => 'layout-1',
		'description' => esc_attr__( 'Choose your menu preset to apply for all pages', 'zoa' ),
		'choices'     => array(
			'layout-1' => esc_attr__( 'Layout 1', 'zoa' ),
			'layout-2' => esc_attr__( 'Layout 2', 'zoa' ),
			'layout-3' => esc_attr__( 'Layout 3', 'zoa' ),
			'layout-4' => esc_attr__( 'Layout 4', 'zoa' ),
			'layout-5' => esc_attr__( 'Layout 5', 'zoa' ),
			'layout-6' => esc_attr__( 'Layout 6', 'zoa' ),
			'layout-7' => esc_attr__( 'Layout 7', 'zoa' ),
		),
	)
);

/*PAGE HEADER LAYOUT*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'select',
		'settings'    => 'c_header_layout',
		'label'       => esc_attr__( 'Page header layout', 'zoa' ),
		'section'     => 'c_general',
		'default'     => 'layout-1',
		'description' => esc_attr__( 'Choose your page header preset to apply for all pages', 'zoa' ),
		'choices'     => array(
			'layout-1' => esc_attr__( 'Layout 1', 'zoa' ),
			'layout-2' => esc_attr__( 'Layout 2', 'zoa' ),
			'disable'  => esc_attr__( 'Disable', 'zoa' ),
		),
	)
);

/*LOADING EFFECT*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'switch',
		'settings'    => 'loading',
		'label'       => esc_attr__( 'Preloader', 'zoa' ),
		'section'     => 'c_general',
		'default'     => false,
		'description' => esc_attr__( 'This option showing a loading animation while your site loads', 'zoa' ),
		'choices'     => array(
			'off' => esc_attr__( 'Off', 'zoa' ),
			'on'  => esc_attr__( 'On', 'zoa' ),
		),
	)
);

/*SMOOTH SCROLL*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'switch',
		'settings'    => 'smooth',
		'label'       => esc_attr__( 'Smooth scrolling', 'zoa' ),
		'section'     => 'c_general',
		'default'     => false,
		'description' => esc_attr__( 'Smooth scrolling for the web', 'zoa' ),
		'choices'     => array(
			'off' => esc_attr__( 'Off', 'zoa' ),
			'on'  => esc_attr__( 'On', 'zoa' ),
		),
	)
);

/* STICKY HEADER MENU */
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'settings' => 'sticky_header',
		'label'    => esc_attr__( 'Sticky Header Menu', 'zoa' ),
		'section'  => 'c_general',
		'default'  => false,
		'choices'  => array(
			'off' => esc_attr__( 'Off', 'zoa' ),
			'on'  => esc_attr__( 'On', 'zoa' ),
		),
	)
);

// Sticky on mobile?
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'settings' => 'mobile_sticky_header',
		'label'    => esc_attr__( 'Sticky On Mobile', 'zoa' ),
		'section'  => 'c_general',
		'default'  => false,
		'choices'  => array(
			'off' => esc_attr__( 'Off', 'zoa' ),
			'on'  => esc_attr__( 'On', 'zoa' ),
		),
		'active_callback' => array(
            array(
                'setting'  => 'sticky_header',
                'operator' => '==',
                'value'    => '1',
            ),
        )
	)
);
