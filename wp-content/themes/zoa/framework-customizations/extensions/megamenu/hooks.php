<?php

if ( ! defined( 'FW' ) ) die( 'Forbidden' );

/*! REPLACE DEFAULT WALKER
------------------------------------------------->*/
add_filter('wp_nav_menu_args', 'zoa_filter_theme_ext_mega_menu_wp_nav_menu_args');
function zoa_filter_theme_ext_mega_menu_wp_nav_menu_args( $args ) {
    $args['walker'] = new zoa_Mega_Menu_Custom_Walker();

    return $args;
}