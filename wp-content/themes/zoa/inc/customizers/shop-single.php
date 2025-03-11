<?php

/* ADD SHOP SINGLE SECTION
***************************************************/
zoa_Kirki::add_section(
	'shop_single', array(
		'title'    => esc_attr__( 'Shop single', 'zoa' ),
		'priority' => 1,
	)
);


/*GALLERY LAYOUT*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'select',
		'label'    => esc_html__( 'Layout', 'zoa' ),
		'settings' => 'shop_gallery_layout',
		'section'  => 'shop_single',
		'default'  => 'vertical',
		'choices'  => array(
			'list'       => esc_attr__( 'List', 'zoa' ),
			'grid'       => esc_attr__( 'Grid', 'zoa' ),
			'vertical'   => esc_attr__( 'Vertical carousel', 'zoa' ),
			'horizontal' => esc_attr__( 'Horizontal carousel', 'zoa' ),
		),
	)
);

/*RELATED PRODUCT*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'number',
		'label'    => esc_html__( 'Related product', 'zoa' ),
		'settings' => 'related_product_item',
		'section'  => 'shop_single',
		'default'  => 5,
		'choices'     => array(
			'min'  => 0,
			'max'  => 20,
			'step' => 1,
		),
	)
);

/* RELATED COLUMNS */
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'number',
		'label'    => esc_html__( 'Related columns', 'zoa' ),
		'settings' => 'related_column',
		'section'  => 'shop_single',
		'default'  => 5,
		'choices'     => array(
			'min'  => 1,
			'max'  => 6,
			'step' => 1,
		),
	)
);

/*AJAX SINGLE ADD TO CART*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'label'    => esc_html__( 'Ajax single add to cart', 'zoa' ),
		'settings' => 'ajax_single_atc',
		'section'  => 'shop_single',
		'default'  => 1,
		'choices'  => array(
			'on'  => esc_attr__( 'Yes', 'zoa' ),
			'off' => esc_attr__( 'No', 'zoa' ),
		),
	)
);

/*SHOP SINGLE NAVIGATION*/
zoa_Kirki::add_field( 'zoa', array(
    'type'        => 'switch',
    'settings'    => 'shop_single_nav',
    'label'       => esc_attr__( 'Shop single navigation', 'zoa' ),
    'section'     => 'shop_single',
    'default'     => true,
    'description' => esc_attr__( 'This option available only on product page', 'zoa' ),
    'choices'     => array(
        'off' => esc_attr__( 'Off', 'zoa' ),
        'on'  => esc_attr__( 'On', 'zoa' ),
    )
) );

/*LIGHTBOX*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'label'    => esc_html__( 'Gallery lightbox', 'zoa' ),
		'settings' => 'gallery_lightbox',
		'section'  => 'shop_single',
		'default'  => 0,
		'choices'  => array(
			'on'  => esc_attr__( 'Yes', 'zoa' ),
			'off' => esc_attr__( 'No', 'zoa' ),
		),
	)
);

/*SHOW CATEGORY*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'label'    => esc_html__( 'Show Category', 'zoa' ),
		'settings' => 'show_product_category',
		'section'  => 'shop_single',
		'default'  => 1,
		'choices'  => array(
			'on'  => esc_attr__( 'Yes', 'zoa' ),
			'off' => esc_attr__( 'No', 'zoa' ),
		),
	)
);

/*SHOW TAG*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'label'    => esc_html__( 'Show Tag', 'zoa' ),
		'settings' => 'show_product_tag',
		'section'  => 'shop_single',
		'default'  => 1,
		'choices'  => array(
			'on'  => esc_attr__( 'Yes', 'zoa' ),
			'off' => esc_attr__( 'No', 'zoa' ),
		),
	)
);

/*SHOW SOCIAL SHARE*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'label'    => esc_html__( 'Show Social Share', 'zoa' ),
		'settings' => 'show_social_share',
		'section'  => 'shop_single',
		'default'  => 1,
		'choices'  => array(
			'on'  => esc_attr__( 'Yes', 'zoa' ),
			'off' => esc_attr__( 'No', 'zoa' ),
		),
		'transport' => 'auto',
	)
);
