<?php
//<!--CAMI SIZE-->
$chart_id = 2023575;
if (class_exists('productsize_chart_Public')) :
    $chart_md = new productsize_chart_Public('productsize-chart-for-woocommerce', $chart_id);
    $assets = $chart_md->productsize_chart_assets($chart_id);
?>
    <div class="remodal size_info_modal" data-remodal-id="cami_size_info" id="cami_size_info" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
        <button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
        <div class="remodal_wraper">
            <div class="pop-up tooltip-pop pop-size">
                <div class="pop-head">
                    <h2 class="pop-title">
                        <i class="oecicon oecicon-alert-circle-que"></i><?php esc_html_e("About Chiyono Anne's Size", 'zoa'); ?>
                    </h2>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="prod-detail-content">
                        <?php
                            $title = !empty($title_wrapper) ? $title_wrapper : 'h2';
                            $enable_additional_chart = 1;

                            $post_data = get_post($chart_id);

                            $pimg = get_post_meta($post_data->ID, 'primary-chart-image', true);
                            if ($pimg) {
                                $position = get_post_meta($post_data->ID, 'primary-image-position', true);
                                $pimg = wp_get_attachment_image_src($pimg, 'full');
                                echo '<div class="chart-1-image image-' . esc_attr($position) . '">
                                <img src="' . esc_url($pimg[0]) . '" alt="' . esc_attr($post_data->post_title) . '"
                                title="' . esc_attr($title) . '" />
                                </div>';
                            }

                            if ($post_data->post_content) {
                                $content = apply_filters('the_content', $post_data->post_content);
                                echo $content;
                            }

                            if (!empty($assets['chart-table'])) {
                                $chart_md->productsize_chart_display_table($assets['chart-table']);
                            }

                            // chart 1 content goes here 
                            if ($enable_additional_chart == 1):

                                $title2 = !empty($title_additional) ? $title_additional : 'h3';

                                if (!empty($assets['chart-1'])):
                                    $title_c1 = $assets['chart-1'][0]['chart-title'];
                                    $image_c1 = $assets['chart-1'][0]['chart-image'];
                                    $content_c1 = $assets['chart-1'][0]['chart-content'];
                                    $position_c1 = (!empty($assets['chart-1'][0]['image-position']) && $assets['chart-1'][0]['image-position'] == 'left') ? 'image-left' : 'image-right';
                                    $chart_c1 = $assets['chart-1'][0]['chart-table'];

                                    echo '<div class="add-chart-1">';
                                    printf('<%1$s id="modal1Title">%2$s</%1$s>', $title2, esc_html($title_c1));

                                    if (!empty($image_c1)) {
                                        $img = wp_get_attachment_image_src($image_c1, 'full');
                                        echo '<div class="chart-1-image ' . esc_attr($position_c1) . '">
                                        <img src="' . esc_url($img[0]) . '" alt="' . esc_attr($title_c1) . '" 
                                        title="' . esc_attr($title_c1) . '" />
                                        </div>';
                                    }

                                    if ($content_c1) {
                                        echo apply_filters('the_content', $content_c1);
                                    }

                                    if ($chart_c1) {
                                        $chart_md->productsize_chart_display_table($chart_c1);
                                    }

                                    echo '</div>
                                    <div class="clear"></div>';

                                endif;

                                if (!empty($assets['chart-2'])):

                                    $title_c2 = $assets['chart-2'][0]['chart-title-1'];
                                    $image_c2 = $assets['chart-2'][0]['chart-image-1'];
                                    $content_c2 = $assets['chart-2'][0]['chart-content-1'];
                                    $position_c2 = (!empty($assets['chart-2'][0]['image-position-1']) && $assets['chart-2'][0]['image-position-1'] == 'left') ? 'image-left' : 'image-right';
                                    $chart_c2 = $assets['chart-2'][0]['chart-table-1'];

                                    echo '<div class="add-chart-2">';
                                    if (!empty($image_c2)) {
                                        $img = wp_get_attachment_image_src($image_c2, 'full');
                                        echo '<div class="chart-2-image ' . esc_attr($position_c2) . '">
                                        <img src="' . esc_url($img[0]) . '" alt="' . esc_attr($title_c2) . '" 
                                        title="' . esc_attr($title_c2) . '" />
                                        </div>';
                                    }

                                    if ($content_c2) {
                                        echo apply_filters('the_content', $content_c2);
                                    }

                                    if ($chart_c2) {
                                        $chart_md->productsize_chart_display_table($chart_c2);
                                    }

                                    echo '</div>';

                                endif;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
                <!--/.row-->
            </div>
        </div>
        <br>
    </div>
    <?php
    endif;
//End
//<!--SHORTS SIZE-->
$chart_id = 2023574;
if (class_exists('productsize_chart_Public')) :
    $chart_md = new productsize_chart_Public('productsize-chart-for-woocommerce', $chart_id);
    $assets = $chart_md->productsize_chart_assets($chart_id);
    ?>
    <div class="remodal size_info_modal" data-remodal-id="shorts_size_info" id="shorts_size_info" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
        <button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
        <div class="remodal_wraper">
            <div class="pop-up tooltip-pop pop-size">
                <div class="pop-head">
                    <h2 class="pop-title">
                        <i class="oecicon oecicon-alert-circle-que"></i><?php esc_html_e("About Chiyono Anne's Size", 'zoa'); ?>
                    </h2>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="prod-detail-content">
                        <?php
                            $title = !empty($title_wrapper) ? $title_wrapper : 'h2';
                            $enable_additional_chart = 1;

                            $post_data = get_post($chart_id);

                            $pimg = get_post_meta($post_data->ID, 'primary-chart-image', true);
                            if ($pimg) {
                                $position = get_post_meta($post_data->ID, 'primary-image-position', true);
                                $pimg = wp_get_attachment_image_src($pimg, 'full');

                                echo '<div class="chart-1-image image-' . esc_attr($position) . '">
                                <img src="' . esc_url($pimg[0]) . '" alt="' . esc_attr($post_data->post_title) . '"
                                title="' . esc_attr($title) . '" />
                                </div>';
                            }

                            if ($post_data->post_content) {
                                $content = apply_filters('the_content', $post_data->post_content);
                                echo $content;
                            }

                            if (!empty($assets['chart-table'])) {
                                $chart_md->productsize_chart_display_table($assets['chart-table']);
                            }

                            if ($enable_additional_chart == 1):

                                $title2 = !empty($title_additional) ? $title_additional : 'h3';

                                if (!empty($assets['chart-1'])):
                                    $title_c1 = $assets['chart-1'][0]['chart-title'];
                                    $image_c1 = $assets['chart-1'][0]['chart-image'];
                                    $content_c1 = $assets['chart-1'][0]['chart-content'];
                                    $position_c1 = (!empty($assets['chart-1'][0]['image-position']) && $assets['chart-1'][0]['image-position'] == 'left') ? 'image-left' : 'image-right';
                                    $chart_c1 = $assets['chart-1'][0]['chart-table'];

                                    echo '<div class="add-chart-1">';
                                    printf('<%1$s id="modal1Title">%2$s</%1$s>', $title2, esc_html($title_c1));

                                    if (!empty($image_c1)) {
                                        $img = wp_get_attachment_image_src($image_c1, 'full');
                                        echo '<div class="chart-1-image ' . esc_attr($position_c1) . '">
                                        <img src="' . esc_url($img[0]) . '" alt="' . esc_attr($title_c1) . '" 
                                        title="' . esc_attr($title_c1) . '" />
                                        </div>';
                                    }

                                    if ($content_c1) {
                                        echo apply_filters('the_content', $content_c1);
                                    }

                                    if ($chart_c1) {
                                        $chart_md->productsize_chart_display_table($chart_c1);
                                    }

                                    echo '</div>
                                    <div class="clear"></div>';

                                endif;

                                if (!empty($assets['chart-2'])):

                                    $title_c2 = $assets['chart-2'][0]['chart-title-1'];
                                    $image_c2 = $assets['chart-2'][0]['chart-image-1'];
                                    $content_c2 = $assets['chart-2'][0]['chart-content-1'];
                                    $position_c2 = (!empty($assets['chart-2'][0]['image-position-1']) && $assets['chart-2'][0]['image-position-1'] == 'left') ? 'image-left' : 'image-right';
                                    $chart_c2 = $assets['chart-2'][0]['chart-table-1'];

                                    echo '<div class="add-chart-2">';
                                    if (!empty($image_c2)) {
                                        $img = wp_get_attachment_image_src($image_c2, 'full');
                                        echo '<div class="chart-2-image ' . esc_attr($position_c2) . '">
                                        <img src="' . esc_url($img[0]) . '" alt="' . esc_attr($title_c2) . '" 
                                        title="' . esc_attr($title_c2) . '" />
                                        </div>';
                                    }

                                    if ($content_c2) {
                                        echo apply_filters('the_content', $content_c2);
                                    }

                                    if ($chart_c2) {
                                        $chart_md->productsize_chart_display_table($chart_c2);
                                    }
                                    echo '</div>';
                                endif;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
                <!--/.row-->
            </div>
        </div>
        <br>
    </div>
    <?php
    endif;
