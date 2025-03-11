<?php
// @codingStandardsIgnoreStart

/* ADD TOPBAR SECTION
***************************************************/
zoa_Kirki::add_section(
	'topbar', array(
		'title' => esc_attr__( 'Topbar', 'zoa' ),
		'panel' => 'menu_panel',
	)
);

/* TOPBAR
***************************************************/
zoa_Kirki::add_field(
	'zoa', array(
		'type'      => 'color',
		'settings'  => 'topbar_color',
		'label'     => esc_attr__( 'Topbar Color', 'zoa' ),
		'section'   => 'topbar',
		'default'   => '#666666',
		'transport' => 'auto',
		'output'    => array(
			array(
				'element'  => array(
					'.topbar',
				),
				'property' => 'color',
			),
		),
	)
);

zoa_Kirki::add_field(
	'zoa', array(
		'type'      => 'color',
		'settings'  => 'topbar_bg',
		'label'     => esc_attr__( 'Topbar Background Color', 'zoa' ),
		'section'   => 'topbar',
		'default'   => '#f7f7f7',
		'transport' => 'auto',
		'output'    => array(
			array(
				'element'  => array(
					'.topbar',
				),
				'property' => 'background-color',
			),
		),
	)
);

zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'textarea',
		'settings'    => 'topbar_left',
		'label'       => esc_attr__( 'Topbar Left Content', 'zoa' ),
		'section'     => 'topbar',
		'default'     => '<span class="topbar__tel">Hotline: <a href="tel:+01234567890">+01 234 567 890</a></span><ul class="menu-social topbar__social">
<li><a href="//facebook.com/zoa"></a></li>
<li><a href="//twitter.com/zoa"></a></li>
<li><a href="//instagram.com/zoa"></a></li>
</ul>',
	)
);

zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'text',
		'settings'    => 'topbar_center',
		'label'       => esc_attr__( 'Topbar Center Content', 'zoa' ),
		'section'     => 'topbar',
		'default'     => esc_attr__( 'Summer sale discount 50&#37; off.', 'zoa' ),
	)
);

zoa_Kirki::add_field(
	'zoa', array(
		'type'        => 'textarea',
		'settings'    => 'topbar_right',
		'label'       => esc_attr__( 'Topbar Right Content', 'zoa' ),
		'section'     => 'topbar',
		'default'     => '<div class="dropdown"><span class="dropdown__current">USD</span><div class="dropdown__content"><a href="#">USD</a><a href="#">EUR</a></div></div><div class="dropdown"><span class="dropdown__current">English</span><div class="dropdown__content"><a href="#">English</a><a href="#">French</a></div></div>',
	)
);