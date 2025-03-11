<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//26-10-2016
final class WOOF_EXT_IMAGE_2 extends WOOF_EXT
{

    public $type = 'html_type';
    public $html_type = 'image'; //your custom key here
    public $html_type_dynamic_recount_behavior = 'multi';
    //public $woof_settings = array();
    public $image_taxonomies = array();
    public $current_taxonomy = '';
    public $folder_name = 'image_2';

    public function __construct()
    {
        parent::__construct();
        //$this->woof_settings = get_option('woof_settings', array());
        $this->init();
    }

    public function get_ext_path()
    {
        return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->woof_settings['custom_extensions_path'] . DIRECTORY_SEPARATOR . $this->folder_name . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link()
    {
        return WP_CONTENT_URL . '/' . $this->woof_settings['custom_extensions_path'] . '/' . $this->folder_name . '/';
    }

    public function init()
    {
        add_filter('woof_add_html_types', array($this, 'woof_add_html_types'));
        //add_action('wp_head', array($this, 'wp_head'), 999);
        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/html_types/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/html_types/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_image';

        add_action('admin_head', array($this, 'admin_head'), 50);

        //***
        foreach ($this->woof_settings['tax_type'] as $tax_key => $type)
        {
            if ($type == 'image')
            {
                $this->image_taxonomies[] = $tax_key;
            }
        }

        if (isset($_GET['taxonomy']) AND in_array($_GET['taxonomy'], $this->image_taxonomies) AND ( isset($_GET['post_type']) AND $_GET['post_type'] == 'product'))
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

        if (isset($_GET['taxonomy']) AND in_array($_GET['taxonomy'], $this->image_taxonomies) AND ( isset($_GET['post_type']) AND $_GET['post_type'] == 'product'))
        {
            wp_enqueue_script('media-upload');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('thickbox');


            wp_enqueue_style('woof_image', $this->get_ext_link() . 'css/term_options.css');
            wp_enqueue_script('woof_image', $this->get_ext_link() . 'js/html_types/term_options.js', array('jquery'));
        }
    }

    public function woof_add_html_types($types)
    {
        $types[$this->html_type] = __('Image', 'woocommerce-products-filter');
        return $types;
    }

    public function edit_form_fields($term, $taxonomy)
    {
        $term_id = $term->term_id;
        $woof_term_style = $this->get_term_style($term_id);
        $woof_term_image = $this->get_term_image($term_id);
        ?>
        <tr class="form-field form-required">
            <th scope="row" valign="top"><label for="woof_term_image"><?php _e('WOOF image settings', 'woocommerce-products-filter') ?></label></th>
            <td>
                <div class="postbox-container woof-postbox-container" style="width: 99%; line-height:normal;">

                    <div class="postbox" style="padding: 7px;">

                        <div class="inside" style="padding: 2px;">                          
                            <input type="text" name="woof_term_image" placeholder="<?php _e('img link', 'woocommerce-products-filter') ?>" value="<?php echo $woof_term_image ?>" class="text" style="width:90%;"><a href="#" class="button woof_term_image_button_upload"><?php _e('Upload', 'woocommerce-products-filter') ?></a>
                            <p class="description"><?php _e('Select image which will be displayed in the search form', 'woocommerce-products-filter') ?></p>
                        </div>

                        <div class="inside" style="padding: 2px;">
                            <textarea name="woof_term_style" class="text" rows="10"><?php echo $woof_term_style ?></textarea>
                            <p class="description">
                                <?php _e('Write here your CSS code for this image. Example:', 'woocommerce-products-filter') ?><br />
                                <code>width: 100px;<br />
                                    height:50px;<br />
                                    margin: 0 3px 3px 0;<br />
                                    background-size: 100% 100%;<br />
                                    background-clip: content-box;<br />
                                    border: 2px solid #e2e6e7;<br />
                                    padding: 2px;<br />
                                    color: #292f38;<br />
                                    font-size: 0;<br />
                                    text-align: center;<br />
                                    cursor: pointer;<br />
                                    -webkit-border-radius: 4px;<br />
                                    border-radius: 4px;<br />
                                    -webkit-transition: border-color .35s ease;<br />
                                    transition: border-color .35s ease;<br />
                                </code>
                            </p>
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
        if (isset($_POST['woof_term_image']) AND in_array($taxonomy, $this->image_taxonomies))
        {
            update_option("_woof_term_image_{$taxonomy}_{$term_id}", $_POST['woof_term_image']);
            update_option("_woof_term_style_{$taxonomy}_{$term_id}", $_POST['woof_term_style']);
        }
    }

    public function draw_tax_columns($columns)
    {
        return array_merge($columns, array(
            'woof_image' => __('WOOF Image', 'woocommerce-products-filter'),
        ));
    }

    public function draw_tax_columns_data($value = '', $column_name = '', $term_id = 0)
    {
        switch ($column_name)
        {
            case 'woof_image':
                $woof_term_image = $this->get_term_image($term_id);
                if (!empty($woof_term_image) AND $woof_term_image != 'hide')
                {
                    ?>
                    <div class="woof_term_image_demo" style="background: url(<?php echo $woof_term_image ?>);"></div>
                    <?php
                }
                break;
        }
    }

    public function get_term_style($term_id, $taxonomy = '')
    {
        if (empty($taxonomy))
        {
            $taxonomy = $this->current_taxonomy;
        }

        $default = "width: 100px;
height:50px;
margin: 0 3px 3px 0;
background-size: 100% 100%;
background-clip: content-box;
border: 2px solid #e2e6e7;
padding: 2px;
color: #292f38;
font-size: 0;
text-align: center;
cursor: pointer;
-webkit-border-radius: 4px;
border-radius: 4px;
-webkit-transition: border-color .35s ease;
transition: border-color .35s ease;";
        return get_option("_woof_term_style_{$taxonomy}_{$term_id}", $default);
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

WOOF_EXT::$includes['taxonomy_type_objects']['image'] = new WOOF_EXT_IMAGE_2();
