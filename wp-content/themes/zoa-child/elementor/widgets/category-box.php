<?php

namespace Elementor;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zoa category box widget.
 *
 * Zoa widget that displays an image, a headline, a text and a button.
 *
 * @since 1.0.0
 */
class Zoa_Category_Box extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve category box widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'zoa-category-box';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve category box widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Category Box', 'zoa' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve category box widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-info-box';
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
			'section_category',
			[
				'label' => esc_html__( 'Category Box', 'zoa' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Choose Layout', 'zoa' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'overlay' => esc_html__( 'Overlay', 'zoa' ),
					'normal'  => esc_html__( 'Normal', 'zoa' ),
				],
				'default'      => 'overlay',
				'prefix_class' => 'zoa-category-box-',
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose Image', 'zoa' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				// Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'       => esc_html__( 'Title Text', 'zoa' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'This is the heading', 'zoa' ),
				'placeholder' => esc_html__( 'Enter your text title', 'zoa' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title_size',
			[
				'label'   => esc_html__( 'Title HTML tag', 'zoa' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h3',
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'        => esc_html__( 'Show Button', 'zoa' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'zoa' ),
				'label_off'    => esc_html__( 'Hide', 'zoa' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'zoa' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Click here', 'zoa' ),
				'placeholder' => esc_html__( 'Enter your button title', 'zoa' ),
				'label_block' => true,
				'condition'   => [
					'show_button' => 'yes',
				],
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
			'section_style_image',
			[
				'label'     => esc_html__( 'Image', 'zoa' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout' => 'normal',
				],
			]
		);

		$this->add_responsive_control(
			'image-bottom-space',
			[
				'label'     => esc_html__( 'Bottom Spacing', 'zoa' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .zoa-category-box-image' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				],
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
			'content_position',
			[
				'label'        => esc_html__( 'Position', 'zoa' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'top-left'      => esc_html__( 'Top Left', 'zoa' ),
					'top-center'    => esc_html__( 'Top Center', 'zoa' ),
					'top-right'     => esc_html__( 'Top Right', 'zoa' ),
					'center-left'   => esc_html__( 'Center Left', 'zoa' ),
					'center-center' => esc_html__( 'Center Center', 'zoa' ),
					'center-right'  => esc_html__( 'Center Right', 'zoa' ),
					'bottom-left'   => esc_html__( 'Bottom Left', 'zoa' ),
					'bottom-center' => esc_html__( 'Bottom Center', 'zoa' ),
					'bottom-right'  => esc_html__( 'Bottom Right', 'zoa' ),
				],
				'default'      => 'center-center',
				'prefix_class' => 'zoa-category-box-',
				'condition'    => [
					'layout' => 'overlay',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Padding', 'zoa' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .zoa-category-box-content' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'layout' => 'overlay',
				],
			]
		);

		$this->add_control(
			'text_align',
			[
				'label'     => esc_html__( 'Alignment', 'zoa' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'zoa' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'zoa' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'zoa' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .zoa-category-box-content' => 'text-align:{{VALUE}};',
				],
				'condition' => [
					'layout' => 'normal',
				],
			]
		);

		$this->add_control(
			'content_title',
			[
				'label'     => esc_html__( 'Title', 'zoa' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_bottom_space',
			[
				'label'     => esc_html__( 'Bottom Spacing', 'zoa' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .zoa-category-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'zoa' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .zoa-category-box-title' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'layout' => 'overlay',
				],
			]
		);

		$this->add_control(
    'title_color',
    [
        'label'     => esc_html__( 'Color', 'zoa' ),
        'type'      => \Elementor\Controls_Manager::COLOR,
        'default'   => '',
        'selectors' => [
            '{{WRAPPER}} .zoa-category-box-title' => 'color:{{VALUE}};',
        ],
    ]
);


		$this->add_control(
    'title_background_color',
    [
        'label'     => esc_html__( 'Background Color', 'zoa' ),
        'type'      => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .zoa-category-box-title' => 'background-color:{{VALUE}};',
        ],
        'default'   => 'transparent',
    ]
);

// 		$this->add_group_control(
//     Group_Control_Typography::get_type(),
//     [
//         'name'     => 'title_typography',
//         'selector' => '{{WRAPPER}} .zoa-category-box-title',
//     ]
// );

		$this->add_control(
			'content_button',
			[
				'label'     => esc_html__( 'Button', 'zoa' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .zoa-category-box-button',
				'condition'   => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'zoa' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .zoa-category-box-button' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Color', 'zoa' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zoa-category-box-button' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button__typography',
				'selector'  => '{{WRAPPER}} .zoa-category-box-button',
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render category box widget output on the front end.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$image   = $settings['image'];
		$image_url = $image['url'];
		$image_alt = zoa_img_alt( $image['id'], esc_attr__( 'Category Box Image', 'zoa' ) );

		$title_text  = $settings['title_text'];
		$button_text = $settings['button_text'];

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
	<div class="zoa-category-box-wrapper">
		<div class="zoa-category-box-image">
			<a href="<?php echo esc_url( $href ); ?>" <?php echo wp_kses_post( $attr ); ?>>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
			</a>
		</div>

		<?php if ( ! empty( $title_text ) && 'yes' === $settings['show_button'] && ! empty( $button_text ) ) : ?>
		<div class="zoa-category-box-content">
			<div class="c-btn-cat_wrap">
			<a href="<?php echo esc_url( $href ); ?>" <?php echo wp_kses_post( $attr ); ?> class="c-btn-arrow c-btn-cat -white js-toggle-picks-overlay">
				<div class="c-btn-cat_wrap_text">
					<div class="c-element-zindex">
						<strong><?php echo esc_html( $title_text ); ?></strong>
						<span class="c-text-uppercase"><?php echo esc_html( $button_text ); ?></span>
					</div>
				</div>
				</a>
			</div>

			<?php if ( empty( $title_text ) && 'yes' === $settings['show_button'] && ! empty( $button_text ) ) : ?>
			<a href="<?php echo esc_url( $href ); ?>" <?php echo wp_kses_post( $attr ); ?> class="zoa-category-box-button"><?php echo esc_html( $button_text ); ?></a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Zoa_Category_Box() );
