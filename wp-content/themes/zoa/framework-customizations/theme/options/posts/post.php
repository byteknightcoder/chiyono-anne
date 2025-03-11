<?php

if ( ! defined( 'FW' ) ) die( 'Forbidden' );

$post_options = array(
    /* PAGE HEADER
    ***************************************************/
    'page_header' => array(
        'title'   => esc_html__( 'General', 'zoa' ),
        'type'    => 'tab',
        'options' => array(
            /* SET MENY LAYOUT FOR BLOG */
            'single_blog_menu' => array(
                'label'   => esc_html__( 'Menu layout', 'zoa' ),
                'type'    => 'short-select',
                'choices' => array(
                    'default'  => esc_html__( 'Default', 'zoa' ),
                    'layout-1' => esc_html__( 'Layout 1', 'zoa' ),
                    'layout-2' => esc_html__( 'Layout 2', 'zoa' ),
                    'layout-3' => esc_html__( 'Layout 3', 'zoa' ),
                    'layout-4' => esc_html__( 'Layout 4', 'zoa' ),
                    'layout-5' => esc_html__( 'Layout 5', 'zoa' ),
                    'layout-6' => esc_html__( 'Layout 5', 'zoa' ),
                    'layout-7' => esc_html__( 'Layout 5', 'zoa' ),
                ),
                'value' => 'default',
            ),

            /* SET PAGE HEADER FOR BLOG SINGLE */
            'p_bread' => array(
                'label'   => esc_html__( 'Page header', 'zoa' ),
                'type'    => 'short-select',
                'choices' => array(
                    'default'  => esc_html__( 'Default', 'zoa' ),
                    'layout-1' => esc_html__( 'Layout 1', 'zoa' ),
                    'layout-2' => esc_html__( 'Layout 2', 'zoa' ),
                    'disable'  => esc_html__( 'Disable', 'zoa' ),
                ),
                'value' => 'default',
            ),

            /* FOOTER DISABLE */
            'p_footer' => array(
                'label'   => esc_html__( 'Footer', 'zoa' ),
                'type'    => 'short-select',
                'choices' => array(
                    'default' => esc_html__( 'Default', 'zoa' ),
                    'enable'  => esc_html__( 'Enable', 'zoa' ),
                    'disable' => esc_html__( 'Disable', 'zoa' ),
                ),
                'value' => 'default',
            ),
        ),
    ),

    /* GALLERY
    ***************************************************/
    'p_gallery' => array(
        'type'    => 'tab',
        'title'   => esc_html__( 'Gallery', 'zoa' ),
        'options' => array(
            'd_gallery'       => array(
                'label' => esc_html__( 'Upload', 'zoa' ),
                'desc'  => esc_html__( 'Multiple Images for Gallery post format.', 'zoa' ),
                'type'  => 'multi-upload',
            ),
            'arrows'       => array(
                'label'       => esc_html__( 'Show arrows', 'zoa' ),
                'type'        => 'switch',
                'value'       => true,
                'left-choice' => array(
                    'value' => false,
                    'label' => esc_html__( 'No', 'zoa' )
                ),
                'right-choice' => array(
                    'value' => true,
                    'label' => esc_html__( 'Yes', 'zoa' )
                ),
            ),
            'dots'       => array(
                'label'       => esc_html__( 'Show dots', 'zoa' ),
                'type'        => 'switch',
                'value'       => false,
                'left-choice' => array(
                    'value' => false,
                    'label' => esc_html__( 'No', 'zoa' )
                ),
                'right-choice' => array(
                    'value' => true,
                    'label' => esc_html__( 'Yes', 'zoa' )
                )
            ),
        )
    ),

    /* VIDEO
    ***************************************************/
    'p_video' => array(
        'type'    => 'tab',
        'title'   => esc_html__( 'Video', 'zoa' ),
        'options' => array(
            'video_type' => array(
                'label'   => esc_html__( 'Video embed', 'zoa' ),
                'type'    => 'short-select',
                'choices' => array(
                    'youtube' => esc_html__( 'Youtube', 'zoa' ),
                    'vimeo'   => esc_html__( 'Vimeo', 'zoa' ),
                ),
                'value' => 'youtube'
            ),
            'video_id' => array(
                'label' => esc_html__( 'Video ID', 'zoa' ),
                'desc'  => sprintf(esc_html__( 'Just paste the bold part of the video\'s URL: %1$s%2$s', 'zoa' ),
                        '<br>https://youtube.com/watch?v=<b>qDvFdj-pFMc</b>',
                        '<br>https://vimeo.com/<b>119287310</b>'),
                'type'  => 'short-text',
                'value' => 'qDvFdj-pFMc'
            ),
        )
    ),

    /* AUDIO
    ***************************************************/
    'p_audio' => array(
        'type' => 'tab',
        'title' => esc_html__( 'Audio', 'zoa' ),
        'options' => array(
            'd_audio' => array(
                'label' => esc_html__( 'Input', 'zoa' ),
                'desc'  => esc_html__( 'Enter iframe for Audio format', 'zoa' ),
                'type'  => 'textarea',
            ),
        )
    ),
);

$options = array(
    'post_layout_box' => array(
        'title'   => esc_html__( 'Post Customizing', 'zoa'),
        'type'    => 'box',
        'options' => $post_options
    ),
);

?>