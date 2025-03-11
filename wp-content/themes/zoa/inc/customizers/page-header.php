<?php

/* ADD PAGE HEADER PANEL
***************************************************/
zoa_Kirki::add_panel( 'page_header_panel', array(
    'title'    => esc_attr__( 'Page Header', 'zoa' ),
    'priority' => 1,
));


/* ADD PAGE HEADER LAYOUT 1 SECTION
***************************************************/
zoa_Kirki::add_section( 'page_header_1', array(
    'title' => esc_attr__( 'Layout 1', 'zoa'),
    'panel' => 'page_header_panel',
));

/* ADD PAGE HEADER LAYOUT 2 SECTION
***************************************************/
zoa_Kirki::add_section( 'page_header_2', array(
    'title' => esc_attr__( 'Layout 2', 'zoa'),
    'panel' => 'page_header_panel',
));


/* LAYOUT 1
******************************************************************************************************/

/*HEIGHT*/
zoa_Kirki::add_field( 'zoa', array(
    'type'     => 'dimensions',
    'settings' => 'c_height',
    'label'    => esc_attr__( 'Space', 'zoa' ),
    'section'  => 'page_header_1',
    'default'  => array(
        'height'        => '180px',
        'margin-bottom' => '50px',
    ),
    'transport' => 'auto',
    'output'    => array(
        array(
            'element'  => '.ph-layout-1',
            'property' => 'height',
            'choice'   => 'height'
        ),
        array(
            'element'  => '.ph-layout-1',
            'property' => 'margin-bottom',
            'choice'   => 'margin-bottom'
        ),
    ),
));

/*BACKGROUND*/
zoa_Kirki::add_field( 'zoa', array(
    'type'      => 'background',
    'settings'  => 'c_header_bg',
    'section'   => 'page_header_1',
    'transport' => 'auto',
    'default'   => array(
        'background-color'      => '#f6f6f6',
        'background-image'      => '',
        'background-repeat'     => 'no-repeat',
        'background-position'   => 'center center',
        'background-size'       => 'cover',
        'background-attachment' => 'scroll',
    ),
    'output' => array(
        array(
            'element' => '.ph-layout-1'
        )
    ),
));

/* LAYOUT 2
******************************************************************************************************/

/*HEIGHT*/
zoa_Kirki::add_field( 'zoa', array(
    'type'     => 'dimensions',
    'settings' => 'c_height_v2',
    'label'    => esc_attr__( 'Space', 'zoa' ),
    'section'  => 'page_header_2',
    'default'  => array(
        'height'        => '55px',
        'margin-bottom' => '50px',
    ),
    'transport' => 'auto',
    'output'    => array(
        array(
            'element'  => '.ph-layout-2',
            'property' => 'height',
            'choice'   => 'height'
        ),
        array(
            'element'  => '.ph-layout-2',
            'property' => 'margin-bottom',
            'choice'   => 'margin-bottom'
        ),
    ),
) );

/*BACKGROUND*/
zoa_Kirki::add_field( 'zoa', array(
    'type'      => 'background',
    'settings'  => 'c_header_bg_v2',
    'section'   => 'page_header_2',
    'transport' => 'auto',
    'default'   => array(
        'background-color'      => '#ffffff',
        'background-image'      => '',
        'background-repeat'     => 'no-repeat',
        'background-position'   => 'center center',
        'background-size'       => 'cover',
        'background-attachment' => 'scroll',
    ),
    'output' => array(
        array(
            'element' => '.ph-layout-2'
        )
    ),
));
