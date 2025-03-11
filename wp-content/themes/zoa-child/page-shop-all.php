<?php
    /* Template Name: Shop page Template */ 
    get_header(); 
?>
<?php 
    global $post,$wp;
    $request = explode( '/', $wp->request );
    $post_data = get_post($post->post_parent);
    $post_slug = $post->post_name;
    $ancestors = get_post_ancestors( $post->ID );
    $parents_id = end($ancestors);
    $parent_slug = get_post($parents_id)->post_name;

    // Check if $request has at least two elements before accessing $request[1]
    $prev_slug = isset($request[1]) ? $request[1] : '';

    // Get the last element of $request safely
    $lastslug = end($request);

    if ( count($request) >= 2 ) {
        $pageClass = 'child-' . $parent_slug;
    } else {
        $pageClass = 'parent-' . $parent_slug;
    }
    $is_sub_myaccount = count($request) > 1 && $request[0] == 'my-account';
?>
<div class="row results-container max-width--site gutter-padding with-left-sidebar woocommerce">
    <?php
        if (!is_singular('product')) {
            do_action('woocommerce_sidebar');
        }
        $coln = '9';
        if (post_password_required()||is_page('special-event')) {
            $coln='12';
        }
    ?>
    <div class="product-grid-container col-12 col-lg-<?php echo $coln; ?>">
    <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                the_content();
           endwhile;
        else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
        <?php endif; ?>
    </div>
</div>
<?php get_footer();
