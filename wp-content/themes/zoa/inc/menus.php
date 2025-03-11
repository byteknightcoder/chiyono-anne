<?php

if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

register_nav_menus( array(
    'primary'    => esc_html__( 'Primary Menu', 'zoa' ),
    'secondary'  => esc_html__( 'Header Left Menu', 'zoa' ),
    'tertiary'   => esc_html__( 'Header Right Menu', 'zoa' ),
    'quaternary' => esc_html__( 'Primary - Sidebar Menu', 'zoa' ),
));