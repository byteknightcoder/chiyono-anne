<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_TPL_1 extends WOOF_EXT
{

    public $type = 'application';
    public $folder_name = 'tpl_1';
    public $html_type_dynamic_recount_behavior = 'none';
    public $woof_settings = array();
    

    public function __construct()
    {
        parent::__construct();
        $this->woof_settings = get_option('woof_settings', array());
        $this->init();
    }

    public function get_ext_path()
    {
        return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->woof_settings['custom_extensions_path'] . DIRECTORY_SEPARATOR . 'tpl_1' . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link()
    {
        return WP_CONTENT_URL . '/' . $this->woof_settings['custom_extensions_path'] . '/tpl_1/';
    }

    public function init()
    {
        add_action('wp_head', array($this, 'wp_head'), 999);
        add_action('woof_print_applications_options_' . $this->folder_name, array($this, 'woof_print_applications_options'), 10, 1);

        self::$includes['js']['woof_' . $this->folder_name] = $this->get_ext_link() . 'js/' . $this->folder_name . '.js';
        self::$includes['css']['woof_' . $this->folder_name] = $this->get_ext_link() . 'css/' . $this->folder_name . '.css';
        //***
        $this->options = array(
            'tpl_1_header_bar_bg_color' => array(
                'type' => 'color',
                'default' => '#E5937F',
                'title' => __('Header background color', 'woocommerce-products-filter'),
                'placeholder' => '',
                'description' => __('Select template header bar background color', 'woocommerce-products-filter')
            ),
            'tpl_1_header_bar_font_color' => array(
                'type' => 'color',
                'default' => '#fff',
                'title' => __('Header font color', 'woocommerce-products-filter'),
                'placeholder' => '',
                'description' => __('Select template header bar font color', 'woocommerce-products-filter')
            ),
            'tpl_1_img_width' => array(
                'type' => 'textinput',
                'default' => 200,
                'title' => __('Image width', 'woocommerce-products-filter'),
                'placeholder' => __('enter integer number', 'woocommerce-products-filter'),
                'description' => __('Product image width', 'woocommerce-products-filter')
            ),
            'tpl_1_img_height' => array(
                'type' => 'textinput',
                'default' => 180,
                'title' => __('Image height', 'woocommerce-products-filter'),
                'placeholder' => __('enter integer number', 'woocommerce-products-filter'),
                'description' => __('Product image height', 'woocommerce-products-filter')
            ),
            'tpl_1_taxonomies' => array(
                'type' => 'textinput',
                'default' => '',
                'title' => __('Show post taxonomies', 'woocommerce-products-filter'),
                'placeholder' => 'pa_size,pa_color',
                'description' => __('Enter taxonomies slugs using comma to show selected terms of them for each product', 'woocommerce-products-filter')
            ),
            'tpl_1_product_title_tag' => array(
                'type' => 'select',
                'select_options' => array(
                    'h2' => 'h2',
                    'h3' => 'h3',
                    'h4' => 'h4',
                    'h5' => 'h5',
                    'h6' => 'h6'
                ),
                'default' => 'h4',
                'title' => __('Product title tag', 'woocommerce-products-filter'),
                'placeholder' => '',
                'description' => __('Select product title tag', 'woocommerce-products-filter')
            )
        );
    }

    public function wp_head()
    {
        global $WOOF;
        ?>      
        <script type="text/javascript"></script>
        <?php

    }

    //app options page hook
    public function woof_print_applications_options()
    {
        global $WOOF;
        echo $WOOF->render_html($this->get_ext_path() . 'views/options.php', array(
            'folder_name' => $this->folder_name,
            'options' => $this->options
                )
        );
    }

    public function draw($products)
    {
        global $WOOF;
        include_once WOOF_PATH . 'lib' . DIRECTORY_SEPARATOR . 'aq_resizer.php';
        echo $WOOF->render_html($this->get_ext_path() . 'views/output.php', array(
            'folder_name' => $this->folder_name,
            'options' => $this->options,
            'woof_settings' => $WOOF->settings,
            'the_products' => $products
                )
        );
    }

}

WOOF_EXT::$includes['applications']['tpl_1'] = new WOOF_EXT_TPL_1();
