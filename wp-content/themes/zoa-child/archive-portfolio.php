<?php get_header(); ?>
<div class="max-width--site gutter-padding portfolio_row">
    <?php
        global $portfolio_count;
        $portfolio_count = 0;
        if ( have_posts() )  {
            get_template_part( 'sidebar', 'portfolio' );
            echo '<div class="main_col">';
            echo '<div class="series_catch">';
            echo '<h3 class="heading heading--small portfolio_cat_parent_title"></h3>';
            echo '<div class="desc portfolio_parent_cat_desc"><p></p></div>';
            echo '<h3 class="heading heading--small portfolio_cat_title"></h3>';
            echo '<div class="desc portfolio_cat_desc"><p></p></div>';
            echo '</div>';
            echo '<div class="portfolio-grids grid row">';
                while ( have_posts() ) : the_post();
                    get_template_part( 'template-parts/content', 'portfolio' );
                endwhile;
            echo '</div>';
            echo '</div>';
            echo '<div class="portfolio-grids-hidden" style="display: none;"></div>';
            zoa_paging();
        } else {
            get_template_part( 'template-parts/content', 'none' );
        }
    ?>
</div>
<?php get_footer(); ?>
