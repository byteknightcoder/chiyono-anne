<?php 
    acf_form_head();
    get_header();
?>
<main id="main" class="page-content">
    <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
            ?>
                <div class="container">
                    <?php get_template_part( 'template-parts/content', 'reservation-confirm' ); ?>
                </div>
            <?php
           endwhile;
        else:
        ?>
            <div class="container">
                <?php get_template_part( 'template-parts/content', 'none' ); ?>
            </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
