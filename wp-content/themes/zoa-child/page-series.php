<?php
/* Template Name: Series page */
get_header();
?>
<main id="main" class="page-content page-short_content">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            $args = array(
                'hide_empty' => false, // also retrieve terms which are not used yet
                'limit' => -1,
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $series = get_terms('series', $args);
            if (!empty($series)) {
            ?>
                <div class="archive-series">
                    <?php
                        $k = 0;
                        foreach ($series as $item) {
                            // get the thumbnail id using the queried category term_id
                            $thumbnail_id = get_term_meta($item->term_id, 'img', true);
                            // get the image URL
                            $image = wp_get_attachment_image_url($thumbnail_id, 'large');
                        ?>
                            <div class="sr-item">
                                <a href="<?php echo esc_url(get_term_link($item)); ?>" class="sr-link">
                                    <?php if (!empty($image)) : ?>
                                        <div class="img">
                                            <img class="sr-img" src="<?php echo $image; ?>"/>
                                        </div>
                                    <?php endif; ?>
                                    <div class="sr-name">
                                        <?php echo $item->name; ?>
                                    </div>
                                </a>
                            </div>
                        <?php
                            $k++;
                        }
                    ?>
                </div>
                <?php
            }
        endwhile;
    else:
    ?>
        <div class="container">
            <?php get_template_part('template-parts/content', 'none'); ?>
        </div>
    <?php endif; ?>
</main>

<?php get_footer();
