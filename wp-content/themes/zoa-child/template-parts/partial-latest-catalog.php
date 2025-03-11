<?php
if (wp_is_mobile()) {
    $per_page = 4;
} else {
    $per_page = 12;
}
$base_args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => $per_page,
    'orderby' => 'date',
    'order' => 'DESC',
    'paged' => 1,
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'product_visibility',
            'terms' => array('exclude-from-catalog'),
            'field' => 'name',
            'operator' => 'NOT IN',
        ),
        array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => array('familysale'),
            'operator' => 'NOT IN',
        )
    ),
    'meta_query' => array(
        array(
            'key' => '_stock_status',
            'value' => 'outofstock',
            'compare' => '!=',
        )
    ),
);

// Query for products with 'home' tag
$home_args = $base_args;
$home_args['posts_per_page'] = -1; // Get all matching products
$home_args['tax_query'][] = array(
    'taxonomy' => 'product_tag',
    'field' => 'slug',
    'terms' => 'home',
);

$home_query = new WP_Query($home_args);
$home_product_count = $home_query->post_count;

if ($home_product_count >= 3) {
    // Use 'home' tagged products
    $args = $home_args;
    $args['posts_per_page'] = $per_page; // Reset to original per_page value
} else {
    // Use the original query
    $args = $base_args;
}

// Get the dynamic section title from ACF
$section_title = get_field('section_title', 1491); // 1491 is the post ID

$products_query = new WP_Query($args);
if ($products_query->have_posts()) {
    echo '<div class="container-md">';
    echo '<div class="latest-catalog">';
	echo '<h3 class="sec-title ff_lovescript mid-title align--center fade-down fade-ani">' . __($section_title, 'zoa') . '</h3>';
    echo '<ul class="products prd_minimal caslick-dot caslick-dot__left">';
    while ($products_query->have_posts()) :
        $products_query->the_post();
        wc_get_template_part('content', 'product');
    endwhile;
    echo '</ul>';
    echo '<div class="mgt-n_1 align--right"><a href="' . home_url('shop-all') . '" class="link__mini link__thinarw">SEE ALL</a></div>';
    echo '</div>';
    echo '</div>';
    woocommerce_reset_loop();
}
wp_reset_postdata();
