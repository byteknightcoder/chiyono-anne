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
class Zoa_Video_Player extends Widget_Base {

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
        return 'zoa_video_player';
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
        return esc_html__('Video Player', 'zoa');
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
        return 'eicon-youtube';
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
                    'section_video_player', [
                'label' => esc_html__('Video player', 'zoa'),
                    ]
            );

            $this->add_control(
                    'poster', [
                'label' => esc_html__('Poster Image', 'zoa'),
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
                    'video_mp4', [
                'label' => esc_html__('Video mp4', 'zoa'),
                'type' => Controls_Manager::MEDIA,
                'media_type' => 'video',
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '',
                ],
                    ]
            );
            $this->add_control(
                    'video_webm', [
                'label' => esc_html__('Video webm', 'zoa'),
                'type' => Controls_Manager::MEDIA,
                'media_type' => 'video',
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '',
                ],
                    ]
            );
            $this->add_control(
                    'video_ogv', [
                'label' => esc_html__('Video ogv', 'zoa'),
                'type' => Controls_Manager::MEDIA,
                'media_type' => 'video',
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '',
                ],
                    ]
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
        $poster = '';
        if (!empty($settings['poster']['url'])) {
            $poster = $settings['poster']['url'];
        }

        ?>
        <!-- video player-->
        <div class="video-wrapper">
            <div class="video-container" id="video-container">
                <video id="video" preload="auto" poster="<?php echo $poster; ?>">
                    <source src="<?php echo $settings['video_mp4']['url']; ?>" type="video/mp4">
                        <source src="<?php echo $settings['video_webm']['url']; ?>" type="video/webm">
                            <source src="<?php echo $settings['video_ogv']['url']; ?>" type="video/ogv">
                                </video>
                                <div class="play-button-wrapper">
                                    <div title="Play video" class="play-gif" id="circle-play-b">
                                        <!-- SVG Play Button -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80">
                                            <path d="M40 0a40 40 0 1040 40A40 40 0 0040 0zM26 61.56V18.44L64 40z" />
                                        </svg>
                                    </div>
                                </div>
                                </div>
                                </div>
                                <!-- end video player-->
                                <?php
                            }

                        }

                        Plugin::instance()->widgets_manager->register_widget_type(new Zoa_Video_Player());
                        