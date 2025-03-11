<?php

namespace Elementor;

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
class Zoa_Card_Style extends Widget_Base {

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
        return 'zoa_card_style';
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
        return esc_html__('Card Style', 'zoa');
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
        for ($i = 1; $i < 7; $i++) {
            $this->start_controls_section(
                    'section_card_style_' . $i, [
                'label' => esc_html__('Card Style ' . $i, 'zoa'),
                    ]
            );
            $this->add_control(
                    'img_' . $i, [
                'label' => esc_html__('Choose Image', 'zoa'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '',
                ],
                    ]
            );
            $this->add_control(
                    'show_type_' . $i, array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Display type', 'zoa'),
                'default' => 'Portrait',
                'options' => array(
                    'Portrait' => 'Portrait',
                    'Landscape' => 'Landscape',
                ),
                    )
            );
            $this->add_control(
                    'main_title_text_' . $i, [
                'label' => esc_html__('Title Text', 'zoa'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'placeholder' => esc_html__('Enter your text title', 'zoa'),
                'label_block' => true,
                    ]
            );
            $this->add_control(
                    'title_align_' . $i, array(
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
                    'title_text_' . $i, [
                'label' => esc_html__('Texts', 'zoa'),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('', 'zoa'),
                'placeholder' => esc_html__('', 'zoa'),
                'label_block' => true,
                    ]
            );
            $this->add_control(
                    'link_' . $i, [
                'label' => esc_html__('Link', 'zoa'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'placeholder' => esc_html__('', 'zoa'),
                'label_block' => true,
                    ]
            );
            $this->add_control(
                    'product_link_' . $i, array(
                'label' => esc_html__('Product link', 'zoa'),
                'type' => Controls_Manager::SELECT2,
                'options' => zoa_get_narrow_data('post', 'product'),
                'multiple' => false,
                    )
            );
            $this->end_controls_section();
        }
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
        $replace = array('<p>', '</p>');
        ?>
        <!--layout02 cardstyle-->
        <div class="layout__02 card__style bsec bsec__wrap">
            <div class="fx fw fas fdr masonry__grid grid">
                <!--start loop-->
                <?php
                for ($i = 1; $i < 7; $i++) {
                    if (!empty($settings['img_' . $i]['url']) && !empty($settings['title_text_' . $i])) {
                        $text = $settings['title_text_' . $i];
                        $show_type = $settings['show_type_' . $i];
                        if ($show_type == 'Portrait') {
                            $class = 'col__thum img_cover portlait';
                        } else {
                            $class = 'col__thum img_cover landscape';
                        }
                        $link = '';
                        if (!empty($settings['link_' . $i])) {
                            $link = $settings['link_' . $i];
                        } else {
                            if (!empty($settings['product_link_' . $i])) {
                                $link = get_permalink($settings['product_link_' . $i]);
                            }
                        }
                        $title_align = 'align--center';
                        if (!empty($settings['title_align_'.$i])) {
                            $title_align = $settings['title_align_'.$i];
                        }
                        ?>
                        <div class="ho card__item grid-item">
                            <div class="inner">
                                <div class="<?php echo $class; ?>">
                                    <a href="<?php echo $link; ?>" class="overlink"></a>
                                    <img src="<?php echo $settings['img_' . $i]['url']; ?>" alt="">
                                </div>
                                <div class="col__desc">
                                    <h3 class="section-title-bar o <?php echo $title_align; ?>"><?php echo $settings['main_title_text_' . $i]; ?></h3>
                                    <div class="paragraph"><p><?php echo str_replace($replace, '', $text); ?></p></div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <!--end loop-->
            </div>
        </div>
        <!-- end layout02 cardstyle-->
        <?php
    }

}

Plugin::instance()->widgets_manager->register_widget_type(new Zoa_Card_Style());
