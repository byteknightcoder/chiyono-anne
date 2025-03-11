<?php

/*<a class="fa fa-facebook" href="//facebook.com">.</a>
<a class="fa fa-twitter" href="//twitter.com">.</a>
<a class="fa fa-instagram" href="//instagram.com">.</a>
<a class="fa fa-pinterest" href="//pinterest.com">.</a>
<a class="fa fa-vk" href="//linkedin.com">.</a>*/

/* ADD FOOTER SECTION
***************************************************/
zoa_Kirki::add_section( 'footer', array(
    'title'      => esc_attr__( 'Footer', 'zoa' ),
    'capability' => 'edit_theme_options',
    'priority'   => 1,
));


/*! SHOW FOOTER
------------------------------------------------->*/
zoa_Kirki::add_field( 'zoa', array(
    'type'        => 'switch',
    'settings'    => 'show_footer',
    'label'       => esc_attr__( 'Show Footer', 'zoa' ),
    'section'     => 'footer',
    'default'     => true,
    'choices'     => array(
        'off' => esc_attr__( 'Off', 'zoa' ),
        'on'  => esc_attr__( 'On', 'zoa' ),
    ),
    'partial_refresh' => array(
        'footer_edit_location' => array(
            'selector'        => '#theme-footer',
            'render_callback' => 'zoa_footer',
        ),
    ),
));


/*! COLUMN
------------------------------------------------->*/
if( is_active_sidebar( 'footer-widget' ) ):
    zoa_Kirki::add_field( 'zoa', array(
        'type'      => 'select',
        'label'     => esc_attr__( 'Column widgets', 'zoa' ),
        'settings'  => 'ft_column',
        'section'   => 'footer',
        'default'   => '4',
        'transport' => 'postMessage',
        'choices'   => array(
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
        ),
        'active_callback' => array(
            array(
                'setting'  => 'show_footer',
                'operator' => '==',
                'value'    => true,
            ),
        )
    ));
endif;

/*! LOGO
------------------------------------------------->*/
zoa_Kirki::add_field( 'zoa', array(
    'type'            => 'image',
    'settings'        => 'ft_logo',
    'section'         => 'footer',
    'label'           => esc_attr__( 'Footer Logo', 'zoa' ),
    'partial_refresh' => array(
        'footer_logo' => array(
            'selector'        => '.footer-logo',
            'render_callback' => 'zoa_footer_logo',
        ),
    ),
    'active_callback' => array(
        array(
            'setting'  => 'show_footer',
            'operator' => '==',
            'value'    => true,
        ),
    ),
));


/*! BAKGROUND
------------------------------------------------->*/
zoa_Kirki::add_field( 'zoa', array(
    'type'      => 'background',
    'settings'  => 'footer_bg',
    'section'   => 'footer',
    'transport' => 'auto',
    'default'   => array(
        'background-color'      => '#fff',
        'background-image'      => '',
        'background-repeat'     => 'no-repeat',
        'background-position'   => 'center center',
        'background-size'       => 'cover',
        'background-attachment' => 'scroll',
    ),
    'output' => array(
        array(
            'element' => '#theme-footer'
        )
    ),
    'active_callback'  => array(
        array(
            'setting'  => 'show_footer',
            'operator' => '==',
            'value'    => true,
        ),
    ),
));


/*! COPYRIGHT
------------------------------------------------->*/
zoa_Kirki::add_field( 'zoa', array(
    'type'            => 'editor',
    'settings'        => 'ft_copyright',
    'section'         => 'footer',
    'default'         => '',
    'label'           => esc_attr__( 'Copyright', 'zoa' ),
    'transport'       => 'postMessage',
    'active_callback' => array(
        array(
            'setting'  => 'show_footer',
            'operator' => '==',
            'value'    => true,
        ),
    ),
    'js_vars'   => array(
        array(
            'element'  => '#theme-footer .footer-copyright',
            'function' => 'html',
        ),
    ),
));

/*! RIGHT CONTENT
------------------------------------------------->*/
zoa_Kirki::add_field( 'zoa', array(
    'type'            => 'editor',
    'settings'        => 'ft_bot_right',
    'section'         => 'footer',
    'default'         => '',
    'label'           => esc_attr__( 'Right content', 'zoa' ),
    'transport'       => 'postMessage',
    'active_callback' => array(
        array(
            'setting'  => 'show_footer',
            'operator' => '==',
            'value'    => true,
        ),
    ),
    'js_vars'   => array(
        array(
            'element'  => '#theme-footer .footer-bot-right',
            'function' => 'html',
        ),
    ),
));