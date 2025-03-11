<?php
    get_header();
    $sidebar = is_active_sidebar( 'blog-widget' ) ? get_theme_mod( 'blog_sidebar', 'right' ) : 'full';
    $category_to_check = get_category_by_slug( 'blog' );
?>

<div class="<?php if ( in_category(array('press')) || in_category( 'blog' ) || post_is_in_descendant_category( $category_to_check->term_id ,$post->ID) ) { ?>max-width--site gutter-padding--full col-sm--fluid<?php } else { ?>container test<?php } ?>">
    <div class="row">
        <?php
            switch( $sidebar ):
                case 'left':
            /*! sidebar in left
            ------------------------------------------------->*/
        ?>
            <div class="col-md-3">
                <?php get_sidebar(); ?>
            </div>

            <main id="main" class="col-md-9 col-lg-9">
                <?php
                    if ( have_posts() ) {                        
                        while ( have_posts() ): the_post();
                            if (is_singular('post')) {
                                if ( in_category(array('press')) ) {
                                    get_template_part( 'template-parts/content', 'press' );
                                } elseif ( is_single(31316) ) {
                                    get_template_part( 'template-parts/content', 'blog-temp02' );
                                } elseif ( in_category( 'blog' ) || post_is_in_descendant_category( $category_to_check->term_id ,$post->ID) ) {
                                    get_template_part( 'template-parts/content', 'blog-temp' );
                                } else {
                                    get_template_part( 'template-parts/content', 'blog' );
                                }
                            } else {
                                get_template_part( 'template-parts/content', get_post_format() );
                            }
                        endwhile;

                        if ( comments_open() || get_comments_number() ) {
                            comments_template();
                        }
                    } else {
                        get_template_part( 'template-parts/content', 'none' );
                    }
                ?>
            </main>
        <?php
            break;
            case 'right':
            /*! sidebar in right
            ------------------------------------------------->*/
        ?>
            <main id="main" class="col-md-9 col-lg-9">
                <?php
                    if ( have_posts() ) {
                        $category_to_check = get_category_by_slug( 'blog' );
                        while ( have_posts() ): the_post();
                            if (is_singular('post')) {
                                if ( in_category(array('press')) ) {
                                    get_template_part( 'template-parts/content', 'press' );
                                } elseif ( is_single(31316) ) {
                                    get_template_part( 'template-parts/content', 'blog-temp02' );
                                } elseif ( is_single(30944) ) {
                                    get_template_part( 'template-parts/content', 'blog-temp01' );
                                } elseif ( in_category( 'blog' ) || post_is_in_descendant_category( $category_to_check->term_id ,$post->ID) ) {
                                    get_template_part( 'template-parts/content', 'blog-temp' );
                                } else {
                                    get_template_part( 'template-parts/content', 'blog' );
                                }
                            } else {
                                get_template_part( 'template-parts/content', get_post_format() );
                            }
                        endwhile;

                        if ( comments_open() || get_comments_number() ) {
                            comments_template();
                        }
                    } else {
                        get_template_part( 'template-parts/content', 'none' );
                    }
                ?>
            </main>

            <div class="col-md-3">
                <?php get_sidebar(); ?>
            </div>
        <?php
            break;
            case 'full':
            /*! no sidebar
            ------------------------------------------------->*/
        ?>
            <main id="main" class="col-md-12 col-lg-12">
                <?php
                    if ( have_posts() ) {
                        $category_to_check = get_category_by_slug( 'blog' );
                        while ( have_posts() ): the_post();
                            if (is_singular('post')) {
                                if ( in_category(array('press')) ) {
                                    get_template_part( 'template-parts/content', 'press' );
                                } elseif( is_single(31316) ) {
                                    get_template_part( 'template-parts/content', 'blog-temp02' );
                                } elseif( is_single(30944) ) {
                                    get_template_part( 'template-parts/content', 'blog-temp01' );
                                } elseif( in_category( 'blog' ) || post_is_in_descendant_category( $category_to_check->term_id,$post->ID ) ) {
                                    get_template_part( 'template-parts/content', 'blog-temp' );
                                    //get_template_part( 'template-parts/content', 'blog-temp01' );
                                } else {
                                    get_template_part( 'template-parts/content', 'blog' );
                                }
                            } else {
                                get_template_part( 'template-parts/content', get_post_format() );
                            }
                        endwhile;

                        if ( comments_open() || get_comments_number() ):
                            comments_template();
                        endif;
                    } else {
                        get_template_part( 'template-parts/content', 'none' );
                    }
                ?>
            </main>
        <?php
            break;
            endswitch;
        ?>
    </div>
	<?php if ( in_category( 'blog' ) || post_is_in_descendant_category( $category_to_check->term_id,$post->ID ) ) { echo do_shortcode('[instagram-feed]'); } ?>
</div>

<?php get_footer();
