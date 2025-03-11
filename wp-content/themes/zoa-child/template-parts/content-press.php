<?php
$c_header = zoa_page_header_slug();
$cat = get_the_category();
$cat = $cat[0];
$pressinfo = get_field('published_date');
$pressinfo_default = $pressinfo;
$magName = get_the_title();
$free_text=get_field('free_text');
/* If WPML is activated if (ICL_LANGUAGE_CODE == "ja") */
if (get_locale() == 'ja') {
    if ($pressinfo['month'] == 'January') {
        $pressinfo['month'] = '1月';
    } else if ($pressinfo['month'] == 'February') {
        $pressinfo['month'] = '2月';
    } else if ($pressinfo['month'] == 'March') {
        $pressinfo['month'] = '3月';
    } else if ($pressinfo['month'] == 'April') {
        $pressinfo['month'] = '4月';
    } else if ($pressinfo['month'] == 'May') {
        $pressinfo['month'] = '5月';
    } else if ($pressinfo['month'] == 'June') {
        $pressinfo['month'] = '6月';
    } else if ($pressinfo['month'] == 'July') {
        $pressinfo['month'] = '7月';
    } else if ($pressinfo['month'] == 'August') {
        $pressinfo['month'] = '8月';
    } else if ($pressinfo['month'] == 'September') {
        $pressinfo['month'] = '9月';
    } else if ($pressinfo['month'] == 'October') {
        $pressinfo['month'] = '10月';
    } else if ($pressinfo['month'] == 'November') {
        $pressinfo['month'] = '11月';
    } else if ($pressinfo['month'] == 'December') {
        $pressinfo['month'] = '12月';
    } else if ($pressinfo['month'] == 'Spring/Summer') {
        $pressinfo['month'] = '春夏';
    } else if ($pressinfo['month'] == 'Autumn/Winter') {
        $pressinfo['month'] = '秋冬';
    }
}
?>
<div class="blog-article press-article single_press" <?php zoa_schema_markup('blog'); ?>>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php zoa_schema_markup('blog_list'); ?>>
        <div itemprop="mainEntityOfPage">
            <div class="blog-article-sum">
                <div class="press-article-header max-width--large gutter-padding--full">
                    <div class="row">
                        <div class="press__header__section col-xs-12">
                            <span class="press__article-hero__cat heading heading--small txt--normal"><?php echo get_cat_name($cat->term_id); ?></span>
                            <h1 class="press__article-hero__title heading heading--main serif"><?php the_title(); ?></h1>
                            <?php
                            $ch_month = $pressinfo['month'];
                            $ch_month_sub_title = $pressinfo['month'];
                            if (isset($pressinfo['custom_month']) && !empty($pressinfo['custom_month']) && !empty($pressinfo['custom_month']['from']) && !empty($pressinfo['custom_month']['to'])) {
                                $ch_month_sub_title = $pressinfo['custom_month']['from'] . '-' . $pressinfo['custom_month']['to'];
                                if (get_locale() == 'ja') {
                                    if ($pressinfo['custom_month']['from'] == 'Spring/Summer') {
                                        $pressinfo['custom_month']['from'] = '春夏';
                                    } else if ($pressinfo['custom_month']['from'] == 'Autumn/Winter') {
                                        $pressinfo['custom_month']['from'] = '秋冬';
                                    } else {
                                        $pressinfo['custom_month']['from'] = date("n", strtotime($pressinfo['custom_month']['from'])) . '月';
                                    }
                                    if ($pressinfo['custom_month']['to'] == 'Spring/Summer') {
                                        $pressinfo['custom_month']['to'] = '春夏';
                                    } else if ($pressinfo['custom_month']['to'] == 'Autumn/Winter') {
                                        $pressinfo['custom_month']['to'] = '秋冬';
                                    } else {
                                        $pressinfo['custom_month']['to'] = date("n", strtotime($pressinfo['custom_month']['to'])) . '月';
                                    }
                                }
                                $ch_month = $pressinfo['custom_month']['from'] . '-' . $pressinfo['custom_month']['to'];
                            }
                            echo '<div class="press__article-hero__credit p3">' . $ch_month_sub_title . '&nbsp;' . $pressinfo_default['year'] . '年</div>';
                            ?>
                            <div class="press__article-hero__descr p3">
                                <?php 
                                $link_with_image=get_field('link_with_image');
                                if(!empty($link_with_image['link'])&&!empty($link_with_image['image']['url'])){
                                    echo '<p>'.$free_text.'</p>';
                                }else{
                                    if(!empty($free_text)){
                                        echo '<p>'.$magName.'の'.$pressinfo['year'].'年'.$ch_month.$free_text.'</p>';
                                    }else{
                                        printf('<p>' . __('Published in %1$s %2$s %3$s', 'zoa') . '</p>', $magName, $ch_month, $pressinfo['year']); 
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                if(!empty($link_with_image['link'])&&!empty($link_with_image['image']['url'])){
                    ?>
                        <div class="row flex-justify-center">
                                <div class="col-md-6 col-xs-12">
                                    <a href="<?php echo $link_with_image['link']; ?>" target="_blank">
                                        <img class="attachment-full size-full" src="<?php echo $link_with_image['image']['url']; ?>"/>
                                    </a>
                                </div>
                        </div>
                <?php
                }else{
                    $images = get_field('press_gallery');
                    $size = 'full';
                    if ($images):
                        ?>
                        <div class="row flex-justify-center">
                            <?php foreach ($images as $image): ?>
                                <div class="col-md-6 col-xs-12">
                                    <a href="<?php echo $image['url']; ?>" data-rel="lightbox">
                                        <?php echo wp_get_attachment_image($image['ID'], $size); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; 
                }
                ?>
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
                        <a href="<?php the_permalink(); ?>" class="blog-read-more"><?php esc_html_e('Read more', 'zoa'); ?><span class="screen-reader-text"><?php esc_html_e('about an interesting article to read', 'zoa'); ?></span></a>
                        <?php endif; ?>
                </footer>
            </div>
        </div>
    </article>
</div>
