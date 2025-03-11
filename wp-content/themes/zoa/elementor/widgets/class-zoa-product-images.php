<?php
/**
 * Elementor Product Images Widget
 *
 * @package Zoa
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for zoa elementor product images widget.
 */
class Zoa_Product_Images extends \Elementor\Widget_Base {
	/**
	 * Category
	 */
	public function get_categories() {
		return [ 'zoa-theme', 'woocommerce-elements-single' ];
	}

	/**
	 * Name
	 */
	public function get_name() {
		return 'zoa-product-images';
	}

	/**
	 * Gets the title.
	 */
	public function get_title() {
		return __( 'Zoa - Product Images', 'zoa' );
	}

	/**
	 * Gets the icon.
	 */
	public function get_icon() {
		return 'eicon-product-images';
	}

	/**
	 * Add a style.
	 */
	public function get_style_depends() {
		return [];
	}

	/**
	 * Gets the keywords.
	 */
	public function get_keywords() {
		return [ 'zoa', 'woocommerce', 'shop', 'store', 'image', 'product', 'gallery', 'lightbox' ];
	}

	/**
	 * General
	 */
	public function general() {
		$this->start_controls_section(
			'general',
			[
				'label' => __( 'Arrows', 'zoa' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Arrows border radius.
		$this->add_control(
			'arrows_border_radius',
			[
				'label'      => __( 'Border Radius', 'zoa' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tns-controls [data-controls]' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Tab Arrows Style.
		$this->start_controls_tabs(
			'arrows_style_tabs',
			[
				'separator' => 'before',
			]
		);
			$this->start_controls_tab(
				'arrows_style_normal_tab',
				[
					'label' => __( 'Normal', 'zoa' ),
				]
			);

				// Arrows background color.
				$this->add_control(
					'arrows_bg_color',
					[
						'type'      => Controls_Manager::COLOR,
						'label'     => esc_html__( 'Background Color', 'zoa' ),
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .tns-controls [data-controls]' => 'background-color: {{VALUE}}',
						],
					]
				);

				// Arrows color.
				$this->add_control(
					'arrows_color',
					[
						'type'      => Controls_Manager::COLOR,
						'label'     => esc_html__( 'Color', 'zoa' ),
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .tns-controls [data-controls]' => 'color: {{VALUE}}',
						],
					]
				);

			$this->end_controls_tab();

			// Tab background start.
			$this->start_controls_tab(
				'arrows_style_hover_tab',
				[
					'label' => __( 'Hover', 'zoa' ),
				]
			);

				// Arrows hover background color.
				$this->add_control(
					'arrows_bg_color_hover',
					[
						'type'      => Controls_Manager::COLOR,
						'label'     => esc_html__( 'Background Color', 'zoa' ),
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .tns-controls [data-controls]:hover' => 'background-color: {{VALUE}}',
						],
						'separator' => 'before',
					]
				);

				// Arrows hover color.
				$this->add_control(
					'arrows_color_hover',
					[
						'type'      => Controls_Manager::COLOR,
						'label'     => esc_html__( 'Color', 'zoa' ),
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .tns-controls [data-controls]:hover' => 'color: {{VALUE}}',
						],
					]
				);

			$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Main carousel
	 */
	public function product_images() {
		$this->start_controls_section(
			'product_images',
			[
				'label' => __( 'Product Images', 'zoa' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Arrows size.
		$this->add_control(
			'arrows_size',
			[
				'label'      => __( 'Arrows Size', 'zoa' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-images .tns-controls [data-controls]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Arrows icon size.
		$this->add_control(
			'arrows_icon_size',
			[
				'label'      => __( 'Arrows Icon Size', 'zoa' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-images .tns-controls [data-controls]:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Thumb carousel
	 */
	public function product_thumbnails() {
		$this->start_controls_section(
			'product_thumbnails',
			[
				'label' => __( 'Product Thumbnails', 'zoa' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Arrows thumbnail size.
		$this->add_control(
			'arrows_thumb_size',
			[
				'label'      => __( 'Arrows Size', 'zoa' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-thumbnail-images .tns-controls [data-controls]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Arrows thumbnail icon size.
		$this->add_control(
			'arrows_thum_icon_size',
			[
				'label'      => __( 'Arrows Icon Size', 'zoa' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-thumbnail-images .tns-controls [data-controls]:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Active border color.
		$this->add_control(
			'thumb_active_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Active Border Color', 'zoa' ),
				'default'   => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .product-thumbnail-images .tns-nav-active img' => 'border: 1px solid {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Controls
	 */
	protected function register_controls() {
		$this->general();
		$this->product_images();
		$this->product_thumbnails();
	}

	/**
	 * Render
	 */
	public function render() {
		?>
		<div class="zoa-product-images-widget">
			<?php zoa_product_gallery(); ?>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new Zoa_Product_Images() );
