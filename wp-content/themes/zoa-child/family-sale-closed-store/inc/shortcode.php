<?php

function woo_products_by_cat_shortcode($atts, $content = null) {
    // Get attribuets
    ob_start();
    $term_obj = get_term_by('slug', 'familysale', 'product_cat');
    echo do_shortcode('[woof_products sid="auto_shortcode" taxonomies="product_cat:' . $term_obj->term_id . '" is_ajax=1 per_page=1000]');
    return ob_get_clean();
}

//use this shortcode to show product:  [woo_products_by_cat_shortcode]
add_shortcode("woo_products_by_cat_shortcode", "woo_products_by_cat_shortcode");