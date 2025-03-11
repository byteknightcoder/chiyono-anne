<?php

if ( ! defined( 'FW' ) ) die( 'Forbidden' );

$post_options = array(
    'general' => array(
        'title'   => esc_html__( 'General', 'zoa' ),
        'type'    => 'tab',
        'options' => array(
            'layout' => array(
                'label'   => esc_html__( 'Layout', 'zoa' ),
                'desc'    => esc_html__( 'Default: Customizer -> Shop single -> Layout', 'zoa' ),
                'type'    => 'select',
                'value'   => 'default',
                'choices' => array(
                    'default'    => esc_html__( 'Default', 'zoa' ),
                    'list'       => esc_attr__( 'List', 'zoa' ),
                    'grid'       => esc_attr__( 'Grid', 'zoa' ),
                    'vertical'   => esc_attr__( 'Vertical carousel', 'zoa' ),
                    'horizontal' => esc_attr__( 'Horizontal carousel', 'zoa' ),
                )
            ),

            'video' => array(
                'label' => esc_html__( 'Video', 'zoa' ),
                'type'  => 'text',
                'desc'  => esc_html__( 'Add a video url of the product', 'zoa' ),
            ),
        ),
    ),

    'label' => array(
        'title'   => esc_html__( 'Label', 'zoa' ),
        'type'    => 'tab',
        'options' => array(
            'label_txt' => array(
                'label' => esc_html__( 'Text', 'zoa' ),
                'type'  => 'text',
                'desc'  => esc_html__( 'Add custom label for this product, example: Hot', 'zoa' ),
            ),

            'label_color' => array(
                'label' => esc_html__( 'Text color', 'zoa' ),
                'type'  => 'color-picker',
                'value' => '#ffffff'
            ),

            'label_bg' => array(
                'label' => esc_html__( 'Background color', 'zoa' ),
                'type'  => 'color-picker',
                'value' => '#ff0000'
            ),
        ),
    )
);

$options = array(
    'product' => array(
        'title'    => esc_html__( 'Product Customizing', 'zoa'),
        'type'     => 'box',
        'options'  => $post_options
    ),
);

?>