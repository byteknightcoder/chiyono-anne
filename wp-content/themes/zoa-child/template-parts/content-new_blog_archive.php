<?php
$c_header = zoa_page_header_slug();
$class_first='';
global $k;

if ($k == 0) {
    $class_first = ' is_first_latest';
    $class_col = 'col-md-12 col-xs-12 ';
    //$summary = '<div class="entry-summary">'. the_excerpt() .'</div>';
} else {
    $class_first = ' is_rest';
    $class_col = 'col-md-4 col-sm-6 col-xs-12 ';
    //$summary = '';
}

// Get the category
$categories = get_the_category(get_the_ID());
$category_name = (!empty($categories) && isset($categories[0])) ? $categories[0]->name : 'Uncategorized'; // Fallback if no category
?>
<div class="<?php echo $class_col; ?> archive_listing blog-article press_articles<?php echo $class_first; ?>" <?php zoa_schema_markup('blog'); ?>>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php zoa_schema_markup('blog_list'); ?>>
        <a href="<?php the_permalink(); ?>" class="over_href"></a>
        <div class="post_row" itemprop="mainEntityOfPage">
            <div class="cover-image_wrap"><?php zoa_post_format(); ?></div>
            <div class="blog-article-sum">
                <div class="blog-article-header">
                    <div class="sub_cat"><span><?php echo esc_html($category_name); ?></span></div>
                    <header class="entry-header">
                        <?php
                        // Get the press information and check if it's set properly
                        $pressinfo = get_field('published_date');
                        $ch_month = isset($pressinfo['month']) ? $pressinfo['month'] : '';

                        // Check for custom month and update if available
                        if (isset($pressinfo['custom_month']['from']) && isset($pressinfo['custom_month']['to'])) {
                            $ch_month = $pressinfo['custom_month']['from'] . '-' . $pressinfo['custom_month']['to'];
                        }

                        // Output the post title
                        echo '<h2 class="entry-title blog-title">' . get_the_title() . '</h2>';
                        ?>
                    </header>
                </div>

                <?php if ($k == 0) { ?>
                <a href="<?php the_permalink(); ?>" class="blog-read-more cta"><?php esc_html_e('Read more', 'zoa'); ?></a>
                <?php } ?>
                
                <span class="entry-meta blog-header-info posted_date_en">
                    <?php echo get_post_time('F d, Y'); ?>
                </span>
            </div>
        </div>
    </article>
</div>
