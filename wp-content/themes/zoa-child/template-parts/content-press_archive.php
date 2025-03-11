<?php
$c_header = zoa_page_header_slug();
?>
<div class="col-md-4 col-xs-6 blog-article press_articles" <?php zoa_schema_markup('blog'); ?>>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php zoa_schema_markup('blog_list'); ?>>
        <div itemprop="mainEntityOfPage">
            <?php 
            $link_with_image=get_field('link_with_image');
            if(!empty($link_with_image['link'])&&!empty($link_with_image['image']['url'])){
                ?>
                <a href="<?php echo esc_url(get_permalink()); ?>">
                    <img src="<?php echo $link_with_image['image']['url']; ?>"/>
                </a>
            <?php
            }else{
                zoa_post_format();
            }
            ?>

            <div class="blog-article-sum">
                <div class="blog-article-header">
                    <header class="entry-header">
                        <?php
                        /* if ( is_single() ) :
                          the_title( '<h1 class="entry-title blog-title">', '</h1>' );
                          else :
                          the_title( '<h2 class="entry-title blog-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                          endif; */
                        $pressinfo = get_field('published_date');
                        $ch_month = $pressinfo['month'];
                        if (isset($pressinfo['custom_month']) && !empty($pressinfo['custom_month']) && !empty($pressinfo['custom_month']['from']) && !empty($pressinfo['custom_month']['to'])) {
                            $ch_month = $pressinfo['custom_month']['from'] . '-' . $pressinfo['custom_month']['to'];
                        }
                        ?>
                        <?php
                        echo '<h2 class="entry-title blog-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . get_the_title() . ' | ' . $ch_month . '&nbsp;' . $pressinfo['year'] . '</a></h2>';
                        ?>
                    </header>
                    <span class="entry-meta blog-header-info posted_date_en">
                        <?php echo get_post_time('F d, Y'); ?>
                    </span>
                </div>

                <div class="entry-summary"><?php zoa_seo_data(); ?></div>

                <div class="entry-content" <?php zoa_schema_markup('post_content'); ?>>
                    <?php
                    if (is_single()) {
                        the_content();
                        zoa_wp_link_pages();
                    } else {
                        the_excerpt();
                    }
                    ?>
                </div>

                <footer class="entry-footer">
                    <?php
                    if (is_single()) :
                        zoa_blog_tags();
                        ?>
                        <div class="posts-nav">
                            <?php
                            $excludeTerm = get_exclude_cat_footer_navigation_press();
                            $prev = get_previous_post(false, $excludeTerm);
                            $next = get_next_post(false, $excludeTerm);
                            $img = get_template_directory_uri() . '/images/thumbnail-default.jpg';

                            if (!empty($prev)) :
                                $prev_img_id = get_post_thumbnail_id($prev->ID);
                                if (!empty($prev_img_id)) {
                                    $prev_img = wp_get_attachment_image_url($prev_img_id, 'thumbnail');
                                    $prev_img_alt = zoa_img_alt($prev_img_id, esc_attr__('Previous post thumbnail', 'zoa'));
                                }
                                ?>
                                <div class="post-nav-item prev-nav">
                                    <?php if (!empty($prev_img)) : ?>
                                        <a href="<?php echo get_permalink($prev->ID); ?>">
                                            <img src="<?php echo esc_url($prev_img); ?>" alt="<?php echo esc_attr($prev_img_alt); ?>">
                                        </a>
                                    <?php endif; ?>

                                    <span class="nav-item-cont">
                                        <span><?php esc_html_e('Previous Post', 'zoa'); ?></span>
                                        <h2 class="entry-title"><a href="<?php echo get_permalink($prev->ID); ?>"><?php echo get_the_title($prev->ID); ?></a></h2>
                                    </span>
                                </div>
                                <?php
                            endif;

                            if (!empty($next)) :
                                $next_img_id = get_post_thumbnail_id($next->ID);
                                if (!empty($next_img_id)) {
                                    $next_img = wp_get_attachment_image_url($next_img_id, 'thumbnail');
                                    $next_img_alt = zoa_img_alt($next_img_id, esc_attr__('Next post thumbnail', 'zoa'));
                                }
                                ?>
                                <div class="post-nav-item next-nav">
                                    <span class="nav-item-cont">
                                        <span><?php esc_html_e('Next Post', 'zoa'); ?></span>
                                        <h2 class="entry-title"><a href="<?php echo get_permalink($next->ID); ?>"><?php echo get_the_title($next->ID); ?></a></h2>
                                    </span>

                                    <?php if (!empty($next_img)) : ?>
                                        <a href="<?php echo get_permalink($next->ID); ?>">
                                            <img src="<?php echo esc_url($next_img); ?>" alt="<?php echo esc_attr($next_img_alt); ?>">
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <a href="<?php the_permalink(); ?>" class="blog-read-more cta"><?php esc_html_e('Read more', 'zoa'); ?><span class="screen-reader-text"><?php esc_html_e('about an interesting article to read', 'zoa'); ?></span></a>
                        <?php endif; ?>
                </footer>
            </div>
        </div>
    </article>
</div>
