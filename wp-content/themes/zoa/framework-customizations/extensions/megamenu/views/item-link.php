<?php if (!defined('FW')) die('Forbidden');
/**
 * @var WP_Post $item
 * @var string $title
 * @var array $attributes
 * @var object $args
 * @var int $depth
 */


$html = '';
if ( fw()->extensions->get( 'megamenu' )->show_icon() && $icon = fw_ext_mega_menu_get_meta( $item, 'icon' ) ){
    $html = '<span class="theme-icon-wp-menu '. $icon .'"></span> ';
}

// Make a menu WordPress way
echo fw_html_tag( 'a', $attributes, $args->link_before . $html . $title . $args->link_after );
