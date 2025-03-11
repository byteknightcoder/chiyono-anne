<?php get_header(); ?>

<main id="main" class="page-content">
    <?php
        if( have_posts() ):
            while ( have_posts() ):
                the_post();

                if( zoa_is_elementor() && zoa_elementor_page( get_the_ID() ) ){
                    /*page build with Elementor*/
                    get_template_part( 'template-parts/content', 'page' );
                }else{
                    /*page without Elementor*/
                    ?>
                    <div class="container">
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
        else:
        ?>
            <div class="container">
                <?php get_template_part( 'template-parts/content', 'none' ); ?>
            </div>
        <?php endif; ?>
</main>

<?php
    get_footer();