<?php

/* ADD TYPO SECTION
***************************************************/
zoa_Kirki::add_section(
	'typo', array(
		'title'      => esc_attr__( 'Typography', 'zoa' ),
		'priority'   => 1,
	)
);


/*LABEL*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'custom',
		'settings' => 'kirki_label_body_font',
		'section'  => 'typo',
		'default'  => zoa_label( esc_attr__( 'Body font', 'zoa' ) ),
	)
);

/*BODY FONT*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'      => 'typography',
		'settings'  => 'typo_body',
		'section'   => 'typo',
		'transport' => 'auto',
		'default'   => array(
			'font-family'    => 'Eina03',
			'variant'        => 'regular',
			'color'          => '#666',
			'line-height'    => '26px',
			'letter-spacing' => '0',
			'text-transform' => 'none',
		),
		'output'      => array(
			array(
				'element' => array(
					'body',
				),
			),
			array(
				'element'  => array(
					'a, input, select, textarea, button',
					'.woocommerce-loop-product__title',
					'.price del',
					'.comment-form-rating .stars:not(.selected) a:hover ~ a',
					'.comment-form-rating .stars.selected .active ~ a',
					'.comment-form-rating .stars.selected a:hover ~ a',
					'.product_meta > span span',
					'.product_meta > span a',
					'.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a',
				),
				'property' => 'color',
				'choice'   => 'color',
			),
			array(
				'element'  => array(
					'input, select, textarea, button',
				),
				'property' => 'font-family',
				'choice'   => 'font-family',
			),
			array(
				'element'  => array(
					'input, select, textarea, button',
				),
				'property' => 'font-family',
				'choice'   => 'font-family',
			),
		),
		'choices' => zoa_custom_font(),
	)
);

/*LABEL*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'     => 'custom',
		'settings' => 'kirki_label_heading_font',
		'default'  => zoa_label( esc_attr__( 'Heading font', 'zoa' ) ),
		'section'  => 'typo',
	)
);

/*HEADING FONT*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'      => 'typography',
		'settings'  => 'typo_heading',
		'section'   => 'typo',
		'transport' => 'auto',
		'default'   => array(
			'font-family'    => 'Eina03',
			'variant'        => '700',
			'line-height'    => '1.2em',
			'color'          => '#222',
			'letter-spacing' => '0',
			'text-transform' => 'none',
		),
		'output'      => array(
			array(
				'element' => 'h1, h2, h3, h4, h5, h6',
			),

			/*COLOR*/
			array(
				'element'  => array(
					'.comment-author-name',
					'.crumbs .last-item span',
					'#theme-search-btn',
					'.menu-woo-user',
					'.menu-woo-cart',
					'.woocommerce-loop-product__title:hover',
					'.onsale',
					'.price ins',
					'.price >.amount',
					'.blog-header-info .if-item a',
					'.blog-read-more',
					'.shop-sidebar .wcapf-layered-nav .chosen',
					'.ht-pagination ul .page-numbers:not(.current)',
					'.single .blog-header-info .if-item a',
					'.blog-article .theme-social-icon a:hover',
					'.woocommerce-tabs .tabs li.active a',
					'.woocommerce-tabs .tabs li a:hover',
					'.woocommerce-review__author',
					'.summary .yith-wcwl-add-to-wishlist a',
					'.product_meta > span',
					'.product_meta .p-shared > span',
					'.variations .label label',
					'.widget.widget_shopping_cart .woocommerce-mini-cart__total strong',
					'.woocommerce-cart .woocommerce-cart-form th',
					'#shipping_method input[type="radio"]:checked + label:before',
					'.methods input[type="radio"]:checked + label:before',
					'#shipping_method input[type="radio"]:checked + label',
					'.methods input[type="radio"]:checked + label',
					'#order_review .shop_table tfoot tr th:first-of-type',
					'#order_review .shop_table tfoot strong',
					'.form-row > label',
					'.form-row input[type="text"]',
					'.form-row input[type="tel"]',
					'.form-row input[type="email"]',
					'.form-row textarea',
					'.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link.is-active a',
					'.woocommerce-form-login-toggle .woocommerce-info',
					'.woocommerce-form-coupon-toggle .woocommerce-info',
				),
				'property' => 'color',
				'choice'   => 'color',
			),

			/*FONT FAMILY*/
			array(
				'element'  => array(
					'strong',
				),
				'property' => 'font-family',
				'choice'   => 'font-family',
			),

			/*BACKGROUND*/
			array(
				'element' => array(
                    '.woocommerce-address-fields .button',
					'.page-numbers.current',
					'.footer-subscribe-form button',
					'.loop-action .product-quick-view-btn',
					'.loop-action .zoa-add-to-cart-btn',
					'.loop-action .yith-wcwl-add-to-wishlist a',
					'.cart .single_add_to_cart_button',
					'.widget.widget_shopping_cart .woocommerce-mini-cart__buttons a',
					'.price_slider_wrapper .ui-widget-header',
					'.price_slider_wrapper .price_slider_amount button',
					'.shop-sidebar .woocommerce-widget-layered-nav-dropdown__submit',
					'.woocommerce-cart .woocommerce-cart-form .actions button[type="submit"]',
					'.woocommerce-cart .cart_totals .checkout-button',
					'#place_order',
					'.woocommerce-form-login button[type="submit"]',
					'.woocommerce-form-register button[type="submit"]',
					'.woocommerce-form-coupon button[type="submit"]',
					'.woocommerce-MyAccount-content .woocommerce-EditAccountForm button[type="submit"]',
					'.flash-sale-atc a',
					'.woocommerce-cart .woocommerce-shipping-calculator button[type="submit"]',
					'.lost_reset_password button[type="submit"]',
				),
				'property' => 'background-color',
				'choice'   => 'color',
			),

			/*BORDER*/
			array(
				'element' => array(
					'.blog-article .tagcloud a:hover',
					'.ht-pagination ul a:hover',
					'.woocommerce-pagination ul a:hover',
				),
				'property' => 'border-color',
				'choice'   => 'color',
			),

			array(
				'element' => array(
					'.blog-read-more',
					'.summary .yith-wcwl-add-to-wishlist',
				),
				'property' => 'border-bottom-color',
				'choice'   => 'color',
			),

			array(
				'element' => array(
					'blockquote',
				),
				'property' => 'border-left-color',
				'choice'   => 'color',
			),
		),
		'choices' => zoa_custom_font(),
	)
);

/*h1*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'text',
		'settings'    => 'typo_h1',
		'label'       => esc_attr__( 'H1', 'zoa' ),
		'description' => esc_attr__( 'Font size', 'zoa' ),
		'section'     => 'typo',
		'transport'   => 'auto',
		'default'     => '46px',
		'output'      => array(
			array(
				'element'  => 'h1',
				'property' => 'font-size',
			),
		),
	)
);

/*h2*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'text',
		'settings'    => 'typo_h2',
		'label'       => esc_attr__( 'H2', 'zoa' ),
		'description' => esc_attr__( 'Font size', 'zoa' ),
		'section'     => 'typo',
		'transport'   => 'auto',
		'default'     => '28px',
		'output'      => array(
			array(
				'element'  => 'h2',
				'property' => 'font-size',
			),
		),
	)
);

/*h3*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'text',
		'settings'    => 'typo_h3',
		'label'       => esc_attr__( 'H3', 'zoa' ),
		'description' => esc_attr__( 'Font size', 'zoa' ),
		'section'     => 'typo',
		'transport'   => 'auto',
		'default'     => '24px',
		'output'      => array(
			array(
				'element'  => 'h3',
				'property' => 'font-size',
			),
		),
	)
);

/*h4*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'text',
		'settings'    => 'typo_h4',
		'label'       => esc_attr__( 'H4', 'zoa' ),
		'description' => esc_attr__( 'Font size', 'zoa' ),
		'section'     => 'typo',
		'transport'   => 'auto',
		'default'     => '22px',
		'output'      => array(
			array(
				'element'  => 'h4',
				'property' => 'font-size',
			),
		),
	)
);

/*h5*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'text',
		'settings'    => 'typo_h5',
		'label'       => esc_attr__( 'H5', 'zoa' ),
		'description' => esc_attr__( 'Font size', 'zoa' ),
		'section'     => 'typo',
		'transport'   => 'auto',
		'default'     => '20px',
		'output'      => array(
			array(
				'element'  => 'h5',
				'property' => 'font-size',
			),
		),
	)
);

/*h6*/
zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'text',
		'settings'    => 'typo_h6',
		'label'       => esc_attr__( 'H6', 'zoa' ),
		'description' => esc_attr__( 'Font size', 'zoa' ),
		'section'     => 'typo',
		'transport'   => 'auto',
		'default'     => '18px',
		'output'      => array(
			array(
				'element'  => 'h6',
				'property' => 'font-size',
			),
		),
	)
);
