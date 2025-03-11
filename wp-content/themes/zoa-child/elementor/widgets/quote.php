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
class Zoa_Quote extends Widget_Base {

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
        return 'zoa_quote';
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
        return esc_html__('Quote for blog', 'zoa');
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
                'section_quote', [
            'label' => esc_html__('Quote for blog', 'zoa'),
                ]
        );
        $this->add_control(
                'title_align', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Align', 'zoa'),
            'default' => 'align--center',
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
        $title_align = 'align--center';
        if (!empty($settings['title_align'])) {
            $title_align = $settings['title_align'];
        }
        echo '<!--quote block-->
					<div class="block__quote bsec">
					<blockquote>
					<h3 class="large-text o '.$title_align.'">' . $settings['quote'] . '</h3>
					</blockquote>
					</div>
					<!--/quote block-->';
    }

}

Plugin::instance()->widgets_manager->register_widget_type(new Zoa_Quote());
