<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
global $WOOF;
if (!function_exists('get_tpl_option'))
{

    function get_tpl_option($option_key, $options)
    {
        global $WOOF;
        return $WOOF->get_option($option_key, $options[$option_key]['default']);
    }

}
?>
<li class="woof_tpl_1">
    <table id="racetimes">
        <tr id="firstrow" style="background-color: <?php echo get_tpl_option('tpl_1_header_bar_bg_color', $options) ?>; color: <?php echo get_tpl_option('tpl_1_header_bar_font_color', $options) ?>;">
            <td><?php _e('Title', 'woocommerce-products-filter') ?></td>
            <td><?php _e('Price', 'woocommerce-products-filter') ?></td>
            <td><?php _e('Description', 'woocommerce-products-filter') ?></td>
            <td><?php _e('View', 'woocommerce-products-filter') ?></td>
        </tr>
        <?php
        while ($the_products->have_posts()) : $the_products->the_post();
            global $post;
            ?>
            <tr>
                <td style="width: 30%;">
                    <div style="padding: 15px;">
                        <a href="<?php the_permalink() ?>" target="_blank" class="thumbnail alignleft"><img src="<?php
                            if (has_post_thumbnail($post->ID))
                            {
                                $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                                echo woof_aq_resize($img_src[0], get_tpl_option('tpl_1_img_width', $options), get_tpl_option('tpl_1_img_height', $options), true);
                            } else
                            {
                                echo WOOF_LINK . 'img/not-found.jpg';
                            }
                            ?>" alt="<?php the_title_product_chiyono() ?>" /></a>
                    </div>
                    <div style="clear: both;"></div>
                </td>
                <td style="width: 5%;"><?php
                    $product = new WC_Product(get_the_ID());
                    echo $product->get_price_html();
                    ?></td>
                <td style="width: 60%; text-align: left;">
                    <<?php echo get_tpl_option('tpl_1_product_title_tag', $options) ?>><a href="<?php the_permalink() ?>" target="_blank"><?php the_title_product_chiyono() ?></a></<?php echo get_tpl_option('tpl_1_product_title_tag', $options) ?>>
                    <div>
                        <?php echo $post->post_excerpt; ?>
                    </div>
                    <div style="clear: both;"></div>
                    <br />
                    <?php
                    $tpl_1_taxonomies = get_tpl_option('tpl_1_taxonomies', $options);
                    if (!empty($tpl_1_taxonomies))
                    {
                        $tpl_1_taxonomies = explode(',', $tpl_1_taxonomies);
                        if (!empty($tpl_1_taxonomies) AND is_array($tpl_1_taxonomies))
                        {
                            foreach ($tpl_1_taxonomies as $taxonomy)
                            {
                                $terms = get_the_terms($post->ID, trim($taxonomy));
                                $tmp = array();
                                if (!empty($terms))
                                {
                                    foreach ($terms as $k => $term)
                                    {
                                        $tmp[] = $term->name;
                                    }
                                }
                                if (!empty($tmp))
                                {
                                    $taxonomy_details = get_taxonomy(trim($taxonomy));
                                    echo '<b>' . $taxonomy_details->labels->name . '</b>: ' . implode(',', $tmp);
                                }
                                echo '<br />';
                            }
                        }
                    }
                    ?>

                    <p style="text-align: center;">
                        <?php
                        if (class_exists('InpostGallery'))
                        {
                            echo do_shortcode('[inpost_fancy thumb_width="50" thumb_height="50" post_id="' . $post->ID . '" thumb_margin_left="5" thumb_margin_bottom="5" thumb_border_radius="200" thumb_shadow="0 1px 4px rgba(0, 0, 0, 0.2)" id="" random="0" group="0" border="" show_in_popup="0" album_cover="" album_cover_width="200" album_cover_height="200" popup_width="800" popup_max_height="600" popup_title="Gallery" type="fancy" sc_id="sc1413390928195"]');
                        }
                        ?>
                    </p>
                </td>
                <td style="width: 5%;"><a href="<?php the_permalink() ?>" target="_blank"><?php _e('View', 'woocommerce-products-filter') ?></a></td>
            </tr>
        <?php endwhile; // end of the loop. ?>
    </table>
</li>
