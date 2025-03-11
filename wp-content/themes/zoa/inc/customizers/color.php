<?php

/* ADD COLOR SECTION
***************************************************/
zoa_Kirki::add_section(
	'color', array(
		'title'      => esc_attr__( 'Colors', 'zoa' ),
		'priority'   => 1,
	)
);



/* PRIMARY COLOR
***************************************************/
zoa_Kirki::add_field(
	'zoa', array(
		'type'      => 'color',
		'label'     => esc_attr__( 'Primary color', 'zoa' ),
		'settings'  => 'primary_color',
		'section'   => 'color',
		'default'   => '#dd2a2a',
		'transport' => 'auto',
		'output'    => array(
			/*! COLOR
			------------------------------------------------->*/
			array(
				'element' => array(
					'a:not(.woocommerce-loop-product__link):hover',
					'.menu-woo-action:hover .menu-woo-user',
					'.woocommerce-mini-cart__total .amount',
					'.read-more-link',
					'.wd-pro-flash-sale .price ins',
					'.woocommerce-form-login-toggle .woocommerce-info a',
					'.woocommerce-form-coupon-toggle .woocommerce-info a',
					'.size-guide__close:hover',
					'.size-guide__close:focus',
					'.product-categories .current-cat > a',
					'.product-categories .current-cat > .count',
					'.product-categories .current-cat > .accordion-cat-toggle',
				),
				'property' => 'color',
			),

			/*! BACKGROUND
			------------------------------------------------->*/
			array(
				'element' => array(
					'#sidebar-menu-content .theme-primary-menu a:before',
					'#sidebar-menu-content .theme-primary-menu a:hover:before',
					'.menu-woo-cart span',
					'.shop-cart-count',
					'.sidebar-action-cart',
					'.loop-action .product-quick-view-btn:hover',
					'.loop-action a:hover',
					'.loop-action .yith-wcwl-add-to-wishlist a:hover',
					'.cart-sidebar-content .woocommerce-mini-cart__buttons .checkout',
					'#page-loader #nprogress .bar,
					.scroll-to-top',
				),
				'property' => 'background-color',
			),

			/*! BORDER
			------------------------------------------------->*/
			array(
				'element' => array(
					'.blog-read-more:hover',
					'.entry-categories a',
					'.swatch.selected:before',
					'.not-found .back-to-home',
					'.p-attr-swatch.p-attr-label.active',
					'.has-default-loop-add-to-cart-button .product .button:hover',
					'.has-default-loop-add-to-cart-button .product .added_to_cart:hover',
				),
				'property' => 'border-color',
			),

			array(
				'element' => array(
					'.blog-read-more:hover',
					'.woocommerce-tabs .tabs li.active a',
				),
				'property' => 'border-bottom-color',
			),

			array(
				'element' => array(
					'.is-loading-effect:before',
				),
				'property' => 'border-top-color',
			),
		),
	)
);
