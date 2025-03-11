<?php
$page_featured = get_page_by_path('featured');
if ($page_featured->ID > 0) {
    $feature_images = get_field('feature_images', $page_featured->ID);
    $link=get_field('feature_link', $page_featured->ID);
    $svgPath = get_stylesheet_directory_uri() . '/fonts/';
    $svgId = 'Featured';
    $svgTitle = '<span class="svg-wrapper svg_' . $svgId . '"><svg class="icoca icoca-' . $svgId . '"><use xlink:href="' . $svgPath . 'symbol-icoca.svg#icoca-' . $svgId . '"></use></svg></span>';

?>
    <div class="container-md">
        <div class="content-featured pos__rl">
            <?php echo '<h3 class="sec-title ff_lovescript mid-title fade-right fade-ani">' . $svgTitle . '</h3>'; ?>
            <?php
            if (!empty($feature_images)) {
            ?>
                <div class="cf-images">
                    <?php
                    foreach ($feature_images as $value) {
                    ?>
                        <div class="cf-img">
                            <div class="cf-img-inner">
                                <img src="<?php echo $value['url']; ?>" />
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>
            <div class="cf-subtitle title-catchy ff_chapa fade-left fade-ani">
                <?php echo get_the_subtitle($page_featured->ID); ?>
            </div>
            <div class="cf-content ff_proxi ls_02 fade-right fade-ani">
                <?php echo $page_featured->post_content; ?>
            </div>
            <a href="<?php echo $link; ?>" class="link__overfill"></a>
        </div>
    </div>
<?php
}
