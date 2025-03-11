<?php
$page = get_page_by_path('bespoke');
$svgPath = get_stylesheet_directory_uri() . '/fonts/';
$svgId = 'Bespoke';
$svgTitle = '<span class="svg-wrapper svg_' . $svgId . '"><svg class="icoca icoca-' . $svgId . '"><use xlink:href="' . $svgPath . 'symbol-icoca.svg#icoca-' . $svgId . '"></use></svg></span>';

if ($page->ID > 0) {
    $data = get_field('display_home', $page->ID);
    if (!empty($data)) {
?>
        <div class="container-fluid">
            <div class="content-fit content-bespoke">
                <div class="cb-left">
                    <?php
                    if (!empty($data['mp4'])) {
                    ?>
                        <video src="<?php echo $data['mp4']['url']; ?>" autoplay playsinline loop muted="muted" controlslist="nodownload"></video>
                        <?php
                    } else {
                        if (!empty($data['image'])) {
                        ?>
                            <img class="cb-img" src="<?php echo $data['image']['url']; ?>" />
                    <?php
                        }
                    }
                    ?>
                </div>
                <div class="cb-right">
                    <h3 class="sec-title ff_lovescript mid-title fade-up fade-ani"><?php echo $svgTitle; ?></h3>
                    <div class="cb-content intro_text pg__light"><?php echo $data['excerpt_content']; ?></div>
                    <div class="cb-appointment">
                        <a class="btn btn__bs_lg btn_rvnow btn_rvnow_tbig" href="<?php echo home_url('reservation-form'); ?>"><?php echo __('REQUEST AN APPOINTMENT', 'zoa'); ?></a>
                    </div>
                    <div class="cb-learnmore">
                        <a class="link__mini link__thinarw" href="<?php echo home_url('bespoke'); ?>"><?php echo __('LEARN MORE', 'zoa'); ?></a>
                    </div>
                </div>
            </div>
        </div>

<?php
    }
}
