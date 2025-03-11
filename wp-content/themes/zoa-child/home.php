<?php

/**
 * Template Name: Home Page Template 2022
 *
 */
get_header();
?>

<main id="main" class="page-content">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            $template = get_page_template();
            if (strpos($template, 'home.php') !== false) {
                //show acf values from Home1 page
                get_template_part('template-parts/section', 'from-home1');

                //show latest catalog with slick slider
                get_and_wrap_template_part('template-parts/partial', 'latest-catalog');

                //show content from Featured page
                get_and_wrap_template_part('template-parts/section', 'from-featured');

                //show Category link with image and name
                get_and_wrap_template_part('template-parts/partial', 'category-link');

                //show Series links with image and name
                get_and_wrap_template_part('template-parts/partial', 'series-links');

                //show acf values from Bespoke page
                get_and_wrap_template_part('template-parts/section', 'from-bespoke');

                //show Blog Post here
                get_and_wrap_template_part('template-parts/section', 'bloghome');

                //show Instagram Feed here
                echo do_shortcode('[instagram-feed]');
            } else {
                the_content();
            }
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>