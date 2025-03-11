<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

global $WOOF;

if (!function_exists('get_dp_tpl_option'))
{

    function get_dp_tpl_option($option_key, $options)
    {
        global $WOOF;
        return $WOOF->get_option($option_key, $options[$option_key]['default']);
    }

}

//***
echo do_shortcode('[displayProduct id="' . get_dp_tpl_option('tpl_dp_id', $options) . '"]');

//wp-content\plugins\displayProduct\displayProduct-shortcodes.php
//#404 -> $query_args = apply_filters('woof_modify_query_args', $query_args);


