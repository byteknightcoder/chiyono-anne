<?php 
$class = '';
$terms = wp_get_post_terms($post->ID, 'portfolio_category');
if (count($terms) > 0) {
    foreach ($terms as $term) {
        $class .= 'p_' . $term->term_id . ' ';
    }
}

// Initialize the global portfolio count
global $portfolio_count;

if (have_rows('images_series')) {
    $serie_index = 0;
    while (have_rows('images_series', $post->ID, true)) : the_row();
        $serie_index++;
        $pt_categorized_images = get_sub_field('images');
        $pt_categorized_images = !empty($pt_categorized_images) ? $pt_categorized_images : array();
        $image_info = get_sub_field('info');

        foreach ($pt_categorized_images as $pt_categorized_image) {
            $portfolio_count++;
            $col_class = ''; // Initialize $col_class

            // Define the column class
            $col_class .= 'col-lg-3 col-md-4 col-sm-6 col-6';
            ?>

            <div class="grid-item all-port <?php echo $col_class; ?> <?php echo $class; ?>">
                <div class="grid-outer">
                    <a href="<?php echo get_permalink($post->ID); ?>" class="pf_link images_series" data-id="<?php echo $post->ID; ?>" data-serie_index="<?php echo $serie_index ?>">
                        <div class="grid-content">
                            <div class="grid-inner">
                                <div class="pf_item">
                                    <img src="<?php echo $pt_categorized_image['sizes']['portfolio'] ?>" class="111 attachment-portfolio size-portfolio wp-post-image" alt="">
                                </div>
                                <div class="pf_caption">
                                    <span class="pf_series"><?php the_title(); ?></span>
                                    <h2 class="pf_title"><?php echo $image_info['title']; ?></h2>
                                    <p class="see_more"><span>See details</span></p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php
            break; // Break after the first categorized image
        }
    endwhile;
} else {
    $portfolio_count++;

    // Initialize $col_class for the second case
    $col_class = '';
    if ($portfolio_count % 3 == 0) {
        $col_class .= 'col-lg-6 col-md-4 col-sm-6 col-6';
    } elseif ($portfolio_count % 4 == 0) {
        $col_class .= 'col-lg-3 col-md-8 col-sm-12 col-6';
    } else {
        $col_class .= 'col-lg-3 col-md-4 col-sm-6 col-6';
    }
    ?>

    <div class="grid-item all-port <?php echo $col_class; ?> <?php echo $class; ?>">
        <div class="grid-outer">
            <a href="<?php echo get_permalink($post->ID); ?>" class="pf_link" data-id="<?php echo $post->ID; ?>">
                <div class="grid-content">
                    <div class="grid-inner">
                        <div class="pf_item">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('portfolio'); ?>
                            <?php else : ?>
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/pf_sample_thum.jpg" alt="sample" />
                            <?php endif; ?>
                        </div>
                        <div class="pf_caption">
                            <h2 class="pf_title"><?php echo $series_title; ?></h2>
                            <p class="see_more"><span>See details</span></p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

<?php } ?>
