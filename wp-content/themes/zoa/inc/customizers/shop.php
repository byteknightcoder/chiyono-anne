<?php

/* ADD SHOP SECTION
***************************************************/
zoa_Kirki::add_section(
	'shop', array(
		'title'    => esc_attr__( 'Shop', 'zoa' ),
		'priority' => 1,
	)
);

/*SHOP HEADER LAYOUT*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'            => 'select',
		'settings'        => 'shop_header_layout',
		'label'           => esc_attr__( 'Page header layout', 'zoa' ),
		'section'         => 'shop',
		'default'         => 'default',
		'description'     => esc_attr__( 'Choose your page header preset to apply for woocommerce pages', 'zoa' ),
		'choices'         => array(
			'default'  => esc_attr__( 'Default', 'zoa' ),
			'layout-1' => esc_attr__( 'Layout 1', 'zoa' ),
			'layout-2' => esc_attr__( 'Layout 2', 'zoa' ),
			'disable'  => esc_attr__( 'Disable', 'zoa' ),
		),
		'partial_refresh' => array(
			'shop_header_layout' => array(
				'selector'        => '#theme-page-header',
				'render_callback' => 'zoa_page_header',
			),
		),
	)
);

/*SHOP TITLE*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'      => 'text',
		'label'     => esc_html__( 'Shop Title', 'zoa' ),
		'settings'  => 'shop_title',
		'section'   => 'shop',
		'default'   => 'Shop',
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'element'  => '.post-type-archive-product .page-title',
				'function' => 'html',
			),
		),
	)
);


/*SIDEBAR*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'radio-image',
		'label'    => esc_html__( 'Sidebar position', 'zoa' ),
		'settings' => 'shop_sidebar',
		'section'  => 'shop',
		'default'  => 'full',
		'choices'  => array(
			'left'  => get_template_directory_uri() . '/images/sidebar/left.png',
			'full'  => get_template_directory_uri() . '/images/sidebar/full.png',
			'right' => get_template_directory_uri() . '/images/sidebar/right.png',
		),
	)
);

/* FLEXIBLE SIDEBAR */
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'label'    => esc_html__( 'Flexible Sidebar', 'zoa' ),
		'settings' => 'flexible_sidebar',
		'section'  => 'shop',
		'default'  => false,
		'choices'  => array(
			'on'  => esc_attr__( 'On', 'zoa' ),
			'off' => esc_attr__( 'Off', 'zoa' ),
		),
		'required' => array(
			array(
				'setting'  => 'shop_sidebar',
				'operator' => '!==',
				'value'    => 'full',
			),
		),
	)
);

/* ONLY SEARCH PRODUCT */
zoa_Kirki::add_field( 'zoa', array(
	'type'        => 'switch',
	'settings'    => 'product_search',
	'label'       => esc_attr__( 'Search only product', 'zoa' ),
	'description' => esc_attr__( 'Show only products in search results', 'zoa' ),
	'section'     => 'shop',
	'default'     => 0,
	'choices'     => array(
		'off' => esc_attr__( 'Off', 'zoa' ),
		'on'  => esc_attr__( 'On', 'zoa' ),
	),
));

/* AJAX SEARCH FOR SHOP PRODUCTS */
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'settings' => 'ajax_search',
		'label'    => esc_attr__( 'Ajax Search for Shop Products', 'zoa' ),
		'section'  => 'shop',
		'default'  => false,
		'choices'  => array(
			'off' => esc_attr__( 'Off', 'zoa' ),
			'on'  => esc_attr__( 'On', 'zoa' ),
		),
	)
);

/* PRODUCT QUICK ACTION ON MOBILE */
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'settings' => 'quick_action',
		'label'    => esc_attr__( 'Quick Actions on Mobile', 'zoa' ),
		'section'  => 'shop',
		'default'  => false,
		'choices'  => array(
			'off' => esc_attr__( 'Off', 'zoa' ),
			'on'  => esc_attr__( 'On', 'zoa' ),
		),
	)
);

/* STICKY ADD TO CART AND CHECKOUT BUTTON */
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'switch',
		'settings' => 'sticky_add_to_cart_and_checkout',
		'label'    => esc_attr__( 'Sticky Add to Cart and Checkout Button', 'zoa' ),
		'section'  => 'shop',
		'default'  => false,
		'choices'  => array(
			'off' => esc_attr__( 'Off', 'zoa' ),
			'on'  => esc_attr__( 'On', 'zoa' ),
		),
	)
);
