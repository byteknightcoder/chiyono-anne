<?php
    get_header();
    $sidebar = get_theme_mod('blog_sidebar', 'right');

    // Get the current category
    $cat = get_category(get_query_var('cat'));

    // Check if $cat is a valid category object before accessing its slug
    if (!is_wp_error($cat)) {
        $cat_slug = $cat->slug;
    } else {
        // Fallback for when $cat is not a valid category
        $cat_slug = 'uncategorized'; // or handle the error in another way
    }
?>

<?php
if ( 'blog' == $cat_slug ) {
    ?>
    <div class="container">
        <div class="row">
            <main id="main" class="col-md-12 col-lg-12">
                <?php
                    global $k;
                    $k = 0;
                    if (have_posts()) :
                        while (have_posts()): the_post();
                            $category_object = get_the_category(get_the_ID());
                            if ( $category_object[0]->slug != 'limited' ) {
                                if ( 0 == $k ) {
                                    echo '<div class="row first_row">';
                                } elseif ( 1 == $k ) {
                                    echo '<div class="row rest_row">';
                                }
                                get_template_part('template-parts/content', 'new_blog_archive');
                                if ( 0 == $k ) {
                                    echo '</div>';
                                }
                                $k++;
                            }
                        endwhile;
                        echo '</div>';
                        zoa_paging();
                    else :
                        get_template_part('template-parts/content', 'none');
                    endif;
                ?>
            </main>
        </div>
    </div>
    <?php
}else {
    ?>
    <?php if ( !$cat_slug == 'press' ) : ?>
        <div class="container">
            <div class="row">
                <?php
                switch ($sidebar):
                    case 'left':
                        /* ! sidebar in left
                          -------------------------------------------------> */
                        ?>
                        <div class="col-md-3">
                            <?php get_sidebar(); ?>
                        </div>
                        <main id="main" lass="col-md-9 col-lg-9">
                            <?php
                                if (have_posts()):
                                    while (have_posts()): the_post();
                                        get_template_part('template-parts/content', get_post_format());
                                    endwhile;
                                    zoa_paging();
                                else :
                                    get_template_part('template-parts/content', 'none');
                                endif;
                            ?>
                        </main>
                        <?php
                        break;
                    case 'right':
                        /* ! sidebar in right
                          -------------------------------------------------> */
                        ?>
                        <main id="main" class="col-md-9 col-lg-9">
                            <?php
                            if (have_posts()):
                                while (have_posts()): the_post();
                                    get_template_part('template-parts/content', get_post_format());
                                endwhile;
                                zoa_paging();
                            else :
                                get_template_part('template-parts/content', 'none');
                            endif;
                            ?>
                        </main>

                        <?php if (!$cat_slug == 'press') : ?>
                            <div class="col-md-3">
                                <?php get_sidebar(); ?>
                            </div>
                        <?php endif; ?>
                        <?php
                        break;
                    case 'full':
                        /* ! no sidebar
                          -------------------------------------------------> */
                        ?>
                            <main id="main" class="col-md-12 col-lg-12">
                                <?php
                                if (have_posts()) :
                                    while (have_posts()): the_post();
                                        get_template_part('template-parts/content', get_post_format());
                                    endwhile;
                                    zoa_paging();
                                else :
                                    get_template_part('template-parts/content', 'none');
                                endif;
                                ?>
                            </main>
                        <?php
                        break;
                endswitch;
                ?>
            </div>
        </div>
    <?php else : ?>
        <div class="container">
            <div class="row">
                <main id="main" class="col-md-12 col-lg-12">
                    <?php
                    if (have_posts()) :
                        echo '<div class="row">';
                        while (have_posts()): the_post();
                            get_template_part('template-parts/content', 'press_archive');
                        endwhile;
                        zoa_paging();
                        echo '</div>';
                    else :
                        get_template_part('template-parts/content', 'none');
                    endif;
                    ?>
                </main>
            </div>
        </div>
    <?php endif; ?>
    <?php
}
get_footer();
