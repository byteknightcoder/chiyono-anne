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
class Zoa_Paragraph_For_Blog extends Widget_Base {

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
        return 'zoa_paragraph_for_blog';
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
        return esc_html__('Single row text for blog', 'zoa');
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
        return 'eicon-t-letter';
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
            'label' => esc_html__('Paragraph For Blog', 'zoa'),
                ]
        );
        $this->add_control(
                'types_title', array(
            'label' => esc_html__('Types title', 'zoa'),
            'type' => Controls_Manager::SELECT,
            'default' => 'type-01',
            'options' => array(
                'type-01' => esc_html__('type-01 {default}', 'zoa'),
                'type-02' => esc_html__('type-02 {English Big Title}', 'zoa'),
                'type-03' => esc_html__('type-03 {underline title}', 'zoa'),
            ),
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
                'title_align', array(
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
                'paragraph', [
            'label' => __('Paragraph', 'zoa'),
            'type' => \Elementor\Controls_Manager::WYSIWYG,
            'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'zoa'),
            'placeholder' => __('Type your text here', 'zoa'),
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
        ?>
        <div class="para__center fx bsec">
            <div class="para_container">
                <?php
                $title_align = 'align--center';
                if (!empty($settings['title_align'])) {
                    $title_align = $settings['title_align'];
                }
                if ($settings['types_title'] == 'type-01') {
                    $class = 'section-title-bar title_style01 '.$title_align;
                } elseif ($settings['types_title'] == 'type-02') {
                    $class = 'section-title-bar bigger o '.$title_align;
                } elseif ($settings['types_title'] == 'type-03') {
                    $class = 'section-title-bar title_style04 t '.$title_align;
                }
                ?>
                <h3 class="<?php echo $class; ?>"><?php
                    if ($settings['types_title'] == 'type-03') {
                        echo '<span>' . $settings['title_text'] . '</span>';
                    } else {
                        echo $settings['title_text'];
                    }
                    ?></h3>
                <div class="main-text-component paragraph"><?php echo $settings['paragraph']; ?></div>
            </div>
        </div>
        <?php
    }

}

Plugin::instance()->widgets_manager->register_widget_type(new Zoa_Paragraph_For_Blog());
