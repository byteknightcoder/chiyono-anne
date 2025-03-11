<?php

/* REGISTER CUSTOMIZE ID `zoa`
------------------------------------------------->*/
zoa_Kirki::add_config( 'zoa', array(
	'option_type' => 'theme_mod',
	'capability'  => 'edit_theme_options',
) );


/* GENERAL
------------------------------------------------->*/
get_template_part( 'inc/customizers/general' );

/* TOPBAR
------------------------------------------------->*/
get_template_part( 'inc/customizers/topbar' );

/* MENU
------------------------------------------------->*/
get_template_part( 'inc/customizers/menu-layout' );

/* PAGE HEADER
------------------------------------------------->*/
get_template_part( 'inc/customizers/page-header' );

/* BLOG
------------------------------------------------->*/
get_template_part( 'inc/customizers/blog' );

/* SHOP
------------------------------------------------->*/
if ( class_exists( 'woocommerce' ) ) {
	get_template_part( 'inc/customizers/shop' );
	get_template_part( 'inc/customizers/shop-single' );
}

/* COLOR
------------------------------------------------->*/
get_template_part( 'inc/customizers/color' );

/* TYPOGRAPHY
------------------------------------------------->*/
get_template_part( 'inc/customizers/typography' );

/* FOOTER
------------------------------------------------->*/
get_template_part( 'inc/customizers/footer' );

/* SITE IDENTITY
------------------------------------------------->*/
get_template_part( 'inc/customizers/title-tagline' );

/* 404
------------------------------------------------->*/
get_template_part( 'inc/customizers/404' );
