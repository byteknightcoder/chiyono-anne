<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

global $WOOF;
if (!function_exists('woof_go_portfolio_query_filter'))
{

    function woof_go_portfolio_query_filter($query_args, $portfolio_id)
    {
        if (isset($_REQUEST['woof_wp_query_args']))
        {
            $query_args = $_REQUEST['woof_wp_query_args'];
        }

        return $query_args;
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

//***
if ($WOOF->is_isset_in_request_data($WOOF->get_swoof_search_slug()))
{
    add_filter('go_portfolio_query_filter', 'woof_go_portfolio_query_filter', 10, 2);
}
echo do_shortcode('[go_portfolio id="' . get_tpl_option('tpl_folio_id', $options) . '"]');


