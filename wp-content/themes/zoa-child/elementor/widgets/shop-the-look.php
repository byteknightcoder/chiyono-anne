<?php

namespace Elementor;
if ( ! class_exists( 'woocommerce' ) ) {
	return;
}
if (!defined('ABSPATH')) {
    return;
}

/**
 * Zoa landing image widget.
 *
 * Zoa widget that displays an landing image for landing page.
 *
 * @since 1.0.0
 */
class Zoa_Shop_The_Look extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve landing image widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'zoa_shop_the_look';
    }

    /**
     * Get widget title.
     *
     * Retrieve landing image widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Shop The Look', 'zoa');
    }

    /**
     * Get widget icon.
     *
     * Retrieve landing image widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-image-rollover';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the icon widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['zoa-theme'];
    }

    /**
     * Register category box widget controls.
     *
     * Add different input fields to allow the user to change and customize the widget settings
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls() {
        $this->start_controls_section(
                'section_shop_the_look', [
            'label' => esc_html__('Shop The Look Main Image', 'zoa'),
                ]
        );
        $this->add_control(
                'img', [
            'label' => esc_html__('Choose Image', 'zoa'),
            'type' => Controls_Manager::MEDIA,
            'dynamic' => [
                'active' => true,
            ],
            'default' => [
                'url' => Utils::get_placeholder_image_src(),
            ],
                ]
        );
        $this->add_control(
                'product_ids', array(
            'label' => esc_html__('Choose product', 'zoa'),
            'type' => Controls_Manager::SELECT2,
            'options' => zoa_get_narrow_data('post', 'product'),
            'multiple' => true,
                )
        );
                $this->add_control(
                    'extra_info', [
                'label' => esc_html__('Extra info', 'zoa'),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('', 'zoa'),
                'placeholder' => esc_html__('', 'zoa'),
                'label_block' => true,
                    ]
        );
        $this->end_controls_section();
    }

    /**
     * Render landing image widget output on the front end.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     *
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $image = $settings['img'];
        $image_url = $image['url'];
        if (!empty($image_url)) {
            $product_skus = array();
            foreach ($settings['product_ids'] as $value) {
                $product = wc_get_product($value);
                $product_skus[] = $product->get_sku();
            }
            echo '<div class="layout__02 column_01 fx fas fdr bsec bsec__wrap">
<div class="ho">';
            echo do_shortcode('[shop_the_look img="' . $image_url . '" product_skus="' . implode(",", $product_skus) . '"][/shop_the_look]');
            if(!empty($settings['extra_info'])){
                echo '<div class="ex_info">'.nl2br($settings['extra_info']).'</div>';
            }
            echo '</div>
</div>';
        }
    }

}

Plugin::instance()->widgets_manager->register_widget_type(new Zoa_Shop_The_Look());
