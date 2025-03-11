<?php

// use this shortcode to show product:  [woo_orf_products_by_tags]
add_shortcode('woo_orf_products_by_tags', 'woo_products_by_tags_shortcode');
function woo_products_by_tags_shortcode($atts, $content = null) {
    // Get attribuets
    $tags = get_option('ch_orf_tag', '');
    ob_start();
    // Define Query Arguments
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'product_tag' => $tags,
        'post_status' => array('publish')
    );
    // Create the new query
    $loop = new WP_Query($args);
    // Get products number
    $product_count = $loop->post_count;
    // If results
    if ($product_count > 0 && is_expired_orf() == false && !empty($tags)) {
        $arr_tag_id = array();
        $tags_arr = explode(",", $tags);
        foreach ($tags_arr as $value) {
            $value = trim($value);
            $tag_obj = get_term_by('slug', $value, 'product_tag');
            $arr_tag_id[] = $tag_obj->term_id;
        }
        echo do_shortcode('[woof_products sid="auto_shortcode" taxonomies="product_tag:' . implode(',', $arr_tag_id) . '" is_ajax=1 per_page=1000]');
    } else {
        esc_html_e('No product matching your criteria.', 'zoa');
    } // endif $product_count > 0
    return ob_get_clean();
}

add_action('wp_footer', 'ch_product_hidden');
function ch_product_hidden() {
    // Get attribuets
    if (is_page('event')) {
        $tags = get_option('ch_orf_tag', '');
        // Define Query Arguments
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'product_tag' => $tags,
            'post_status' => array('publish')
        );
        // Create the new query
        $loop = new WP_Query($args);
        // Get products number
        $product_count = $loop->post_count;
        // If results
        if ($product_count > 0 && is_expired_orf() == false && !empty($tags)) :
            echo '<ul style="display:none;" class="event_products">';
            // Start the loop
            while ($loop->have_posts()) : $loop->the_post();
                $link = get_the_permalink() . '?type=' . order_type_for_event();
                if (order_sub_type_for_event() != '') {
                    $link .= '&sub_type=' . order_sub_type_for_event();
                }
            ?>
                <span id="<?php echo get_the_ID(); ?>" ch_link_event="<?php echo $link; ?>" class="ch_link_event" style="display: none;"></span>
            <?php
            endwhile;
            echo '</ul>';
        endif; // endif $product_count > 0
    }
}

add_action('template_redirect', 'event_page_add_tag', 999);
function event_page_add_tag() {
    if (isset($_SERVER['HTTPS']) &&
        ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
    ) {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }

    $currenturl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $currenturl_relative = wp_make_link_relative($currenturl);

    switch ($currenturl_relative) {
        case '/event':
            $urlto = add_query_arg('product_tag', get_option('ch_orf_tag', ''), home_url('event'));
            break;
        case '/event/':
            $urlto = add_query_arg('product_tag', get_option('ch_orf_tag', ''), home_url('event'));
            break;
        default:
            return;
    }
    if ($currenturl != $urlto) {
        exit(wp_redirect($urlto, 301));
    }
}
