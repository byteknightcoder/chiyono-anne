<?php
// @codingStandardsIgnoreStart

namespace Elementor;

/*! RETURN IF ELEMENTOR IS NOT ACTIVE
------------------------------------------------->*/
if( ! zoa_is_elementor() ) {
	return;
}

/*! ADD PAGE SETTINGS
------------------------------------------------->*/
add_action( 'elementor/element/wp-post/document_settings/before_section_end', 'Elementor\zoa_page_settings' );
function zoa_page_settings( \Elementor\Core\DocumentTypes\PageBase $page ){

    if( ! isset( $page ) ) return;

    $settings = glob( get_template_directory() . '/elementor/page-settings/*.php' );

    foreach( $settings as $key ){
        if ( file_exists( $key ) ) {
            require_once $key;
        }
    }
}


/*! ADD THEME WIDGETS
------------------------------------------------->*/
add_action( 'elementor/widgets/widgets_registered', 'Elementor\zoa_widgets' );
function zoa_widgets() {

    $widgets = glob( get_template_directory() . '/elementor/widgets/*.php' );

    foreach( $widgets as $key ){
        if ( file_exists( $key ) ) {
            require_once $key;
        }
    }
}


/*! ADD WIDGET CATEGORIES
------------------------------------------------->*/
add_action( 'elementor/elements/categories_registered', 'Elementor\zoa_widget_categories' );
function zoa_widget_categories( $elements_manager ) {

    $elements_manager->add_category(
        'zoa-theme',
        array(
            'title' => esc_html__( 'Zoa Theme', 'zoa' )
        )
    );
}