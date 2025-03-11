<?php
global $post;
if ($post->ID > 0) {
    $main_slider = get_field('main_slider', $post->ID);
    $link_main = get_field('link_for_main_slider', $post->ID);
    $main_slider_schedule = get_field('main_slider_schedule', $post->ID);
    $current_time = current_time('timestamp');
    if (!empty($main_slider) || !empty($main_slider_schedule)) {
        ?>
        <div class="content-cahome">
            <div class="home-left home-hero">
                <?php
                if (!empty($main_slider_schedule['link']) && !empty($main_slider_schedule['images']) && !empty($main_slider_schedule['start_date']) && !empty($main_slider_schedule['end_date']) && $current_time > strtotime($main_slider_schedule['start_date']) && $current_time < strtotime($main_slider_schedule['end_date'])) {
                    ?>
                    <a class="over_link" href="<?php echo $main_slider_schedule['link']; ?>">
                        <?php
                        foreach ($main_slider_schedule['images'] as $value) {
                            ?>
                            <div class="gl-image">
                                <img src="<?php echo $value['url']; ?>" />
                            </div>
                            <?php
                        }
                        ?>
                    </a>
                    <?php
                } else {
                    ?>
                    <a class="over_link" href="<?php echo $link_main; ?>">
                        <?php
                        foreach ($main_slider as $value) {
                            ?>
                            <div class="gl-image">
                                <img src="<?php echo $value['url']; ?>" />
                            </div>
                            <?php
                        }
                        ?>
                    </a>
                <?php } ?>
            </div>
            <div class="home-right">
                <?php
                $campaign = get_field('campaign', $post->ID);
                $campaign_schedule = get_field('campaign_schedule', $post->ID);
                if (!empty($campaign) || !empty($campaign_schedule)) {
                    if (!empty($campaign_schedule['link']) && !empty($campaign_schedule['image']['url']) && !empty($campaign_schedule['start_date']) && !empty($campaign_schedule['end_date']) && $current_time > strtotime($campaign_schedule['start_date']) && $current_time < strtotime($campaign_schedule['end_date'])) {
                        ?>
                        <div class="over_t over_linear hr-bnr hr-campaign">
                            <a class="over_link" href="<?php echo $campaign_schedule['link']; ?>"></a>
                            <img src="<?php echo $campaign_schedule['image']['url']; ?>" />
                        </div>
                        <?php
                    } else {
                        ?>

                        <div class="over_t over_linear hr-bnr hr-campaign">
                            <a class="over_link" href="<?php echo $campaign['link']; ?>"></a>
                            <img src="<?php echo $campaign['image']['url']; ?>" />
                            <div class="over_elem over_elem__center">
                                <div class="hr-title"><?php echo $campaign['title']; ?></div>
                                <div class="hr-subject"><?php echo $campaign['sub_title']; ?></div>
                            </div>
                        </div>

                        <?php
                    }
                }
                $tgyb = get_field('tbyb', $post->ID);
                if (!empty($tgyb)) {
                    ?>

                    <div class="over_t over_linear hr-bnr hr-tbyb">
                        <a class="over_link" href="<?php echo $tgyb['link']; ?>"></a>
                        <video src="<?php echo $tgyb['mp4']['url']; ?>" autoplay playsinline loop muted="muted" controlslist="nodownload"></video>
                        <div class="over_elem over_elem__btmrt hr-caption"><?php echo $tgyb['caption']; ?></div>
                    </div>

                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
}
