<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_ESSENTIAL extends WOOF_EXT
{

    public $type = 'connector';
    public $folder_name = 'tpl_essential';
    public $html_type_dynamic_recount_behavior = 'none';
    public $woof_settings = array();
    public $options = array();

    public function __construct()
    {
        parent::__construct();
        $this->woof_settings = get_option('woof_settings', array());
        $this->init();
    }

    public function get_ext_path()
    {
        return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->woof_settings['custom_extensions_path'] . DIRECTORY_SEPARATOR . 'tpl_essential' . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link()
    {
        return WP_CONTENT_URL . '/' . $this->woof_settings['custom_extensions_path'] . '/tpl_essential/';
    }

    public function init()
    {
        //add_action('wp_head', array($this, 'wp_head'), 999);
        add_action('woof_print_applications_options_' . $this->folder_name, array($this, 'woof_print_applications_options'), 10, 1);

        //self::$includes['js']['woof_' . $this->folder_name] = $this->get_ext_link() . 'js/' . $this->folder_name . '.js';
        //self::$includes['css']['woof_' . $this->folder_name] = $this->get_ext_link() . 'css/' . $this->folder_name . '.css';
        //***
        $this->options = array(
            'tpl_essential_slug' => array(
                'type' => 'textinput',
                'default' => 'woo2',
                'title' => __('Essential grid template slug', 'woocommerce-products-filter'),
                'placeholder' => '',
                'description' => __('Essential grid template slug', 'woocommerce-products-filter')
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
        echo $WOOF->render_html($this->get_ext_path() . 'views/output.php', array(
            'folder_name' => $this->folder_name,
            'options' => $this->options,
            'woof_settings' => $WOOF->settings,
            'the_products' => $products
                )
        );
    }

}

WOOF_EXT::$includes['applications']['tpl_essential'] = new WOOF_EXT_ESSENTIAL();
