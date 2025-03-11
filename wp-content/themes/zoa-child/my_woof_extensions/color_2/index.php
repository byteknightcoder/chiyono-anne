<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//26-10-2016
final class WOOF_EXT_COLOR_2 extends WOOF_EXT
{
    public $type = 'html_type';
    public $html_type = 'color'; //your custom key here
    public $html_type_dynamic_recount_behavior = 'multi';
    //public $woof_settings = array();
    public $color_taxonomies = array();
    public $current_taxonomy = '';

    public function __construct()
    {
        parent::__construct();
        //$this->woof_settings = get_option('woof_settings', array());
        $this->init();
    }

    public function get_ext_path()
    {
        return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->woof_settings['custom_extensions_path'] . DIRECTORY_SEPARATOR . 'color_2' . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link()
    {
        return WP_CONTENT_URL . '/' . $this->woof_settings['custom_extensions_path'] . '/color_2/';
    }

    public function init()
    {
        add_filter('woof_add_html_types', array($this, 'woof_add_html_types'));
        add_action('wp_head', array($this, 'wp_head'), 999);
        //add_action('woof_print_tax_additional_options_color', array($this, 'print_additional_options'), 10, 1);
        //add_action('woof_print_design_additional_options', array($this, 'woof_print_design_additional_options'), 10, 1);
        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/html_types/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/html_types/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_colors';

        add_action('admin_head', array($this, 'admin_head'), 50);
	
	//***
	
	$this->taxonomy_type_additional_options = array(
	    'show_tooltip' => array(
		'title' => __('Show tooltip', 'woocommerce-products-filter'),
		'tip' => __('Show tooltip on hover', 'woocommerce-products-filter'),
		'type' => 'select',
		'options' => array(
		    1 => __('Yes', 'woocommerce-products-filter'),
		    0 => __('No', 'woocommerce-products-filter')		    
		)
	    )
	);

        //***
        foreach ($this->woof_settings['tax_type'] as $tax_key => $type)
        {
            if ($type == 'color')
            {
                $this->color_taxonomies[] = $tax_key;
            }
        }

        if (isset($_GET['taxonomy']) AND in_array($_GET['taxonomy'], $this->color_taxonomies) AND ( isset($_GET['post_type']) AND $_GET['post_type'] == 'product'))
        {
            $this->current_taxonomy = $_GET['taxonomy'];
            add_action($this->current_taxonomy . "_edit_form_fields", array($this, "edit_form_fields"), 10, 2);

            add_filter('manage_edit-' . $this->current_taxonomy . '_columns', array($this, 'draw_tax_columns'));
            add_filter('manage_' . $this->current_taxonomy . '_custom_column', array($this, 'draw_tax_columns_data'), 5, 3);
        }
        add_action('edit_term', array($this, 'term_type_update'), 10, 3);
    }

    public function admin_head()
    {

        if (isset($_GET['taxonomy']) AND in_array($_GET['taxonomy'], $this->color_taxonomies) AND ( isset($_GET['post_type']) AND $_GET['post_type'] == 'product'))
        {
            wp_enqueue_script('media-upload');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('thickbox');

            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');

            wp_enqueue_style('woof_color', $this->get_ext_link() . 'css/term_options.css');
            wp_enqueue_script('woof_color', $this->get_ext_link() . 'js/html_types/term_options.js', array('jquery'));
        }

        if (isset($_GET['tab']) AND $_GET['tab'] == 'woof')
        {
            wp_enqueue_style('woof_color', $this->get_ext_link() . 'css/admin.css');
            wp_enqueue_script('woof_color', $this->get_ext_link() . 'js/html_types/plugin_options.js', array('jquery'));
        }
    }

    public function woof_add_html_types($types)
    {
        $types[$this->html_type] = __('Color', 'woocommerce-products-filter');
        return $types;
    }

    public function wp_head()
    {
        global $WOOF;
        ?>
        <style type="text/css">
        <?php
        if (isset($WOOF->settings['checked_color_img']))
        {
            if (!empty($WOOF->settings['checked_color_img']))
            {
                ?>
                    .checked .woof_color_checked{
                        background: url(<?php echo $WOOF->settings['checked_color_img'] ?>) !important;
                    }             
                <?php
            }
        }
        ?>
        </style>
        <?php
    }

    public function edit_form_fields($term, $taxonomy)
    {
        $term_id = $term->term_id;
        //$woof_term_color = get_option("_woof_term_color_{$taxonomy}_{$term_id}");
        //$woof_term_image = get_option("_woof_term_image_{$taxonomy}_{$term_id}");
        $woof_term_color = $this->get_term_color($term_id);
        $woof_term_image = $this->get_term_image($term_id);
        ?>
        <tr class="form-field form-required">
            <th scope="row" valign="top"><label for="woof_term_image"><?php _e('WOOF color settings', 'woocommerce-products-filter') ?></label></th>
            <td>
                <div class="postbox-container woof-postbox-container" style="width: 99%; line-height:normal;">

                    <div class="postbox">

                        <div class="inside" style="padding: 2px;">
                            <input type="text" name="woof_term_color" placeholder="<?php _e('select color', 'woocommerce-products-filter') ?>" value="<?php echo $woof_term_color ?>" style="width:90%;" class="woof-color-picker text" />
                            <p class="description"><?php _e('Select color which will be displayed in the search form', 'woocommerce-products-filter') ?></p>
                            <hr />
                            <input type="text" name="woof_term_image" placeholder="<?php _e('background image url 25x25', 'woocommerce-products-filter') ?>" value="<?php echo $woof_term_image ?>" class="text" style="width:90%;"><a href="#" class="button woof_term_image_button_upload"><?php _e('Upload', 'woocommerce-products-filter') ?></a>
                            <p class="description"><?php _e('Select image which will be displayed in the search form', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                    <div id="woof_buffer" style="display: none;"></div>
                </div>
            </td>
        </tr>

        <?php
    }

    // These hooks are called after adding and editing to save $_POST['tag-term']
    public function term_type_update($term_id, $tt_id, $taxonomy)
    {
        if (isset($_POST['woof_term_color']) AND in_array($taxonomy, $this->color_taxonomies))
        {
            update_option("_woof_term_color_{$taxonomy}_{$term_id}", $_POST['woof_term_color']);
            update_option("_woof_term_image_{$taxonomy}_{$term_id}", $_POST['woof_term_image']);
        }
    }

    public function draw_tax_columns($columns)
    {
        return array_merge($columns, array(
            'woof_color' => __('WOOF Color', 'woocommerce-products-filter'),
        ));
    }

    public function draw_tax_columns_data($value = '', $column_name = '', $term_id = 0)
    {
        switch ($column_name)
        {
            case 'woof_color':
                $woof_term_image = $this->get_term_image($term_id);
                if (!empty($woof_term_image) AND $woof_term_image != 'none')
                {
                    ?>
                    <div class="woof_term_color_demo" style="background: url(<?php echo $woof_term_image ?>);"></div>
                    <?php
                } else
                {
                    $woof_term_color = $this->get_term_color($term_id);
                    ?>
                    <div class="woof_term_color_demo" style="background: <?php echo $woof_term_color ?>;"></div>
                    <?php
                }
                break;
        }
    }

    public function get_term_color($term_id, $taxonomy = '')
    {
        if (empty($taxonomy))
        {
            $taxonomy = $this->current_taxonomy;
        }
        return get_option("_woof_term_color_{$taxonomy}_{$term_id}", '#000000');
    }

    public function get_term_image($term_id, $taxonomy = '')
    {
        if (empty($taxonomy))
        {
            $taxonomy = $this->current_taxonomy;
        }
        return get_option("_woof_term_image_{$taxonomy}_{$term_id}", 'none');
    }

}

WOOF_EXT::$includes['taxonomy_type_objects']['color'] = new WOOF_EXT_COLOR_2();
