<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

register_post_type(
	'size_guide',
	array(
		'labels'          => array(
			'name'          => __( 'Size Guides', 'zoa' ),
			'singular_name' => __( 'Size Guides', 'zoa' ),
			'add_new_item'  => __( 'Add New Size Guide', 'zoa' ),
			'edit_item'     => __( 'Edit Size Guide', 'zoa' ),
			'all_items'     => __( 'All Size Guides', 'zoa' ),
		),
		'public'          => true,
		'menu_icon'       => 'dashicons-book-alt',
		'supports'        => array( 'title', 'elementor' ),
		'hierarchical'    => false,
		'capability_type' => 'post',
	)
);
