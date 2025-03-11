<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
global $WOOF;
if (!function_exists('woof_essgrid_get_posts'))
{

    function woof_essgrid_get_posts($query, $grid_id)
    {
        if (isset($_REQUEST['woof_wp_query_args']))
        {
            $query = $_REQUEST['woof_wp_query_args'];
        }

        return $query;
    }

}
if (!function_exists('get_tpl_option'))
{

    function get_tpl_option($option_key, $options)
    {
        global $WOOF;
        return $WOOF->get_option($option_key, $options[$option_key]['default']);
    }

}
?>
<li class="woof_tpl_essential">
    <?php
    add_filter('essgrid_query_caching', '__return_false', 10, 2);
    add_filter('essgrid_get_posts', 'woof_essgrid_get_posts', 10, 2);
    echo do_shortcode('[ess_grid alias="' . get_tpl_option('tpl_essential_slug', $options) . '"]');
    ?>
</li>
