<?php 
    /* Template Name: Mid Contain */
    get_header(); 
?>
<main id="main" class="page-content">
    <?php
        if ( have_posts() ) :
            echo '<div class="flex-justify-center row"><div class="col-lg-8 col-md-10 col-12">';
            while ( have_posts() ) :
                the_post();

                if ( zoa_is_elementor() && zoa_elementor_page( get_the_ID() ) ) {
                    /*page build with Elementor*/
                    get_template_part( 'template-parts/content', 'page' );
                } else {
                    /*page without Elementor*/
                ?>
                    <div class="<?php if ( is_account_page() || is_cart() ) { ?>max-width--site gutter-padding--full<?php } else {?>container<?php } ?>">
                        <?php
                            get_template_part( 'template-parts/content', 'page' );

                            if ( comments_open() || get_comments_number() ) {
                                comments_template();
                            }
                        ?>
                    </div>
                <?php
                }
            endwhile;
            echo '</div></div>';
        else : ?>
            <div class="container">
                <?php get_template_part( 'template-parts/content', 'none' ); ?>
            </div>
        <?php endif; ?>
</main>

<?php get_footer();
