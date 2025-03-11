<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Zoa landing image widget.
 *
 * Zoa widget that displays an landing image for landing page.
 *
 * @since 1.0.0
 */

class Zoa_Landing_Image extends Widget_Base {
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
		return 'zoa_landing_image';
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
		return esc_html__( 'Landing Image', 'zoa' );
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
		return [ 'zoa-theme' ];
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
			'section_landing',
			[
				'label' => esc_html__( 'Landing Image', 'zoa' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'zoa' ),
				'type'  => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'title_text',
			[
				'label' => esc_html__( 'Title Text', 'zoa' ),
				'type'  => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'This is the heading', 'zoa' ),
				'placeholder' => esc_html__( 'Enter your text title', 'zoa' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link to', 'zoa' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'zoa' ),
				'separator'   => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Content', 'zoa' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_style',
			[
				'label'     => esc_html__( 'Title', 'zoa' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .zoa-landing-image-title',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
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

		$image     = $settings['image'];
		$image_url = $image['url'];
		$image_alt = zoa_img_alt( $image['id'], esc_attr__( 'Landing Image', 'zoa' ) );

		$href = '#';
		$attr = '';

		if ( ! empty( $settings['link']['url'] ) ) {
			$href = $settings['link']['url'];

			if ( 'on' === $settings['link']['is_external'] ) {
				$attr .= ' target="_blank"';
			}

			if ( 'on' === $settings['link']['nofollow'] ) {
				$attr .= ' rel="nofollow"';
			}
		}
	?>
		<div class="zoa-landing-image">
			<div class="zoa-landing-image-rollover" style="background-image: url('<?php echo esc_url( $image_url ); ?>');">
				<a href="<?php echo esc_url( $href ); ?>" <?php echo wp_kses_post( $attr ); ?> class="zoa-landing-image-link"></a>
			</div>

			<h4 class="zoa-landing-image-title">
				<a href="<?php echo esc_url( $href ); ?>" <?php echo wp_kses_post( $attr ); ?>><?php echo esc_html( $settings['title_text'] ); ?></a>
			</h4>
		</div>
	<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Zoa_Landing_Image() );
