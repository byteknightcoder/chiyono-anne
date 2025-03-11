<?php

namespace Elementor;

if (!class_exists('woocommerce')) {
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
class Zoa_Shop_The_Look_And_Text extends Widget_Base {

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
        return 'zoa_shop_the_look_and_text';
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
        return esc_html__('Shop The Look And Text block And Quote And Products', 'zoa');
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
            'label' => esc_html__('Shop The Look', 'zoa'),
                ]
        );
        $this->add_control(
                'view_type', array(
            'label' => esc_html__('Types', 'zoa'),
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => array(
                'default' => esc_html__('default', 'zoa'),
                'reverse' => esc_html__('reverse', 'zoa'),
                'wide' => esc_html__('wide', 'zoa'),
            ),
                )
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
        $this->start_controls_section(
                'section__textblock', [
            'label' => esc_html__('Text block', 'zoa'),
                ]
        );
        $this->add_control(
                'num', [
            'label' => esc_html__('Number', 'zoa'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => esc_html__('', 'zoa'),
            'placeholder' => esc_html__('', 'zoa'),
            'label_block' => true,
                ]
        );
        $this->add_control(
                'title_text', [
            'label' => esc_html__('Title Text', 'zoa'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => esc_html__('This is the heading', 'zoa'),
            'placeholder' => esc_html__('Enter your text title', 'zoa'),
            'label_block' => true,
                ]
        );
        $this->add_control(
                'title_align', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Title align', 'zoa'),
            'default' => 'align--left',
            'options' => array(
                'align--left' => 'Left',
                'align--center' => 'Center',
                'align--right' => 'Right',
            ),
                )
        );
        $this->add_control(
                'paragraph', [
            'label' => __('Text block', 'zoa'),
            'type' => \Elementor\Controls_Manager::WYSIWYG,
            'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'zoa'),
            'placeholder' => __('Type your text here', 'zoa'),
                ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
                'section_quote', [
            'label' => esc_html__('Quote', 'zoa'),
                ]
        );
        $this->add_control(
                'quote_language', array(
            'label' => esc_html__('Languages', 'zoa'),
            'type' => Controls_Manager::SELECT,
            'default' => 'English',
            'options' => array(
                'English' => esc_html__('English', 'zoa'),
                'Japanese' => esc_html__('Japanese', 'zoa')
            ),
                )
        );
        $this->add_control(
                'title_align_quote', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Align', 'zoa'),
            'default' => 'align--left',
            'options' => array(
                'align--left' => 'Left',
                'align--center' => 'Center'
            ),
                )
        );
        $this->add_control(
                'quote', [
            'label' => __('Quote', 'zoa'),
            'type' => \Elementor\Controls_Manager::WYSIWYG,
            'default' => __('There was a PART of me that felt I needed to catch up… I was still quite NAIVE and felt I had to sort of LEVEL up my Britishness.', 'zoa'),
            'placeholder' => __('Type your text here', 'zoa'),
                ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
                'product_content', array(
            'label' => esc_html__('Product list', 'zoa'),
                )
        );
        $this->add_control(
                'pro_hide', array(
            'type' => Controls_Manager::SWITCHER,
            'label' => esc_html__('Hide', 'zoa'),
            'default' => '',
            'label_on' => esc_html__('Yes', 'zoa'),
            'label_off' => esc_html__('No', 'zoa'),
            'return_value' => 'yes',
                )
        );
        $this->add_control(
                'title_text_shop', [
            'label' => esc_html__('Title Text', 'zoa'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => esc_html__('This is the heading', 'zoa'),
            'placeholder' => esc_html__('Enter your text title', 'zoa'),
            'label_block' => true,
                ]
        );
        $this->add_control(
                'title_align_p', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Title align', 'zoa'),
            'default' => 'align--center',
            'options' => array(
                'align--left' => 'Left',
                'align--center' => 'Center',
                'align--right' => 'Right',
            ),
                )
        );
        $this->add_control(
                'col', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Columns', 'zoa'),
            'default' => 4,
            'options' => array(
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
            ),
                )
        );

        $this->add_control(
                'pro_pagi', array(
            'type' => Controls_Manager::SWITCHER,
            'label' => esc_html__('Pagination', 'zoa'),
            'default' => '',
            'label_on' => esc_html__('Yes', 'zoa'),
            'label_off' => esc_html__('No', 'zoa'),
            'return_value' => 'yes',
                )
        );

//        $this->add_control(
//                'product_cat', array(
//            'label' => esc_html__('Categories', 'zoa'),
//            'type' => Controls_Manager::SELECT2,
//            'options' => zoa_get_narrow_data('term', 'product_cat'),
//            'multiple' => true,
//                )
//        );
//
//        $this->add_control(
//                'pro_exclude', array(
//            'label' => esc_html__('Exclude product', 'zoa'),
//            'type' => Controls_Manager::SELECT2,
//            'options' => zoa_get_narrow_data('post', 'product'),
//            'multiple' => true,
//                )
//        );

        $this->add_control(
                'pro_include', array(
            'label' => esc_html__('Include product', 'zoa'),
            'type' => Controls_Manager::SELECT2,
            'options' => zoa_get_narrow_data('post', 'product'),
            'multiple' => true,
                )
        );

        $this->add_control(
                'count', array(
            'label' => esc_html__('Posts Per Page', 'zoa'),
            'type' => Controls_Manager::NUMBER,
            'default' => 4,
            'min' => 1,
            'max' => 100,
            'step' => 1,
                )
        );

        $this->add_control(
                'order_by', array(
            'label' => esc_html__('Order By', 'zoa'),
            'type' => Controls_Manager::SELECT,
            'default' => 'id',
            'options' => array(
                'id' => esc_html__('ID', 'zoa'),
                'name' => esc_html__('Name', 'zoa'),
                'date' => esc_html__('Date', 'zoa'),
                'rand' => esc_html__('Random', 'zoa'),
            ),
                )
        );

        $this->add_control(
                'order', array(
            'label' => esc_html__('Order', 'zoa'),
            'type' => Controls_Manager::SELECT,
            'default' => 'ASC',
            'options' => array(
                'ASC' => esc_html__('ASC', 'zoa'),
                'DESC' => esc_html__('DESC', 'zoa'),
            ),
                )
        );

        $this->end_controls_section();
        $this->start_controls_section(
                'pro_pagi_section', array(
            'label' => esc_html__('Pagination', 'zoa'),
            'condition' => array(
                'pro_pagi' => 'yes',
            ),
                )
        );

        $this->add_responsive_control(
                'pagi_position', array(
            'type' => Controls_Manager::CHOOSE,
            'label' => esc_html__('Alignment', 'zoa'),
            'options' => array(
                'left' => array(
                    'title' => esc_html__('Left', 'zoa'),
                    'icon' => 'fa fa-align-left',
                ),
                'center' => array(
                    'title' => esc_html__('Center', 'zoa'),
                    'icon' => 'fa fa-align-center',
                ),
                'right' => array(
                    'title' => esc_html__('Right', 'zoa'),
                    'icon' => 'fa fa-align-right',
                ),
            ),
            'default' => 'center',
            'tablet_default' => 'center',
            'mobile_default' => 'center',
            'selectors' => array(
                '{{WRAPPER}} .ht-pagination' => 'text-align: {{VALUE}};',
            ),
                )
        );

        $this->add_responsive_control(
                'pagi_space', array(
            'type' => Controls_Manager::DIMENSIONS,
            'label' => esc_html__('Space', 'zoa'),
            'size_units' => array('px', 'em'),
            'default' => array(
                'top' => '30',
                'right' => '0',
                'bottom' => '0',
                'left' => '0',
                'unit' => 'px',
                'isLinked' => false,
            ),
            'tablet_default' => array(
                'top' => '20',
                'right' => '0',
                'bottom' => '20',
                'left' => '0',
                'unit' => 'px',
                'isLinked' => false,
            ),
            'mobile_default' => array(
                'top' => '15',
                'right' => '0',
                'bottom' => '15',
                'left' => '0',
                'unit' => 'px',
                'isLinked' => false,
            ),
            'selectors' => array(
                '{{WRAPPER}} .ht-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
                )
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
    $image_url = isset($image['url']) ? $image['url'] : '';

    // Check if 'product_cat' exists in $settings
    $cat_id = isset($settings['product_cat']) ? $settings['product_cat'] : [];

    // Check if 'pro_exclude' exists in $settings
    $pro_exclude = isset($settings['pro_exclude']) ? $settings['pro_exclude'] : [];

    $paged = get_query_var('paged') ? intval(get_query_var('paged')) : 1;

    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'post__not_in' => $pro_exclude,  // Use the checked 'pro_exclude'
        'posts_per_page' => isset($settings['count']) ? $settings['count'] : 10,  // Set a default if 'count' is not set
        'orderby' => isset($settings['order_by']) ? $settings['order_by'] : 'date',  // Set a default ordering method
        'order' => isset($settings['order']) ? $settings['order'] : 'DESC',  // Set a default order direction
        'paged' => $paged,
    );

    // Add category filter if 'product_cat' is set and not empty
    if (!empty($cat_id)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $cat_id,
            ),
        );
    }

        if (!empty($settings['pro_include'])) {
            $args['post__in'] = $settings['pro_include'];
        }

        if (!empty($cat_id)) :
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $cat_id,
                ),
            );
        endif;

        $products_query = new \WP_Query($args);
        $class_type = 'layout__01 bsec bsec__wrap';
        if ($settings['view_type'] == 'reverse') {
            $class_type .= ' bsec_even';
        } elseif ($settings['view_type'] == 'wide') {
            $class_type = 'para__center bsec';
        }
if (!empty($settings['product_ids']) && is_array($settings['product_ids'])) {
    $product_skus = array();
    foreach ($settings['product_ids'] as $value) {
        $product = wc_get_product($value);
        // It's still good to check if the product exists before accessing its SKU
        if ($product) {
            $product_skus[] = $product->get_sku();
        }
    }
            $title_align = 'align--left';
            if (!empty($settings['title_align'])) {
                $title_align = $settings['title_align'];
            }
            echo '<div class="' . $class_type . '">
						<div class="col__left col__img">';
            echo do_shortcode('[shop_the_look img="' . $image_url . '" product_skus="' . implode(",", $product_skus) . '"][/shop_the_look]');
            if (!empty($settings['extra_info'])) {
                echo '<div class="ex_info">' . nl2br($settings['extra_info']) . '</div>';
            }
            echo '</div>
						<div class="col__right col__txt">
							<div class="text_inner">
                                                        <h3 class="section-title-bar title_style03 num_ttl ' . $title_align . '"><span class="num">' . $settings['num'] . '</span><span>' . $settings['title_text'] . '</span></h3>
                                                                  <div class="paragraph">
								' . $settings['paragraph'] . '
                                                                    </div>';
            if (!empty($settings['quote'])) {
                $title_align_quote = 'align--left';
                if (!empty($settings['title_align_quote'])) {
                    $title_align_quote = $settings['title_align_quote'];
                }
                $quote_language = '';
                if ($settings['quote_language'] == 'Japanese') {
                    $quote_language = 'quote_ja';
                }
                echo '<!--quote block-->
					<div class="block__quote bsec ' . $quote_language . '">
					<blockquote>
					<h3 class="large-text o '.$title_align_quote.'">' . $settings['quote'] . '</h3>
					</blockquote>
					</div>
					<!--/quote block-->';
            }
            $title_align_p = 'align--center';
            if (!empty($settings['title_align_p'])) {
                $title_align_p = $settings['title_align_p'];
            }
            if ($products_query->have_posts() && $settings['pro_hide'] != 'yes') {
                $total = $products_query->found_posts;
                $class_col = '';
                if ($total == 1 || $settings['count'] == 1) {
                    $class_col = ' col_01';
                } elseif ($total == 2 || $settings['count'] == 2) {
                    $class_col = ' col_02';
                }
                ?>
                <div class="bsec bsec__wrap<?php echo $class_col; ?>">
                    <h5 class="section-subttl o upper <?php echo $title_align_p; ?>"><?php echo $settings['title_text_shop']; ?></h5>
                    <div class="zoa-widget-products woocommerce list-shops">

                        <?php
                        global $woocommerce_loop;

                        $woocommerce_loop['columns'] = (int) $settings['col'];

                        woocommerce_product_loop_start();

                        while ($products_query->have_posts()) :
                            $products_query->the_post();
                            //fw_print( get_the_title() );
                            wc_get_template_part('content', 'product');
                        endwhile;

                        woocommerce_product_loop_end();

                        woocommerce_reset_loop();

                        if ('yes' == $settings['pro_pagi']) {
                            zoa_paging($products_query);
                        }

                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
                <?php
            }
            echo '</div>
                </div>
					</div>';
        }
    }

}

Plugin::instance()->widgets_manager->register_widget_type(new Zoa_Shop_The_Look_And_Text());
