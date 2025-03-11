<?php

namespace Elementor;

class zoa_widget_products_for_blog extends Widget_Base {

    public function get_categories() {
        return array('zoa-theme');
    }

    public function get_name() {
        return 'products_for_blog';
    }

    public function get_title() {
        return esc_html__('Woo - Products for blog', 'zoa');
    }

    public function get_icon() {
        return 'eicon-woocommerce';
    }

    protected function _register_controls() {
        $this->sectionGeneral();
        $this->sectionQuery();
        $this->sectionPagination();
    }

    private function sectionGeneral() {
        $this->start_controls_section(
                'product_content', array(
            'label' => esc_html__('General', 'zoa'),
                )
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
                'subtitle_text', [
            'label' => esc_html__('Sub Title Text', 'zoa'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => esc_html__('Sub Title Text', 'zoa'),
            'placeholder' => esc_html__('Sub Title Text', 'zoa'),
            'label_block' => true,
                ]
        );
        $this->add_control(
                'title_align', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Title and sub title align', 'zoa'),
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

        $this->end_controls_section();
    }

    private function sectionQuery() {
        $this->start_controls_section(
                'product_query', array(
            'label' => esc_html__('Query', 'zoa'),
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
    }

    private function sectionPagination() {
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

    protected function render() {
        $settings = $this->get_settings_for_display();
        $cat_id = $settings['product_cat'];
        $paged = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'post__not_in' => $settings['pro_exclude'],
            'posts_per_page' => $settings['count'],
            'orderby' => $settings['order_by'],
            'order' => $settings['order'],
            'paged' => $paged,
        );

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
        if (!$products_query->have_posts()) {
            return;
        }
        $total = $products_query->found_posts;
        $class_col = '';
        if ($total == 1 || $settings['count'] == 1) {
            $class_col = ' col_01';
        } elseif ($total == 2 || $settings['count'] == 2) {
            $class_col = ' col_02';
        }
        $title_align='align--center';
        if(!empty($settings['title_align'])){
            $title_align=$settings['title_align'];
        }
        ?>
        <div class="bsec bsec__wrap<?php echo $class_col; ?>">
            <h3 class="section-title-centre <?php echo $title_align; ?>"><span class="tsm"><?php echo $settings['subtitle_text']; ?></span><span><?php echo $settings['title_text']; ?></span></h3>
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

}

Plugin::instance()->widgets_manager->register_widget_type(new zoa_widget_products_for_blog());
