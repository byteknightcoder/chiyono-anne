<?php

namespace Elementor;

/* PAGE MENU LAYOUT */
$page->add_control( 'page_menu_layout', array(
    'type'    => \Elementor\Controls_Manager::SELECT,
    'label'   => esc_html__( 'Page menu layout', 'zoa' ),
    'default' => 'default',
    'options' => array(
        'default'  => esc_html__( 'Default', 'zoa' ),
        'layout-1' => esc_html__( 'Layout 1', 'zoa' ),
        'layout-2' => esc_html__( 'Layout 2', 'zoa' ),
        'layout-3' => esc_html__( 'Layout 3', 'zoa' ),
        'layout-4' => esc_html__( 'Layout 4', 'zoa' ),
        'layout-5' => esc_html__( 'Layout 5', 'zoa' ),
        'layout-6' => esc_html__( 'Layout 6', 'zoa' ),
        'layout-7' => esc_html__( 'Layout 7', 'zoa' ),
    )
) );

/* PAGE HEADER LAYOUT */
$page->add_control( 'p_header_layout', array(
    'type'    => \Elementor\Controls_Manager::SELECT,
    'label'   => esc_html__( 'Page header layout', 'zoa' ),
    'default' => 'default',
    'options' => array(
        'default'  => esc_html__( 'Default', 'zoa' ),
        'layout-1' => esc_html__( 'Layout 1', 'zoa' ),
        'layout-2' => esc_html__( 'Layout 2', 'zoa' ),
        'disable'  => esc_html__( 'Disable', 'zoa' ),
    )
) );

/* PAGE FOOTER LAYOUT */
$page->add_control( 'p_footer_layout', array(
    'type'    => \Elementor\Controls_Manager::SELECT,
    'label'   => esc_html__( 'Page footer layout', 'zoa' ),
    'default' => 'default',
    'options' => array(
        'default' => esc_html__( 'Default', 'zoa' ),
        'enable'  => esc_html__( 'Enable', 'zoa' ),
        'disable' => esc_html__( 'Disable', 'zoa' ),
    )
) );