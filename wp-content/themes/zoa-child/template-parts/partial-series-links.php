<?php
$svgPath = get_stylesheet_directory_uri() . '/fonts/';
$svgId = 'Collection';
$svgTitle = '<span class="svg-wrapper svg_' . $svgId . '"><svg class="icoca icoca-' . $svgId . '"><use xlink:href="' . $svgPath . 'symbol-icoca.svg#icoca-' . $svgId . '"></use></svg></span>';
$args = array(
    'hide_empty' => false, // also retrieve terms which are not used yet
    'limit' => -1,
    'meta_key' => 'custom_order',
    'orderby' => 'custom_order',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'show_on_home_page',
            'value' => '1',
            'compare' => '=='
        )
    ),
);
$series = get_terms('series', $args);
if (!empty($series)) {
    ?>
    <div class="container-fluid">
        <div class="content-fit series-link">
            <div class="sl-left">
                <h3 class="sec-title ff_lovescript mid-title fade-down fade-ani"><?php echo $svgTitle; ?></h3>
                <div id="scrollnav-horizontal" class="series-items-nav">
                    <div class="series-items">
                        <?php
                        $k = 0;
                        foreach ($series as $item) {
                            if (is_allow_show_home($item->term_id)) {
                                ?>
                                <div class="sl-item">
                                    <a index="<?php echo $k; ?>" class="ff_chapa sl-link <?php echo $k == 0 ? 'active' : ''; ?>" href="javascript:;">
                                        <?php echo $item->name; ?>
                                    </a>
                                </div>
                                <?php
                                $k++;
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="sl-view-all show-lm-desktop">
                    <a class="link__mini link__thinarw" href="<?php echo home_url('shop-all') ?>"><?php echo __('VIEW ALL', 'zoa'); ?></a>
                </div>
            </div>
            <div class="sl-right">
                <div class="series-items-images">
                    <?php
                    foreach ($series as $item) {
                        if (is_allow_show_home($item->term_id)) {
                            // get the thumbnail id using the queried category term_id
                            $thumbnail_id = get_term_meta($item->term_id, 'img', true);

                            // get the image URL
                            $image = wp_get_attachment_image_url($thumbnail_id, 'large');
                            ?>
                            <div class="sl-item-image pos__rl">
                                <span class="sl-item-sized"><img class="sl-img" src="<?php echo $image; ?>" /></span><a class="sl-item-link link__overfill" href="<?php echo home_url('/shop-all/?swoof=1&series=' . $item->slug . '&paged=1') ?>"></a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="sl-view-all show-lm-mobile">
                    <a class="link__mini link__thinarw" href="<?php echo home_url('shop-all') ?>"><?php echo __('VIEW ALL', 'zoa'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
}
